@extends('layouts.simple.master')

@section('title', 'Agregar dispositivo')

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6"><h3>Agregar dispositivo</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.default_dashboard') }}">
                            <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg>
                        </a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.dispositivos.index') }}">Dispositivos</a></li>
                        <li class="breadcrumb-item active">Agregar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">Revisa los campos resaltados antes de guardar.</div>
        @endif
        @include('admin.dispositivos.partials.form')
    </div>
@endsection

@section('scripts')
    @include('admin.dispositivos.partials.form-scripts')
@endsection
