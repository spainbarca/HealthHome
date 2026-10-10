@extends('layouts.simple.master')

@section('title', 'Detalle del dispositivo')

@section('css')
    <style>
        .dispositivo-icono-detalle { font-size: 86px; }
    </style>
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6"><h3>Detalle del dispositivo</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.default_dashboard') }}">
                            <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg>
                        </a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dispositivos.index') }}">Dispositivos</a></li>
                        <li class="breadcrumb-item active">Detalle</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="rounded mb-3 d-flex align-items-center justify-content-center bg-light" style="height:250px;overflow:hidden;">
                            @if ($dispositivo->imagen)
                                <img src="{{ Storage::disk('public')->url($dispositivo->imagen) }}"
                                     alt="{{ $dispositivo->nombre }}" style="width:100%;height:100%;object-fit:contain;">
                            @else
                                @include('admin.dispositivos.partials.health-icon', [
                                    'filename' => $dispositivo->icono,
                                    'class' => 'text-secondary dispositivo-icono-detalle',
                                    'fallbackText' => 'Sin icono'
                                ])
                            @endif
                        </div>
                        <h4>{{ $dispositivo->nombre }}</h4>
                        <p class="text-muted">{{ $dispositivo->tipo }}</p>
                        <span class="badge {{ $dispositivo->activo ? 'bg-success' : 'bg-secondary' }}">
                            {{ $dispositivo->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                        <div class="mt-4 d-flex justify-content-center gap-2">
                            <a href="{{ route('admin.dispositivos.edit', $dispositivo) }}" class="btn btn-primary">
                                <i class="fa-solid fa-pen me-1"></i>Editar
                            </a>
                            <a href="{{ route('admin.dispositivos.index') }}" class="btn btn-light">Volver</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Ficha técnica</h5></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><strong>Marca:</strong><div>{{ $dispositivo->marca ?: '—' }}</div></div>
                            <div class="col-md-6"><strong>Modelo:</strong><div>{{ $dispositivo->modelo ?: '—' }}</div></div>
                            <div class="col-md-6"><strong>Número de serie:</strong><div>{{ $dispositivo->numero_serie ?: '—' }}</div></div>
                            <div class="col-md-6"><strong>Fecha de compra:</strong><div>{{ $dispositivo->fecha_compra?->format('d/m/Y') ?: '—' }}</div></div>
                            <div class="col-md-6"><strong>Última calibración:</strong><div>{{ $dispositivo->fecha_ultima_calibracion?->format('d/m/Y') ?: '—' }}</div></div>
                            <div class="col-md-6"><strong>Próxima calibración:</strong><div>{{ $dispositivo->fecha_proxima_calibracion?->format('d/m/Y') ?: '—' }}</div></div>
                            <div class="col-md-6"><strong>Controles registrados:</strong><div>{{ $dispositivo->sesiones_control_count }}</div></div>
                            <div class="col-md-6">
                                <strong>Health Icons — Filename:</strong>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    @include('admin.dispositivos.partials.health-icon', [
                                        'filename' => $dispositivo->icono,
                                        'class' => 'fs-2 text-primary',
                                        'fallbackText' => 'Sin icono'
                                    ])
                                    <code>{{ $dispositivo->icono ?: '—' }}</code>
                                </div>
                            </div>
                            <div class="col-12"><hr></div>
                            <div class="col-12"><strong>Descripción: </strong><p class="mt-2 mb-0" style="white-space:pre-line">{{ $dispositivo->descripcion ?: ' Sin descripción.' }}</p></div>
                            <div class="col-12"><strong>Observaciones: </strong><p class="mt-2 mb-0" style="white-space:pre-line">{{ $dispositivo->observaciones ?: ' Sin observaciones.' }}</p></div>
                        </div>
                    </div>
                    @if ($dispositivo->sesiones_control_count == 0)
                        <div class="card-footer text-end">
                            <form method="POST" action="{{ route('admin.dispositivos.destroy', $dispositivo) }}"
                                  onsubmit="return confirm('¿Eliminar este dispositivo? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Eliminar dispositivo</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
