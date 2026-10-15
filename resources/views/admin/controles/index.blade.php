@extends('layouts.simple.master')

@section('title', 'Controles médicos')

@section('css')
    @include('admin.controles.partials.styles')
    @include('admin.personas.partials.styles')
    <style>
        .hh-control-person-picker .form-select { min-height: 48px; }
        .hh-control-person-cover {
            height: 112px;
            background: linear-gradient(115deg, var(--theme-default, #7366ff), #aca1ff);
            border-radius: 14px 14px 0 0;
        }
        .hh-control-person-head { margin-top: -53px; position: relative; }
        .hh-control-person-head .hh-persona-avatar { background-color: #eeedff; }
        .hh-control-person-summary { min-width: 0; }
        .hh-control-person-summary h4 { overflow-wrap: anywhere; }
        .hh-control-stat {
            padding: 13px 16px;
            background: rgba(115, 102, 255, .055);
            border-radius: 10px;
            height: 100%;
        }
        .hh-control-history .hh-control-icon { width: 38px; height: 38px; font-size: 19px; }
        .hh-control-history .hh-value-badges { display: flex; flex-wrap: wrap; gap: 5px; }
        .hh-control-history .hh-value-badges .badge {
            font-weight: 500; font-size: 11px; white-space: nowrap;
        }
        .hh-control-history td { padding-top: 13px; padding-bottom: 13px; }
        .hh-control-select-placeholder { max-width: 600px; margin: auto; padding: 34px 12px; }
        .hh-control-select-placeholder .hh-control-select-icon {
            display: inline-flex; justify-content: center; align-items: center;
            width: 76px; height: 76px; font-size: 31px; border-radius: 50%;
            color: var(--theme-default, #7366ff); background: rgba(115, 102, 255, .09);
        }
        @media (max-width: 575.98px) {
            .hh-control-person-cover { height: 88px; }
            .hh-control-person-head { margin-top: -40px; }
            .hh-control-history td { min-width: 90px; }
        }
    </style>
@endsection

@section('main_content')
    @include('admin.controles.partials.breadcrumb', ['titulo' => 'Controles médicos'])

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>Revisa los filtros ingresados.
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h5 class="mb-1">Historial de controles domiciliarios</h5>
                <p class="text-muted mb-0">Selecciona una persona para consultar exclusivamente sus controles médicos.</p>
            </div>
            @if ($personaSeleccionada)
                <a href="{{ route('admin.controles.create', ['persona_id' => $personaSeleccionada->id]) }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>Registrar control
                </a>
            @endif
        </div>

        {{-- Paso 1: elegir la persona. No se lista ningún control antes de seleccionarla. --}}
        <div class="card hh-control-person-picker mb-3">
            <div class="card-body">
                <form id="hh-form-elegir-persona" method="GET" action="{{ route('admin.controles.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-xl-9 col-lg-8">
                            <label for="hh-persona-id" class="form-label fw-semibold">
                                <i class="fa-solid fa-user-check text-primary me-1"></i> Persona a consultar <span class="text-danger">*</span>
                            </label>
                            <select id="hh-persona-id" name="persona_id" class="form-select" required>
                                <option value="">-- Selecciona una persona --</option>
                                @foreach ($personas as $persona)
                                    <option value="{{ $persona->id }}" @selected($personaSeleccionada?->id === $persona->id)>
                                        {{ $persona->nombre_completo ?: 'Usuario sin nombres' }}
                                        @if ($persona->numero_documento) — {{ $persona->numero_documento }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-4 d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-magnifying-glass me-2"></i>Consultar historial
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if (!$personaSeleccionada)
            <div class="card">
                <div class="card-body text-center">
                    <div class="hh-control-select-placeholder">
                        <span class="hh-control-select-icon mb-3"><i class="fa-solid fa-user-doctor"></i></span>
                        <h4 class="mb-2">Elige a quién deseas consultar</h4>
                        <p class="text-muted mb-0">Cuando selecciones una persona verás su fotografía, datos básicos y controles médicos ordenados desde el más reciente. No se muestran controles de otras personas.</p>
                    </div>
                </div>
            </div>
        @else
            {{-- Ficha de la persona: fotografía proveniente de User / MediaLibrary. --}}
            <div class="card overflow-hidden mb-3">
                <div class="hh-control-person-cover"></div>
                <div class="card-body">
                    <div class="hh-control-person-head d-flex flex-column flex-sm-row align-items-center align-items-sm-end gap-3 mb-4">
                        @include('admin.personas.partials.avatar', ['user' => $personaSeleccionada->user, 'size' => 'lg'])
                        <div class="hh-control-person-summary text-center text-sm-start flex-grow-1">
                            <h4 class="mb-1">{{ $personaSeleccionada->nombre_completo ?: 'Usuario sin nombre' }}</h4>
                            <span class="badge bg-light-primary text-primary me-2">{{ $personaSeleccionada->parentesco ?: 'Sin parentesco' }}</span>
                            <span class="text-muted small">
                                {{ $personaSeleccionada->tipo_documento ?: 'Documento' }}: {{ $personaSeleccionada->numero_documento ?: 'No registrado' }}
                            </span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap justify-content-center">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.personas.show', $personaSeleccionada) }}">
                                <i class="fa-solid fa-address-card me-1"></i> Ver ficha
                            </a>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.controles.index') }}">
                                <i class="fa-solid fa-arrow-right-arrow-left me-1"></i> Cambiar persona
                            </a>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-lg-4 col-sm-6">
                            <div class="hh-control-stat">
                                <div class="text-muted small mb-1"><i class="fa-solid fa-heart-pulse me-1"></i> Total de sesiones</div>
                                <h4 class="mb-0">{{ $personaSeleccionada->sesiones_control_count }}</h4>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="hh-control-stat">
                                <div class="text-muted small mb-1"><i class="fa-regular fa-clock me-1"></i> Último control</div>
                                <h6 class="mb-0">{{ $ultimaSesion?->fecha_hora?->format('d/m/Y H:i') ?: 'Sin controles' }}</h6>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="hh-control-stat">
                                <div class="text-muted small mb-1"><i class="fa-solid fa-envelope me-1"></i> Correo</div>
                                <div class="fw-semibold text-break">{{ $personaSeleccionada->user?->email ?: 'No registrado' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Paso 2: los filtros solo se activan para esta persona. --}}
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0"><i class="fa-solid fa-filter text-primary me-2"></i>Filtrar controles de esta persona</h5></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.controles.index') }}">
                        <input type="hidden" name="persona_id" value="{{ $personaSeleccionada->id }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-xl-3 col-md-6">
                                <label for="buscar" class="form-label">Buscar</label>
                                <input type="search" id="buscar" name="q" class="form-control"
                                       value="{{ request('q', '') }}" placeholder="Control, equipo u observación">
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <label for="tipo" class="form-label">Tipo de control</label>
                                <select id="tipo" name="tipo_control_id" class="form-select">
                                    <option value="">Todos los controles</option>
                                    @foreach ($tiposControl as $tipo)
                                        <option value="{{ $tipo->id }}" @selected((int) request('tipo_control_id') === $tipo->id)>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <label for="desde" class="form-label">Desde</label>
                                <input type="date" id="desde" name="desde" class="form-control" value="{{ request('desde', '') }}">
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <label for="hasta" class="form-label">Hasta</label>
                                <input type="date" id="hasta" name="hasta" class="form-control" value="{{ request('hasta', '') }}">
                            </div>
                            <div class="col-xl-1 col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1" title="Aplicar filtros" aria-label="Aplicar filtros">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <a href="{{ route('admin.controles.index', ['persona_id' => $personaSeleccionada->id]) }}"
                                   class="btn btn-outline-secondary" title="Limpiar filtros" aria-label="Limpiar filtros">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Paso 3: tabla paginada y ordenada por fecha/hora DESC desde el servidor. --}}
            <div class="card hh-control-history">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Controles registrados</h5>
                    <small class="text-muted">
                        {{ $controles->total() }} resultados · Ordenados del más reciente al más antiguo
                    </small>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-scrollbar">
                        <table class="table align-middle table-hover hh-control-table mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha y hora</th>
                                    <th>Control</th>
                                    <th>Última toma</th>
                                    <th>Dispositivo</th>
                                    <th>Tomas</th>
                                    <th>Origen</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($controles as $control)
                                    @php $ultimaToma = $control->mediciones->last(); @endphp
                                    <tr>
                                        <td class="text-nowrap">
                                            <span class="fw-semibold">{{ $control->fecha_hora?->format('d/m/Y') ?: '—' }}</span>
                                            <div class="small text-muted">{{ $control->fecha_hora?->format('H:i') ?: '' }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="hh-control-icon">
                                                    <i class="{{ $control->tipoControl?->icono ?: 'fa-solid fa-heart-pulse' }}"></i>
                                                </span>
                                                <a class="fw-semibold text-decoration-none" href="{{ route('admin.controles.show', $control) }}">
                                                    {{ $control->tipoControl?->nombre ?: 'Sin tipo' }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($ultimaToma && $ultimaToma->valores->isNotEmpty())
                                                <div class="hh-value-badges">
                                                    @foreach ($ultimaToma->valores as $valor)
                                                        <span class="badge bg-light-primary text-primary">
                                                            {{ $valor->parametro?->nombre_corto ?: $valor->parametro?->nombre ?: 'Valor' }}:
                                                            @if ($valor->valor !== null)
                                                                {{ number_format((float) $valor->valor, (int) ($valor->parametro?->decimales ?? 0), '.', '') }}
                                                            @else
                                                                {{ $valor->valor_texto ?: '—' }}
                                                            @endif
                                                            {{ $valor->parametro?->unidad ?? '' }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted small">Sin valores</span>
                                            @endif
                                        </td>
                                        <td>{{ $control->dispositivo?->nombre ?: 'Sin equipo' }}</td>
                                        <td><span class="badge badge-light-primary">{{ $control->mediciones_count }}</span></td>
                                        <td><small>{{ ucfirst(mb_strtolower(str_replace('_', ' ', (string) $control->origen))) }}</small></td>
                                        <td class="text-end text-nowrap">
                                            <a href="{{ route('admin.controles.show', $control) }}"
                                               class="btn btn-outline-primary btn-sm" title="Ver control" aria-label="Ver control">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.controles.edit', $control) }}"
                                               class="btn btn-outline-secondary btn-sm" title="Editar control" aria-label="Editar control">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fa-solid fa-clipboard-list fa-2x text-muted mb-3 d-block"></i>
                                            <h6>Sin controles para esta búsqueda</h6>
                                            <p class="text-muted mb-0">Puedes ajustar los filtros o registrar una nueva sesión.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($controles->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $controles->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selector = document.getElementById('hh-persona-id');
            const formulario = document.getElementById('hh-form-elegir-persona');
            if (selector && formulario) {
                selector.addEventListener('change', () => {
                    if (selector.value) {
                        formulario.requestSubmit();
                    } else {
                        window.location.assign(formulario.action);
                    }
                });
            }
        });
    </script>
@endsection
