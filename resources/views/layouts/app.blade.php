<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('demo2/assets/') }}" data-api-path="{{ url('/api/') }}"
    data-template="vertical-menu-template" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('title', 'Dashboard')</title>

    @if (isset($appSetting) && $appSetting->logo)
        <link rel="icon" href="{{ asset('storage/settings/' . $appSetting->logo) }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('storage/settings/' . $appSetting->logo) }}">

        <link rel="apple-touch-icon" href="{{ asset('storage/settings/' . $appSetting->logo) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @endif
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Libre+Franklin:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    {{-- <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" /> --}}

    {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Libre+Franklin:ital,wght@0,100..900;1,100..900&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/flag-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/layout.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/css/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('notiflix/notiflix-3.2.8.min.css') }}" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="{{ asset('demo2/assets/js/helpers.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/config.js') }}"></script>
    @stack('css')

    {{-- <script src="{{ asset('demo2/assets/js/template-customizer.js') }}"></script> --}}
</head>

<body class="layout-menu-fixed">
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Content -->
            @include('layouts.partials.sidebar')
            <div class="layout-page">
                @include('layouts.partials.navbar')
                <div class="content-wrapper">
                    {{-- loaders from uiball --}}
                    @include('components.loader')

                    {{-- end of loaders --}}
                    @yield('content')
                    {{-- @include('partials.footer') --}}

                    <div class="content-backdrop fade"></div>
                </div>

                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->

        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{ asset('demo2/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('demo2/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('demo2/assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/hammer.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('demo2/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>


    <!-- Main JS -->
    <script src="{{ asset('demo2/assets/js/menu.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/main.js') }}"></script>

    {{-- loaders JS --}}
    <script>
        function showLoader() {
            document.getElementById('loading-container').style.display = 'flex';
        }

        function hideLoader() {
            document.getElementById('loading-container').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            hideLoader();

            // Handle AJAX requests
            $(document).ajaxStart(function() {
                showLoader();
            }).ajaxStop(function() {
                hideLoader();
            });
        });

        window.showLoader = showLoader;
        window.hideLoader = hideLoader;
    </script>

    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            authToken: '{{ Auth::check() ? 'Bearer ' . session('api_token') : '' }}'
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': window.Laravel.csrfToken,
                'Authorization': window.Laravel.authToken
            }
        });
    </script>

    <!-- Page JS -->
    <script src="{{ asset('notiflix/notiflix-3.2.8.min.js') }}"></script>
    <script src="{{ asset('demo2/assets/js/app-navbar-notifications.js') }}"></script>
    @stack('scripts')


</body>

</html>
