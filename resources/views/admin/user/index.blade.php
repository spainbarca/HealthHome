@extends('layouts.simple.master')

@section('title', 'Users Management')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
@endsection

@section('main_content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Users Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.default_dashboard') }}"> <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Users Management</li>
                        <li class="breadcrumb-item active">Users Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-xxl-2 col-md-4 box-col-4">
                <div class="card user-management">
                    <div class="card-body bg-primary">
                        <div class="blog-card p-0">
                            <div class="blog-card-content">
                                <div class="blog-tags">
                                    <div class="tags-icon">
                                        <svg class="stroke-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-task') }}"></use>
                                        </svg>
                                    </div>
                                    <div class="tag-details">
                                        <h2 class="total-num counter">{{ Spatie\Permission\Models\Role::where('system_reserve', false)->count() }}</h2>
                                        <p>Total Roles</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-md-8 box-col-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Total Users by Role</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="total-num counter">
                                    @php
                                        $roles = Spatie\Permission\Models\Role::where('system_reserve', false)->with('users')->latest()->take(7)->get();
                                    @endphp
                                    <div class="d-flex by-role custom-scrollbar">
                                        @foreach ($roles as $role)
                                            <div>
                                                <div class="total-user bg-light-primary">
                                                    <h5> {{ $role->name }} </h5>
                                                    <span class="total-num counter">{{ sprintf("%02d",$role->users->count()) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-2 col-sm-6 box-col-6">
               <div class="card user-role">
                    <div class="card-body">
                        <div class="btn-light1-primary b-r-15">
                            <div class="upcoming-box">
                                <div class="upcoming-icon bg-primary">
                                    <i class="fa-slab fa-regular fa-users fa-flip-horizontal fa-2xl"></i>
                                </div>
                                <p>User</p>
                                <a href="{{ route('admin.user.create') }}" class="btn btn-primary">{{ __('Add User') }}</a>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
            <div class="col-xxl-2 col-sm-6 box-col-6 tag-card">
                <div class="card user-role">
                    <div class="card-body">
                        <div class="btn-light1-secondary b-r-15">
                            <div class="upcoming-box">
                                <div class="upcoming-icon bg-secondary">
                                    <svg class="stroke-icon">
                                        <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                                    </svg>
                                </div>
                                <p>Role</p>
                                <a href="{{ route('admin.role.create') }}" class="btn btn-secondary">{{ __('Add Role') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar">
                            {!! $dataTable->table() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Prueba Health Icons</h5>
            </div>

            <div class="card-body">

                <div class="d-flex align-items-center gap-4">

                    <div class="text-center">
                        <i class="healthicons-blood-bag fs-1 text-danger"></i>
                        <p class="mt-2">Blood Bag</p>
                    </div>

                    <div class="text-center">
                        <i class="healthicons-dialysis fs-1 text-primary"></i>
                        <p class="mt-2">Dialysis</p>
                    </div>

                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Prueba Remix Icon</h5>
            </div>

            <div class="card-body">
                <div class="d-flex gap-4 align-items-center">

                    <i class="ri-user-line fs-1 text-primary"></i>

                    <i class="ri-heart-pulse-line fs-1 text-danger"></i>

                    <i class="ri-file-list-3-line fs-1 text-success"></i>

                    <i class="ri-settings-3-line fs-1 text-warning"></i>

                    <i class="ri-save-3-line fs-1 text-info"></i>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Phosphor Icons</h5>
            </div>

            <div class="card-body">

                <div class="d-flex align-items-center gap-4">

                    <i class="ph ph-user fs-1 text-primary"></i>

                    <i class="ph-light ph-heart fs-1 text-danger"></i>

                    <i class="ph-bold ph-house fs-1 text-success"></i>

                    <i class="ph-fill ph-gear fs-1 text-warning"></i>

                    <i class="ph-duotone ph-stethoscope fs-1 text-info"></i>

                    <i class="ph-thin ph-calendar fs-1"></i>

                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Streamline Icons</h5>
            </div>

            <div class="card-body">

                <div class="d-flex align-items-center gap-4">

                    <div class="text-center">
                        <x-streamline-icon
                            name="sharp/line/interface-essential/new-file"
                            size="32"
                            class="text-primary monochrome"
                        />

                        <p class="mt-2">32 px</p>
                    </div>

                    <div class="text-center">
                        <x-streamline-icon
                            name="memes/memes-hand-drawn/nyan-cat-hand-drawn"
                            size="128"
                            class="text-danger monochrome"
                        />

                        <p class="mt-2">128 px</p>
                    </div>

                    <div class="text-center">
                        <x-streamline-icon
                            name="core/gradient/mail/chat-bubble-square-question"
                            size="64"
                            class="text-success monochrome"
                        />

                        <p class="mt-2">64 px</p>
                    </div>

                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Prueba de Iconify</h5>
            </div>

            <div class="card-body">
                <div class="d-flex align-items-center gap-4">

                    <iconify-icon
                        icon="mdi:heart-pulse"
                        width="40"
                        class="text-danger">
                    </iconify-icon>

                    <iconify-icon
                        icon="ph:heartbeat"
                        width="40"
                        class="text-primary">
                    </iconify-icon>

                    <iconify-icon
                        icon="lucide:activity"
                        width="40"
                        class="text-success">
                    </iconify-icon>

                    <iconify-icon
                        icon="material-symbols:ecg-heart"
                        width="40"
                        class="text-warning">
                    </iconify-icon>

                    <iconify-icon
    icon="noto:broccoli"
    width="48"
    height="48">
</iconify-icon>
<iconify-icon icon="logos:broccoli"></iconify-icon>
<iconify-icon
    icon="logos:broccoli"
    width="48">
</iconify-icon>
<iconify-icon icon="streamline-ultimate-color:binocular" width="48"></iconify-icon>
<iconify-icon icon="zondicons:battery-full" width="48"></iconify-icon>

<div class="d-flex gap-4 align-items-center">

    <i class="uil uil-heart fs-1 text-danger"></i>

    <i class="uil uil-comments fs-1 text-primary"></i>

</div>

                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
@endsection

@section('scripts')
    <!-- calendar js-->
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    {!! $dataTable->scripts() !!}
@endsection
