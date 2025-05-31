@extends('layouts.app')

@section('title', 'Laporan Pengguna')

@push('css')
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('demo2/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
@endpush

@section('content')

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-6">
                <div class="card table-responsive">
                    <h5 class="card-header text-md-start pb-0 text-center">Filter</h5>
                    <div class="card-datatable text-nowrap">
                        <div class="dt-action-buttons d-flex text-xl-end ...">

                            <div class="date_filter ms-5 mt-2" style="width: 180px">
                                <!-- Datefilter akan di-append di sini oleh JavaScript -->
                            </div>

                            <div class="month_filter ms-5 mt-2" style="width: 180px">
                                <!-- Datefilter akan di-append di sini oleh JavaScript -->
                            </div>

                            <div class="year_filter ms-5 mt-2" style="width: 180px">
                                <!-- Datefilter akan di-append di sini oleh JavaScript -->
                            </div>
                            <div class="reset_filter ms-5 mt-2" style="width: 180px"></div>
                        </div>
                    </div>

                    <div class="card-datatable table-responsive">
                        <h5 class="card-header text-md-start pb-0 text-center">Laporan Pengguna</h5>
                        <table id="report-user-datatable" data-url="{{ route('api.report-user.datatables') }}"
                            class="datatables-users table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Pengguna</th>
                                    <th>Nama Pengguna</th>
                                    <th>Peran</th>
                                    <th>Alamat</th>
                                    <th>Nomor Telepon</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>



                    @push('scripts')
                        <script src="{{ asset('demo2/assets/js/app-report-user.js') }}"></script>
                        <script src="{{ asset('demo2/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
                        <script src="{{ asset('demo2/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

                        <script
                            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js">
                        @endpush
                    @endsection
