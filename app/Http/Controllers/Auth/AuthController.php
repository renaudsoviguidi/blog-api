<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoogleLoginRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\AuthService;
use App\Services\SocialAuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    //
    use ApiResponseTrait;

    public function __construct(private AuthService $authService, private SocialAuthService $socialAuthService) {}

    /// - Fonction pour l'enregistrement des utilisateurs
    public function register(RegisterRequest $request)
    {
        try {
            //code...
            $data = $this->authService->register($request->validated());

            return $this->success([
                "user" => new UserResource($data["user"]),
            ], "Inscription réussie",  201);

        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                "Erreur lors de l'inscription",
                500
            );
        }
    }

    /// - Fonction pour la connexion d'un utilisateur
    public function login(LoginRequest $request)
    {

        $data = $this->authService->login($request->validated());

        if(!$data){
            return $this->error("Identifiants incorrects",null,401);
        }

        return $this->success([
            "user" => new UserResource($data["user"]),
            "token" => $data["token"],
            "refreshToken" => $data["refreshToken"]
        ], "Connexion réussie", 201);
    }

    public function googleLogin(GoogleLoginRequest $request)
    {
        try {

            $data = $this->socialAuthService->loginWithGoogle(
                $request->validated()['token']
            );

            return $this->success([
                "user" => new UserResource($data["user"]),
                "token" => $data["token"]
            ], "Connexion Google réussie");

        } catch (\Throwable $th) {

            return $this->error(
                $th->getMessage(),
                "Erreur authentification Google",
                500
            );
        }
    }

    /// - Fonction pour la déconnexion
    public function logout()
    {
        $this->authService->logout(auth()->user());

        return $this->success(null, "Déconnexion réussie");
    }
    

    /* public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {


            $googleUser = Socialite::driver('google')->stateless()->user();

            $data = $this->authService->registerWithGoogle($googleUser);


            return $this->success([
                "user" => new UserResource($data["user"]),
                "token" => $data["token"],
                "refreshToken" => $data["refreshToken"]
            ], "Inscription Google réussie");

        } catch (\Throwable $th) {

            return $this->error(
                $th->getMessage(),
                "Erreur connexion Google",
                500
            );
        }
    } */

}
