<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, LogsActivity;

    public $table = "users";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "name",
        "email",
        "password",
        "google_id",
        "provider",
        "avatar",
        "email_verified_at",
        "ref",
        "is_active",
        "code_email",
        "code_email_expired_at",
        "created_at",
        "updated_at"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ref = Str::uuid();
        });
    }


    public function get_role_user()
    {
        return $this->hasMany(RoleUser::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id','role_id');
    }

    public function hasRole($role)
    {
        return $this->roles()->where('libelle',$role)->exists();
    }

    public function habilitations()
    {
        return $this->roles()->with('habilitations')->get()->pluck('habilitations')->flatten()->unique('id')->values();
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function hasHabilitation(string $slug): bool
    {
        // Chargement des rôles → habilitations du user
        return $this->load('roles.habilitations')->roles()
            ->with('habilitations')
            ->get()
            ->pluck('habilitations')
            ->flatten()
            ->pluck('slug')
            ->contains($slug);
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
