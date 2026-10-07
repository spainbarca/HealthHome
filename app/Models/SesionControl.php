<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SesionControl extends Model
{
    protected $table = 'sesiones_control';

    protected $fillable = [
        'persona_id',
        'tipo_control_id',
        'dispositivo_id',
        'fecha_hora',
        'origen',
        'estado_previo',
        'observaciones',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function persona(): BelongsTo
    {
        return $this->belongsTo(
            Persona::class,
            'persona_id'
        );
    }

    public function tipoControl(): BelongsTo
    {
        return $this->belongsTo(
            TipoControl::class,
            'tipo_control_id'
        );
    }

    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(
            Dispositivo::class,
            'dispositivo_id'
        );
    }

    public function mediciones(): HasMany
    {
        return $this->hasMany(
            Medicion::class,
            'sesion_control_id'
        )->orderBy('numero_medicion');
    }

    public function sintomas(): BelongsToMany
    {
        return $this->belongsToMany(
            Sintoma::class,
            'sesion_sintomas',
            'sesion_control_id',
            'sintoma_id'
        )
        ->withPivot([
            'intensidad',
            'observacion',
        ])
        ->withTimestamps();
    }

    public function archivos(): MorphMany
    {
        return $this->morphMany(
            ArchivoAdjunto::class,
            'adjuntable'
        );
    }
}
