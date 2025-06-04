@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Profil Saya</h5>
                            <span class="badge bg-label-primary">
                                {{ ucfirst(auth()->user()->getRoleNames()->first()) }}
                            </span>
                        </div>
                        <div class="card-body">
                            @php
                                $user = auth()->user()->load('userData');
                            @endphp

                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Profile Image -->
                                <div class="mb-4 text-center">
                                    @if ($user->userData && $user->userData->image)
                                        <img src="{{ asset('storage/profile_images/' . basename($user->userData->image)) }}"
                                            class="rounded-circle mb-3" width="120" height="120" alt="Profile Image">
                                    @else
                                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 120px; height: 120px;">
                                            <i class="ti ti-user text-white" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control d-none" id="profile_image" name="image">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="document.getElementById('profile_image').click()">
                                        Ganti Foto
                                    </button>
                                </div>

                                <!-- Full Name -->
                                <div class="mb-4">
                                    <label class="form-label" for="name">Nama Lengkap</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-user"></i></span>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="John Doe"
                                            value="{{ old('name', $user->userData->name ?? '') }}" required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="mb-4">
                                    <label class="form-label" for="email">Email</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" placeholder="john.doe@example.com"
                                            value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone Number -->
                                <div class="mb-4">
                                    <label class="form-label" for="phone_number">Nomor Telepon</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                        <input type="text"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            id="phone_number" name="phone_number" placeholder="081234567890"
                                            value="{{ old('phone_number', $user->userData->phone_number ?? '') }}">
                                    </div>
                                    @error('phone_number')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="mb-4">
                                    <label class="form-label" for="address">Alamat</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-home"></i></span>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2"
                                            placeholder="Your full address" required>{{ old('address', $user->userData->address ?? '') }}</textarea>
                                    </div>
                                    @error('address')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Perbarui Profil</button>

                                @php
                                    $dashboardRoute = 'user.dashboard';
                                    if (auth()->user()->hasRole('admin')) {
                                        $dashboardRoute = 'admin.dashboard';
                                    } elseif (auth()->user()->hasRole('teknisi')) {
                                        $dashboardRoute = 'teknisi.dashboard';
                                    }
                                @endphp
                                @auth
                                    <a href="{{ route($dashboardRoute) }}" class="btn btn-secondary">
                                        Kembali
                                    </a>
                                @endauth
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
