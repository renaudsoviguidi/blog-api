<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Services\PasswordResetService;
use App\Traits\ApiResponseTrait;

use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    //
    use ApiResponseTrait;

    public function __construct(private PasswordResetService $service) {}

    public function sendOtp(ForgotPasswordRequest $request)
    {
        try {
            /// - Appel du service
            $this->service->sendOtp($request->email);

            /// - Appel de "ApiResponseTrait" pour les messages
            return $this->success(null, "Si cet email existe, un code vous a été envoyé.");
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), "Erreur", 500);
        }
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $this->service->verifyOtp($request->email, $request->otp);
            return $this->success(null, "Code valide.");
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), "Code invalide", 422);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->service->resetPassword(
                $request->email,
                $request->otp,
                $request->password
            );
            return $this->success(null, "Mot de passe réinitialisé avec succès.");
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), "Erreur", 422);
        }
    }
}
