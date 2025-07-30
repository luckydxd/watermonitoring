@extends('layouts.auth')

@section('title', 'Login')

@push('css')
@endpush
<style>
    /* public/css/custom-login.css */

    body {
        margin: 0;
        padding: 0;
        /* Ganti dengan font yang Anda inginkan */
        overflow: hidden;
        /* Penting untuk menghilangkan scrollbar jika gambar meluap */
    }

    html,
    body,
    .login-wrapper {
        height: 100%;
    }

    .login-wrapper {
        display: flex;
        align-items: stretch;
        /* Agar kolom mengisi tinggi penuh */
        min-height: 100vh;
        /* Pastikan mengambil seluruh tinggi viewport */
        background-color: #f8f8f8;
        /* Warna background fallback */
    }

    /* Container utama di dalam login-wrapper */
    .login-wrapper .container-fluid {
        padding: 0;
        /* Hapus padding default Bootstrap */
    }

    /* Row di dalam container-fluid */
    .login-wrapper .row {
        margin: 0;
        /* Hapus margin default row */
    }

    /* Left Side: Image Container */
    .login-image-container {
        background-color: #e0e0e0;
        /* Warna background jika gambar tidak dimuat */
        position: relative;
        /* Untuk gambar absolut di dalamnya */
        padding: 0;
        /* Hapus padding default col */
        height: 100vh;
        /* Agar kolom gambar mengambil tinggi penuh viewport */
        overflow: hidden;
        /* Penting untuk memotong gambar yang meluap */
    }

    .login-cover-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }

    .login-form-container {
        background-color: #ffffff;
        padding: 2rem;
        height: 100vh;
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.05);
    }

    .login-form-content {
        max-width: 400px;
        width: 100%;
        padding: 2rem;
        border-radius: 8px;

    }

    .input-group #togglePassword {
        border-top-right-radius: var(--bs-border-radius);
        border-bottom-right-radius: var(--bs-border-radius);
    }

    @media (max-width: 767.98px) {

        .login-image-container {
            display: none !important;
        }

        .login-form-container {
            width: 100%;
            padding: 1.5rem;
            box-shadow: none;
            height: auto;
            min-height: 100vh;
        }

        .login-form-content {
            padding: 1.5rem;
        }
    }


    .login-image-container {
        background-image: url('{{ asset('demo2/auth/bubbles.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
</style>

@section('content')
    <div class="login-wrapper">
        <div class="container-fluid h-100">
            <div class="row h-100 align-items-center">

                <div class="col-lg-7 col-md-6 d-none d-md-block login-image-container p-0">

                </div>

                <div class="col-lg-5 col-md-6 d-flex align-items-center justify-content-center login-form-container">
                    <div class="login-form-content">
                        {{-- <div class="text-center mb-4">
                            <img src="{{ asset('path/to/your/logo.png') }}" alt="App Logo" style="width: 80px;">
                        </div> --}}

                        <h4 class="mb-3 text-center">Selamat Datang!</h4>
                        <p class="mb-4 text-center">Masuk ke akun anda untuk melanjutkan.</p>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach ($errors->all() as $error)
                                    <p class="mb-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-exclamation-circle me-1" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                            <path
                                                d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                        </svg>
                                        {{ $error }}
                                    </p>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="ti ti-eye fs-5"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-end mb-3">
                                <a href="{{ route('password.request') }}" class="text-decoration-none">Lupa Password?</a>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">Masuk</button>
                            </div>
                        </form>
                        @if (!auth()->check() && !request()->is('admin/*') && !request()->is('teknisi/*'))
                            <p class="mt-3 text-center">
                                Belum Memiliki Akun? <a href="{{ route('register') }}" class="text-decoration-none">Daftar
                                    Sekarang</a>
                            </p>
                            <div class="mt-2 text-center">
                                <a href="{{ route('landing.index') }}" class="text-decoration-none">
                                    <i class="ti ti-caret-left me-1"></i> Kembali ke Halaman Utama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- toogle password -->
    <script src="{{ asset('../resources/js/app/auth.js') }}"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function(e) {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            // Toggle icon
            this.querySelector('i').classList.toggle('ti-eye');
            this.querySelector('i').classList.toggle('ti-eye-off');
        });
    </script>

@endsection
