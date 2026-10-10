@php
    $editando = isset($persona);
    $usuario = $editando ? $persona->user : null;
    $accion = $editando ? route('admin.personas.update', $persona) : route('admin.personas.store');
@endphp

<form method="POST" action="{{ $accion }}" class="edit-profile">
    @csrf
    @if ($editando) @method('PUT') @endif
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Identificación de la persona</h5></div>
                <div class="card-body custom-input">
                    <div class="text-center mb-4">
                        <div id="persona-avatar-preview" class="d-flex justify-content-center mb-3">
                            @include('admin.personas.partials.avatar', ['user' => $usuario, 'size' => 'lg'])
                        </div>
                        <h5 id="persona-nombre-preview" class="mb-1">{{ $editando ? $persona->nombre_completo : 'Selecciona un usuario' }}</h5>
                        <p id="persona-email-preview" class="text-muted small mb-0">{{ $usuario?->email ?: 'La ficha utilizará los datos de su cuenta.' }}</p>
                    </div>

                    @if (!$editando)
                        <div class="mb-3">
                            <label class="form-label" for="user_id">Usuario asociado <span class="text-danger">*</span></label>
                            <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Selecciona un usuario --</option>
                                @foreach ($usuarios as $opcion)
                                    @php
                                        $fotoOpcion = $opcion->media?->first(fn ($m) => str_starts_with((string) $m->mime_type, 'image/'));
                                    @endphp
                                    <option value="{{ $opcion->id }}"
                                            data-nombre="{{ trim($opcion->first_name . ' ' . $opcion->last_name) }}"
                                            data-email="{{ $opcion->email }}"
                                            data-foto="{{ $fotoOpcion?->getUrl() }}"
                                            @selected((string) old('user_id') === (string) $opcion->id)>
                                        {{ trim($opcion->first_name . ' ' . $opcion->last_name) }} — {{ $opcion->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Solo aparecen usuarios que aún no tienen una ficha médica.</div>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label">Usuario asociado</label>
                            <input class="form-control" value="{{ $persona->nombre_completo }} (ID {{ $persona->user_id }})" disabled>
                            <div class="form-text">El vínculo no se cambia para proteger el historial médico.</div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="parentesco" class="form-label">Parentesco</label>
                        <input type="text" id="parentesco" name="parentesco" list="parentescos-sugeridos" maxlength="50"
                               class="form-control @error('parentesco') is-invalid @enderror"
                               value="{{ old('parentesco', $persona->parentesco ?? '') }}"
                               placeholder="Ej. Titular, madre, padre, hijo/a...">
                        <datalist id="parentescos-sugeridos">
                            <option value="Titular"></option><option value="Madre"></option>
                            <option value="Padre"></option><option value="Hijo/a"></option>
                            <option value="Hermano/a"></option><option value="Pareja"></option>
                            <option value="Abuelo/a"></option><option value="Otro familiar"></option>
                        </datalist>
                        @error('parentesco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Datos de identificación</h5></div>
                <div class="card-body custom-input">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="tipo_documento" class="form-label">Tipo de documento</label>
                            <select class="form-select @error('tipo_documento') is-invalid @enderror" name="tipo_documento" id="tipo_documento">
                                <option value="">Sin documento</option>
                                @foreach (['DNI' => 'DNI', 'CE' => 'Carné de extranjería', 'PAS' => 'Pasaporte', 'OTRO' => 'Otro'] as $codigo => $etiqueta)
                                    <option value="{{ $codigo }}" @selected(old('tipo_documento', $persona->tipo_documento ?? '') === $codigo)>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                            @error('tipo_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-7">
                            <label class="form-label" for="numero_documento">Número de documento</label>
                            <input class="form-control @error('numero_documento') is-invalid @enderror" type="text"
                                   name="numero_documento" id="numero_documento" maxlength="20"
                                   value="{{ old('numero_documento', $persona->numero_documento ?? '') }}"
                                   placeholder="Ej. 12345678">
                            @error('numero_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Para DNI se validan 8 dígitos. Los documentos son opcionales.</div>
                        </div>
                        <div class="col-12"><hr class="my-1"></div>
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">
                                <h6 class="mb-2"><i class="fa-solid fa-circle-info text-primary me-2"></i>Datos de la cuenta</h6>
                                <p class="mb-1">Nombre, apellidos, correo, nacimiento, sexo, teléfono y fotografía se toman del modelo <code>User</code>.</p>
                                <p class="mb-0">Esta ficha no altera contraseña, roles ni credenciales de acceso. Para modificar datos personales de la cuenta, utiliza el módulo Usuarios.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.personas.index') }}" class="btn btn-light me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-2"></i>{{ $editando ? 'Actualizar ficha' : 'Crear ficha personal' }}</button>
                </div>
            </div>
        </div>
    </div>
</form>
