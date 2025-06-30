@extends('layouts.auth')

@section('title', 'Reset Password') {{-- Judul halaman untuk Reset Password --}}

<style>
    /* CSS yang Anda berikan, disematkan langsung di Blade */
    /* Pastikan ini adalah salinan persis dari CSS yang ada di halaman login/register Anda */
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        overflow: hidden;
    }

    html,
    body,
    .login-wrapper {
        height: 100%;
    }

    .login-wrapper {
        display: flex;
        align-items: stretch;
        min-height: 100vh;
        background-color: #f8f8f8;
    }

    .login-wrapper .container-fluid {
        padding: 0;
    }

    .login-wrapper .row {
        margin: 0;
    }

    .login-image-container {
        background-color: #e0e0e0;
        position: relative;
        padding: 0;
        height: 100vh;
        overflow: hidden;

        /* Ini adalah gaya background-image yang Anda inginkan */
        background-image: url('{{ asset('demo2/auth/key.png') }}');
        /* Gunakan gambar cover yang sama */
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .login-form-container {
        background-color: #ffffff;
        padding: 2rem;
        height: 100vh;
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.05);

        /* Tambahkan ini agar konten form terpusat secara vertikal dan horizontal */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .login-form-content {
        max-width: 400px;
        width: 100%;
        padding: 2rem;
        border-radius: 8px;
    }

    /* Penyesuaian untuk Input Group Toggle Password */
    .input-group #togglePassword,
    .input-group #toggleConfirmPassword {
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
</style>

@section('content')
    <div class="login-wrapper">
        <div class="container-fluid h-100">
            <div class="row h-100 align-items-center">

                <div class="col-lg-7 col-md-6 d-none d-md-block login-image-container p-0">
                    {{-- Gambar akan dimuat via background-image di CSS --}}
                </div>

                <div class="col-lg-5 col-md-6 d-flex align-items-center justify-content-center login-form-container">
                    <div class="login-form-content">
                        {{-- Logo (jika ada) --}}
                        {{-- <div class="text-center mb-4">
                            <img src="{{ asset('path/to/your/logo.png') }}" alt="App Logo" style="width: 80px;">
                        </div> --}}

                        <h4 class="mb-3 text-center">Reset Password Anda</h4>
                        <p class="mb-4 text-center">Untuk keamanan Anda, kami sarankan menggunakan kata sandi yang kuat.</p>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
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

                        <form id="formAuthentication" class="mb-4" action="{{ route('password.update') }}" method="POST">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ $email ?? old('email') }}" required
                                    autocomplete="email" autofocus readonly>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        required autocomplete="new-password" />
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="ti ti-eye fs-5"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        required autocomplete="new-password" />
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="ti ti-eye fs-5"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button class="btn btn-primary" type="submit">Set Password Baru</button>
                            </div>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="{{ route('login.user') }}" class="text-decoration-none">
                                <i class="ti ti-caret-left me-1"></i> Kembali ke Halaman Masuk
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        // Toggle Password
        document.getElementById('togglePassword').addEventListener('click', function(e) {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('ti-eye');
            this.querySelector('i').classList.toggle('ti-eye-off');
        });

        // Toggle Confirm Password
        document.getElementById('toggleConfirmPassword').addEventListener('click', function(e) {
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('ti-eye');
            this.querySelector('i').classList.toggle('ti-eye-off');
        });
    </script>
@endsection
