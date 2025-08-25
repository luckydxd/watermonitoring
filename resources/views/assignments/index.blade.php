@extends('layouts.app')

@section('title', 'Manajemen Penugasan')

@push('css')
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('demo2/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daftar Semua Penugasan</h5>
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label for="statusFilter" class="form-label">Filter berdasarkan Status:</label>
                            <select id="statusFilter" class="form-select text-capitalize">
                                <option value="">Semua Status</option>
                                <option value="in_progress">Sedang Dikerjakan</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        {{-- ID tabel diubah menjadi 'assignments-datatable' --}}
                        <table class="table" id="assignments-datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis & Detail Tugas</th>
                                    <th>Status</th>

                                    @role('admin')
                                        <th>Pelanggan</th>
                                        <th>Teknisi Ditugaskan</th>
                                    @endrole

                                    @role('teknisi')
                                        <th>Pelanggan</th>
                                        <th>Catatan Admin</th>
                                    @endrole

                                    <th>Waktu Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @role('teknisi')
        <div class="modal fade" id="completeAssignmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Bukti Penyelesaian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="completionForm">
                            <input type="hidden" id="assignmentIdInput" name="assignment_id">
                            <div class="mb-3">
                                <label for="completionNotes" class="form-label">Catatan Penyelesaian (Wajib)</label>
                                <textarea class="form-control" id="completionNotes" name="completion_notes" rows="4"
                                    placeholder="Jelaskan tindakan yang telah dilakukan..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="completionImage" class="form-label">Unggah Bukti Foto (Opsional)</label>
                                <input class="form-control" type="file" id="completionImage" name="completion_image"
                                    accept="image/*">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="submitCompletionBtn">Kirim Laporan</button>
                    </div>
                </div>
            </div>
        </div>
    @endrole
@endsection

@push('scripts')
    {{-- Load file JS yang baru --}}
    <script src="{{ asset('demo2/assets/js/app-assignment.js') }}"></script>
    <script>
        // Kirim variabel ke JavaScript
        const currentUserRole = @json(auth()->user()->getRoleNames()->first());
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // Ganti dengan route API untuk mengambil data assignment
        const assignmentsUrl = "{{ route('api.assignments.index') }}";
    </script>
@endpush
