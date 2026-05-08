<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RefreshToken extends Model
{
    //
    use HasFactory;

    protected $fillable = ['token', 'user_id', 'revoked'];

    public static function createForUser($userId)
    {
        $token = Str::random(60);

        return self::create([
            'token' => hash('sha256', $token),
            'user_id' => $userId,
            'revoked' => false,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid()
    {
        return !$this->revoked && $this->created_at->diffInMinutes(now()) <= config('auth.refresh_token_lifetime');
    }

}
