<?php

namespace App\Enums;

enum CommentStatusEnum: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Spam = 'spam';
    case Hidden = 'hidden';

    // Label lisible pour l'UI / l'admin
    public function label(): string
    {
        return match($this) {
            self::Pending => 'En attente',
            self::Approved => 'Approuvé',
            self::Rejected => 'Rejeté',
            self::Spam => 'Spam',
            self::Hidden => 'Masqué',
        };
    }

    // Couleur badge pour le frontend (optionnel mais pratique)
    public function color(): string
    {
        return match($this) {
            self::Pending => 'yellow',
            self::Approved => 'green',
            self::Rejected => 'red',
            self::Spam => 'orange',
            self::Hidden => 'gray',
        };
    }

    // Statuts visibles publiquement
    public static function visible(): array
    {
        return [self::Approved];
    }
}