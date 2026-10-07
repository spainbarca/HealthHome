<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TipoParametro extends Model
{
    protected $table = 'tipos_parametro';

    protected $fillable = [
        'codigo',
        'nombre',
        'nombre_corto',
        'unidad',
        'tipo_dato',
        'decimales',
        'activo',
    ];

    protected $casts = [
        'decimales' => 'integer',
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function tiposControl(): BelongsToMany
    {
        return $this->belongsToMany(
            TipoControl::class,
            'tipo_control_parametros',
            'tipo_parametro_id',
            'tipo_control_id'
        )
        ->withPivot([
            'obligatorio',
            'orden',
        ])
        ->withTimestamps();
    }

    public function valores(): HasMany
    {
        return $this->hasMany(
            MedicionValor::class,
            'tipo_parametro_id'
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
}
