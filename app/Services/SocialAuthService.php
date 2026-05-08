<?php
namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthService
{

    public function loginWithGoogle(string $googleToken): array
    {
        try {
            //code...

            DB::beginTransaction();

            $client = new GoogleClient([
                'client_id' => config('services.google.client_id')
            ]);

            $payload = $client->verifyIdToken($googleToken);

            if (!$payload) {
                throw new \Exception("Token Google invalide");
            }

            if (!$payload['email_verified']) {
                throw new \Exception("Email Google non vérifié");
            }

            $email = $payload['email'];
            $googleId = $payload['sub'];
            $name = $payload['name'];
            $avatar = $payload['picture'] ?? null;

            $user = User::where("google_id", $googleId)->first();

            if (!$user) {

                $user = User::where("email", $email)->first();

                if ($user && $user->provider !== "google") {
                    throw new \Exception(
                        "Cet email est déjà utilisé avec une connexion classique"
                    );
                }

                if (!$user) {

                    $user = User::create([
                        "name" => $name,
                        "email" => $email,
                        "google_id" => $googleId,
                        "provider" => "google",
                        "avatar" => $avatar,
                        "is_active" => 1,
                        "email_verified_at" => now(),
                        "password" => Hash::make(Str::random(16))
                    ]);

                    $role = Role::where("libelle", "USER")->first();

                    $user->roles()->attach($role->id, [
                        "ref" => Str::uuid()
                    ]);
                }
            }

            $token = $user->createToken("blog-api")->accessToken;

            DB::commit();

            return [
                "user" => $user,
                "token" => $token
            ];

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}