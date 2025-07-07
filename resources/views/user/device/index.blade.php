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



    @push('scripts')
        <script src="{{ asset('demo2/assets/js/app-user-device.js') }}"></script>
    @endpush
@endsection
