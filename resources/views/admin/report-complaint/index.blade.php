@extends('layouts.app')

@section('title', 'Laporan Keluhan')

@push('css')
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('demo2/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />>
@endpush

@section('content')

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-6">
                <div class="card table-responsive">
                    <h5 class="card-header text-md-start pb-0 text-center">Filters</h5>
                    <div class="card-datatable text-nowrap">
                        <div class="dt-action-buttons d-flex text-xl-end ...">
                            @role('admin')
                                <div class="date_filter ms-5 mt-2" style="width: 180px">
                                    <!--  JavaScript -->
                                </div>
                            @endrole

                            <div class="month_filter ms-5 mt-2" style="width: 180px">
                                <!--  JavaScript -->
                            </div>

                            <div class="year_filter ms-5 mt-2" style="width: 180px">
                                <!--  JavaScript -->
                            </div>
                            <div class="reset_filter ms-5 mt-2" style="width: 180px"></div>
                        </div>

                        <h5 class="card-header text-md-start pb-0 text-center">Laporan Keluhan</h5>
                        <table class="datatables-complaint table" id="complaints-datatable"
                            data-url="{{ route('api.report-complaint.datatables') }}">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">User</th>
                                    <th class="text-center">Gambar</th>
                                    <th class="text-center">Judul</th>
                                    <th class="text-center">Deskripsi</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Waktu</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>





    @push('scripts')
        <script src="{{ asset('demo2/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js">
        </script>
        <script src="{{ asset('demo2/assets/js/app-report-complaint.js') }}"></script>
    @endpush
@endsection
