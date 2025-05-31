@extends('layouts.auth')

@section('title', 'Register Pengguna')

@section('content')
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">
            <!-- Left Text -->
            <div class="d-none d-lg-flex col-lg-8 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="demo2/assets/img/illustrations/bg-shape-image-light.png" alt="auth-register-cover"
                        class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png"
                        data-app-dark-img="illustrations/bg-shape-image-dark.png" />
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Register -->
            <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-sm-12 p-6">
                <div class="w-px-400 mx-auto mt-12 pt-5">
                    <h4 class="mb-1">Buat Akun Baru</h4>
                    <p class="mb-6">Isi formulir untuk mendaftar</p>

                    <form id="formRegistration" class="mb-4" action="{{ route('register') }}" method="POST">
                        @csrf

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Enter your username" required autofocus>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter your email" required>
                        </div>

                        <!-- Password -->
                        <div class="form-password-toggle mb-3">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    required minlength="8" />
                                <span class="input-group-text cursor-pointer" id="toggle-password">
                                    <i class="ti ti-eye-off"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-password-toggle mb-3">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control"
                                    name="password_confirmation"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    required minlength="8" />
                                <span class="input-group-text cursor-pointer" id="toggle-confirm-password">
                                    <i class="ti ti-eye-off"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter your full name" required>
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter your address" required></textarea>
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number"
                                placeholder="Enter your phone number" required>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach ($errors->all() as $error)
                                    <p class="mb-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                            <path
                                                d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                        </svg>
                                        {{ $error }}
                                    </p>
                                @endforeach
                            </div>
                        @endif

                        <div class="mb-3">
                            <button class="btn btn-dark d-grid w-100" type="submit">Register</button>
                        </div>
                    </form>

                    <p class="text-center">
                        <span>Already have an account?</span>
                        <a href="{{ route('login-user') }}">
                            <span>Sign in instead</span>
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>

    <!-- Toggle Password Script -->
    <script>
        document.getElementById("toggle-password").addEventListener("click", function() {
            const passwordField = document.getElementById("password");
            const icon = this.querySelector("i");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("ti-eye-off");
                icon.classList.add("ti-eye");
            } else {
                passwordField.type = "password";
                icon.classList.remove("ti-eye");
                icon.classList.add("ti-eye-off");
            }
        });

        document.getElementById("toggle-confirm-password").addEventListener("click", function() {
            const passwordField = document.getElementById("password_confirmation");
            const icon = this.querySelector("i");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("ti-eye-off");
                icon.classList.add("ti-eye");
            } else {
                passwordField.type = "password";
                icon.classList.remove("ti-eye");
                icon.classList.add("ti-eye-off");
            }
        });
    </script>
@endsection
