@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-8 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    {{-- <img src="demo2/assets/img/elvektor.png" alt="auth-login-cover" class="auth-illustration my-5"
                        data-app-light-img="illustrations/auth-login-illustration-light.png"
                        data-app-dark-img="illustrations/auth-login-illustration-dark.png" /> --}}

                    <img src="demo2/assets/img/illustrations/bg-shape-image-light.png" alt="auth-login-cover"
                        class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png"
                        data-app-dark-img="illustrations/bg-shape-image-dark.png" />
                </div>
            </div>
            <!-- /Left Text -->
            <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
                <div class="w-px-400 mx-auto mt-12 pt-5">
                    <h4 class="mb-1">Lupa Password Anda? </h4>
                    <p class="mb-6">Masukkan email dan kami akan mengirimkan instruksi untuk mereset password.
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="formAuthentication" class="mb-4" action="{{ route('password.email') }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" placeholder="Masukkan email Anda" autofocus value="{{ old('email') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button class="btn btn-dark d-grid w-100" type="submit">Kirim Link Reset</button>
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

    @endsection
