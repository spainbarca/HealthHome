<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DispositivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Proteger las rutas desde el middleware de autenticación/rol del panel.
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Acepta filename o clase completa, pero en BD solo guarda el filename.
        $icono = $this->input('icono');
        if (is_string($icono)) {
            $icono = strtolower(trim($icono));
            $icono = preg_replace('/^healthicons-/', '', $icono);
            $this->merge(['icono' => $icono === '' ? null : $icono]);
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'tipo' => ['required', 'string', 'max:80'],
            'marca' => ['nullable', 'string', 'max:100'],
            'modelo' => ['nullable', 'string', 'max:100'],
            'numero_serie' => ['nullable', 'string', 'max:120'],
            'icono' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9][a-z0-9_-]*$/'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'eliminar_imagen' => ['nullable', 'boolean'],
            'fecha_compra' => ['nullable', 'date'],
            'fecha_ultima_calibracion' => ['nullable', 'date'],
            'fecha_proxima_calibracion' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
            'activo' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'tipo' => 'tipo de dispositivo',
            'marca' => 'marca',
            'modelo' => 'modelo',
            'numero_serie' => 'número de serie',
            'icono' => 'filename del icono Health Icons',
            'imagen' => 'imagen',
            'fecha_compra' => 'fecha de compra',
            'fecha_ultima_calibracion' => 'fecha de última calibración',
            'fecha_proxima_calibracion' => 'fecha de próxima calibración',
            'observaciones' => 'observaciones',
        ];
    }
}
