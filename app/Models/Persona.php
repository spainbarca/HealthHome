<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'tipo_documento',
        'numero_documento',
        'fecha_nacimiento',
        'sexo',
        'parentesco',
        'activo',
        'foto'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function sesionesControl(): HasMany
    {
        return $this->hasMany(SesionControl::class, 'persona_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getNombreCompletoAttribute(): string
    {
        return trim(
            $this->nombres . ' ' .
            $this->apellido_paterno . ' ' .
            $this->apellido_materno
        );
    }
}
