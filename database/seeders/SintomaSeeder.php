<?php

namespace Database\Seeders;

use App\Models\Sintoma;
use Illuminate\Database\Seeder;

class SintomaSeeder extends Seeder
{
    public function run(): void
    {
        $sintomas = [
            [
                'codigo' => 'MAREO',
                'nombre' => 'Mareo',
                'activo' => true,
            ],
            [
                'codigo' => 'DOLOR_CABEZA',
                'nombre' => 'Dolor de cabeza',
                'activo' => true,
            ],
            [
                'codigo' => 'FATIGA',
                'nombre' => 'Fatiga',
                'activo' => true,
            ],
            [
                'codigo' => 'FALTA_AIRE',
                'nombre' => 'Sensación de falta de aire',
                'activo' => true,
            ],
            [
                'codigo' => 'PALPITACIONES',
                'nombre' => 'Palpitaciones',
                'activo' => true,
            ],
            [
                'codigo' => 'TOS',
                'nombre' => 'Tos',
                'activo' => true,
            ],
            [
                'codigo' => 'CONGESTION',
                'nombre' => 'Congestión',
                'activo' => true,
            ],
            [
                'codigo' => 'NAUSEAS',
                'nombre' => 'Náuseas',
                'activo' => true,
            ],
            [
                'codigo' => 'DEBILIDAD',
                'nombre' => 'Debilidad',
                'activo' => true,
            ],
            [
                'codigo' => 'SUDORACION',
                'nombre' => 'Sudoración',
                'activo' => true,
            ],
            [
                'codigo' => 'TEMBLOR',
                'nombre' => 'Temblor',
                'activo' => true,
            ],
            [
                'codigo' => 'VISION_BORROSA',
                'nombre' => 'Visión borrosa',
                'activo' => true,
            ],
            [
                'codigo' => 'DOLOR_PECHO',
                'nombre' => 'Dolor en el pecho',
                'activo' => true,
            ],
            [
                'codigo' => 'ESCALOFRIOS',
                'nombre' => 'Escalofríos',
                'activo' => true,
            ],
            [
                'codigo' => 'SOMNOLENCIA',
                'nombre' => 'Somnolencia',
                'activo' => true,
            ],
        ];

        foreach ($sintomas as $sintoma) {
            Sintoma::updateOrCreate(
                [
                    'codigo' => $sintoma['codigo'],
                ],
                $sintoma
            );
        }
    }
}
