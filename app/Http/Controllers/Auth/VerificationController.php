<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    //
    use ApiResponseTrait;

    public function __construct(private AuthService $authService) {}

    public function verify(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        Log::info('verify-email called', ['email' => $email, 'token' => $token]);

        if (!$token || !$email) {
            return $this->error(
                "Lien invalide ou incomplet.",
                null,
                422
            );
        }

        try {
            $this->authService->verifyEmail($email, $token);

            return $this->success(
                null,
                "Compte activé avec succès !",
                200
            );

        } catch (\Exception $e) {
            return $this->error(
                $e->getMessage(),
                null,
                $e->getCode() ?: 400
            );
        }
    }

    public function resend(Request $request)
    {
        $request->validate([
            "email" => "required|email",
        ]);

        try {
            $this->authService->resendActivationLink($request->email);

            return $this->success(null, "Un nouveau lien d'activation a été envoyé.", 200);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, $e->getCode() ?: 422);
        }
    }
}
