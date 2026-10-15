<?php

namespace App\Services;

use App\Models\Medicion;
use App\Models\SesionControl;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ControlMedicoService
{
    public function guardar(array $datos, ?SesionControl $control = null): SesionControl
    {
        return DB::transaction(function () use ($datos, $control) {
            $atributos = Arr::only($datos, [
                'persona_id', 'tipo_control_id', 'dispositivo_id',
                'fecha_hora', 'origen', 'estado_previo', 'observaciones',
            ]);

            if ($control) {
                $control->update($atributos);
            } else {
                $control = SesionControl::create($atributos);
            }

            $control->sintomas()->sync(array_fill_keys($datos['sintomas'] ?? [], [
                'intensidad' => null,
                'observacion' => null,
            ]));

            $medicionesNuevas = $datos['mediciones'];
            $idsConservar = [];
            $idsExistentes = $control->mediciones()->pluck('id')->all();

            foreach ($medicionesNuevas as $posicion => $toma) {
                $id = isset($toma['id']) ? (int) $toma['id'] : null;
                if ($id && !in_array($id, $idsExistentes, true)) {
                    throw ValidationException::withMessages([
                        'mediciones_json' => 'Una medición no pertenece a esta sesión. Actualiza la página.',
                    ]);
                }

                if ($id) {
                    $medicion = $control->mediciones()->findOrFail($id);
                    $medicion->update([
                        'numero_medicion' => $posicion + 1,
                        'fecha_hora' => $toma['fecha_hora'],
                        'observaciones' => $toma['observaciones'] ?? null,
                    ]);
                } else {
                    $medicion = $control->mediciones()->create([
                        'numero_medicion' => $posicion + 1,
                        'fecha_hora' => $toma['fecha_hora'],
                        'observaciones' => $toma['observaciones'] ?? null,
                    ]);
                }

                foreach ($toma['valores'] as $parametroId => $valor) {
                    if ($valor === '' || $valor === null) {
                        $medicion->valores()->where('tipo_parametro_id', (int) $parametroId)->delete();
                        continue;
                    }
                    $medicion->valores()->updateOrCreate(
                        ['tipo_parametro_id' => (int) $parametroId],
                        ['valor' => $valor, 'valor_texto' => null]
                    );
                }
                // Al editar una toma, si un valor opcional ya no se envía, se retira.
                $medicion->valores()->whereNotIn(
                    'tipo_parametro_id', array_map('intval', array_keys($toma['valores']))
                )->delete();
                $idsConservar[] = $medicion->id;
            }

            $eliminar = array_diff($idsExistentes, $idsConservar);
            if ($eliminar) {
                // Evitar borrar las fotografías/evidencias de tomas existentes.
                $protegidos = Medicion::query()->whereIn('id', $eliminar)->whereHas('archivos')->exists();
                if ($protegidos) {
                    throw ValidationException::withMessages([
                        'mediciones_json' => 'No puedes quitar una toma que tiene archivos asociados.',
                    ]);
                }
                $control->mediciones()->whereIn('id', $eliminar)->delete();
            }

            return $control->refresh();
        });
    }
}
