@extends('layouts.app')

@section('title', 'Alat Saya')

@push('css')
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet"
        href="{{ asset('demo2/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-datatable text-nowrap">
                    <h5 class="card-header text-md-start pb-0 text-center">Daftar Alat Saya</h5>
                    <div class="card-body table-responsive">
                        <table class="datatables-device table" id="user-devices-datatable"
                            data-url="{{ route('api.user.devices') }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Unique ID</th>
                                    <th>Jenis Alat</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- OFFCANVAS UNTUK FORM PENDAFTARAN ALAT --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRegisterDevice"
        aria-labelledby="offcanvasRegisterDeviceLabel">
        <div class="offcanvas-header border-bottom">
            <h5 id="offcanvasRegisterDeviceLabel" class="offcanvas-title">
                Daftarkan Perangkat Baru
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-4">
            <form id="registerDeviceForm" class="pt-0" data-url="{{ route('device.assign') }}"
                data-token="{{ csrf_token() }}">
                <div class="mb-3">
                    <label for="unique_id" class="form-label">ID Unik Perangkat</label>
                    <input type="text" id="unique_id" name="unique_id" class="form-control"
                        placeholder="Masukkan ID yang tertera pada alat" required />
                </div>

                <div class="mb-3" id="initial-reading-wrapper" style="display: none">
                    <label for="initial_meter_reading" class="form-label">Pembacaan Meteran Awal (Liter)</label>
                    <input type="text" step="0.01" id="initial_meter_reading" name="initial_meter_reading"
                        class="form-control" placeholder="Contoh: 100" />
                </div>

                <button type="submit" class="btn btn-primary me-sm-3 me-1" id="submitRegisterBtn">
                    Daftarkan
                </button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">
                    Batal
                </button>
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditDevice" aria-labelledby="offcanvasEditDeviceLabel">
        <div class="offcanvas-header border-bottom">
            <h5 id="offcanvasEditDeviceLabel" class="offcanvas-title">Edit Perangkat</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-4">
            <form id="editDeviceForm" class="pt-0">
                @csrf
                {{-- Input tersembunyi untuk menyimpan ID assignment --}}
                <input type="hidden" name="id" id="edit_assignment_id">

                <div class="mb-3">
                    <label class="form-label">ID Unik</label>
                    {{-- ID Unik tidak bisa diubah oleh pengguna, jadi kita tampilkan sebagai teks biasa --}}
                    <p class="fw-bold" id="edit_unique_id_display">-</p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Alat</label>
                    <p id="edit_device_type_display">-</p>
                </div>

                <div class="mb-3">
                    <label for="edit_notes" class="form-label">Catatan / Label (Opsional)</label>
                    <textarea class="form-control" id="edit_notes" name="notes" rows="3"
                        placeholder="Contoh: Meteran Air Rumah Depan"></textarea>
                </div>

                <div class="mb-3">
                    <label for="edit_is_active" class="form-label">Status Penugasan</label>
                    <select id="edit_is_active" name="is_active" class="form-select">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                    <small class="text-muted">Jika tidak aktif, perangkat tidak akan mengirim data ke akun Anda.</small>
                </div>

                <button type="submit" class="btn btn-primary me-sm-3 me-1">Simpan Perubahan</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Batal</button>
            </form>
        </div>
    </div>



    @push('scripts')
        <script src="{{ asset('demo2/assets/js/app-user-device.js') }}"></script>
    @endpush
@endsection
