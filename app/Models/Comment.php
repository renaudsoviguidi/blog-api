<?php

namespace App\Models;

use App\Enums\CommentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Comment extends Model
{
    //
    use HasFactory, LogsActivity;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'guest_name',
        'guest_email',
        'ref',
        'status',
        'rejection_reason',
        'ip_address',
        'user_agent',
        'likes_count',
        'moderated_by',
        'moderated_at'
    ];

    protected $casts = [
        'status' => CommentStatusEnum::class,
        'moderated_at' => 'datetime',
        'likes_count' => 'integer',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }

    public function moderatedBy()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->ref = (string) Str::uuid();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'content',
                'status',
                'moderated_by',
            ]) //Spécifier les attributs concernés par les logs
            ->logOnlyDirty() //Pour enregistrer dans le log uniquement l'attribut qui a subit de changement
            ->logFillable(['*']) //Spécifier les attributs de la variable $fillable à utiliser
            //->setDescriptionForEvent(fn(string $eventName) => "Ce model a été {$eventName}")
            ->dontSubmitEmptyLogs() //Empecher l'enregistrement de log vide
            ->useLogName('system'); //Utiliser system comme log name
    }

    /// ─ Helpers
    public function moderate(CommentStatusEnum $status, int $moderatorId): void
    {
        $this->update([
            'status' => $status,
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
        ]);
    }

    public function approve(int $moderatorId): void
    {
        $this->moderate(CommentStatusEnum::Approved, $moderatorId);
    }

    public function reject(int $moderatorId): void
    {
        $this->moderate(CommentStatusEnum::Rejected, $moderatorId);
    }

    /// ─ Scopes
    public function scopeApproved($q)
    {
        return $q->where('status', CommentStatusEnum::Approved);
    }

    public function scopePending($q)
    {
        return $q->where('status', CommentStatusEnum::Pending);
    }

    public function scopeSpam($q)
    {
        return $q->where('status', CommentStatusEnum::Spam);
    }

    public function scopeHidden($q)
    {
        return $q->where('status', CommentStatusEnum::Hidden);
    }

    // Scope générique pour les statuts visibles publiquement
    public function scopeVisible($q)
    {
        return $q->whereIn('status', CommentStatusEnum::visible());
    }
}
