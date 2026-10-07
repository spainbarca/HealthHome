<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicionValor extends Model
{
    protected $table = 'medicion_valores';

    protected $fillable = [
        'medicion_id',
        'tipo_parametro_id',
        'valor',
        'valor_texto',
    ];

    protected $casts = [
        'valor' => 'decimal:3',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function medicion(): BelongsTo
    {
        return $this->belongsTo(
            Medicion::class,
            'medicion_id'
        );
    }

    public function parametro(): BelongsTo
    {
        return $this->belongsTo(
            TipoParametro::class,
            'tipo_parametro_id'
        );
    }
}
