@extends('layouts.app')

@section('title', 'Penggunaan Saya')

@push('css')
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet"
        href="{{ asset('demo2/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
@endpush

@section('content')

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-datatable text-nowrap">
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
                        </div>

                        <h5 class="card-header text-md-start pb-0 text-center">Monitoring Penggunaan Air</h5>
                        <div class="card-body table-responsive">
                            <table class="datatables-usage table" id="user-consumption-datatable"
                                data-url="{{ route('api.user.usage') }}">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Total Konsumsi (liter)</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        @push('scripts')
            <script src="{{ asset('demo2/assets/js/app-user-usage.js') }}"></script>
            <script src="{{ asset('demo2/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
            <script src="{{ asset('demo2/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js">
            </script>
        @endpush
    @endsection
