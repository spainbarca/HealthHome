@extends('layouts.simple.master')

@section('title', 'Create User')

@section('css')

    {!! $form->getIncludes('css') !!}

    <style>
        .user-form-header {
            padding: 1.5rem;
            border-radius: 12px;
            background: rgba(var(--bs-primary-rgb), .06);
            border: 1px solid rgba(var(--bs-primary-rgb), .10);
        }

        .user-form-icon,
        .section-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 10px;
            background: rgba(var(--bs-primary-rgb), .10);
            color: var(--bs-primary);
        }

        .user-form-icon {
            width: 52px;
            height: 52px;
            font-size: 22px;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .user-form-section {
            border-radius: 12px;
            overflow: hidden;
        }

        .user-form-section .card-header {
            padding: 1rem 1.25rem;
        }

        .user-form-section .card-body {
            padding: 1.5rem;
        }

        #userForm .form-label {
            font-weight: 500;
        }

        #userForm .input-group-text {
            min-width: 44px;
            justify-content: center;
        }

        #userForm .form-control,
        #userForm .form-select {
            min-height: 42px;
        }
    </style>

@endsection


@section('main_content')

    {{-- ========================================================= --}}
    {{-- PAGE TITLE --}}
    {{-- ========================================================= --}}

    <div class="container-fluid">

        <div class="page-title">

            <div class="row">

                <div class="col-sm-6">

                    <h3>
                        Users Management
                    </h3>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb">

                        <li class="breadcrumb-item">

                            <a href="{{ route('admin.default_dashboard') }}">

                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg>

                            </a>

                        </li>

                        <li class="breadcrumb-item">
                            Users
                        </li>

                        <li class="breadcrumb-item active">
                            Create
                        </li>

                    </ol>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}

    <div class="container-fluid">

        <div class="row">

            <div class="col-12 col-xxl-10 mx-auto">

                {!! $form->getCode() !!}

            </div>

        </div>

    </div>

@endsection


@section('scripts')

    {!! $form->getIncludes('js') !!}

    {!! $form->getJsCode() !!}

@endsection
