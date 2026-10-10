<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La autorización del rol administrativo debe aplicarse en las rutas/policies.
        return true;
    }

    protected function prepareForValidation(): void
    {
        $tipo = $this->input('tipo_documento');
        $numero = $this->input('numero_documento');

        $this->merge([
            'tipo_documento' => is_string($tipo) && trim($tipo) !== '' ? strtoupper(trim($tipo)) : null,
            'numero_documento' => is_string($numero) && trim($numero) !== '' ? strtoupper(trim($numero)) : null,
        ]);
    }

    public function rules(): array
    {
        $persona = $this->route('persona');
        $personaId = $persona?->getKey();
        $tipo = $this->input('tipo_documento');

        return [
            // No permitir cambiar el User relacionado después de crear la ficha.
            'user_id' => $this->isMethod('post')
                ? [
                    'required',
                    'integer',
                    Rule::exists('users', 'id')->whereNull('deleted_at'),
                    Rule::unique('personas', 'user_id'),
                ]
                : ['prohibited'],
            'tipo_documento' => ['nullable', 'required_with:numero_documento', 'string', Rule::in(['DNI', 'CE', 'PAS', 'OTRO'])],
            'numero_documento' => [
                'nullable',
                'required_with:tipo_documento',
                'string',
                'max:20',
                'regex:/^[A-Z0-9-]+$/',
                Rule::when($tipo === 'DNI', ['digits:8']),
                Rule::unique('personas', 'numero_documento')
                    ->where(fn ($query) => $query->where('tipo_documento', $tipo))
                    ->ignore($personaId),
            ],
            'parentesco' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'usuario',
            'tipo_documento' => 'tipo de documento',
            'numero_documento' => 'número de documento',
            'parentesco' => 'parentesco',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.unique' => 'Este usuario ya tiene una ficha de persona.',
            'numero_documento.unique' => 'Este documento ya está registrado para otra persona.',
            'numero_documento.digits' => 'El DNI debe contener exactamente 8 dígitos.',
            'user_id.prohibited' => 'No se puede cambiar el usuario vinculado a una ficha existente.',
        ];
    }
}
