@extends('layouts.simple.master')
@section('title', 'Nueva ficha personal')
@section('css') @include('admin.personas.partials.styles') @endsection
@section('main_content')
    @include('admin.personas.partials.breadcrumb', ['titulo' => 'Nueva ficha personal'])
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">Revisa los campos indicados antes de continuar.</div>
        @endif
        @if ($usuarios->isEmpty())
            <div class="alert alert-warning">No hay usuarios disponibles para asociar. Registra primero una cuenta en Usuarios.</div>
        @else
            @include('admin.personas.partials.form')
        @endif
    </div>
@endsection
@section('scripts') @include('admin.personas.partials.form-scripts') @endsection
