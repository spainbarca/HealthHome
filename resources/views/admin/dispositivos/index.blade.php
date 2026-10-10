@extends('layouts.simple.master')

@section('title', 'Dispositivos')

@section('css')
    <style>
        .dispositivo-img {
            height: 200px;
            background: #f7f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .dispositivo-img > img { width: 100%; height: 100%; object-fit: contain; }
        .dispositivo-img .dispositivo-icono { font-size: 76px; color: #8191a9; }
        .dispositivo-card { height: 100%; }
        .dispositivo-filtros .form-label { font-weight: 600; margin-bottom: 7px; }
        .dispositivo-filtros .form-control, .dispositivo-filtros .form-select { min-height: 43px; }
        .dispositivo-filtros .btn { min-height: 43px; white-space: nowrap; }
        .dispositivo-card .product-details { min-height: 176px; padding: 18px; }
        .dispositivo-card .product-details h5 { overflow-wrap: anywhere; }
        .dispositivo-card .product-details p { min-height: 40px; }
        .dispositivo-card .product-hover { z-index: 2; }
        .dispositivo-grid.is-list .dispositivo-col { width: 100%; flex: 0 0 100%; max-width: 100%; }
        .dispositivo-grid.is-list .dispositivo-card { display: grid; grid-template-columns: minmax(160px, 250px) 1fr; }
        .dispositivo-grid.is-list .dispositivo-img { height: 100%; min-height: 185px; }
        .dispositivo-grid.is-list .product-details { min-height: 0; }
        @media (max-width: 575px) {
            .dispositivo-grid.is-list .dispositivo-card { grid-template-columns: 1fr; }
            .dispositivo-grid.is-list .dispositivo-img { height: 200px; }
        }
    </style>
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6"><h3>Dispositivos médicos</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.default_dashboard') }}">
                                <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Dispositivos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid product-wrapper">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <div class="product-grid">
            <div class="feature-products">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <p class="text-muted mb-0">Administra tus equipos de control médico domiciliario.</p>
                    <a href="{{ route('admin.dispositivos.create') }}" class="btn btn-primary f-w-500">
                        <i class="fa-solid fa-plus me-2"></i>Agregar dispositivo
                    </a>
                </div>

                {{-- Barra de filtros horizontal: buscador y filtros en un único formulario GET --}}
                <div class="card mb-3 dispositivo-filtros">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.dispositivos.index') }}" id="form-filtros">
                            <div class="row g-3 align-items-end">
                                <div class="col-xxl-4 col-xl-3 col-lg-6 col-md-12">
                                    <label class="form-label" for="filtro-busqueda">Buscar dispositivo</label>
                                    <input class="form-control" type="search" id="filtro-busqueda" name="q"
                                           value="{{ request('q', '') }}"
                                           placeholder="Nombre, marca, modelo o número de serie">
                                </div>

                                <div class="col-xxl-2 col-xl-2 col-lg-6 col-sm-6">
                                    <label class="form-label" for="filtro-tipo">Tipo</label>
                                    <select class="form-select" id="filtro-tipo" name="tipo">
                                        <option value="">Todos los tipos</option>
                                        @foreach ($tipos as $tipo)
                                            <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ $tipo }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-xxl-2 col-xl-2 col-lg-3 col-sm-6">
                                    <label class="form-label" for="filtro-estado">Estado</label>
                                    <select class="form-select" id="filtro-estado" name="estado">
                                        <option value="" @selected(!request('estado'))>Todos</option>
                                        <option value="activos" @selected(request('estado') === 'activos')>Activos</option>
                                        <option value="inactivos" @selected(request('estado') === 'inactivos')>Inactivos</option>
                                    </select>
                                </div>

                                <div class="col-xxl-2 col-xl-2 col-lg-4 col-sm-6">
                                    <label class="form-label" for="filtro-orden">Ordenar por</label>
                                    <select class="form-select" id="filtro-orden" name="orden">
                                        <option value="recientes" @selected(request('orden', 'recientes') === 'recientes')>Más recientes</option>
                                        <option value="nombre" @selected(request('orden') === 'nombre')>Nombre</option>
                                        <option value="marca" @selected(request('orden') === 'marca')>Marca</option>
                                    </select>
                                </div>

                                <div class="col-xxl-2 col-xl-3 col-lg-4 col-sm-6">
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-primary flex-fill" type="submit">
                                            <i class="fa-solid fa-magnifying-glass me-1"></i>Buscar
                                        </button>
                                        <a href="{{ route('admin.dispositivos.index') }}" class="btn btn-outline-secondary flex-fill">
                                            <i class="fa-duotone fa-regular fa-eraser fa-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div class="d-flex gap-2">
                        <button type="button" id="boton-grid" class="btn btn-outline-primary btn-sm" title="Vista Grid" aria-pressed="true">
                            <i class="fa-solid fa-border-all"></i>
                        </button>
                        <button type="button" id="boton-lista" class="btn btn-outline-secondary btn-sm" title="Vista Lista" aria-pressed="false">
                            <i class="fa-solid fa-list"></i>
                        </button>
                    </div>
                    <span class="f-w-600">
                        Mostrando {{ $dispositivos->firstItem() ?? 0 }}–{{ $dispositivos->lastItem() ?? 0 }}
                        de {{ $dispositivos->total() }} dispositivos
                    </span>
                </div>

                {{-- El Grid ocupa toda la fila, sin columna lateral de filtros --}}
                <div class="product-wrapper-grid">
                    <div class="row g-3 dispositivo-grid" id="dispositivo-grid">
                        @forelse ($dispositivos as $dispositivo)
                            <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 dispositivo-col">
                                <div class="card h-100 mb-0">
                                    <div class="product-box dispositivo-card">
                                        <div class="product-img dispositivo-img">
                                            <span class="badge {{ $dispositivo->activo ? 'bg-success' : 'bg-secondary' }} position-absolute top-0 end-0 m-3">
                                                {{ $dispositivo->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                            @if ($dispositivo->imagen)
                                                <img src="{{ Storage::disk('public')->url($dispositivo->imagen) }}"
                                                     alt="{{ $dispositivo->nombre }}" loading="lazy">
                                            @else
                                                @include('admin.dispositivos.partials.health-icon', [
                                                    'filename' => $dispositivo->icono,
                                                    'class' => 'dispositivo-icono',
                                                    'fallbackText' => 'Sin icono'
                                                ])
                                            @endif
                                            <div class="product-hover">
                                                <ul>
                                                    <li><a class="btn" href="{{ route('admin.dispositivos.show', $dispositivo) }}" title="Ver detalle"><i class="fa-solid fa-eye"></i></a></li>
                                                    <li><a class="btn" href="{{ route('admin.dispositivos.edit', $dispositivo) }}" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="product-details">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                @include('admin.dispositivos.partials.health-icon', [
                                                    'filename' => $dispositivo->icono,
                                                    'class' => 'fs-4 text-primary',
                                                ])
                                                <small class="text-muted">{{ $dispositivo->tipo ?: 'Sin tipo' }}</small>
                                            </div>
                                            <a href="{{ route('admin.dispositivos.show', $dispositivo) }}" class="text-decoration-none">
                                                <h5 class="mb-2">{{ $dispositivo->nombre }}</h5>
                                            </a>
                                            <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($dispositivo->descripcion ?: 'Sin descripción registrada.', 90) }}</p>
                                            <div class="small mb-3">
                                                <strong>Marca:</strong> {{ $dispositivo->marca ?: '—' }}
                                                @if ($dispositivo->modelo) | {{ $dispositivo->modelo }} @endif
                                            </div>
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-top pt-3">
                                                <span class="small text-muted"><i class="fa-solid fa-clock-rotate-left me-1"></i>{{ $dispositivo->sesiones_control_count }} controles</span>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.dispositivos.edit', $dispositivo) }}" title="Editar">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </a>
                                                    @if ($dispositivo->sesiones_control_count == 0)
                                                        <form method="POST" action="{{ route('admin.dispositivos.destroy', $dispositivo) }}"
                                                              onsubmit="return confirm('¿Eliminar este dispositivo? Esta acción no se puede deshacer.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    @else
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled
                                                                title="No se puede eliminar porque tiene controles asociados">
                                                            <i class="fa-solid fa-lock"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="card"><div class="card-body text-center py-5">
                                    <i class="fa-solid fa-kit-medical fa-3x text-muted mb-3"></i>
                                    <h5>No se encontraron dispositivos</h5>
                                    <p class="text-muted">Crea tu primer equipo o modifica los filtros de búsqueda.</p>
                                    <a href="{{ route('admin.dispositivos.create') }}" class="btn btn-primary">Agregar dispositivo</a>
                                </div></div>
                            </div>
                        @endforelse

                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $dispositivos->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const grid = document.getElementById('dispositivo-grid');
            const botonGrid = document.getElementById('boton-grid');
            const botonLista = document.getElementById('boton-lista');
            if (!grid || !botonGrid || !botonLista) return;

            const mostrar = (modo) => {
                const lista = modo === 'lista';
                grid.classList.toggle('is-list', lista);
                botonGrid.classList.toggle('btn-outline-secondary', lista);
                botonGrid.classList.toggle('btn-outline-primary', !lista);
                botonLista.classList.toggle('btn-outline-primary', lista);
                botonLista.classList.toggle('btn-outline-secondary', !lista);
                botonGrid.setAttribute('aria-pressed', String(!lista));
                botonLista.setAttribute('aria-pressed', String(lista));
            };
            botonGrid.addEventListener('click', () => mostrar('grid'));
            botonLista.addEventListener('click', () => mostrar('lista'));
        });
    </script>
@endsection
