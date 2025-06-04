@extends('layouts.app')

@section('title', 'Manajemen Keluhan Pengguna')

@push('css')
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet"
        href="{{ asset('demo2/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-datatable text-nowrap">
                    <!-- Filter Section -->
                    <h5 class="card-header text-md-start pb-0 text-center">Filter</h5>
                    <div class="dt-action-buttons d-flex text-xl-end">
                        <select id="statusFilter" class="form-select text-capitalize ms-5 mt-2" style="width: 200px;">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="processed">Processed</option>
                            <option value="resolved">Resolved</option>
                            <option value="rejected">Rejected</option>
                        </select>

                    </div>

                    <!-- Table Section -->
                    <h5 class="card-header text-md-start pb-0 text-center">Manajemen Komplain</h5>
                    <div class="card-body table-responsive">
                        <div class="d-flex justify-content-start mt-2">
                            @if (auth()->user()->hasRole('admin'))
                                <button class="add-new btn btn-primary waves-effect waves-light" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasAddComplaint">
                                    <i class="ti ti-plus me-sm-1 ti-xs me-0"></i>
                                    <span class="d-none d-sm-inline-block">Tambah Data Komplain</span>
                                </button>
                            @endif
                        </div>

                        <table class="datatables-complaint table" id="complaints-datatable"
                            data-url="{{ route('api.complaints.index') }}">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">User</th>
                                    <th class="text-center">Gambar</th>
                                    <th class="text-center">Judul</th>
                                    <th class="text-center">Deskripsi</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Waktu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas Tambah Keluhan -->
    <div class="offcanvas offcanvas-end" id="offcanvasAddComplaint">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Tambah Keluhan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">

            <form id="addComplaintForm">
                @csrf
                <input type="hidden" name="status" value="pending">
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
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

    <!-- Offcanvas Edit Keluhan -->
    <div class="offcanvas offcanvas-end" id="offcanvasEditComplaint">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Edit Keluhan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="editComplaintForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_complaint_id" name="id">

                @role('admin')
                    <div class="mb-3">
                        <label class="form-label">Judul*</label>
                        <input type="text" class="form-control" id="edit_title" name="title">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi*</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                @endrole

                @role('teknisi')
                    <div class="mb-3">
                        <label class="form-label" hidden>Judul*</label>
                        <input type="text" class="form-control" id="edit_title" name="title" hidden>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" hidden>Deskripsi*</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" hidden></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status*</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="pending" selected>Pending</option>
                            <option value="processed">Diproses</option>
                            <option value="resolved">Selesai</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                @endrole

                @role('admin')
                    <div class="mb-3">
                        <label class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="image">
                        <div id="edit_image_preview" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status*</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="pending" selected>Pending</option>
                            <option value="processed">Diproses</option>
                            <option value="resolved">Selesai</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                @endrole

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('demo2/assets/js/app-complaint.js') }}"></script>
    <script>
        const currentUserRole = @json(auth()->user()->getRoleNames()->first());
    </script>
@endpush
