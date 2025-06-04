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
                                    <th class="text-center">No</th>
                                    <th class="text-center">Unique ID</th>
                                    <th class="text-center">Jenis Alat</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Lokasi</th>
                                    <th class="text-center">Tanggal Dibuat</th>
                                    {{-- <th class="text-center">Aksi</th> --}}
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @push('scripts')
        <script src="{{ asset('demo2/assets/js/app-user-device.js') }}"></script>
    @endpush
@endsection
