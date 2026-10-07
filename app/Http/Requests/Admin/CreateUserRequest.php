<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'persona' => [
                'tipo_documento' => $this->input(
                    'persona_tipo_documento'
                ),

                'numero_documento' => $this->input(
                    'persona_numero_documento'
                ),

                'parentesco' => $this->input(
                    'persona_parentesco'
                ),
            ],
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            'first_name' => [
                'required',
                'string',
                'max:255',
            ],

            'last_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at'),
            ],

            'confirm_email' => [
                'nullable',
                'same:email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'phone' => [
                'required',
                'digits_between:6,15',
                Rule::unique('users', 'phone')
                    ->whereNull('deleted_at'),
            ],

            'gender' => [
                'required',
                Rule::in([
                    'male',
                    'female',
                ]),
            ],

            'status' => [
                'required',
                'boolean',
            ],

            'postal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'dob' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'country_id' => [
                'nullable',
                'integer',
                'exists:countries,id',
            ],

            'state_id' => [
                'nullable',
                'integer',
                'exists:states,id',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'bio' => [
                'nullable',
                'string',
            ],

            'role_id' => [
                'nullable',
                'integer',
                'exists:roles,id',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            /*
            |--------------------------------------------------------------------------
            | Persona
            |--------------------------------------------------------------------------
            */

            'persona' => [
                'required',
                'array',
            ],

            'persona.tipo_documento' => [
                'required',
                'string',
                Rule::in([
                    'DNI',
                    'CE',
                    'PASAPORTE',
                ]),
            ],

            'persona.numero_documento' => [
                'required',
                'string',
                'max:20',

                Rule::unique(
                    'personas',
                    'numero_documento'
                )->where(function ($query) {
                    return $query->where(
                        'tipo_documento',
                        $this->input('persona.tipo_documento')
                    );
                }),
            ],

            'persona.parentesco' => [
                'nullable',
                'string',
                Rule::in([
                    'TITULAR',
                    'PADRE',
                    'MADRE',
                    'HIJO',
                    'PAREJA',
                    'HERMANO',
                    'OTRO',
                ]),
            ],
        ];
    }
}
