<?php

namespace Database\Seeders;

use App\Models\TipoParametro;
use Illuminate\Database\Seeder;

class TipoParametroSeeder extends Seeder
{
    public function run(): void
    {
        $parametros = [
            [
                'codigo' => 'SYS',
                'nombre' => 'Presión sistólica',
                'nombre_corto' => 'Sistólica',
                'unidad' => 'mmHg',
                'tipo_dato' => 'decimal',
                'decimales' => 0,
                'activo' => true,
            ],
            [
                'codigo' => 'DIA',
                'nombre' => 'Presión diastólica',
                'nombre_corto' => 'Diastólica',
                'unidad' => 'mmHg',
                'tipo_dato' => 'decimal',
                'decimales' => 0,
                'activo' => true,
            ],
            [
                'codigo' => 'PULSE',
                'nombre' => 'Frecuencia cardíaca',
                'nombre_corto' => 'Pulso',
                'unidad' => 'lpm',
                'tipo_dato' => 'decimal',
                'decimales' => 0,
                'activo' => true,
            ],
            [
                'codigo' => 'SPO2',
                'nombre' => 'Saturación de oxígeno',
                'nombre_corto' => 'SpO₂',
                'unidad' => '%',
                'tipo_dato' => 'decimal',
                'decimales' => 0,
                'activo' => true,
            ],
            [
                'codigo' => 'GLUCOSE',
                'nombre' => 'Glucosa',
                'nombre_corto' => 'Glucosa',
                'unidad' => 'mg/dL',
                'tipo_dato' => 'decimal',
                'decimales' => 0,
                'activo' => true,
            ],
            [
                'codigo' => 'TEMP',
                'nombre' => 'Temperatura corporal',
                'nombre_corto' => 'Temperatura',
                'unidad' => '°C',
                'tipo_dato' => 'decimal',
                'decimales' => 1,
                'activo' => true,
            ],
            [
                'codigo' => 'WEIGHT',
                'nombre' => 'Peso corporal',
                'nombre_corto' => 'Peso',
                'unidad' => 'kg',
                'tipo_dato' => 'decimal',
                'decimales' => 2,
                'activo' => true,
            ],
            [
                'codigo' => 'PEAK_FLOW',
                'nombre' => 'Flujo espiratorio máximo',
                'nombre_corto' => 'Peak Flow',
                'unidad' => 'L/min',
                'tipo_dato' => 'decimal',
                'decimales' => 0,
                'activo' => true,
            ],
            [
                'codigo' => 'RESP_RATE',
                'nombre' => 'Frecuencia respiratoria',
                'nombre_corto' => 'Respiraciones',
                'unidad' => 'rpm',
                'tipo_dato' => 'decimal',
                'decimales' => 0,
                'activo' => true,
            ],
        ];

        foreach ($parametros as $parametro) {
            TipoParametro::updateOrCreate(
                [
                    'codigo' => $parametro['codigo'],
                ],
                $parametro
            );
        }
    }
}
