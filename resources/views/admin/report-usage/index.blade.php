@extends('layouts.app')

@section('title', 'Laporan Penggunaan')

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



    <!-- Content -->
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-6">
                <div class="card table-responsive">
                    <h5 class="card-header text-md-start pb-0 text-center">Filter </h5>
                    <div class="card-datatable text-nowrap">
                        <div class="dt-action-buttons d-flex text-xl-end ...">
                            <!-- Date Picker -->
                            <div class="date_picker ms-5 mt-2" style="width: 180px">
                                <!-- Datepicker akan di-append di sini oleh JavaScript -->
                            </div>

                            <div class="month_filter ms-5 mt-2" style="width: 180px">
                                <!-- Datepicker akan di-append di sini oleh JavaScript -->
                            </div>

                            <div class="year_filter ms-5 mt-2" style="width: 180px">
                                <!-- Datepicker akan di-append di sini oleh JavaScript -->
                            </div>
                            <!-- Month Filter -->
                            {{-- <select id="monthFilter" class="form-select text-capitalize ms-5 mt-2" style="width: 180px">
                                <option value="">Pilih Bulan</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                    </option>
                                @endfor
                            </select>

                            <!-- Year Filter -->
                            <select id="yearFilter" class="form-select text-capitalize ms-5 mt-2" style="width: 180px">
                                <option value="">Pilih Tahun</option>
                                @for ($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select> --}}
                        </div>

                        <h5 class="card-header text-md-start pb-0 text-center">Laporan Penggunaan</h5>
                        <table class="datatables-usage table" id="report-usage-datatable"
                            data-url="{{ route('api.report-usage.datatables') }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Tanggal</th>
                                    <th>Total Konsumsi (liter)</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>





            @push('scripts')
                <script src="{{ asset('demo2/assets/js/app-report-usage.js') }}"></script>
                <script src="{{ asset('demo2/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
                <script src="{{ asset('demo2/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js">
                </script>
            @endpush

        @endsection
