<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ArchivoAdjunto extends Model
{
    protected $table = 'archivos_adjuntos';

    protected $fillable = [
        'adjuntable_id',
        'adjuntable_type',
        'nombre',
        'archivo',
        'mime_type',
        'tamano',
        'descripcion',
    ];

    protected $casts = [
        'tamano' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function adjuntable(): MorphTo
    {
        return $this->morphTo();
    }
}
