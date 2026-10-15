<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-sm-6"><h3>{{ $titulo }}</h3></div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.default_dashboard') }}">
                            <svg class="stroke-icon"><use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use></svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.controles.index') }}">Controles médicos</a></li>
                    <li class="breadcrumb-item active">{{ $titulo }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
