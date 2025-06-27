@extends('layouts.app')

@section('title', 'Manajemen Tentang Kami')

@push('css')
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('demo2/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-datatable text-nowrap">
                    <h5 class="card-header text-md-start pb-0 text-center">Manajemen Tentang Landingpage</h5>
                    <div class="card-body table-responsive">
                        <table class="datatables-complaint table" id="about-datatable"
                            data-url="{{ route('api.about.index') }}">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Gambar</th>
                                    <th class="text-center">Judul</th>
                                    <th class="text-center">Deskripsi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas Tambah Tentang Kami -->
    <div class="offcanvas offcanvas-end" id="offcanvasAddAbout">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Tambah Tentang Kami</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">

            <form id="addAboutForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gambar</label>
                    <input type="file" class="form-control" name="image">
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Offcanvas Edit Tentang Kami -->
    <div class="offcanvas offcanvas-end" id="offcanvasEditAbout">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Edit Tentang Kami</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="editAboutForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_about_id">

                <div class="mb-4">
                    <label class="form-label">Judul</label>
                    <input type="text" class="form-control" id="edit_title" name="title">
                </div>

                <div class="mb-4">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Gambar</label>
                    <input type="file" class="form-control" name="image">
                    <div id="edit_image_preview" class="mt-2"></div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    </div>

@endsection


@push('scripts')
    <script src="{{ asset('demo2/assets/js/app-about-setting.js') }}"></script>
@endpush
