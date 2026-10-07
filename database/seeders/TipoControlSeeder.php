<?php

namespace Database\Seeders;

use App\Models\TipoControl;
use Illuminate\Database\Seeder;

class TipoControlSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'codigo' => 'PRESION',
                'nombre' => 'Presión arterial',
                'icono' => 'fa-solid fa-heart-pulse',
                'descripcion' => 'Control domiciliario de presión arterial.',
                'permite_multiples_mediciones' => true,
                'activo' => true,
            ],
            [
                'codigo' => 'OXIMETRIA',
                'nombre' => 'Oximetría',
                'icono' => 'fa-solid fa-lungs',
                'descripcion' => 'Control de saturación de oxígeno y frecuencia cardíaca.',
                'permite_multiples_mediciones' => true,
                'activo' => true,
            ],
            [
                'codigo' => 'GLUCOSA',
                'nombre' => 'Glucosa',
                'icono' => 'fa-solid fa-droplet',
                'descripcion' => 'Control domiciliario de glucosa capilar.',
                'permite_multiples_mediciones' => false,
                'activo' => true,
            ],
            [
                'codigo' => 'TEMPERATURA',
                'nombre' => 'Temperatura corporal',
                'icono' => 'fa-solid fa-temperature-half',
                'descripcion' => 'Control de temperatura corporal.',
                'permite_multiples_mediciones' => false,
                'activo' => true,
            ],
            [
                'codigo' => 'PESO',
                'nombre' => 'Peso corporal',
                'icono' => 'fa-solid fa-weight-scale',
                'descripcion' => 'Control de peso corporal.',
                'permite_multiples_mediciones' => false,
                'activo' => true,
            ],
            [
                'codigo' => 'PEAK_FLOW',
                'nombre' => 'Flujo espiratorio máximo',
                'icono' => 'fa-solid fa-wind',
                'descripcion' => 'Control de flujo espiratorio máximo mediante medidor Peak Flow.',
                'permite_multiples_mediciones' => true,
                'activo' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoControl::updateOrCreate(
                [
                    'codigo' => $tipo['codigo'],
                ],
                $tipo
            );
        }
    }
}
