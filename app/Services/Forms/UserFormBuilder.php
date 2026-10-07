<?php

namespace App\Services\Forms;

use App\Enums\RoleEnum;
use App\Helpers\Helpers;
use App\Models\State;
use App\Models\User;
use phpformbuilder\Form;

class UserFormBuilder
{
    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(
        User $user,
        $roles,
        $countries
    ): Form {
        return $this->build(
            $user,
            $roles,
            $countries,
            false
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(
        User $user,
        $roles,
        $countries
    ): Form {
        return $this->build(
            $user,
            $roles,
            $countries,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD
    |--------------------------------------------------------------------------
    */

    private function build(
        User $user,
        $roles,
        $countries,
        bool $editing = false
    ): Form {

        require_once base_path(
            'phpformbuilder/autoload.php'
        );

        /*
         * Limpiamos la sesión interna de PHP Form Builder.
         *
         * Los valores los restauraremos nosotros utilizando old()
         * y los datos existentes del modelo.
         */
        Form::clear('userForm');

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

        /*
        |--------------------------------------------------------------------------
        | Action
        |--------------------------------------------------------------------------
        */

        if ($editing) {

            $form->setAction(
                route(
                    'admin.user.update',
                    $user->id
                ),
                false
            );

        } else {

            $form->setAction(
                route('admin.user.store'),
                false
            );
        }

        /*
         * HTML solamente soporta GET/POST.
         *
         * Laravel utiliza _method=PUT para actualización.
         */
        $form->setMethod('POST');

        /*
        |--------------------------------------------------------------------------
        | CSRF
        |--------------------------------------------------------------------------
        */

        $form->addInput(
            'hidden',
            '_token',
            csrf_token()
        );

        /*
        |--------------------------------------------------------------------------
        | Method Spoofing Laravel
        |--------------------------------------------------------------------------
        */

        if ($editing) {

            $form->addInput(
                'hidden',
                '_method',
                'PUT'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Errores Laravel
        |--------------------------------------------------------------------------
        */

        $this->addLaravelErrors($form);

        /*
        |--------------------------------------------------------------------------
        | CABECERA
        |--------------------------------------------------------------------------
        */

        $form->addHtml(
            '
            <div class="user-form-header mb-4">

                <div class="d-flex align-items-center">

                    <div class="user-form-icon me-3">

                        <i class="' .
                            (
                                $editing
                                    ? 'fa-solid fa-user-pen'
                                    : 'fa-solid fa-user-plus'
                            ) .
                        '"></i>

                    </div>

                    <div>

                        <h4 class="mb-1">
                            ' .
                            (
                                $editing
                                    ? 'Editar usuario'
                                    : 'Nuevo usuario'
                            ) .
                        '
                        </h4>

                        <p class="text-muted mb-0">
                            ' .
                            (
                                $editing
                                    ? 'Actualice la cuenta y la información personal del usuario.'
                                    : 'Registre la cuenta y la información personal utilizada para los controles médicos.'
                            ) .
                        '
                        </p>

                    </div>

                </div>

            </div>
            '
        );

        /*
        |--------------------------------------------------------------------------
        | INFORMACIÓN DE CUENTA
        |--------------------------------------------------------------------------
        */

        $this->openSection(
            $form,
            'fa-solid fa-user',
            'Información de cuenta',
            'Datos principales para identificar y autenticar al usuario.'
        );

        /*
        |--------------------------------------------------------------------------
        | Nombre / Apellido
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
            'required,placeholder=Repita el correo electrónico'
        );

        $form->endCol();

        $form->endRow();

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        $form->startRow('g-3');

        /*
         * Password
         */

        $form->startCol(6, 'md');

        $form->addIcon(
            'password',
            '<i class="fa-solid fa-lock"></i>',
            'before'
        );

        if ($editing) {

            $form->addHelper(
                'Déjelo vacío para conservar la contraseña actual.',
                'password'
            );

            $form->addInput(
                'password',
                'password',
                '',
                'Nueva contraseña',
                'minlength=8,autocomplete=new-password,placeholder=Nueva contraseña'
            );

        } else {

            $form->addHelper(
                'Debe contener como mínimo 8 caracteres.',
                'password'
            );

            $form->addInput(
                'password',
                'password',
                '',
                'Contraseña',
                'required,minlength=8,autocomplete=new-password,placeholder=Ingrese una contraseña'
            );
        }

        $form->endCol();

        /*
         * Confirmación
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
            $editing
                ? 'Confirmar nueva contraseña'
                : 'Confirmar contraseña',
            $editing
                ? 'minlength=8,autocomplete=new-password,placeholder=Repita la nueva contraseña'
                : 'required,minlength=8,autocomplete=new-password,placeholder=Repita la contraseña'
        );

        $form->endCol();

        $form->endRow();

        $this->closeSection($form);

        /*
        |--------------------------------------------------------------------------
        | INFORMACIÓN PERSONAL
        |--------------------------------------------------------------------------
        */

        $this->openSection(
            $form,
            'fa-solid fa-id-card',
            'Información personal',
            'Datos asociados al perfil utilizado para los controles médicos.'
        );

        /*
         * IMPORTANTE:
         *
         * PHP Form Builder 6.2 tiene problemas con campos como:
         *
         * persona[numero_documento]
         *
         * Por eso utilizamos nombres planos:
         *
         * persona_tipo_documento
         * persona_numero_documento
         * persona_parentesco
         *
         * CreateUserRequest / UpdateUserRequest los transforman
         * posteriormente mediante prepareForValidation().
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

        /*
        |--------------------------------------------------------------------------
        | Documento
        |--------------------------------------------------------------------------
        */

        $form->startRow('g-3');

        /*
         * Tipo documento
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
         * Número documento
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
            'required,id=numero_documento,maxlength=20,placeholder=Ingrese el número'
        );

        $form->endCol();

        /*
         * Parentesco
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
        | Fecha nacimiento / género
        |--------------------------------------------------------------------------
        */

        $form->startRow('g-3');

        /*
         * Fecha
         */

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

        /*
         * Género
         */

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
            'Información telefónica del usuario.'
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

            $attributes = [];

            if (
                (string) $countryCode ===
                (string) $option->calling_code
            ) {
                $attributes[] = 'selected';
            }

            if (!empty($option->flag)) {

                $attributes[] =
                    'data-image=' .
                    asset(
                        'assets/images/flags/' .
                        $option->flag
                    );
            }

            $form->addOption(
                'country_code',
                $option->calling_code,
                '+' . $option->calling_code,
                '',
                implode(',', $attributes)
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
        | ACCESO AL SISTEMA
        |--------------------------------------------------------------------------
        */

        $this->openSection(
            $form,
            'fa-solid fa-shield-halved',
            'Acceso al sistema',
            'Configure el rol y estado del usuario.'
        );

        $form->startRow('g-3');

        /*
        |--------------------------------------------------------------------------
        | Rol
        |--------------------------------------------------------------------------
        */

        $form->startCol(6, 'md');

        $selectedRole = old(
            'role_id',
            $user->roles
                ->pluck('id')
                ->first()
        );

        $form->addOption(
            'role_id',
            '',
            'Seleccione...'
        );

        foreach ($roles as $role) {

            /*
             * Conservamos el comportamiento que tenía Cuba:
             * no mostrar ADMIN.
             */
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
        | Status
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
            'País, departamento/estado y ciudad de residencia.'
        );

        /*
        |--------------------------------------------------------------------------
        | País actual
        |--------------------------------------------------------------------------
        */

        $selectedCountryId = old(
            'country_id',
            $user->country_id ?? ''
        );

        $selectedCountryName = '';

        if (!empty($selectedCountryId)) {

            $selectedCountryName =
                $countries[$selectedCountryId]
                ?? '';
        }

        $selectedCountryName = old(
            'country_selector',
            $selectedCountryName
        );

        /*
         * addCountrySelect utiliza la sesión interna de
         * PHP Form Builder para establecer la selección.
         */
        if (!isset($_SESSION['userForm'])) {
            $_SESSION['userForm'] = [];
        }

        $_SESSION['userForm']['country_selector'] =
            $selectedCountryName;

        /*
        |--------------------------------------------------------------------------
        | State actual
        |--------------------------------------------------------------------------
        */

        $stateId = old(
            'state_id',
            $user->state_id ?? ''
        );

        $form->startRow('g-3');

        /*
        |--------------------------------------------------------------------------
        | Country Select
        |--------------------------------------------------------------------------
        */

        $form->startCol(6, 'md');

        $countryOptions = [
            'plugin' => 'select2',
            'lang' => 'en',
            'flags' => true,
            'flag_size' => 32,
            'return_value' => 'name',
        ];

        $form->addCountrySelect(
            'country_selector',
            'País',
            'id=country_selector,title=Seleccione un país',
            $countryOptions
        );

        /*
         * ID real que será almacenado en users.country_id
         */
        $form->addInput(
            'hidden',
            'country_id',
            (string) $selectedCountryId,
            '',
            'id=country_id'
        );

        $form->endCol();

        /*
        |--------------------------------------------------------------------------
        | Estado / Departamento
        |--------------------------------------------------------------------------
        */

        $form->startCol(6, 'md');

        $form->addOption(
            'state_id',
            '',
            'Seleccione...'
        );

        if (!empty($selectedCountryId)) {

            $states = State::query()
                ->where(
                    'country_id',
                    $selectedCountryId
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
        | Ciudad / Postal Code
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
            'placeholder=Ingrese la ciudad o ubicación'
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
            'Fotografía e información adicional.'
        );

        /*
        |--------------------------------------------------------------------------
        | Imagen actual en Edit
        |--------------------------------------------------------------------------
        */

        if ($editing) {

            $image = $user->getFirstMedia(
                'image'
            );

            if ($image) {

                $form->addHtml(
                    '
                    <div class="mb-4">

                        <div class="d-flex align-items-center gap-3">

                            <img
                                src="' .
                                    e($image->getUrl()) .
                                '"
                                alt="Avatar"
                                class="rounded-circle border"
                                width="90"
                                height="90"
                                style="object-fit: cover;"
                            >

                            <div>

                                <div class="fw-semibold mb-1">
                                    Fotografía actual
                                </div>

                                <div class="text-muted small mb-2">
                                    Puede seleccionar otra fotografía para reemplazarla.
                                </div>

                                <a
                                    href="' .
                                        e(
                                            route(
                                                'admin.user.removeImage',
                                                $user->id
                                            )
                                        ) .
                                    '"
                                    class="btn btn-sm btn-outline-danger"
                                >

                                    <i class="fa-solid fa-trash me-1"></i>

                                    Eliminar fotografía

                                </a>

                            </div>

                        </div>

                    </div>
                    '
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Imagen / About Me
        |--------------------------------------------------------------------------
        */

        $form->startRow('g-3');

        $form->startCol(4, 'md');

        $form->addInput(
            'file',
            'image',
            '',
            $editing
                ? 'Cambiar fotografía'
                : 'Fotografía',
            'accept=image/*'
        );

        $form->addHelper(
            'Formatos permitidos: JPG, PNG o WEBP.',
            'image'
        );

        $form->endCol();


        $form->startCol(8, 'md');

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
            'rows=4,placeholder=Ingrese información adicional'
        );

        $this->closeSection($form);

        /*
        |--------------------------------------------------------------------------
        | BOTONES
        |--------------------------------------------------------------------------
        */

        $form->startDiv(
            'd-flex justify-content-end gap-2 mt-4 mb-4'
        );

        $form->addHtml(
            '<a
                href="' .
                e(route('admin.user.index')) .
                '"
                class="btn btn-light px-4"
            >
                <i class="fa-solid fa-arrow-left me-1"></i>
                Cancelar
            </a>'
        );

        $form->addBtn(
            'submit',
            'submit',
            1,
            $editing
                ? '<i class="fa-solid fa-floppy-disk me-1"></i> Actualizar usuario'
                : '<i class="fa-solid fa-floppy-disk me-1"></i> Guardar usuario',
            'class=btn btn-primary px-4'
        );

        $form->endDiv();

        /*
        |--------------------------------------------------------------------------
        | Validación visual PHP Form Builder
        |--------------------------------------------------------------------------
        */

        $form->addPlugin(
            'formvalidation',
            '#userForm'
        );

        /*
        |--------------------------------------------------------------------------
        | Javascript personalizado
        |--------------------------------------------------------------------------
        |
        | Country Select devuelve el nombre del país.
        |
        | Nuestro User necesita countries.id.
        |
        | Convertimos:
        |
        | Peru -> 604
        |
        | y posteriormente cargamos los estados vía AJAX.
        |
        */

        $countryMap = [];

        foreach ($countries as $id => $name) {
            $countryMap[$name] = $id;
        }

        $countryMapJson = json_encode(
            $countryMap,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );

        $statesUrl = route(
            'admin.user.get-states'
        );

        $form->addHtml(
            '
            <script>

                document.addEventListener(
                    "DOMContentLoaded",
                    function () {

                        const countryMap = ' .
                            $countryMapJson .
                        ';

                        /*
                        |--------------------------------------------------------------------------
                        | Country Select
                        |--------------------------------------------------------------------------
                        */

                        $("#country_selector").on(
                            "change",
                            function () {

                                const countryName =
                                    $(this).val();

                                const countryId =
                                    countryMap[countryName]
                                    ?? "";

                                /*
                                 * ID real para Laravel.
                                 */
                                $("#country_id").val(
                                    countryId
                                );

                                /*
                                 * Reiniciar estados.
                                 */
                                $("#state").html(
                                    \'<option value="">Cargando...</option>\'
                                );

                                if (!countryId) {

                                    $("#state").html(
                                        \'<option value="">Seleccione un país</option>\'
                                    );

                                    return;
                                }

                                $.ajax({

                                    url: "' .
                                        e($statesUrl) .
                                    '",

                                    type: "GET",

                                    data: {
                                        country_id: countryId
                                    },

                                    dataType: "json",

                                    success: function (
                                        result
                                    ) {

                                        $("#state").html(
                                            \'<option value="">Seleccione...</option>\'
                                        );

                                        $.each(
                                            result.states,
                                            function (
                                                key,
                                                value
                                            ) {

                                                $("#state").append(
                                                    $("<option>", {
                                                        value:
                                                            value.id,

                                                        text:
                                                            value.name
                                                    })
                                                );
                                            }
                                        );
                                    },

                                    error: function () {

                                        $("#state").html(
                                            \'<option value="">No se pudieron cargar los estados</option>\'
                                        );
                                    }

                                });

                            }
                        );

                    }
                );

            </script>
            '
        );

        return $form;
    }

    /*
    |--------------------------------------------------------------------------
    | OPEN SECTION
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

        $form->addHtml(
            '
            <div class="card-header bg-transparent border-bottom">

                <div class="d-flex align-items-center">

                    <div class="section-icon me-3">

                        <i class="' .
                            e($icon) .
                        '"></i>

                    </div>

                    <div>

                        <h5 class="mb-1">
                            ' .
                                e($title) .
                            '
                        </h5>

                        <small class="text-muted">
                            ' .
                                e($description) .
                            '
                        </small>

                    </div>

                </div>

            </div>
            '
        );

        $form->startDiv(
            'card-body'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CLOSE SECTION
    |--------------------------------------------------------------------------
    */

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
    | Laravel Errors
    |--------------------------------------------------------------------------
    */

    private function addLaravelErrors(
        Form $form
    ): void {

        $errors = session(
            'errors'
        );

        if (
            !$errors ||
            !$errors->any()
        ) {
            return;
        }

        $html = '
            <div
                class="
                    alert
                    alert-danger
                    border-0
                    shadow-sm
                    mb-4
                "
            >

                <div class="d-flex">

                    <div class="me-3">

                        <i
                            class="
                                fa-solid
                                fa-circle-exclamation
                                fa-lg
                            "
                        ></i>

                    </div>

                    <div>

                        <strong>
                            Revise la información ingresada.
                        </strong>

                        <ul class="mb-0 mt-2">
        ';

        foreach (
            $errors->all() as $error
        ) {

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
