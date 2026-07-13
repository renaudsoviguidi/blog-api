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
            return redirect(
                config('app.frontend_url') . '/auth/login'
                . '?error=invalid_link'
                . '&message=' . urlencode('Lien invalide ou incomplet.')
            );
        }

        try {
            $this->authService->verifyEmail($email, $token);

            return redirect(
                config('app.frontend_url') . '/auth/login'
                . '?verified=1'
                . '&message=' . urlencode('Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.')
            );

        } catch (\Exception $e) {
            $errorMap = [
                409 => 'already_verified',
                410 => 'link_expired',
                422 => 'invalid_link',
                404 => 'invalid_link',
            ];

            $errorCode = $errorMap[$e->getCode()] ?? 'invalid_link';

            return redirect(
                config('app.frontend_url') . '/auth/login'
                . '?error=' . $errorCode
                . '&message=' . urlencode($e->getMessage())
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
