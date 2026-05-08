<?php
namespace App\Services;

use App\Mail\NotificationNewCompte;
use App\Models\RefreshToken;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Mail;

class AuthService
{

    /// - Génère un code unique pour l'activation de compte
    private function generateUniqueCode(): string
    {
        do {
            $code = sha1(time());
        } while (User::where('code_email', $code)->exists());

        return $code;
    }

    public function register(array $data)
    {

        DB::beginTransaction();
        try {
            //code...

            $code = $this->generateUniqueCode();
            //dd($code);

            $user = new User();
            $user->setAttribute("name", $data["name"]);
            $user->setAttribute("email", $data["email"]);
            $user->setAttribute("password", Hash::make($data["password"]));
            $user->setAttribute("code_email", $code);
            $user->setAttribute("code_email_expired_at", now()->addMinutes(60));
            $user->save();

            /* $user = User::create([
                "name" => $data["name"],
                "email" => $data["email"],
                "password" => Hash::make($data["password"]),
                "code_email" => $code,
                "code_email_expired_at" => now()->addMinutes(60), // expire dans 60 mins
            ]); */

            // récupérer rôle USER
            $role = Role::where("libelle", "USER")->first();

            $user->roles()->attach($role->id, [
                "ref" => Str::uuid(),
            ]);

            /// - Envoi du mail à l'utilisateur qui vient de s'inscrire

            /// - Construit l'URL d'activation avec le token dans l'URL
            $activationUrl = env('FRONTEND_URL') . '/verify-email?token=' . $code . '&email=' . urlencode($user->email);
            $contenu = [
                "nom_prenoms" => $user->name,
                "email" => $user->email,
                "subject" => "Nouveau compte",
                "code_activation" => $code,
                'activation_url' => $activationUrl,
            ];

            Mail::to($user->email)->bcc(env("MAIL_BCC"))->send(new NotificationNewCompte($contenu));

            DB::commit();

            return [
                "user" => $user,
            ];

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /// ─Vérification du code
    public function verifyEmail(string $email, string $token): User
    {
        $user = User::where("email", $email)->first();

        if (!$user) {
            throw new \Exception('Aucun compte trouvé avec cet email.', 404);
        }

        if ($user->email_verified_at) {
            throw new \Exception('Ce compte est déjà activé.', 409);
        }

        /// - Vérifie l'expiration AVANT de vérifier le token
        if ($user->code_email_expired_at && now()->isAfter($user->code_email_expired_at)) {
            throw new \Exception('Ce lien a expiré. Demandez un nouveau lien.', 410);
        }

        /// - Vérifie le token séparément pour un message précis
        if ($user->code_email !== $token) {
            throw new \Exception('Lien invalide ou déjà utilisé.', 422);
        }

        /// - Active le compte et nettoie le code
        $user->update([
            "is_active" => 1,
            "email_verified_at" => now(),
            "code_email" => null,
            "code_email_expired_at" => null
        ]);

        return $user;
    }

    /// ─ Renvoi du lien d'activation
    public function resendActivationLink(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('Aucun compte trouvé avec cet email.', 404);
        }

        if ($user->email_verified_at) {
            throw new \Exception('Ce compte est déjà activé.', 409);
        }

        $code = $this->generateUniqueCode();
        $activationUrl = env('FRONTEND_URL') . '/verify-email?token=' . $code . '&email=' . urlencode($user->email);

        $user->update([
            "code_email" => $code,
            "code_email_expired_at" => now()->addMinutes(60),
        ]);

        $contenu = [
            "nom_prenoms" => $user->name,
            "email" => $user->email,
            "subject" => "Nouveau code d'activation MonBlog",
            'activation_url' => $activationUrl,
        ];

        Mail::to($user->email)
            ->bcc(env("MAIL_BCC"))
            ->send(new NotificationNewCompte($contenu));
    }

    public function login(array $data)
    {

        if (!Auth::attempt($data)) {
            return null;
        }

        $user = Auth::user();

        $token = $user->createToken("blog-api")->accessToken;

        /// - Génération du refreshToken
        $refreshToken = RefreshToken::createForUser($user->id)->token;

        return [
            "user" => $user,
            "token" => $token,
            "refreshToken" => $refreshToken
        ];
    }

    public function logout($user)
    {
        $user->token()->revoke();
    }

}
