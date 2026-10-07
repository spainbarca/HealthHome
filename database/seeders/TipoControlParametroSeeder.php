<?php

namespace Database\Seeders;

use App\Models\TipoControl;
use App\Models\TipoParametro;
use Illuminate\Database\Seeder;

class TipoControlParametroSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Presión arterial
        |--------------------------------------------------------------------------
        */

        $presion = TipoControl::where('codigo', 'PRESION')->firstOrFail();

        $sys = TipoParametro::where('codigo', 'SYS')->firstOrFail();
        $dia = TipoParametro::where('codigo', 'DIA')->firstOrFail();
        $pulse = TipoParametro::where('codigo', 'PULSE')->firstOrFail();

        $presion->parametros()->sync([
            $sys->id => [
                'obligatorio' => true,
                'orden' => 1,
            ],
            $dia->id => [
                'obligatorio' => true,
                'orden' => 2,
            ],
            $pulse->id => [
                'obligatorio' => false,
                'orden' => 3,
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Oximetría
        |--------------------------------------------------------------------------
        */

        $oximetria = TipoControl::where('codigo', 'OXIMETRIA')->firstOrFail();

        $spo2 = TipoParametro::where('codigo', 'SPO2')->firstOrFail();

        $oximetria->parametros()->sync([
            $spo2->id => [
                'obligatorio' => true,
                'orden' => 1,
            ],
            $pulse->id => [
                'obligatorio' => true,
                'orden' => 2,
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Glucosa
        |--------------------------------------------------------------------------
        */

        $glucosa = TipoControl::where('codigo', 'GLUCOSA')->firstOrFail();

        $glucose = TipoParametro::where('codigo', 'GLUCOSE')->firstOrFail();

        $glucosa->parametros()->sync([
            $glucose->id => [
                'obligatorio' => true,
                'orden' => 1,
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Temperatura
        |--------------------------------------------------------------------------
        */

        $temperatura = TipoControl::where('codigo', 'TEMPERATURA')->firstOrFail();

        $temp = TipoParametro::where('codigo', 'TEMP')->firstOrFail();

        $temperatura->parametros()->sync([
            $temp->id => [
                'obligatorio' => true,
                'orden' => 1,
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Peso
        |--------------------------------------------------------------------------
        */

        $peso = TipoControl::where('codigo', 'PESO')->firstOrFail();

        $weight = TipoParametro::where('codigo', 'WEIGHT')->firstOrFail();

        $peso->parametros()->sync([
            $weight->id => [
                'obligatorio' => true,
                'orden' => 1,
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Peak Flow
        |--------------------------------------------------------------------------
        */

        $peakFlow = TipoControl::where('codigo', 'PEAK_FLOW')->firstOrFail();

        $peakFlowParametro = TipoParametro::where(
            'codigo',
            'PEAK_FLOW'
        )->firstOrFail();

        $peakFlow->parametros()->sync([
            $peakFlowParametro->id => [
                'obligatorio' => true,
                'orden' => 1,
            ],
        ]);
    }
}
