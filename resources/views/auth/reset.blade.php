@extends('layouts.auth') {{-- Pastikan layout ini ada dan menyediakan struktur dasar HTML --}}

@section('title', 'Reset Password') {{-- Ubah judul halaman --}}

@section('content')
    <div class="authentication-wrapper authentication-cover">
        {{-- Jika Anda ingin menampilkan logo, Anda bisa meng-uncomment bagian ini dan menyesuaikan path gambar/SVG --}}
        {{-- <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
            <span class="app-brand-logo demo">
                </span>
            <span class="app-brand-text demo text-heading fw-bold">FloWater</span>
        </a> --}}
        <div class="authentication-inner row m-0">
            <div class="d-none d-lg-flex col-lg-8 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    {{-- Pastikan path gambar benar --}}
                    <img src="{{ asset('demo2/assets/img/illustrations/bg-shape-image-light.png') }}"
                        alt="auth-reset-password-cover" class="platform-bg"
                        data-app-light-img="illustrations/bg-shape-image-light.png"
                        data-app-dark-img="illustrations/bg-shape-image-dark.png" />
                    {{-- Gambar ilustrasi utama Anda, jika ada --}}
                    {{-- <img src="{{ asset('demo2/assets/img/illustrations/auth-reset-password-illustration-light.png') }}"
                        alt="auth-reset-password-illustration" class="auth-illustration my-5"
                        data-app-light-img="illustrations/auth-reset-password-illustration-light.png"
                        data-app-dark-img="illustrations/auth-reset-password-illustration-dark.png" /> --}}
                </div>
            </div>
            <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
                <div class="w-px-400 mx-auto mt-12 pt-5">
                    <h4 class="mb-1">Reset Password </h4>
                    <p class="mb-6">Untuk keamanan Anda, kami sarankan menggunakan kata sandi yang kuat.</p>

                    {{-- Status Session untuk Notifikasi Sukses/Gagal --}}
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="formAuthentication" class="mb-4" action="{{ route('password.update') }}" method="POST">
                        @csrf {{-- CSRF token untuk keamanan --}}

                        {{-- Hidden input untuk token reset password --}}
                        <input type="hidden" name="token" value="{{ $token }}">

                        {{-- Input Email (biasanya sudah ada dari link reset, jadi bisa readonly) --}}
                        <div class="mb-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                                readonly> {{-- Tambahkan 'readonly' --}}
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Input Password Baru --}}
                        <div class="form-password-toggle mb-6">
                            <label class="form-label" for="password">Password Baru</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" required autocomplete="new-password" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Input Konfirmasi Password Baru --}}
                        <div class="form-password-toggle mb-6">
                            <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control"
                                    name="password_confirmation"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    required autocomplete="new-password" aria-describedby="password_confirmation" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>

                        {{-- Tombol Submit --}}
                        <button class="btn btn-dark d-grid w-100 mb-6" type="submit">Set Password Baru</button>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('login.user') }}" class="d-flex align-items-center justify-content-center">
                            <i class="ti ti-caret-left mx-2"></i>
                            Kembali ke Halaman Masuk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('../resources/js/app/auth.js') }}"></script>

@endsection
