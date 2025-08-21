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
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endpush

@section('content')

    <!-- Content -->
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            {{-- Chart Section --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="text-end">
                        <button class="btn btn-outline-primary roll-up-btn d-none mb-4" id="rollUpBtn">
                            <i class="ti ti-arrow-up me-1"></i>
                        </button>
                        {{-- <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="yearFilter" class="form-select ms-5 mt-2" disabled>
                                <option value="">Pilih Tahun</option>
                                @for ($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="monthFilter" class="form-select ms-5 mt-2" disabled>
                                <option value="">Pilih Bulan</option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        </div> --}}

                    </div>
                    <div id="columnchart"></div>
                </div>
            </div>
            {{-- Chart Section --}}

            <div class="card">
                <div class="card-datatable text-nowrap">
                    <h5 class="card-header text-md-start pb-0 text-center">Laporan Konsumsi Air</h5>
                    <div class="card-body table-responsive">
                        <!-- Breadcrumb Navigation -->
                        <div id="report-breadcrumb" class="mb-3"></div>



                        <!-- Filter Controls -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select id="yearFilter" class="form-select ms-5 mt-2" disabled>
                                    <option value="">Pilih Tahun</option>
                                    @for ($y = date('Y'); $y >= 2020; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="monthFilter" class="form-select ms-5 mt-2" disabled>
                                    <option value="">Pilih Bulan</option>
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="col-md-3">
                                <button id="rollUpBtn" class="btn btn-secondary d-none">
                                    <i class="ti ti-arrow-up me-1"></i>Roll Up
                                </button>
                            </div> --}}
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table id="report-usage-datatable" class="table" style="width:100%"
                                data-url="{{ route('api.report-usage.admin') }}">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th class="text-center">Periode</th>
                                        <th class="text-center">Total Konsumsi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>







        @push('scripts')
            <script>
                // Pastikan data user dikirim dengan benar
                window.printUserData = {
                    name: @json(optional(auth()->user()->userData)->name ?? auth()->user()->name),
                    role: @json(auth()->user()->getRoleNames()->first() ?? 'Pengguna'),
                    branch: @json(optional(auth()->user()->branch)->name ?? ''),
                    position: @json(optional(auth()->user()->position)->name ?? '')
                };
            </script>
            <script>
                window.pdfExportData = {
                    appName: @json($appSettings->name_app ?? 'SIMOARA'),
                    appAddress: @json($appSettings->address ?? 'Perum Graha Panyindangan No A8'),
                    appPhone: @json($appSettings->phone ?? '08123456789'),
                    appUrl: @json(url('/')),
                    userName: @json(optional(auth()->user()->userData)->name ?? auth()->user()->name),
                    userRole: @json(auth()->user()->getRoleNames()->first() ?? 'Pengguna')
                };
            </script>
            <script src="{{ asset('demo2/assets/js/app-report-usage.js') }}"></script>
            <script src="{{ asset('demo2/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
            <script src="{{ asset('demo2/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js">
            </script>
            <script src="{{ asset('demo2/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
        @endpush

    @endsection
