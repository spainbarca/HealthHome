@php
    $editando = isset($control);
    $fechaSesion = old('fecha_hora', $editando ? $control->fecha_hora?->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
    $seleccionados = old('sintomas', $editando ? $control->sintomas->pluck('id')->all() : []);
@endphp

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <h6 class="mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Revisa el formulario</h6>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" id="control-medico-form" class="hh-control-form"
      action="{{ $editando ? route('admin.controles.update', $control) : route('admin.controles.store') }}">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header"><h5><i class="fa-solid fa-clipboard-user me-2 text-primary"></i>Datos de la sesión</h5></div>
                <div class="card-body custom-input">
                    <div class="mb-3">
                        <label class="form-label" for="persona_id">Persona <span class="text-danger">*</span></label>
                        @if ($editando)
                            <input type="hidden" name="persona_id" value="{{ $control->persona_id }}">
                        @endif
                        <select class="form-select @error('persona_id') is-invalid @enderror" id="persona_id"
                                name="{{ $editando ? '' : 'persona_id' }}" required @disabled($editando)>
                            <option value="">Selecciona una persona</option>
                            @foreach ($personas as $persona)
                                <option value="{{ $persona->id }}" @selected((int) old('persona_id', $seleccionPersona) === $persona->id)>
                                    {{ $persona->nombre_completo }} {{ $persona->numero_documento ? '· '.$persona->numero_documento : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('persona_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($editando)<small class="text-muted">La persona no puede cambiarse para proteger el historial.</small>@endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="tipo_control_id">Tipo de control <span class="text-danger">*</span></label>
                        @if ($editando)
                            <input type="hidden" name="tipo_control_id" value="{{ $control->tipo_control_id }}">
                        @endif
                        <select class="form-select @error('tipo_control_id') is-invalid @enderror" id="tipo_control_id"
                                name="{{ $editando ? '' : 'tipo_control_id' }}" required @disabled($editando)>
                            <option value="">Selecciona un control</option>
                            @foreach ($tiposControl as $tipo)
                                <option value="{{ $tipo->id }}" @selected((int) old('tipo_control_id', $control->tipo_control_id ?? 0) === $tipo->id)>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_control_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($editando)<small class="text-muted">El tipo de control se conserva durante la edición.</small>@endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="dispositivo_id">Dispositivo utilizado</label>
                        <select name="dispositivo_id" id="dispositivo_id" class="form-select @error('dispositivo_id') is-invalid @enderror">
                            <option value="">Sin dispositivo / ingreso manual</option>
                            @foreach ($dispositivos as $dispositivo)
                                <option value="{{ $dispositivo->id }}" @selected((int) old('dispositivo_id', $control->dispositivo_id ?? 0) === $dispositivo->id)>
                                    {{ $dispositivo->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                        @error('dispositivo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="fecha_hora">Fecha y hora de inicio <span class="text-danger">*</span></label>
                        <input type="datetime-local" id="fecha_hora" name="fecha_hora" value="{{ $fechaSesion }}"
                               class="form-control @error('fecha_hora') is-invalid @enderror" required>
                        @error('fecha_hora')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="origen">Origen <span class="text-danger">*</span></label>
                        <select id="origen" name="origen" class="form-select" required>
                            @foreach (['DOMICILIARIO' => 'Domiciliario', 'CENTRO_MEDICO' => 'Centro médico', 'PROFESIONAL_SALUD' => 'Profesional de salud', 'LABORATORIO' => 'Laboratorio'] as $codigo => $etiqueta)
                                <option value="{{ $codigo }}" @selected(old('origen', $control->origen ?? 'DOMICILIARIO') === $codigo)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="estado_previo">Condición previa</label>
                        <input list="condiciones-previas" id="estado_previo" name="estado_previo" class="form-control"
                               value="{{ old('estado_previo', $control->estado_previo ?? '') }}" maxlength="255"
                               placeholder="Ej. En reposo, en ayunas, después de caminar">
                        <datalist id="condiciones-previas">
                            <option value="En reposo"></option>
                            <option value="Recién despertado"></option>
                            <option value="En ayunas"></option>
                            <option value="Antes de comer"></option>
                            <option value="Después de comer"></option>
                            <option value="Después de caminar"></option>
                            <option value="Después de ejercicio"></option>
                            <option value="Antes de dormir"></option>
                        </datalist>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5><i class="fa-solid fa-notes-medical me-2 text-primary"></i>Observaciones y síntomas</h5></div>
                <div class="card-body custom-input">
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones generales</label>
                        <textarea id="observaciones" name="observaciones" class="form-control" rows="4" maxlength="5000"
                                  placeholder="Motivo del control, síntomas, hora de comida, reposo, etc.">{{ old('observaciones', $control->observaciones ?? '') }}</textarea>
                    </div>
                    <div class="accordion" id="sintomas-acordeon">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="sintomas-heading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sintomas-body" aria-expanded="false" aria-controls="sintomas-body">
                                    Síntomas asociados (opcional)
                                </button>
                            </h2>
                            <div id="sintomas-body" class="accordion-collapse collapse" aria-labelledby="sintomas-heading">
                                <div class="accordion-body">
                                    <div class="row g-2">
                                        @foreach ($sintomas as $sintoma)
                                            <div class="col-12">
                                                <div class="hh-symptom-item">
                                                    <label class="form-check mb-0">
                                                        <input class="form-check-input" type="checkbox" name="sintomas[]" value="{{ $sintoma->id }}"
                                                               @checked(in_array($sintoma->id, array_map('intval', (array) $seleccionados), true))>
                                                        <span class="form-check-label">{{ $sintoma->nombre }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between gap-2 align-items-center">
                    <div>
                        <h5><i class="fa-solid fa-table-cells me-2 text-primary"></i>Mediciones</h5>
                        <span class="hh-control-subtitle">Una sesión puede contener varias tomas según el tipo de control.</span>
                    </div>
                    <span class="badge badge-light-primary" id="contador-mediciones">0 tomas</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border d-flex gap-2 align-items-start">
                        <i class="fa-solid fa-circle-info mt-1 text-primary"></i>
                        <div>
                            <strong>Cómo registrar:</strong> selecciona el tipo de control y completa la fila superior de jsGrid.
                            Pulsa <strong>+</strong> para añadir una toma. Para corregir una, usa el lápiz de su fila.
                            Los cambios se guardan en MySQL únicamente al pulsar <strong>Guardar control</strong>.
                        </div>
                    </div>
                    <div id="sin-tipo" class="text-center py-5 border rounded">
                        <i class="fa-solid fa-heart-pulse fs-1 text-primary" aria-hidden="true"></i>
                        <h6 class="mt-2">Selecciona un tipo de control</h6>
                        <p class="text-muted mb-0">Aquí aparecerán sus parámetros y las tomas registradas.</p>
                    </div>
                    <div id="contenedor-grid" style="display:none">
                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                            <div id="ayuda-parametros" class="text-muted small"></div>
                            <span id="aviso-multiples" class="badge badge-light-secondary"></span>
                        </div>
                        <div id="error-grid" class="alert alert-danger d-none" role="alert"></div>
                        <div class="hh-measure-grid"><div id="mediciones-grid"></div></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex gap-2 align-items-center">
                        <i class="fa-solid fa-shield-heart fa-xl text-primary"></i>
                        <small class="text-muted">Registro de seguimiento, no constituye diagnóstico médico.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.controles.index') }}" class="btn btn-light">Cancelar</a>
                        <button class="btn btn-primary" id="guardar-control" type="submit">
                            <i class="fa-solid fa-floppy-disk me-1"></i>{{ $editando ? 'Actualizar control' : 'Guardar control' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="mediciones_json" name="mediciones_json" value="{{ old('mediciones_json', '') }}">
</form>
