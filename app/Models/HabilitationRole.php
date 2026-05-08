<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class HabilitationRole extends Pivot
{
    //
    use HasFactory, LogsActivity;

    public $timestamps = false;

    protected $fillable = ['role_id', 'habilitation_id'];


    public function habilitation()
    {
        return $this->belongsTo(Habilitation::class, 'habilitation_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
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
