@extends('layouts.app')

@section('title', 'Profil Saya')

@push('css')
    <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
    <style>
        .dropify-wrapper {
            display: block;
            margin: 0 auto;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            overflow: hidden;
            border: 1px solid transition: all .4s ease-in-out;
        }

        .dropify-wrapper .dropify-render img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .dropify-wrapper .dropify-message .dropify-font-upload {
            font-size: 50px;
        }
    </style>
@endpush

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
                                <div class="mb-4">
                                    <label for="profile_image" class="form-label">Foto Profil</label>
                                    <input type="file" id="profile_image" name="image" class="dropify"
                                        data-height="150"
                                        @if ($user->userData && $user->userData->image) data-default-file="{{ asset('storage/profile_images/' . basename($user->userData->image)) }}" @endif
                                        data-type="profile_image" data-allowed-file-extensions="jpg jpeg png gif" />
                                    <small class="text-muted font-xs mb-2">*max
                                        2MB</small>
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

                                <button type="submit" class="btn btn-primary me-2">Perbarui Profil</button>

                                @php
                                    $dashboardRoute = 'user.dashboard';
                                    if (auth()->user()->hasRole('admin')) {
                                        $dashboardRoute = 'admin.dashboard';
                                    } elseif (auth()->user()->hasRole('teknisi')) {
                                        $dashboardRoute = 'teknisi.dashboard';
                                    }
                                @endphp
                                @auth
                                    <a href="{{ route($dashboardRoute) }}" class="btn btn-outline-secondary">
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

    @push('scripts')
        <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
        <script src="{{ asset('dropify/dropify.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('.dropify').dropify();
                $('.dropify').on('dropify.afterClear', function(event, element) {
                    // Langsung kirim request AJAX untuk menghapus gambar profil
                    $.ajax({
                        url: '{{ route('profile.image.delete') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            console.log('Profile image deleted successfully.');
                            // Ganti alert() dengan Notiflix.Notify
                            Notiflix.Notify.success('Foto profil telah dihapus.');
                        },
                        error: function(xhr) {
                            console.error('Error deleting profile image:', xhr.responseText);
                            // Ganti alert() dengan Notiflix.Notify
                            Notiflix.Notify.failure('Gagal menghapus foto profil.');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
