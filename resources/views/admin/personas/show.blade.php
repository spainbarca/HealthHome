@extends('layouts.simple.master')

@section('title', 'Ficha personal - HealthHome')

@section('css')
    @include('admin.personas.partials.styles')
@endsection

@section('main_content')
    @include('admin.personas.partials.breadcrumb', ['titulo' => 'Ficha personal'])

    @php
        $usuario = $persona->user;
        $fechaNacimiento = $usuario?->dob ? \Illuminate\Support\Carbon::parse($usuario->dob) : null;
        $pestanas = ['ficha', 'controles', 'historial', 'graficos', 'informes'];
        $tabActual = in_array(request('tab'), $pestanas, true) ? request('tab') : 'ficha';
    @endphp

    <div class="container-fluid user-profile">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}<button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}<button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Perfil con imagen real del usuario, no la fotografía de ejemplo de Cuba. --}}
        <div class="card overflow-hidden mb-4">
            <div class="hh-ficha-cover"></div>
            <div class="card-body pt-0">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                    <div class="d-flex flex-wrap align-items-end gap-3 hh-ficha-identity">
                        @include('admin.personas.partials.avatar', ['user' => $usuario, 'size' => 'lg'])
                        <div class="pb-1 pt-3">
                            <h3 class="mb-1">{{ $persona->nombre_completo ?: 'Usuario no disponible' }}</h3>
                            <div class="text-muted mb-2">{{ $usuario?->email ?: 'Cuenta no disponible' }}</div>
                            <span class="badge bg-light-primary text-primary">{{ $persona->parentesco ?: 'Sin parentesco registrado' }}</span>
                            <span class="badge bg-light-secondary text-secondary ms-1">Ficha #{{ $persona->id }}</span>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 pb-1">
                        <a href="{{ route('admin.personas.edit', $persona) }}" class="btn btn-primary"><i class="fa-solid fa-user-pen me-2"></i>Editar ficha</a>
                        <a href="{{ route('admin.personas.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i>Volver</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cifras del historial, datos reales de la persona. --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-sm-6">
                <div class="hh-stat h-100"><div class="text-muted mb-2"><i class="fa-solid fa-heart-pulse text-primary me-2"></i>Controles realizados</div>
                    <h3 class="mb-0">{{ number_format($persona->sesiones_control_count) }}</h3></div>
            </div>
            <div class="col-lg-4 col-sm-6">
                <div class="hh-stat h-100"><div class="text-muted mb-2"><i class="fa-solid fa-list-check text-primary me-2"></i>Tipos de control utilizados</div>
                    <h3 class="mb-0">{{ $resumenTipos->count() }}</h3></div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <div class="hh-stat h-100"><div class="text-muted mb-2"><i class="fa-regular fa-calendar-check text-primary me-2"></i>Último control</div>
                    <h6 class="mb-0">{{ $ultimaSesion?->fecha_hora?->format('d/m/Y H:i') ?: 'Sin controles' }}</h6>
                    @if ($ultimaSesion?->tipoControl)<small class="text-muted">{{ $ultimaSesion->tipoControl->nombre }}</small>@endif
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-3">
                <div class="card"><div class="card-body">
                    <div class="nav nav-pills flex-column gap-1 hh-profile-tabs" role="tablist" aria-label="Secciones de la ficha personal">
                        <button type="button" class="nav-link {{ $tabActual === 'ficha' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#panel-ficha" data-tab="ficha" role="tab" aria-controls="panel-ficha" aria-selected="{{ $tabActual === 'ficha' ? 'true' : 'false' }}">
                            <i class="fa-regular fa-id-card"></i><span>Ficha técnica</span>
                        </button>
                        <button type="button" class="nav-link {{ $tabActual === 'controles' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#panel-controles" data-tab="controles" role="tab" aria-controls="panel-controles" aria-selected="{{ $tabActual === 'controles' ? 'true' : 'false' }}">
                            <i class="healthicons-blood-bag"></i><span>Controles</span>
                        </button>
                        <button type="button" class="nav-link {{ $tabActual === 'historial' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#panel-historial" data-tab="historial" role="tab" aria-controls="panel-historial" aria-selected="{{ $tabActual === 'historial' ? 'true' : 'false' }}">
                            <i class="fa-solid fa-clock-rotate-left"></i><span>Historial</span>
                        </button>
                        <button type="button" class="nav-link {{ $tabActual === 'graficos' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#panel-graficos" data-tab="graficos" role="tab" aria-controls="panel-graficos" aria-selected="{{ $tabActual === 'graficos' ? 'true' : 'false' }}">
                            <i class="fa-solid fa-chart-line"></i><span>Gráficos</span>
                        </button>
                        <button type="button" class="nav-link {{ $tabActual === 'informes' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#panel-informes" data-tab="informes" role="tab" aria-controls="panel-informes" aria-selected="{{ $tabActual === 'informes' ? 'true' : 'false' }}">
                            <i class="fa-regular fa-file-pdf"></i><span>Informes</span>
                        </button>
                    </div>
                </div></div>
            </div>

            <div class="col-xl-9">
                <div class="tab-content">
                    {{-- Panel 1: ficha con datos leídos desde User y Persona. --}}
                    <div id="panel-ficha" class="tab-pane fade {{ $tabActual === 'ficha' ? 'show active' : '' }}" role="tabpanel">
                        <div class="card">
                            <div class="card-header"><h5 class="mb-0"><i class="fa-regular fa-address-card me-2 text-primary"></i>Información personal</h5></div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-lg-6 col-md-6"><div class="hh-info-label">Nombres y apellidos</div><div class="hh-info-value">{{ $persona->nombre_completo ?: '—' }}</div></div>
                                    <div class="col-lg-3 col-md-6"><div class="hh-info-label">Tipo de documento</div><div class="hh-info-value">{{ $persona->tipo_documento ?: '—' }}</div></div>
                                    <div class="col-lg-3 col-md-6"><div class="hh-info-label">Número de documento</div><div class="hh-info-value">{{ $persona->numero_documento ?: '—' }}</div></div>
                                    <div class="col-lg-3 col-md-6"><div class="hh-info-label"><i class="fa-solid fa-cake-candles me-1"></i>Fecha de nacimiento</div><div class="hh-info-value">{{ $fechaNacimiento?->format('d/m/Y') ?: '—' }}</div></div>
                                    <div class="col-lg-3 col-md-6"><div class="hh-info-label">Edad</div><div class="hh-info-value">{{ $fechaNacimiento ? $fechaNacimiento->age . ' años' : '—' }}</div></div>
                                    <div class="col-lg-3 col-md-6"><div class="hh-info-label">Sexo / género registrado</div><div class="hh-info-value">{{ $usuario?->gender ?: '—' }}</div></div>
                                    <div class="col-lg-3 col-md-6"><div class="hh-info-label">Parentesco</div><div class="hh-info-value">{{ $persona->parentesco ?: '—' }}</div></div>
                                    <div class="col-lg-6 col-md-6"><div class="hh-info-label"><i class="fa-regular fa-envelope me-1"></i>Correo electrónico</div><div class="hh-info-value">{{ $usuario?->email ?: '—' }}</div></div>
                                    <div class="col-lg-6 col-md-6"><div class="hh-info-label"><i class="fa-solid fa-phone me-1"></i>Teléfono</div><div class="hh-info-value">{{ $usuario?->phone ?: '—' }}</div></div>
                                    <div class="col-12"><hr class="my-0"></div>
                                    <div class="col-lg-6"><div class="hh-info-label">Ubicación / dirección registrada</div><div class="hh-info-value">{{ $usuario?->location ?: '—' }}</div></div>
                                    <div class="col-lg-6"><div class="hh-info-label">Fecha de creación de la ficha</div><div class="hh-info-value">{{ $persona->created_at?->format('d/m/Y H:i') ?: '—' }}</div></div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex gap-3 align-items-start">
                                    <i class="fa-solid fa-shield-heart text-primary fa-xl mt-1"></i>
                                    <div><h6 class="mb-1">Ficha de seguimiento domiciliario</h6>
                                        <p class="text-muted mb-0">Esta sección reúne datos administrativos y mediciones realizadas en casa. No sustituye una historia clínica ni un diagnóstico médico profesional.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Panel 2: resumen de controles + recientes. --}}
                    <div id="panel-controles" class="tab-pane fade {{ $tabActual === 'controles' ? 'show active' : '' }}" role="tabpanel">
                        <div class="card"><div class="card-header"><h5 class="mb-0">Controles por tipo</h5></div>
                            <div class="card-body">
                                @if ($resumenTipos->isEmpty())
                                    <div class="hh-placeholder"><i class="fa-solid fa-heart-pulse"></i><h6>Aún no se han registrado controles</h6><p class="text-muted mb-0">Al registrar los primeros controles aparecerán aquí sus tipos y cantidades.</p></div>
                                @else
                                    <div class="row g-3">
                                        @foreach ($resumenTipos as $resumen)
                                            <div class="col-md-6 col-lg-4"><div class="hh-stat h-100">
                                                <div class="text-muted mb-2">{{ $resumen->tipoControl?->nombre ?: 'Otro control' }}</div>
                                                <h4 class="mb-0">{{ number_format($resumen->total) }}</h4>
                                            </div></div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if ($sesiones->isNotEmpty())
                            <div class="card"><div class="card-header"><h5 class="mb-0">Controles recientes</h5></div>
                                <div class="card-body"><div class="hh-timeline">
                                    @foreach ($sesiones->take(5) as $sesion)
                                        <div class="hh-timeline-item">
                                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                                <strong>{{ $sesion->tipoControl?->nombre ?: 'Control' }}</strong>
                                                <span class="small text-muted">{{ $sesion->fecha_hora?->format('d/m/Y H:i') ?: 'Sin fecha' }}</span>
                                            </div>
                                            <div class="text-muted small mt-1">{{ $sesion->dispositivo?->nombre ?: 'Sin dispositivo asociado' }}</div>
                                            @include('admin.personas.partials.session-values')
                                        </div>
                                    @endforeach
                                </div></div>
                            </div>
                        @endif
                    </div>

                    {{-- Panel 3: todas las sesiones paginadas con valores. --}}
                    <div id="panel-historial" class="tab-pane fade {{ $tabActual === 'historial' ? 'show active' : '' }}" role="tabpanel">
                        <div class="card"><div class="card-header"><h5 class="mb-0">Historial de controles</h5></div>
                            <div class="card-body">
                                @forelse ($sesiones as $sesion)
                                    <div class="border rounded p-3 mb-3">
                                        <div class="d-flex flex-wrap justify-content-between gap-2">
                                            <div><h6 class="mb-1">{{ $sesion->tipoControl?->nombre ?: 'Control médico' }}</h6>
                                                <div class="small text-muted"><i class="fa-solid fa-microchip me-1"></i>{{ $sesion->dispositivo?->nombre ?: 'Sin dispositivo asociado' }}</div></div>
                                            <span class="small text-muted"><i class="fa-regular fa-calendar me-1"></i>{{ $sesion->fecha_hora?->format('d/m/Y H:i') ?: 'Sin fecha' }}</span>
                                        </div>
                                        @include('admin.personas.partials.session-values')
                                        @if ($sesion->observaciones)
                                            <p class="text-muted small mb-0 mt-2">{{ $sesion->observaciones }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <div class="hh-placeholder"><i class="fa-solid fa-clock-rotate-left"></i><h6>Sin historial todavía</h6><p class="text-muted mb-0">Aquí aparecerán las sesiones cuando implementemos el módulo Registrar Control.</p></div>
                                @endforelse
                                <div class="d-flex justify-content-center mt-3">{{ $sesiones->appends(['tab' => 'historial'])->links('pagination::bootstrap-5') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Paneles futuros: sin botones que aparenten funcionar. --}}
                    <div id="panel-graficos" class="tab-pane fade {{ $tabActual === 'graficos' ? 'show active' : '' }}" role="tabpanel">
                        <div class="card"><div class="card-header"><h5 class="mb-0">Gráficos de evolución</h5></div>
                            <div class="card-body"><div class="hh-placeholder">
                                <i class="fa-solid fa-chart-line"></i><h6>Próximo módulo: Gráficos</h6>
                                <p class="text-muted mb-0">Aquí integraremos ApexCharts para visualizar la evolución de presión, pulso, glucosa y otros parámetros cuando esté listo el registro de controles.</p>
                            </div></div>
                        </div>
                    </div>
                    <div id="panel-informes" class="tab-pane fade {{ $tabActual === 'informes' ? 'show active' : '' }}" role="tabpanel">
                        <div class="card"><div class="card-header"><h5 class="mb-0">Informes médicos domiciliarios</h5></div>
                            <div class="card-body"><div class="hh-placeholder">
                                <i class="fa-regular fa-file-pdf"></i><h6>Próximo módulo: Informes PDF</h6>
                                <p class="text-muted mb-0">Podrás generar informes por fecha, persona y tipo de control. Los documentos indicarán que contienen registros domiciliarios.</p>
                            </div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.hh-profile-tabs [data-bs-toggle="pill"]').forEach(boton => {
                boton.addEventListener('shown.bs.tab', event => {
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', event.target.dataset.tab);
                    window.history.replaceState({}, '', url);
                });
            });
        });
    </script>
@endsection
