<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Like extends Model
{
    //
    use HasFactory, LogsActivity;

    protected $fillable = ['user_id'];

    public function likeable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ref = Str::uuid();
        });
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
