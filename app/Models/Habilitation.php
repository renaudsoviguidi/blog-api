<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Habilitation extends Model
{
    //
    use HasFactory, LogsActivity;
    public $table = "habilitations";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = ['libelle', 'slug', 'description', 'ref', 'created_by', 'updated_by'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ref = Str::uuid();
        });
    }

    public function get_created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function get_updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'habilitation_role'
        )->using(HabilitationRole::class);
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
