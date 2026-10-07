<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'user_id',
        'tipo_documento',
        'numero_documento',
        'parentesco',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sesionesControl(): HasMany
    {
        return $this->hasMany(
            SesionControl::class,
            'persona_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getNombreCompletoAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->user?->first_name,
            $this->user?->last_name
        ])));
    }
}
