<!doctype html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('demo2/assets/') }}/" data-template="vertical-menu-template" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login Pengguna</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('demo2/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    {{-- <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" /> --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Libre+Franklin:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/fontawesome.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/layout.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/theme.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/demo.css') }}" />

    <!--s CSS -->
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/typehead.css') }}" />

    <!-- -->
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/form-validation.css') }}" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('demo2/assets/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset('demo2/assets/js/template-customizer.js') }}"></script>

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('demo2/assets/js/config.js') }}"></script>

    @stack('css')
</head>

<body>

    @yield('content')
    <!-- Core JS -->
    <!-- build:js assets/js/core.js -->

    <script src="{{ asset('demo2/assets/js/jquery.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/popper.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/node-waves.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/hammer.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/i18n.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/typeahead.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!--s JS -->
    <script src="{{ asset('demo2/assets/js/popular.js') }}"></script>
    <script src="{{ asset('demo2/assets/vendor/js/bootstrap.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('demo2/assets/js/auto-focus.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('demo2/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('demo2/assets/js/pages-auth.js') }}"></script>
</body>

</html>
