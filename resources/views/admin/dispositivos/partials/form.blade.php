@php
    $editando = isset($dispositivo);
    $accion = $editando
        ? route('admin.dispositivos.update', $dispositivo)
        : route('admin.dispositivos.store');
    $iconoActual = preg_replace('/^healthicons-/i', '', trim((string) old('icono', $dispositivo->icono ?? '')));
    $imagenActual = $editando && $dispositivo->imagen
        ? Storage::disk('public')->url($dispositivo->imagen)
        : null;
@endphp

<style>
    .dispositivo-preview {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: var(--bs-light, #f8f9fa);
        border: 1px dashed #d9dee3;
        border-radius: 12px;
    }
    .dispositivo-preview img { width: 100%; height: 220px; object-fit: contain; }
    .dispositivo-preview .icono-vacio { font-size: 74px; color: #8c94a3; }
    .healthicon-preview-box { min-height: 112px; background: var(--bs-light, #f8f9fa); border: 1px dashed #d9dee3; border-radius: 12px; }
    .healthicon-preview-box i { font-size: 54px; display: inline-block; line-height: 1.2; }
    .healthicon-preview-box i[hidden], .dispositivo-preview i[hidden] { display: none !important; }
</style>

<form method="POST" action="{{ $accion }}" enctype="multipart/form-data" class="edit-profile">
    @csrf
    @if ($editando)
        @method('PUT')
    @endif

    <div class="row">
        {{-- Columna izquierda: información principal --}}
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Datos principales</h5>
                </div>
                <div class="card-body custom-input">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del dispositivo <span class="text-danger">*</span></label>
                        <input type="text" id="nombre" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               maxlength="150" required
                               value="{{ old('nombre', $dispositivo->nombre ?? '') }}"
                               placeholder="Ej. Tensiómetro de brazo">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <input type="text" id="tipo" name="tipo" list="tipos-sugeridos"
                               class="form-control @error('tipo') is-invalid @enderror"
                               maxlength="80" required
                               value="{{ old('tipo', $dispositivo->tipo ?? '') }}"
                               placeholder="Ej. Tensiómetro">
                        <datalist id="tipos-sugeridos">
                            <option value="Tensiómetro"></option>
                            <option value="Glucómetro"></option>
                            <option value="Oxímetro"></option>
                            <option value="Termómetro"></option>
                            <option value="Balanza"></option>
                            <option value="Nebulizador"></option>
                            <option value="Peak Flow"></option>
                            <option value="Espirómetro"></option>
                            <option value="ECG portátil"></option>
                        </datalist>
                        <div class="form-text">Puedes escoger una sugerencia o escribir otro tipo.</div>
                        @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3" maxlength="2000"
                                  class="form-control @error('descripcion') is-invalid @enderror"
                                  placeholder="Descripción general del equipo">{{ old('descripcion', $dispositivo->descripcion ?? '') }}</textarea>
                        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="imagen">Imagen</label>
                        <div class="dispositivo-preview mb-3">
                            <img id="vista-previa-imagen"
                                 src="{{ $imagenActual ?? '' }}" alt="Vista previa del dispositivo"
                                 @if (!$imagenActual) style="display:none" @endif>
                            <div id="imagen-vacia" class="text-center" @if ($imagenActual) style="display:none" @endif>
                                <i id="vista-previa-icono-imagen" class="icono-vacio" hidden aria-hidden="true"></i>
                                <span id="imagen-sin-icono" class="text-muted small d-block">Sin imagen</span>
                            </div>
                        </div>
                        <input id="imagen" name="imagen" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                               class="form-control @error('imagen') is-invalid @enderror">
                        <div class="form-text">JPG, PNG o WebP. Máximo 3 MB.</div>
                        @error('imagen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if ($imagenActual)
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="eliminar_imagen" id="eliminar_imagen" value="1"
                                       @checked(old('eliminar_imagen', false))>
                                <label class="form-check-label" for="eliminar_imagen">Quitar imagen actual</label>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="icono">Health Icons — Filename</label>
                        <input id="icono" name="icono" type="text" maxlength="120" list="iconos-sugeridos"
                               class="form-control @error('icono') is-invalid @enderror"
                               value="{{ $iconoActual }}" autocomplete="off" spellcheck="false"
                               placeholder="Ej. blood-bag o blood-b_n">
                        <datalist id="iconos-sugeridos">
                            <option value="blood-bag"></option>
                            <option value="blood-b_n"></option>
                        </datalist>
                        @error('icono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text mb-2">Escribe el filename del paquete instalado, sin <code>healthicons-</code>. También puedes pegar la clase completa. No se carga ningún SVG.</div>
                        <div class="healthicon-preview-box d-flex flex-column align-items-center justify-content-center gap-2 p-3" aria-live="polite">
                            <i id="vista-previa-icono" class="text-primary" hidden aria-hidden="true"></i>
                            <span id="nombre-vista-previa-icono" class="small text-muted text-center">Escribe el filename para ver el icono</span>
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" class="form-check-input" role="switch" id="activo" name="activo" value="1"
                               @checked((string) old('activo', $dispositivo->activo ?? 1) === '1')>
                        <label class="form-check-label" for="activo">Dispositivo activo</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: información adicional y mantenimiento --}}
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información adicional</h5>
                </div>
                <div class="card-body">
                    <div class="row custom-input">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="marca">Marca</label>
                            <input class="form-control @error('marca') is-invalid @enderror" id="marca" name="marca"
                                   maxlength="100" value="{{ old('marca', $dispositivo->marca ?? '') }}" placeholder="Ej. Omron">
                            @error('marca') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="modelo">Modelo</label>
                            <input class="form-control @error('modelo') is-invalid @enderror" id="modelo" name="modelo"
                                   maxlength="100" value="{{ old('modelo', $dispositivo->modelo ?? '') }}" placeholder="Ej. HEM-7120">
                            @error('modelo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="numero_serie">Número de serie</label>
                            <input class="form-control @error('numero_serie') is-invalid @enderror" id="numero_serie"
                                   name="numero_serie" maxlength="120"
                                   value="{{ old('numero_serie', $dispositivo->numero_serie ?? '') }}"
                                   placeholder="N.º de serie del equipo">
                            @error('numero_serie') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="fecha_compra">Fecha de compra</label>
                            <input class="form-control @error('fecha_compra') is-invalid @enderror" type="date"
                                   id="fecha_compra" name="fecha_compra"
                                   value="{{ old('fecha_compra', isset($dispositivo) ? $dispositivo->fecha_compra?->format('Y-m-d') : '') }}">
                            @error('fecha_compra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="fecha_ultima_calibracion">Última calibración</label>
                            <input class="form-control @error('fecha_ultima_calibracion') is-invalid @enderror" type="date"
                                   id="fecha_ultima_calibracion" name="fecha_ultima_calibracion"
                                   value="{{ old('fecha_ultima_calibracion', isset($dispositivo) ? $dispositivo->fecha_ultima_calibracion?->format('Y-m-d') : '') }}">
                            @error('fecha_ultima_calibracion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="fecha_proxima_calibracion">Próxima calibración</label>
                            <input class="form-control @error('fecha_proxima_calibracion') is-invalid @enderror" type="date"
                                   id="fecha_proxima_calibracion" name="fecha_proxima_calibracion"
                                   value="{{ old('fecha_proxima_calibracion', isset($dispositivo) ? $dispositivo->fecha_proxima_calibracion?->format('Y-m-d') : '') }}">
                            @error('fecha_proxima_calibracion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea id="observaciones" name="observaciones" rows="5" maxlength="5000"
                                      class="form-control @error('observaciones') is-invalid @enderror"
                                      placeholder="Notas de uso, baterías, mantenimiento, estado del equipo, etc.">{{ old('observaciones', $dispositivo->observaciones ?? '') }}</textarea>
                            @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.dispositivos.index') }}" class="btn btn-light me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        {{ $editando ? 'Actualizar dispositivo' : 'Guardar dispositivo' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
