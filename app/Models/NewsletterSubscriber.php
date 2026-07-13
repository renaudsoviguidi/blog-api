<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class NewsletterSubscriber extends Model
{
    //
    use HasFactory, LogsActivity;

    protected $fillable = [
        'email',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_token',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /// - Cette fonction permet à Laravel de prendre ref comme attribut dans les urls
    public function getRouteKeyName()
    {
        return 'ref';
    }
    
    protected static function booted(): void
    {
        static::creating(function ($subscriber) {
            $subscriber->ref = (string) Str::uuid();
            $subscriber->unsubscribe_token = Str::random(64);
            $subscriber->subscribed_at = now();
        });
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeInactive($q)
    {
        return $q->where('is_active', false);
    }

    /// - Helpers
    public function unsubscribe(): void
    {
        $this->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    public function resubscribe(): void
    {
        $this->update([
            'is_active' => true,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']) //Spécifier les attributs concernés par les logs
            ->logOnlyDirty() //Pour enregistrer dans le log uniquement l'attribut qui a subit de changement
            ->logFillable(['*']) //Spécifier les attributs de la variable $fillable à utiliser
            //->setDescriptionForEvent(fn(string $eventName) => "Ce model a été {$eventName}")
            ->dontSubmitEmptyLogs() //Empecher l'enregistrement de log vide
            ->useLogName('system'); //Utiliser system comme log name
    }

}
