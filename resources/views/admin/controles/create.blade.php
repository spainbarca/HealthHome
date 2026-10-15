@extends('layouts.simple.master')

@section('title', 'Registrar control médico')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jsgrid.css') }}">
    @include('admin.controles.partials.styles')
@endsection

@section('main_content')
    @include('admin.controles.partials.breadcrumb', ['titulo' => 'Registrar control médico'])
    <div class="container-fluid">@include('admin.controles.partials.form')</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/jsgrid/jsgrid.min.js') }}"></script>
    @include('admin.controles.partials.form-scripts')
@endsection
