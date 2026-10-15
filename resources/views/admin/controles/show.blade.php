@extends('layouts.simple.master')

@section('title', 'Detalle del control médico')
@section('css')
    @include('admin.controles.partials.styles')
@endsection

@section('main_content')
    @include('admin.controles.partials.breadcrumb', ['titulo' => 'Detalle de control'])
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

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="hh-control-icon"><i class="{{ $control->tipoControl?->icono ?: 'fa-solid fa-heart-pulse' }}"></i></div>
                <div>
                    <h4 class="mb-0">{{ $control->tipoControl?->nombre }}</h4>
                    <span class="hh-control-subtitle">Sesión #{{ $control->id }} · {{ $control->fecha_hora?->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.controles.index') }}" class="btn btn-light">Volver</a>
                <a href="{{ route('admin.controles.edit', $control) }}" class="btn btn-primary"><i class="fa-solid fa-pen me-1"></i>Editar sesión</a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-md-6"><div class="hh-control-overview"><small>Persona</small><h6 class="mt-2 mb-1">{{ $control->persona?->nombre_completo }}</h6><div class="small text-muted">{{ $control->persona?->numero_documento ?: 'Sin documento' }}</div></div></div>
            <div class="col-xl-3 col-md-6"><div class="hh-control-overview"><small>Dispositivo</small><h6 class="mt-2 mb-1">{{ $control->dispositivo?->nombre ?: 'Sin dispositivo' }}</h6><div class="small text-muted">{{ $control->dispositivo?->marca ?: 'Medición manual' }}</div></div></div>
            <div class="col-xl-3 col-md-6"><div class="hh-control-overview"><small>Mediciones</small><h4 class="mt-2 mb-1">{{ $control->mediciones->count() }}</h4><div class="small text-muted">Tomas de la sesión</div></div></div>
            <div class="col-xl-3 col-md-6"><div class="hh-control-overview"><small>Condición previa</small><h6 class="mt-2 mb-1">{{ $control->estado_previo ?: 'No registrada' }}</h6><div class="small text-muted">{{ str_replace('_', ' ', $control->origen) }}</div></div></div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="fa-solid fa-table-list me-2 text-primary"></i>Lecturas registradas</h5></div>
            <div class="card-body">
                <div class="table-responsive custom-scrollbar">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th><th>Fecha y hora</th>
                                @foreach ($control->tipoControl->parametros as $parametro)
                                    <th>{{ $parametro->nombre_corto ?: $parametro->nombre }}<br><small class="text-muted">{{ $parametro->unidad }}</small></th>
                                @endforeach
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($control->mediciones as $medicion)
                                <tr>
                                    <td>{{ $medicion->numero_medicion }}</td>
                                    <td class="text-nowrap">{{ $medicion->fecha_hora?->format('d/m/Y H:i') }}</td>
                                    @foreach ($control->tipoControl->parametros as $parametro)
                                        @php($valor = $medicion->valores->firstWhere('tipo_parametro_id', $parametro->id))
                                        <td class="fw-semibold">
                                            {{ $valor && $valor->valor !== null ? number_format((float) $valor->valor, (int) $parametro->decimales, '.', '') : ($valor?->valor_texto ?: '—') }}
                                        </td>
                                    @endforeach
                                    <td>{{ $medicion->observaciones ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ $control->tipoControl->parametros->count() + 3 }}" class="text-center py-4">No hay mediciones registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header"><h5 class="mb-0"><i class="fa-solid fa-file-medical me-2 text-primary"></i>Observaciones</h5></div>
                    <div class="card-body"><p style="white-space:pre-line">{{ $control->observaciones ?: 'No se registraron observaciones generales.' }}</p></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header"><h5 class="mb-0"><i class="fa-solid fa-notes-medical me-2 text-primary"></i>Síntomas asociados</h5></div>
                    <div class="card-body">
                        @forelse ($control->sintomas as $sintoma)
                            <span class="badge bg-light text-dark border me-1 mb-2">{{ $sintoma->nombre }}</span>
                        @empty
                            <p class="text-muted">Sin síntomas registrados.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3 mb-4">
            <span class="text-muted small"><i class="fa-solid fa-shield-heart me-1"></i>Registro domiciliario; no constituye diagnóstico médico.</span>
            <form method="POST" action="{{ route('admin.controles.destroy', $control) }}" onsubmit="return confirm('¿Eliminar esta sesión y todas sus tomas? Esta acción no se puede deshacer.');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Eliminar sesión</button>
            </form>
        </div>
    </div>
@endsection
