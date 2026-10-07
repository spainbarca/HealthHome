<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sintoma extends Model
{
    protected $table = 'sintomas';

    protected $fillable = [
        'codigo',
        'nombre',
        'activo',
        'descripcion',
        'icono',
        'imagen',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function sesionesControl(): BelongsToMany
    {
        return $this->belongsToMany(
            SesionControl::class,
            'sesion_sintomas',
            'sintoma_id',
            'sesion_control_id'
        )
        ->withPivot([
            'intensidad',
            'observacion',
        ])
        ->withTimestamps();
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
