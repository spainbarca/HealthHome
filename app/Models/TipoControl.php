<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TipoControl extends Model
{
    protected $table = 'tipos_control';

    protected $fillable = [
        'codigo',
        'nombre',
        'icono',
        'descripcion',
        'permite_multiples_mediciones',
        'activo',
    ];

    protected $casts = [
        'permite_multiples_mediciones' => 'boolean',
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function parametros(): BelongsToMany
    {
        return $this->belongsToMany(
            TipoParametro::class,
            'tipo_control_parametros',
            'tipo_control_id',
            'tipo_parametro_id'
        )
        ->withPivot([
            'obligatorio',
            'orden',
        ])
        ->withTimestamps()
        ->orderByPivot('orden');
    }

    public function sesionesControl(): HasMany
    {
        return $this->hasMany(SesionControl::class, 'tipo_control_id');
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
}
