<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispositivo extends Model
{
    protected $table = 'dispositivos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'marca',
        'modelo',
        'numero_serie',
        'icono',
        'imagen',
        'fecha_compra',
        'fecha_ultima_calibracion',
        'fecha_proxima_calibracion',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_ultima_calibracion' => 'date',
        'fecha_proxima_calibracion' => 'date',
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function sesionesControl(): HasMany
    {
        return $this->hasMany(
            SesionControl::class,
            'dispositivo_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getNombreCompletoAttribute(): string
    {
        $datos = array_filter([
            $this->nombre,
            $this->marca,
            $this->modelo,
        ]);

        return implode(' - ', $datos);
    }
}
