<?php

namespace App\Services\Forms;

use App\Enums\RoleEnum;
use App\Helpers\Helpers;
use App\Models\State;
use App\Models\User;
use phpformbuilder\Form;

class UserFormBuilder
{
    public function create(
        User $user,
        $roles,
        $countries
    ): Form {

        require_once base_path('phpformbuilder/autoload.php');

        /*
        |--------------------------------------------------------------------------
        | Formulario
        |--------------------------------------------------------------------------
        */

        $form = new Form(
            'userForm',
            'vertical',
            'novalidate,enctype=multipart/form-data',
            'bs5'
        );

        $form->setAction(
            route('admin.user.store'),
            false
        );

        $form->setMethod('POST');

        /*
        |--------------------------------------------------------------------------
        | CSRF Laravel
        |--------------------------------------------------------------------------
        */

        $form->addInput(
            'hidden',
            '_token',
            csrf_token()
        );

        /*
        |--------------------------------------------------------------------------
        | Errores Laravel
        |--------------------------------------------------------------------------
        */

        $this->addLaravelErrors($form);

        /*
        |--------------------------------------------------------------------------
        | CUENTA
        |--------------------------------------------------------------------------
        */

        $this->openSection(
            $form,
            'fa-solid fa-user',
            'Información de cuenta',
            'Datos principales del usuario'
        );

        /*
        |--------------------------------------------------------------------------
        | Nombres
        |--------------------------------------------------------------------------
        */

        $form->startRow('g-3');

        $form->startCol(6, 'md');

        $form->addIcon(
            'first_name',
            '<i class="fa-solid fa-user"></i>',
            'before'
        );

        $form->addInput(
            'text',
            'first_name',
            (string) old(
                'first_name',
                $user->first_name ?? ''
            ),
            'Nombres',
            'required,placeholder=Ingrese los nombres'
        );

        $form->endCol();


        /*
        |--------------------------------------------------------------------------
        | Apellidos
        |--------------------------------------------------------------------------
        */

        $form->startCol(6, 'md');

        $form->addIcon(
            'last_name',
            '<i class="fa-solid fa-user"></i>',
            'before'
        );

        $form->addInput(
            'text',
            'last_name',
            (string) old(
                'last_name',
                $user->last_name ?? ''
            ),
            'Apellidos',
            'required,placeholder=Ingrese los apellidos'
        );

        $form->endCol();

        $form->endRow();


        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

        $form->startRow('g-3');

        $form->startCol(6, 'md');

        $form->addIcon(
            'email',
            '<i class="fa-solid fa-envelope"></i>',
            'before'
        );

        $form->addInput(
            'email',
            'email',
            (string) old(
                'email',
                $user->email ?? ''
            ),
            'Correo electrónico',
            'required,placeholder=correo@ejemplo.com'
        );

        $form->endCol();


        /*
        |--------------------------------------------------------------------------
        | Confirmación email
        |--------------------------------------------------------------------------
        */

        $form->startCol(6, 'md');

        $form->addIcon(
            'confirm_email',
            '<i class="fa-solid fa-envelope-circle-check"></i>',
            'before'
        );

        $form->addInput(
            'email',
            'confirm_email',
            (string) old(
                'confirm_email',
                $user->email ?? ''
            ),
            'Confirmar correo',
            'required,placeholder=Repita el correo'
        );

        $form->endCol();

        $form->endRow();


        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        $form->startRow('g-3');

        $form->startCol(6, 'md');

        $form->addIcon(
            'password',
            '<i class="fa-solid fa-lock"></i>',
            'before'
        );

        $form->addHelper(
            'Mínimo 8 caracteres',
            'password'
        );

        $form->addInput(
            'password',
            'password',
            '',
            'Contraseña',
            'required,minlength=8,autocomplete=new-password,placeholder=Ingrese una contraseña'
        );

        $form->endCol();


        /*
        |--------------------------------------------------------------------------
        | Confirmar Password
        |--------------------------------------------------------------------------
        */

        $form->startCol(6, 'md');

        $form->addIcon(
            'confirm_password',
            '<i class="fa-solid fa-lock"></i>',
            'before'
        );

        $form->addInput(
            'password',
            'confirm_password',
            '',
            'Confirmar contraseña',
            'required,minlength=8,autocomplete=new-password,placeholder=Repita la contraseña'
        );

        $form->endCol();

        $form->endRow();

        $this->closeSection($form);


        /*
        |--------------------------------------------------------------------------
        | DATOS PERSONALES
        |--------------------------------------------------------------------------
        */

        $this->openSection(
            $form,
            'fa-solid fa-id-card',
            'Datos personales',
            'Información asociada al perfil médico'
        );

        /*
        |--------------------------------------------------------------------------
        | Persona
        |--------------------------------------------------------------------------
        */

        $tipoDocumento = old(
            'persona_tipo_documento',
            old(
                'persona.tipo_documento',
                $user->persona?->tipo_documento ?? ''
            )
        );

        $numeroDocumento = old(
            'persona_numero_documento',
            old(
                'persona.numero_documento',
                $user->persona?->numero_documento ?? ''
            )
        );

        $parentesco = old(
            'persona_parentesco',
            old(
                'persona.parentesco',
                $user->persona?->parentesco ?? ''
            )
        );

        $form->startRow('g-3');

        /*
        |--------------------------------------------------------------------------
        | Tipo documento
        |--------------------------------------------------------------------------
        */

        $form->startCol(4, 'md');

        $form->addOption(
            'persona_tipo_documento',
            '',
            'Seleccione...'
        );

        $form->addOption(
            'persona_tipo_documento',
            'DNI',
            'DNI',
            '',
            $tipoDocumento === 'DNI'
                ? 'selected'
                : ''
        );

        $form->addOption(
            'persona_tipo_documento',
            'CE',
            'Carné de extranjería',
            '',
            $tipoDocumento === 'CE'
                ? 'selected'
                : ''
        );

        $form->addOption(
            'persona_tipo_documento',
            'PASAPORTE',
            'Pasaporte',
            '',
            $tipoDocumento === 'PASAPORTE'
                ? 'selected'
                : ''
        );

        $form->addSelect(
            'persona_tipo_documento',
            'Tipo de documento',
            'required,id=tipo_documento'
        );

        $form->endCol();


        /*
        |--------------------------------------------------------------------------
        | Número documento
        |--------------------------------------------------------------------------
        */

        $form->startCol(4, 'md');

        $form->addIcon(
            'persona_numero_documento',
            '<i class="fa-solid fa-id-card"></i>',
            'before'
        );

        $form->addInput(
            'text',
            'persona_numero_documento',
            (string) $numeroDocumento,
            'Número de documento',
            'required,id=numero_documento,maxlength=20,placeholder=Número de documento'
        );

        $form->endCol();


        /*
        |--------------------------------------------------------------------------
        | Parentesco
        |--------------------------------------------------------------------------
        */

        $form->startCol(4, 'md');

        $form->addOption(
            'persona_parentesco',
            '',
            'Seleccione...'
        );

        $parentescos = [
            'TITULAR' => 'Titular',
            'PADRE' => 'Padre',
            'MADRE' => 'Madre',
            'HIJO' => 'Hijo/a',
            'PAREJA' => 'Pareja',
            'HERMANO' => 'Hermano/a',
            'OTRO' => 'Otro',
        ];

        foreach ($parentescos as $codigo => $nombre) {

            $form->addOption(
                'persona_parentesco',
                $codigo,
                $nombre,
                '',
                $parentesco === $codigo
                    ? 'selected'
                    : ''
            );
        }

        $form->addSelect(
            'persona_parentesco',
            'Parentesco',
            'id=parentesco'
        );

        $form->endCol();

        $form->endRow();


        /*
        |--------------------------------------------------------------------------
        | Fecha / género
        |--------------------------------------------------------------------------
        */

        $form->startRow('g-3');

        $form->startCol(6, 'md');

        $form->addIcon(
            'dob',
            '<i class="fa-solid fa-calendar-days"></i>',
            'before'
        );

        $form->addInput(
            'date',
            'dob',
            (string) old(
                'dob',
                $user->dob ?? ''
            ),
            'Fecha de nacimiento',
            'max=' . now()->format('Y-m-d')
        );

        $form->endCol();


        $form->startCol(6, 'md');

        $gender = old(
            'gender',
            $user->gender ?? ''
        );

        $form->addOption(
            'gender',
            '',
            'Seleccione...'
        );

        $form->addOption(
            'gender',
            'male',
            'Masculino',
            '',
            $gender === 'male'
                ? 'selected'
                : ''
        );

        $form->addOption(
            'gender',
            'female',
            'Femenino',
            '',
            $gender === 'female'
                ? 'selected'
                : ''
        );

        $form->addOption(
            'gender',
            'other',
            'Otro',
            '',
            $gender === 'other'
                ? 'selected'
                : ''
        );

        $form->addSelect(
            'gender',
            'Género',
            'required'
        );

        $form->endCol();

        $form->endRow();

        $this->closeSection($form);


        /*
        |--------------------------------------------------------------------------
        | CONTACTO
        |--------------------------------------------------------------------------
        */

        $this->openSection(
            $form,
            'fa-solid fa-address-book',
            'Contacto',
            'Teléfono y medios de contacto'
        );

        $form->startRow('g-3');


        /*
        |--------------------------------------------------------------------------
        | Código país
        |--------------------------------------------------------------------------
        */

        $form->startCol(4, 'md');

        $countryCode = old(
            'country_code',
            $user->country_code ?? 1
        );

        foreach (
            Helpers::getCountryCode() as $option
        ) {

            $attributes = '';

            if (
                (string) $countryCode ===
                (string) $option->calling_code
            ) {
                $attributes = 'selected';
            }

            $form->addOption(
                'country_code',
                $option->calling_code,
                '+' . $option->calling_code,
                '',
                $attributes
            );
        }

        $form->addSelect(
            'country_code',
            'Código de país',
            'id=country_code'
        );

        $form->endCol();


        /*
        |--------------------------------------------------------------------------
        | Teléfono
        |--------------------------------------------------------------------------
        */

        $form->startCol(8, 'md');

        $form->addIcon(
            'phone',
            '<i class="fa-solid fa-phone"></i>',
            'before'
        );

        $form->addInput(
            'tel',
            'phone',
            (string) old(
                'phone',
                $user->phone ?? ''
            ),
            'Teléfono',
            'required,placeholder=Ingrese el teléfono'
        );

        $form->endCol();

        $form->endRow();

        $this->closeSection($form);


        /*
        |--------------------------------------------------------------------------
        | ACCESO
        |--------------------------------------------------------------------------
        */

        $this->openSection(
            $form,
            'fa-solid fa-shield-halved',
            'Acceso al sistema',
            'Rol y estado de la cuenta'
        );

        $form->startRow('g-3');

        /*
        |--------------------------------------------------------------------------
        | Rol
        |--------------------------------------------------------------------------
        */

        $form->startCol(6, 'md');

        $selectedRole = old(
            'role_id'
        );

        $form->addOption(
            'role_id',
            '',
            'Seleccione...'
        );

        foreach ($roles as $role) {

            if ($role->name === RoleEnum::ADMIN) {
                continue;
            }

            $form->addOption(
                'role_id',
                $role->id,
                $role->name,
                '',
                (string) $selectedRole ===
                (string) $role->id
                    ? 'selected'
                    : ''
            );
        }

        $form->addSelect(
            'role_id',
            'Rol',
            'required,id=role_id'
        );

        $form->endCol();


        /*
        |--------------------------------------------------------------------------
        | Estado
        |--------------------------------------------------------------------------
        */

        $form->startCol(6, 'md');

        $status = old(
            'status',
            $user->status ?? 1
        );

        $form->addOption(
            'status',
            1,
            'Activo',
            '',
            (string) $status === '1'
                ? 'selected'
                : ''
        );

        $form->addOption(
            'status',
            0,
            'Inactivo',
            '',
            (string) $status === '0'
                ? 'selected'
                : ''
        );

        $form->addSelect(
            'status',
            'Estado',
            'required,id=status'
        );

        $form->endCol();

        $form->endRow();

        $this->closeSection($form);


        /*
        |--------------------------------------------------------------------------
        | UBICACIÓN
        |--------------------------------------------------------------------------
        */

        $this->openSection(
            $form,
            'fa-solid fa-location-dot',
            'Ubicación',
            'Información geográfica del usuario'
        );

        $countryId = old(
            'country_id',
            $user->country_id ?? ''
        );

        $stateId = old(
            'state_id',
            $user->state_id ?? ''
        );

        $form->startRow('g-3');

        /*
        |--------------------------------------------------------------------------
        | País
        |--------------------------------------------------------------------------
        */

        $form->startCol(6, 'md');

        $form->addOption(
            'country_id',
            '',
            'Seleccione...'
        );

        foreach ($countries as $id => $country) {

            $form->addOption(
                'country_id',
                $id,
                $country,
                '',
                (string) $countryId ===
                (string) $id
                    ? 'selected'
                    : ''
            );
        }

        $form->addSelect(
            'country_id',
            'País',
            'id=country'
        );

        $form->endCol();


        /*
        |--------------------------------------------------------------------------
        | Departamento
        |--------------------------------------------------------------------------
        */

        $form->startCol(6, 'md');

        $form->addOption(
            'state_id',
            '',
            'Seleccione...'
        );

        if (!empty($countryId)) {

            $states = State::query()
                ->where(
                    'country_id',
                    $countryId
                )
                ->orderBy('name')
                ->get();

            foreach ($states as $state) {

                $form->addOption(
                    'state_id',
                    $state->id,
                    $state->name,
                    '',
                    (string) $stateId ===
                    (string) $state->id
                        ? 'selected'
                        : ''
                );
            }
        }

        $form->addSelect(
            'state_id',
            'Departamento / Estado',
            'id=state'
        );

        $form->endCol();

        $form->endRow();


        /*
        |--------------------------------------------------------------------------
        | Ciudad / Código postal
        |--------------------------------------------------------------------------
        */

        $form->startRow('g-3');

        $form->startCol(8, 'md');

        $form->addIcon(
            'location',
            '<i class="fa-solid fa-city"></i>',
            'before'
        );

        $form->addInput(
            'text',
            'location',
            (string) old(
                'location',
                $user->location ?? ''
            ),
            'Ciudad / ubicación',
            'placeholder=Ingrese ciudad o ubicación'
        );

        $form->endCol();


        $form->startCol(4, 'md');

        $form->addInput(
            'text',
            'postal_code',
            (string) old(
                'postal_code',
                $user->postal_code ?? ''
            ),
            'Código postal',
            'maxlength=20,placeholder=Código postal'
        );

        $form->endCol();

        $form->endRow();

        $this->closeSection($form);


        /*
        |--------------------------------------------------------------------------
        | PERFIL
        |--------------------------------------------------------------------------
        */

        $this->openSection(
            $form,
            'fa-solid fa-image-portrait',
            'Perfil',
            'Fotografía e información adicional'
        );

        $form->startRow('g-3');

        /*
        |--------------------------------------------------------------------------
        | Avatar
        |--------------------------------------------------------------------------
        */

        $form->startCol(12, 'md');

        $form->addInput(
            'file',
            'image',
            '',
            'Fotografía',
            'accept=image/jpeg|image/png|image/webp'
        );

        $form->addHelper(
            'JPG, PNG o WEBP. Máximo recomendado 5 MB.',
            'image'
        );

        $form->endCol();


        /*
        |--------------------------------------------------------------------------
        | About Me
        |--------------------------------------------------------------------------
        */

        $form->startCol(12, 'md');

        $form->addTextarea(
            'about_me',
            (string) old(
                'about_me',
                $user->about_me ?? ''
            ),
            'Acerca de mí',
            'rows=3,placeholder=Información general'
        );

        $form->endCol();

        $form->endRow();


        /*
        |--------------------------------------------------------------------------
        | Bio
        |--------------------------------------------------------------------------
        */

        $form->addTextarea(
            'bio',
            (string) old(
                'bio',
                $user->bio ?? ''
            ),
            'Biografía / Observaciones',
            'rows=3,placeholder=Observaciones adicionales'
        );

        $this->closeSection($form);


        /*
        |--------------------------------------------------------------------------
        | ACCIONES
        |--------------------------------------------------------------------------
        */

        $form->startDiv(
            'd-flex justify-content-end gap-2 mt-4'
        );

        $form->addHtml(
            '<a
                href="' .
                e(route('admin.user.index')) .
                '"
                class="btn btn-light"
            >
                <i class="fa-solid fa-arrow-left me-1"></i>
                Cancelar
            </a>'
        );

        $form->addBtn(
            'submit',
            'submit',
            1,
            '<i class="fa-solid fa-floppy-disk me-1"></i>
             Guardar usuario',
            'class=btn btn-primary px-4'
        );

        $form->endDiv();


        /*
        |--------------------------------------------------------------------------
        | Plugin de validación visual
        |--------------------------------------------------------------------------
        */

        $form->addPlugin(
            'formvalidation',
            '#userForm'
        );

        return $form;
    }


    /*
    |--------------------------------------------------------------------------
    | Secciones estilo Extended Users
    |--------------------------------------------------------------------------
    */

    private function openSection(
        Form $form,
        string $icon,
        string $title,
        string $description
    ): void {

        $form->startDiv(
            'user-form-section card border-0 shadow-sm mb-4'
        );

        $form->addHtml('
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex align-items-center">

                    <div class="section-icon me-3">
                        <i class="' . e($icon) . '"></i>
                    </div>

                    <div>
                        <h5 class="mb-1">
                            ' . e($title) . '
                        </h5>

                        <small class="text-muted">
                            ' . e($description) . '
                        </small>
                    </div>

                </div>
            </div>
        ');

        $form->startDiv(
            'card-body'
        );
    }


    private function closeSection(
        Form $form
    ): void {

        /*
         * card-body
         */
        $form->endDiv();

        /*
         * card
         */
        $form->endDiv();
    }


    /*
    |--------------------------------------------------------------------------
    | Errores Laravel
    |--------------------------------------------------------------------------
    */

    private function addLaravelErrors(
        Form $form
    ): void {

        $errors = session('errors');

        if (!$errors || !$errors->any()) {
            return;
        }

        $html = '
            <div class="alert alert-danger border-0 shadow-sm mb-4">

                <div class="d-flex">

                    <div class="me-3">
                        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                    </div>

                    <div>

                        <strong>
                            Revise la información ingresada.
                        </strong>

                        <ul class="mb-0 mt-2">
        ';

        foreach ($errors->all() as $error) {

            $html .=
                '<li>' .
                    e($error) .
                '</li>';
        }

        $html .= '
                        </ul>

                    </div>

                </div>

            </div>
        ';

        $form->addHtml(
            $html
        );
    }
}
