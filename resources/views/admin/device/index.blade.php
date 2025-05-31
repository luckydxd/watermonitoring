@extends('layouts.app')

@section('title', 'Manage IoT Devices')

@push('css')
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('demo2/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')

    <!-- Content -->
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-6">
                <!-- Card Border Shadow -->
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial bg-label-primary rounded"><i
                                            class="ti ti-cpu ti-28px"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $totalDevices }}</h4>
                            </div>
                            <p class="mb-1">Total devices</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-border-shadow-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial bg-label-info rounded"><i
                                            class="ti ti-power ti-28px"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $activeDevices }}</h4>
                            </div>
                            <p class="mb-1">Active devices</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-border-shadow-danger h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial bg-label-danger rounded"><i
                                            class="ti ti-time-duration-off ti-28px"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $inactiveDevices }}</h4>
                            </div>
                            <p class="mb-1">Non-active devices</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial bg-label-warning rounded"><i
                                            class="ti ti-alert-triangle ti-28px"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $maintenanceDevices }}</h4>
                            </div>
                            <p class="mb-1">Maintenance devices</p>
                        </div>
                    </div>
                </div>

                <!--/ Card Border Shadow -->

                <!-- DataTable Section -->
                <div class="card table-responsive">
                    <h5 class="card-header text-md-start pb-0 text-center">Filters</h5>
                    <div class="card-datatable text-nowrap">
                        <div class="dt-action-buttons d-flex text-xl-end ...">
                            <select id="statusFilter" class="form-select text-capitalize ms-5 mt-2" style="width: 200px;">
                                <!-- Akan diisi otomatis -->
                            </select>
                            <select id="typeFilter" class="form-select text-capitalize ms-5 mt-2" style="width: 200px;">
                                <!-- Akan diisi otomatis -->
                            </select>
                        </div>

                        <h5 class="card-header text-md-start pb-0 text-center">Manage Devices</h5>
                        <table class="datatables-device table" id="devices-datatable"
                            data-url="{{ route('api.devices.index') }}">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Unique ID</th>
                                    <th class="text-center">Device Type</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Location</th>
                                    <th class="text-center">Created At</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ Ajax Sourced Server-side -->

            <!-- Offcanvas Add Device -->
            <div class="offcanvas offcanvas-end" id="offcanvasAddDevice" aria-labelledby="offcanvasAddDeviceLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasAddDeviceLabel" class="offcanvas-title">Add New Device</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-6">
                    <form class="add-new-device pt-0" id="addNewDeviceForm" onsubmit="return false">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="mb-6">
                            <label class="form-label" for="unique_id">Unique ID</label>
                            <input type="text" class="form-control" id="unique_id" name="unique_id" required />
                            <small class="text-muted">ID unik untuk device</small>
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="device_type_id">Tipe Device</label>
                            <select id="device_type_id" name="device_type_id" class="form-select" required>
                                <option value="" disabled selected>Loading device types...</option>
                                <!-- Options will be loaded via AJAX -->
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="status">Status</label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="" disabled selected>Pilih Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="error">Error</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="latitude">Latitude</label>
                            <input type="number" step="any" class="form-control" id="latitude" name="latitude" />
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="longitude">Longitude</label>
                            <input type="number" step="any" class="form-control" id="longitude"
                                name="longitude" />
                        </div>

                        <button type="submit" class="btn btn-primary data-submit me-3">Submit</button>
                        <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancel</button>
                    </form>
                </div>
            </div>

            <!-- Offcanvas Edit Device -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditDevice"
                aria-labelledby="offcanvasEditDeviceLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasEditDeviceLabel" class="offcanvas-title">Edit Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-6">
                    <form id="editDeviceForm">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label for="edit_unique_id" class="form-label">Unique ID</label>
                            <input type="text" class="form-control" id="edit_unique_id" name="unique_id" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_device_type_id" class="form-label">Tipe Device</label>
                            <select class="form-select" id="edit_device_type_id" name="device_type_id" required>
                                <option value="" disabled selected>Loading device types...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                                <option value="error">Error</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_latitude" class="form-label">Latitude</label>
                            <input type="number" step="any" class="form-control" id="edit_latitude"
                                name="latitude">
                        </div>

                        <div class="mb-3">
                            <label for="edit_longitude" class="form-label">Longitude</label>
                            <input type="number" step="any" class="form-control" id="edit_longitude"
                                name="longitude">
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="offcanvas">Batal</button>
                    </form>
                </div>
            </div>

            @push('scripts')
                <script src="{{ asset('demo2/assets/js/app-device.js') }}"></script>
            @endpush
        @endsection
