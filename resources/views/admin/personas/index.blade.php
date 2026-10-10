@extends('layouts.simple.master')

@section('title', 'Personas - HealthHome')

@section('css')
    @include('admin.personas.partials.styles')
@endsection

@section('main_content')
    @include('admin.personas.partials.breadcrumb', ['titulo' => 'Personas'])
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button></div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h4 class="mb-1">Fichas personales</h4>
                <p class="text-muted mb-0">Cada ficha vincula una persona con una cuenta de usuario existente.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.personas.create') }}"><i class="fa-solid fa-user-plus me-2"></i>Nueva persona</a>
        </div>

        <div class="card hh-persona-filter mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.personas.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-xl-4 col-lg-6">
                            <label class="form-label" for="buscar-persona">Buscar</label>
                            <input id="buscar-persona" name="q" class="form-control" type="search"
                                   value="{{ request('q', '') }}" placeholder="Nombre, correo, documento...">
                        </div>
                        <div class="col-xl-2 col-lg-3 col-sm-6">
                            <label class="form-label" for="f-parentesco">Parentesco</label>
                            <select id="f-parentesco" name="parentesco" class="form-select">
                                <option value="">Todos</option>
                                @foreach ($parentescos as $relacion)
                                    <option value="{{ $relacion }}" @selected(request('parentesco') === $relacion)>{{ $relacion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-sm-6">
                            <label class="form-label" for="f-controles">Controles</label>
                            <select id="f-controles" name="controles" class="form-select">
                                <option value="" @selected(!request('controles'))>Todos</option>
                                <option value="con" @selected(request('controles') === 'con')>Con controles</option>
                                <option value="sin" @selected(request('controles') === 'sin')>Sin controles</option>
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-6 col-sm-6">
                            <label class="form-label" for="f-orden">Orden</label>
                            <select id="f-orden" name="orden" class="form-select">
                                <option value="recientes" @selected(request('orden','recientes') === 'recientes')>Más recientes</option>
                                <option value="nombre" @selected(request('orden') === 'nombre')>Nombre</option>
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-6 col-sm-6">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill"><i class="fa-solid fa-magnifying-glass me-1"></i>Buscar</button>
                                <a href="{{ route('admin.personas.index') }}" class="btn btn-outline-secondary flex-fill">Limpiar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-3 text-muted small">
            Mostrando {{ $personas->firstItem() ?? 0 }}–{{ $personas->lastItem() ?? 0 }} de {{ $personas->total() }} personas
        </div>

        <div class="row g-3">
            @forelse ($personas as $persona)
                <div class="col-xxl-3 col-xl-4 col-md-6">
                    <div class="card hh-persona-card mb-0">
                        <div class="card-body">
                            <div class="d-flex gap-3 align-items-center mb-3">
                                @include('admin.personas.partials.avatar', ['user' => $persona->user, 'size' => ''])
                                <div class="min-w-0 flex-grow-1">
                                    <a class="text-decoration-none" href="{{ route('admin.personas.show', $persona) }}">
                                        <h5 class="hh-persona-name mb-1">{{ $persona->nombre_completo ?: 'Usuario no disponible' }}</h5>
                                    </a>
                                    <span class="badge bg-light-primary text-primary">{{ $persona->parentesco ?: 'Sin parentesco' }}</span>
                                </div>
                            </div>
                            <div class="person-detail mb-2"><i class="fa-regular fa-id-card me-2"></i>{{ $persona->tipo_documento ?: 'Sin documento' }} {{ $persona->numero_documento ?: '' }}</div>
                            <div class="person-detail mb-3 text-break"><i class="fa-regular fa-envelope me-2"></i>{{ $persona->user?->email ?: 'Cuenta no disponible' }}</div>
                            <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-auto">
                                <div class="small"><i class="fa-solid fa-heart-pulse text-primary me-1"></i><strong>{{ $persona->sesiones_control_count }}</strong> controles</div>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.personas.show', $persona) }}" title="Ver ficha"><i class="fa-solid fa-eye"></i></a>
                                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.personas.edit', $persona) }}" title="Editar ficha"><i class="fa-solid fa-pen"></i></a>
                                    @if ($persona->sesiones_control_count == 0)
                                        <form method="POST" action="{{ route('admin.personas.destroy', $persona) }}" onsubmit="return confirm('¿Eliminar solamente esta ficha? El usuario permanecerá registrado.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar ficha"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Tiene controles médicos registrados"><i class="fa-solid fa-lock"></i></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card"><div class="card-body text-center py-5">
                        <i class="fa-solid fa-users fa-3x text-muted mb-3"></i>
                        <h5>No se encontraron fichas</h5>
                        <p class="text-muted">Puedes agregar una persona o ajustar los filtros.</p>
                        <a href="{{ route('admin.personas.create') }}" class="btn btn-primary">Crear ficha personal</a>
                    </div></div>
                </div>
            @endforelse
        </div>
        <div class="d-flex justify-content-center mt-4">{{ $personas->links('pagination::bootstrap-5') }}</div>
    </div>
@endsection
