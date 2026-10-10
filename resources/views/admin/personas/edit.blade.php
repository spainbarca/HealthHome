@extends('layouts.simple.master')
@section('title', 'Editar ficha personal')
@section('css') @include('admin.personas.partials.styles') @endsection
@section('main_content')
    @include('admin.personas.partials.breadcrumb', ['titulo' => 'Editar ficha personal'])
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger">Revisa los campos indicados antes de continuar.</div>
        @endif
        @include('admin.personas.partials.form')
    </div>
@endsection
@section('scripts') @include('admin.personas.partials.form-scripts') @endsection
