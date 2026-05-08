<?php
namespace App\Services;

use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetService
{
    public function sendOtp(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            // On ne révèle pas si l'email existe — sécurité
            return;
        }

        // Supprime les anciens OTPs
        PasswordResetOtp::where('email', $email)->delete();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($email)->send(new PasswordResetOtpMail($otp, $user->name));
    }

    public function verifyOtp(string $email, string $otp): PasswordResetOtp
    {
        $record = PasswordResetOtp::where('email', $email)->latest()->first();

        if (!$record || $record->isExpired() || $otp !== $record->otp) {
            throw new \Exception("Code invalide ou expiré.");
        }

        return $record;
    }

    public function resetPassword(string $email, string $otp, string $password): void
    {
        $record = $this->verifyOtp($email, $otp);

        User::where('email', $email)->update([
            'password' => Hash::make($password),
        ]);

        $record->delete();
    }
}