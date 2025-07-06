@extends('layouts.app')

@section('title', 'Pengaturan Web')

@push('css')
    <link rel="stylesheet" href="{{ asset('summer-note/summernote-bs4.css') }}">
    <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
@endpush

@section('content')

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Role cards -->
            <div class="row g-6">
                @foreach ($roles as $role)
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-normal text-body mb-0">Total {{ $role->users_count }} pengguna</h6>
                                    <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">

                                        {{-- AWAL PERBAIKAN AVATAR --}}
                                        @foreach ($role->users->take(5) as $user)
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                                title="{{ optional($user->userData)->name ?? $user->name }}"
                                                class="avatar avatar-sm pull-up">

                                                @if ($user->userData && $user->userData->image)
                                                    {{-- JIKA USER PUNYA GAMBAR --}}
                                                    <img class="rounded-circle"
                                                        src="{{ asset('storage/' . $user->userData->image) }}"
                                                        alt="Avatar">
                                                @else
                                                    {{-- JIKA USER TIDAK PUNYA GAMBAR, BUAT INISIAL --}}
                                                    @php
                                                        $name = optional($user->userData)->name ?? $user->name;
                                                        $state = [
                                                            'success',
                                                            'danger',
                                                            'warning',
                                                            'info',
                                                            'primary',
                                                            'secondary',
                                                        ][
                                                            array_rand([
                                                                'success',
                                                                'danger',
                                                                'warning',
                                                                'info',
                                                                'primary',
                                                                'secondary',
                                                            ])
                                                        ];
                                                        $words = explode(' ', trim($name));
                                                        $initials = '';
                                                        if (isset($words[0]) && !empty($words[0])) {
                                                            $initials .= strtoupper(substr($words[0], 0, 1));
                                                        }
                                                        if (count($words) > 1) {
                                                            $initials .= strtoupper(substr(end($words), 0, 1));
                                                        }
                                                        if (empty($initials)) {
                                                            $initials = 'NN';
                                                        }
                                                    @endphp
                                                    <span
                                                        class="avatar-initial rounded-circle bg-label-{{ $state }}">{{ $initials }}</span>
                                                @endif

                                            </li>
                                        @endforeach
                                        {{-- AKHIR PERBAIKAN AVATAR --}}

                                        @if ($role->users_count > 5)
                                            <li class="avatar avatar-sm pull-up">
                                                <span
                                                    class="avatar-initial rounded-circle bg-label-primary">+{{ $role->users_count - 5 }}</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                <div class="d-flex justify-content-between align-items-end">
                                    <div class="role-heading">
                                        <h5 class="mb-1">{{ ucfirst($role->name) }}</h5>
                                        <a href="javascript:;" class="role-edit-modal" data-bs-toggle="modal"
                                            data-bs-target="#roleEditModal" data-role-id="{{ $role->id }}"
                                            data-role-name="{{ $role->name }}"
                                            data-role-permissions="{{ $role->permissions->pluck('id')->toJson() }}">
                                            <span>Edit Role</span>
                                        </a>
                                    </div>
                                    {{-- Logika badge tidak perlu diubah, sudah bagus --}}
                                    <span
                                        class="badge bg-label-{{ $role->name == 'admin' ? 'primary' : ($role->name == 'teknisi' ? 'info' : 'success') }}">
                                        {{ strtoupper($role->name) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pengaturan Aplikasi</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-4">
                                        <label class="form-label" for="name_app">Nama Aplikasi</label>
                                        <input type="text" class="form-control @error('name_app') is-invalid @enderror"
                                            id="name_app" name="name_app" placeholder="Nama Aplikasi Anda"
                                            value="{{ old('name_app', $settings->name_app ?? '') }}" required>
                                        @error('name_app')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label" for="desc">Deskripsi</label>
                                        <textarea class="form-control @error('desc') is-invalid @enderror" id="desc" name="desc" rows="5">{{ old('desc', $settings->desc ?? '') }}</textarea>
                                        @error('desc')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-4">
                                        <label class="form-label" for="logo">Logo Utama (Navbar)</label>
                                        <input type="file" class="dropify @error('logo') is-invalid @enderror"
                                            id="logo" name="logo" data-height="300"
                                            data-default-file="{{ $settings->logo ? asset('storage/' . $settings->logo) : '' }}"
                                            data-max-file-size="2M" data-allowed-file-extensions="jpg jpeg png gif svg">
                                        @error('logo')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="secondary_logo">Logo Sekunder (Opsional)</label>
                                    <input type="file" class="dropify @error('secondary_logo') is-invalid @enderror"
                                        id="secondary_logo" name="secondary_logo"
                                        data-default-file="{{ $settings->secondary_logo ? asset('storage/' . $settings->secondary_logo) : '' }}"
                                        data-max-file-size="2M" data-allowed-file-extensions="jpg jpeg png gif svg">
                                    @error('secondary_logo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="app_mockup">Gambar Mockup Aplikasi (Landing Page)</label>
                                    <input type="file" class="dropify @error('app_mockup') is-invalid @enderror"
                                        id="app_mockup" name="app_mockup"
                                        data-default-file="{{ $settings->app_mockup ? asset('storage/' . $settings->app_mockup) : '' }}"
                                        data-max-file-size="2M" data-allowed-file-extensions="jpg jpeg png">
                                    @error('app_mockup')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="phone">Nomor Telepon</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" placeholder="08123456789"
                                        value="{{ old('phone', $settings->phone ?? '') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="kontak@example.com"
                                        value="{{ old('email', $settings->email ?? '') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="address">Alamat</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $settings->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="gmap_coordinat">Koordinat Google Map</label>
                                <input type="text" class="form-control @error('gmap_coordinat') is-invalid @enderror"
                                    id="gmap_coordinat" name="gmap_coordinat" placeholder="-6.175, 106.827"
                                    value="{{ old('gmap_coordinat', $settings->gmap_coordinat ?? '') }}">
                                @error('gmap_coordinat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="whatsapp">Whatsapp</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+62</span>
                                        <input type="text" class="form-control @error('whatsapp') is-invalid @enderror"
                                            id="whatsapp" name="whatsapp" placeholder="8123456789"
                                            value="{{ old('whatsapp', $settings->whatsapp ?? '') }}">
                                    </div>
                                    @error('whatsapp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="instagram">Instagram</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text"
                                            class="form-control @error('instagram') is-invalid @enderror" id="instagram"
                                            name="instagram" placeholder="username"
                                            value="{{ old('instagram', $settings->instagram ?? '') }}">
                                    </div>
                                    @error('instagram')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="youtube">URL Channel YouTube</label>
                                    <input type="url" class="form-control @error('youtube') is-invalid @enderror"
                                        id="youtube" name="youtube" placeholder="https://www.youtube.com/channel/..."
                                        value="{{ old('youtube', $settings->youtube ?? '') }}">
                                    @error('youtube')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label" for="price_per_liter">Harga Konsumsi Air Per Liter</label>
                                    <div class="input-group">

                                        <span class="input-group-text">Rp.</span>
                                        <input type="url"
                                            class="form-control @error('price_per_liter') is-invalid @enderror"
                                            id="price_per_liter" name="price_per_liter" placeholder="Rp.5,2 /L"
                                            value="{{ old('price_per_liter', $settings->price_per_liter ?? '') }}">
                                        @error('price_per_liter')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <span class="input-group-text text-muted">/L</span>

                                    </div>

                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
    </div>

    <!-- Edit Permission -->
    <div class="modal fade" id="roleEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit <span id="modalRoleName"></span> Permissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="roleEditForm">
                    @csrf
                    <input type="hidden" name="role_id" id="editRoleId">
                    <div class="modal-body">
                        <div class="row">
                            @foreach ($permissions as $group => $groupPermissions)
                                <div class="col-md-6 permission-group mb-4">
                                    <h6 class="fw-semibold mb-3">
                                        <input type="checkbox" class="form-check-input permission-group-checkbox">
                                        {{ ucfirst($group) }} Permissions
                                    </h6>
                                    @foreach ($groupPermissions as $permission)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                name="permissions[]" value="{{ $permission->id }}"
                                                id="perm-{{ $permission->id }}">
                                            <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                {{ ucwords(str_replace('-', ' ', $permission->name)) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('demo2/assets/js/app-edit-role.js') }}"></script>
        <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
        <script src="{{ asset('dropify/dropify.js') }}"></script>

        <script src="{{ asset('summer-note/summernote-bs4.min.js') }}"></script>

        <script>
            $(document).ready(function() {
                // Initialize Summernote
                $('#desc').summernote({
                    height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });

                // Initialize Dropify
                $('.dropify').dropify({
                    messages: {
                        'default': 'Seret dan lepas file disini atau klik',
                        'replace': 'Seret dan lepas file disini atau klik untuk merubah gambar',
                        'remove': 'Hapus',
                        'error': 'Upss, Sepertinya terjadi kesalahan.'
                    },
                });
            });
        </script>

        <script>
            // Simpan data role permissions ke JavaScript
            window.rolePermissions = @json($rolePermissions);

            $(document).ready(function() {
                // Ketika modal 'Edit Role' ditampilkan, isi data yang relevan
                $('#roleEditModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var roleId = button.data('role-id');
                    var roleName = button.data('role-name');
                    var permissions = button.data('role-permissions');

                    var modal = $(this);
                    modal.find('#modalRoleName').text(roleName);
                    modal.find('#editRoleId').val(roleId);

                    // Reset semua checkbox
                    modal.find('.permission-checkbox').prop('checked', false);
                    modal.find('.permission-group-checkbox').prop('checked', false);

                    // Centang permission yang dimiliki oleh role tersebut
                    if (permissions && Array.isArray(permissions)) {
                        permissions.forEach(function(permissionId) {
                            modal.find('#perm-' + permissionId).prop('checked', true);
                        });
                    }
                });

                // Menangani submit form #roleEditForm via AJAX
                $('#roleEditForm').on('submit', function(e) {
                    e.preventDefault(); // Mencegah form submit default

                    var form = $(this);
                    // Menggunakan nama route yang benar dari web.php Anda
                    var url = "{{ route('admin.settings.update-role') }}";
                    var formData = form.serialize();

                    // Tampilkan loading indicator sebelum AJAX dimulai
                    Notiflix.Loading.standard('Menyimpan perubahan...');

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                $('#roleEditModal').modal('hide');

                                // Ganti Swal dengan Notiflix.Notify.success
                                Notiflix.Notify.success(response.message ||
                                    'Permissions updated successfully!');

                                // Muat ulang halaman setelah 1.5 detik agar user bisa membaca notifikasi
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                            var errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                            // Ambil pesan error dari response JSON jika ada
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }

                            // Ganti Swal dengan Notiflix.Notify.failure
                            Notiflix.Notify.failure(errorMsg);
                        },
                        complete: function() {
                            // Hapus loading indicator setelah AJAX selesai (baik sukses maupun gagal)
                            Notiflix.Loading.remove();
                        }
                    });
                });
            });
        </script>

        <script type="text/javascript">
            window.allPermissions = @json($permissions);
        </script>
    @endpush
@endsection
