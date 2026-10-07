<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Medicion extends Model
{
    protected $table = 'mediciones';

    protected $fillable = [
        'sesion_control_id',
        'numero_medicion',
        'fecha_hora',
        'observaciones',
    ];

    protected $casts = [
        'numero_medicion' => 'integer',
        'fecha_hora' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function sesionControl(): BelongsTo
    {
        return $this->belongsTo(
            SesionControl::class,
            'sesion_control_id'
        );
    }

    public function valores(): HasMany
    {
        return $this->hasMany(
            MedicionValor::class,
            'medicion_id'
        );
    }

    public function archivos(): MorphMany
    {
        return $this->morphMany(
            ArchivoAdjunto::class,
            'adjuntable'
        );
    }
}
