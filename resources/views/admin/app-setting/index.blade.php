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
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-normal text-body mb-0">Total {{ $role->users_count }} users</h6>
                                    <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                        @foreach ($role->users->take(3) as $user)
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                                title="{{ $user->name }}" class="avatar pull-up">
                                                <img class="rounded-circle"
                                                    src="{{ $user->avatar ?? asset('assets/img/avatars/1.png') }}"
                                                    alt="Avatar" />
                                            </li>
                                        @endforeach
                                        @if ($role->users_count > 3)
                                            <li class="avatar pull-up">
                                                <span
                                                    class="avatar-initial rounded-circle bg-label-primary">+{{ $role->users_count - 3 }}</span>
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

                            <!-- App Name -->
                            <div class="mb-4">
                                <label class="form-label" for="name_app">Nama Aplikasi</label>
                                <input type="text" class="form-control @error('name_app') is-invalid @enderror"
                                    id="name_app" name="name_app" placeholder="My Awesome App"
                                    value="{{ old('name_app', $settings->name_app ?? '') }}" required>
                                @error('name_app')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description with Summernote -->
                            <div class="mb-4">
                                <label class="form-label" for="desc">Deskripsi</label>
                                <textarea class="form-control @error('desc') is-invalid @enderror" id="desc" name="desc">{{ old('desc', $settings->desc ?? '') }}</textarea>
                                @error('desc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Logo with Dropify -->
                            <div class="mb-4">
                                <label class="form-label" for="logo">Logo Utama</label>
                                <input type="file" class="dropify @error('logo') is-invalid @enderror" id="logo"
                                    name="logo"
                                    data-default-file="{{ $settings->logo ? asset('storage/' . $settings->logo) : '' }}"
                                    data-max-file-size="2M" data-allowed-file-extensions="jpg jpeg png gif">
                                @error('logo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Secondary Logo with Dropify -->
                            <div class="mb-4">
                                <label class="form-label" for="secondary_logo">Logo Sekunder</label>
                                <input type="file" class="dropify @error('secondary_logo') is-invalid @enderror"
                                    id="secondary_logo" name="secondary_logo"
                                    data-default-file="{{ $settings->secondary_logo ? asset('storage/' . $settings->secondary_logo) : '' }}"
                                    data-max-file-size="2M" data-allowed-file-extensions="jpg jpeg png gif">
                                @error('secondary_logo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label" for="no_contact">Kontak Nomor Telepon</label>
                                    <input type="text" class="form-control @error('no_contact') is-invalid @enderror"
                                        id="no_contact" name="no_contact" placeholder="+62 123 4567 8901"
                                        value="{{ old('no_contact', $settings->no_contact ?? '') }}">
                                    @error('no_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="email">Kontak Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="contact@example.com"
                                        value="{{ old('email', $settings->email ?? '') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Social Media -->
                            <div class="mb-4">
                                <label class="form-label" for="instagram">Instagram</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" class="form-control @error('instagram') is-invalid @enderror"
                                        id="instagram" name="instagram" placeholder="username"
                                        value="{{ old('instagram', $settings->instagram ?? '') }}">
                                </div>
                                @error('instagram')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="mb-4">
                                <label class="form-label" for="alamat">Alamat</label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', $settings->alamat ?? '') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Google Maps Coordinates -->
                            <div class="mb-4">
                                <label class="form-label" for="gmap_coordinat">Koordinat Google Map</label>
                                <input type="text" class="form-control @error('gmap_coordinat') is-invalid @enderror"
                                    id="gmap_coordinat" name="gmap_coordinat" placeholder="-6.175392, 106.827153"
                                    value="{{ old('gmap_coordinat', $settings->gmap_coordinat ?? '') }}">
                                <small class="text-muted">Contoh: -6.175392, 106.827153</small>
                                @error('gmap_coordinat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
    </div>

    <!-- Di bagian sebelum penutup section content -->
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

        <script src="{{ asset('summer-note/summernote-bs4.min.js') }}"></script>
        <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>


        <!-- Initialize plugins -->
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
                // Handler saat modal ditampilkan
                $('#roleEditModal').on('show.bs.modal', function(event) {
                    const button = $(event.relatedTarget);
                    const roleId = button.data('role-id');
                    const roleName = button.data('role-name');

                    // Set data modal
                    $('#modalRoleName').text(roleName);
                    $('#editRoleId').val(roleId);

                    // Uncheck semua checkbox terlebih dahulu
                    $('.permission-checkbox').prop('checked', false);

                    // Dapatkan permissions untuk role ini
                    const permissions = window.rolePermissions[roleId] || [];

                    // Check checkbox yang sesuai
                    permissions.forEach(permissionId => {
                        $(`#perm-${permissionId}`).prop('checked', true);
                    });

                    // Update group checkbox
                    updateGroupCheckboxes();
                });

                // Fungsi untuk update group checkbox
                function updateGroupCheckboxes() {
                    $('.permission-group').each(function() {
                        const group = $(this);
                        const checkboxes = group.find('.permission-checkbox');
                        const groupCheckbox = group.find('.permission-group-checkbox');

                        const allChecked = checkboxes.length === checkboxes.filter(':checked').length;
                        groupCheckbox.prop('checked', allChecked);
                    });
                }

                // Handler untuk group checkbox
                $('.permission-group-checkbox').change(function() {
                    const group = $(this).closest('.permission-group');
                    const isChecked = $(this).is(':checked');

                    group.find('.permission-checkbox').prop('checked', isChecked);
                });

                // Handler untuk individual permission checkbox
                $(document).on('change', '.permission-checkbox', function() {
                    updateGroupCheckboxes();
                });

                // Form submission handler
                $('#roleEditForm').submit(function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('admin.settings.update-role') }}",
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#roleEditModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON.message || 'Something went wrong!'
                            });
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
