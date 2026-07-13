<?php

namespace App\Services;

use App\Mail\NotificationNewCompte;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserService
{
    public function __construct(
        private readonly UserRepository $repository
    ) {}

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function stats(): array
    {
        return $this->repository->countByStatus();
    }

    public function create(array $data): User
    {
        $user =  DB::transaction(function () use ($data) {
            $roleIds = $data['role_ids'] ?? [];

            /// - Générer le code d'activation
            $code = $this->generateUniqueCode();
            $expiry  = now()->addHours(24);

            $user = $this->repository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => $data['is_active'] ?? false,
                'code_email' => $code,
                'code_email_expired_at' => $expiry,
            ]);

            if ($roleIds) {
                $user->roles()->sync($roleIds);
            }

            return $user;
        });

        /// ── Envoi du mail HORS transaction
        /// - Si le mail échoue, le user est déjà créé → pas de rollback
        try {
            $activationUrl = config('app.url')
                . '/api/auth/verify-email?token='
                . $user->code_email
                . '&email='
                . urlencode($user->email);

            Mail::to($user->email)
                ->bcc(config('mail.bcc'))
                ->send(new NotificationNewCompte([
                    'nom_prenoms' => $user->name,
                    'email' => $user->email,
                    'subject' => 'Activation de votre compte',
                    'code_activation' => $user->code_email,
                    'activation_url' => $activationUrl,
                ]));

        } catch (\Throwable $e) {
            // Logger l'erreur sans bloquer la création
            report($e);
        }

        return $user->load('roles');
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'] ?? $user->is_active,
            ];

            // Mot de passe optionnel à la modification
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user = $this->repository->update($user, $updateData);

            // Sync des rôles si fournis
            if (isset($data['role_ids'])) {
                $user->roles()->sync($data['role_ids']);
            }

            return $user->load('roles');
        });
    }

    public function toggleActive(User $user): User
    {
        $user->update(['is_active' => !$user->is_active]);
        return $user->fresh();
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Détacher les rôles avant suppression
            $user->roles()->detach();
            $this->repository->delete($user);
        });
    }

    /// - Génère un code unique pour l'activation de compte
    private function generateUniqueCode(): string
    {
        do {
            $code = sha1(time());
        } while (User::where('code_email', $code)->exists());

        return $code;
    }
}