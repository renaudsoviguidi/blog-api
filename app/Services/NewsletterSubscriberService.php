<?php

namespace App\Services;

use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscriber;
use App\Repositories\NewsletterSubscriberRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class NewsletterSubscriberService
{
    public function __construct(
        private readonly NewsletterSubscriberRepository $repository,
    ) {}

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    /**
     * Abonner un email. Si déjà abonné et confirmé → erreur métier.
     */
    public function subscribe(array $data): NewsletterSubscriber
    {
        $existing = $this->repository->findByEmail($data['email']);

        if ($existing) {
            if ($existing->is_active) {
                throw ValidationException::withMessages([
                    'email' => ['Cette adresse est déjà abonnée.'],
                ]);
            }

            $existing->resubscribe();

            // Renvoi du mail de bienvenue
            Mail::to($existing->email)->send(new NewsletterWelcomeMail($existing));

            return $existing->fresh();
        }

        $subscriber = $this->repository->create([
            'email' => $data['email'],
            'is_active' => true,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Envoi du mail de bienvenue
        Mail::to($subscriber->email)->send(new NewsletterWelcomeMail($subscriber));

        return $subscriber;
    }

    /**
     * Confirmer via le token reçu par email.
     */
    /* public function confirm(string $token): NewsletterSubscriber
    {
        $subscriber = $this->repository->findByToken($token);

        abort_if(!$subscriber, 404, 'Token invalide ou expiré.');

        $subscriber->confirm();

        return $subscriber;
    } */

    /**
     * Se désabonner via le token.
     */
    public function unsubscribe(string $token): void
    {
        $subscriber = $this->repository->findByToken($token);

        abort_if(!$subscriber, 404, 'Token invalide.');
        abort_if(!$subscriber->is_active, 422, 'Déjà désabonné.');

        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    public function delete(NewsletterSubscriber $subscriber): void
    {
        DB::transaction(function () use ($subscriber) {
            $this->repository->delete($subscriber);
        });
    }
}