@extends('layouts.auth')

@section('title', 'Register Pengguna')

@push('css')
@endpush

<style>
    body {
        margin: 0;
        padding: 0;
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

        background-image: url('{{ asset('demo2/auth/drop.png') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .login-form-container {
        background-color: #ffffff;
        /* Padding di sekitar form, bukan di konten scrollable */
        padding: 2rem;
        height: 100vh;
        /* Tinggi penuh agar bisa di-scroll */
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.05);

        /* Flexbox untuk konten utama (di dalamnya ada scroll-content) */
        display: flex;
        /* <-- Tambahkan ini */
        flex-direction: column;
        /* <-- Tambahkan ini */
        align-items: center;
        /* <-- Tambahkan ini, untuk center login-form-content jika tingginya kurang */
        justify-content: center;
        /* <-- Tambahkan ini, untuk center login-form-content jika tingginya kurang */
    }

    /* Ini adalah container yang akan memiliki scrollbar */
    .scrollable-form-content {
        max-height: 100vh;
        /* Max tinggi sama dengan viewport */
        overflow-y: auto;
        /* Aktifkan scroll vertikal jika konten meluap */
        padding-right: 15px;
        /* Beri padding untuk scrollbar */
        box-sizing: border-box;
        /* Pastikan padding dihitung dalam lebar */
        width: 100%;
        /* Pastikan mengambil lebar penuh */
        /* Anda mungkin perlu menyesuaikan max-width sesuai login-form-content jika login-form-content adalah pembungkusnya */
        max-width: 400px;
        /* Batasi lebar agar konsisten dengan login-form-content */
    }

    .login-form-content {
        /* max-width: 400px; <-- Ini sekarang pindah ke .scrollable-form-content */
        width: 100%;
        padding: 2rem;
        border-radius: 8px;
        /* Hilangkan overflow: auto jika ini bukan elemen scrollable */
        /* overflow-y: auto; */
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
            /* Biarkan tinggi menyesuaikan konten di mobile */
            min-height: 100vh;
        }

        .scrollable-form-content {
            max-height: unset;
            /* Di mobile, biarkan konten scroll seperti biasa tanpa batasan tinggi viewport */
            overflow-y: visible;
            /* Biarkan scroll browser default */
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
                    {{-- Konten form dibungkus dalam div scrollable --}}
                    <div class="scrollable-form-content">
                        <div class="login-form-content">
                            <h4 class="mb-3 text-center">Buat Akun Baru</h4>
                            <p class="mb-4 text-center">Isi formulir untuk mendaftar</p>

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <p class="mb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-exclamation-circle me-1"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
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

                            <form id="formRegistration" class="mb-4" action="{{ route('register.submit') }}"
                                method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Masukkan nama lengkap" required value="{{ old('name') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Masukkan email" required value="{{ old('email') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="password">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            required minlength="6" />
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="ti ti-eye fs-5"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            required minlength="6" />
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                            <i class="ti ti-eye fs-5"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" placeholder="Masukkan alamat" required
                                        style="resize: none;">{{ old('address') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                                        placeholder="Masukkan nomor telepon" required value="{{ old('phone_number') }}">
                                </div>

                                <div class="d-grid mb-3">
                                    <button class="btn btn-dark" type="submit">Daftar</button>
                                </div>
                            </form>

                            <p class="mt-3 text-center">
                                <span>Sudah Memiliki Akun?</span>
                                <a href="{{ route('login.user') }}">
                                    <span>Masuk</span>
                                </a>
                            </p>
                            <div class="mt-2 text-center">
                                <a href="{{ route('landing.index') }}" class="text-decoration-none">
                                    <i class="ti ti-caret-left me-1"></i> Kembali ke Halaman Utama
                                </a>
                            </div>
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

        // Inisialisasi Perfect Scrollbar
        // Pastikan PerfectScrollbar sudah dimuat (misal dari layouts/auth.blade.php)
        document.addEventListener('DOMContentLoaded', function() {
            const scrollableElement = document.querySelector('.scrollable-form-content');
            if (scrollableElement && typeof PerfectScrollbar !== 'undefined') {
                new PerfectScrollbar(scrollableElement, {
                    wheelPropagation: false // Mencegah scroll event propagasi ke elemen parent
                });
            }
        });
    </script>
@endsection
