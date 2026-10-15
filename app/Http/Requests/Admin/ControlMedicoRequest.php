<?php

namespace App\Http\Requests\Admin;

use App\Models\SesionControl;
use App\Models\TipoControl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ControlMedicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El middleware administrativo debe autorizar el acceso a este módulo.
        return true;
    }

    /** Decodificamos la tabla jsGrid antes de aplicar reglas de validación. */
    protected function prepareForValidation(): void
    {
        $raw = $this->input('mediciones_json');
        $decoded = is_string($raw) ? json_decode($raw, true) : null;

        $this->merge([
            'mediciones' => is_array($decoded) && array_is_list($decoded) ? $decoded : null,
        ]);
    }

    public function rules(): array
    {
        $control = $this->route('control');
        $editando = $control instanceof SesionControl;

        return [
            'persona_id' => ['required', 'integer', Rule::exists('personas', 'id'),
                ...($editando ? [Rule::in([$control->persona_id])] : [])],
            'tipo_control_id' => [
                'required', 'integer',
                Rule::exists('tipos_control', 'id'),
                ...($editando ? [Rule::in([$control->tipo_control_id])] : []),
            ],
            'dispositivo_id' => ['nullable', 'integer', Rule::exists('dispositivos', 'id')],
            'fecha_hora' => ['required', 'date'],
            'origen' => ['required', Rule::in(['DOMICILIARIO', 'CENTRO_MEDICO', 'PROFESIONAL_SALUD', 'LABORATORIO'])],
            'estado_previo' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
            'sintomas' => ['nullable', 'array', 'max:30'],
            'sintomas.*' => ['required', 'integer', 'distinct', Rule::exists('sintomas', 'id')],
            'mediciones_json' => ['required', 'string', 'max:200000'],
            'mediciones' => ['required', 'array', 'min:1', 'max:40'],
            // En creación no existe un ID; en edición solo se acepta uno si ya está persistido.
            'mediciones.*.id' => $editando
                ? ['nullable', 'integer', 'min:1']
                : ['prohibited'],
            'mediciones.*.fecha_hora' => ['required', 'date'],
            'mediciones.*.observaciones' => ['nullable', 'string', 'max:1000'],
            'mediciones.*.valores' => ['required', 'array'],
        ];
    }

    /**
     * Reglas basadas en el catálogo existente, nunca confiamos en IDs enviados
     * por el frontend. Comprueba obligatorios, tipos y existencia de mediciones.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $tipo = TipoControl::with('parametros')->find($this->integer('tipo_control_id'));
            if (!$tipo || (!$tipo->activo && !($this->route('control') instanceof SesionControl)) || $tipo->parametros->isEmpty()) {
                $validator->errors()->add('tipo_control_id', 'Selecciona un tipo de control configurado con parámetros.');
                return;
            }

            $mediciones = $this->input('mediciones', []);
            if (!$tipo->permite_multiples_mediciones && count($mediciones) > 1) {
                $validator->errors()->add('mediciones_json', 'Este tipo de control admite una sola medición por sesión.');
            }

            $params = $tipo->parametros->keyBy('id');
            $idsMedicion = [];
            foreach ($mediciones as $i => $medicion) {
                if (!is_array($medicion)) {
                    $validator->errors()->add("mediciones.$i", 'El formato de la toma es inválido.');
                    continue;
                }

                if (isset($medicion['id'])) {
                    $id = (int) $medicion['id'];
                    if (in_array($id, $idsMedicion, true)) {
                        $validator->errors()->add("mediciones.$i.id", 'Se repite el identificador de una medición.');
                    }
                    $idsMedicion[] = $id;
                }

                $valores = $medicion['valores'] ?? [];
                if (!is_array($valores)) {
                    continue;
                }
                foreach ($valores as $idParametro => $valor) {
                    if (!ctype_digit((string) $idParametro) || !$params->has((int) $idParametro)) {
                        $validator->errors()->add("mediciones.$i.valores", 'La medición contiene parámetros que no pertenecen al control.');
                        continue;
                    }
                    $parametro = $params->get((int) $idParametro);
                    if ($valor !== null && $valor !== '') {
                        if (!is_scalar($valor) || !is_numeric($valor) || !is_finite((float) $valor) || abs((float) $valor) > 999999999) {
                            $validator->errors()->add("mediciones.$i.valores.$idParametro", "El valor de {$parametro->nombre} debe ser numérico y válido.");
                        } elseif ((int) ($parametro->decimales ?? 0) === 0 && floor((float) $valor) != (float) $valor) {
                            $validator->errors()->add("mediciones.$i.valores.$idParametro", "{$parametro->nombre} debe ser un número entero.");
                        } elseif (strlen((string) $valor) > 32) {
                            $validator->errors()->add("mediciones.$i.valores.$idParametro", "El valor de {$parametro->nombre} es demasiado largo.");
                        } elseif (preg_match('/\.(\d+)/', (string) $valor, $partes)
                            && strlen(rtrim($partes[1], '0')) > (int) ($parametro->decimales ?? 0)) {
                            $validator->errors()->add("mediciones.$i.valores.$idParametro", "{$parametro->nombre} admite hasta {$parametro->decimales} decimales.");
                        }
                    }
                }
                foreach ($params as $parametro) {
                    if ($parametro->pivot->obligatorio && (!array_key_exists((string) $parametro->id, $valores) || $valores[$parametro->id] === '' || $valores[$parametro->id] === null)) {
                        $validator->errors()->add("mediciones.$i.valores.{$parametro->id}", "Falta {$parametro->nombre} en la toma " . ($i + 1) . '.');
                    }
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'persona_id' => 'persona',
            'tipo_control_id' => 'tipo de control',
            'dispositivo_id' => 'dispositivo',
            'fecha_hora' => 'fecha y hora de la sesión',
            'mediciones_json' => 'mediciones',
            'mediciones' => 'mediciones',
        ];
    }
}
