    <!-- Font Awesome-->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/brands.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/chisel-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/duotone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/duotone-light.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/duotone-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/duotone-thin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/etch-solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/graphite-thin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/jelly-duo-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/jelly-fill-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/jelly-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/light.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/mosaic-solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/notdog-duo-solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/notdog-solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/pixel-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/sharp-duotone-light.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/sharp-duotone-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/sharp-duotone-solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/sharp-duotone-thin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/sharp-light.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/sharp-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/sharp-solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/sharp-thin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/slab-duo-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/slab-press-duo-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/slab-press-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/slab-regular.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/thin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/thumbprint-light.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/utility-duo-semibold.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/utility-fill-semibold.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/utility-semibold.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/vellum-solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-pro-7.2.0/css/whiteboard-semibold.min.css') }}">

    {{-- ====================================================== --}}
    {{-- Google Material Symbols --}}
    {{-- ====================================================== --}}

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded&display=block" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=block" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp&display=block" rel="stylesheet">

    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flag-icon.css') }}">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}">
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/slick.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/slick-theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/scrollbar.css') }}">
    <!-- Plugins css Ends-->
    @yield('css')
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <!-- App css-->
    @vite(['public/assets/scss/style.scss'])
    <link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/toastr.min.css')}}">

