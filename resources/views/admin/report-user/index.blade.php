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
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0" id="report-title">Laporan Pengguna per Cabang</h5>
                {{-- Tombol kembali, awalnya disembunyikan --}}


            </div>
            <div class="card-datatable table-responsive">
                <table id="report-user-datatable" class="table" data-url="{{ route('api.report-user.datatables') }}">
                    {{-- JavaScript yang mengisinya --}}
                    <thead></thead>
                </table>
            </div>
        </div>
    </div>
@endsection



@push('scripts')
    <script>
        window.printUserData = {
            name: @json(optional(auth()->user()->userData)->name ?? auth()->user()->email),
            role: @json(auth()->user()->getRoleNames()->first() ?? 'N/A'),
            branch: @json(optional(auth()->user()->branch)->name ?? 'N/A')
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
    <script src="{{ asset('demo2/assets/js/app-report-user.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js">
    </script>
    <script src="{{ asset('demo2/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('demo2/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
@endpush
