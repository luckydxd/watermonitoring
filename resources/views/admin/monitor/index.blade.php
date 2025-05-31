@extends('layouts.app')

@section('title', 'Monitor Water Usage')

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
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <!-- Widget Konsumsi Bulan Ini -->
            <div class="col-sm-6 col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Water Consumption</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $currentMonthTotal }} <small class="text-muted">L</small></h4>
                                    <p class="mb-0">
                                        @if ($percentageChange > 0)
                                            <span class="text-danger">(+{{ $percentageChange }}%)</span>
                                        @elseif ($percentageChange < 0)
                                            <span class="text-success">({{ $percentageChange }}%)</span>
                                        @else
                                            <span class="text-muted">(0%)</span>
                                        @endif
                                    </p>
                                </div>
                                <small class="mb-0">Compared to Last Month</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-label-twitter rounded">
                                    <i class="ti ti-droplet-filled ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Highest Usage</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $topUser->name }} <small
                                            class="text-muted">({{ $topUser->total_consumption }} L)</small></h4>
                                    <p class="mb-0">
                                        @if ($topUser->percentage > 0)
                                            <span class="text-danger">(+{{ $topUser->percentage }}%)</span>
                                        @elseif($topUser->percentage < 0)
                                            <span class="text-success">({{ $topUser->percentage }}%)</span>
                                        @else
                                            <span class="text-muted">(0%)</span>
                                        @endif
                                    </p>
                                </div>
                                <small class="mb-0">Compared to Last Month</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-label-warning rounded">
                                    <i class="ti ti-droplet-dollar ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Avg Daily Usage</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $currentMonthAvg }} <small class="text-muted">L/day</small>
                                    </h4>
                                </div>
                                <small class="text-muted mb-0">This Month</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-label-info rounded">
                                    <i class="ti ti-chart-arrows-vertical ti-md"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Active Users</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2"> {{ $activeUsers }}</h4>
                                    <p class="mb-0">
                                        @if ($growth > 0)
                                            <span class="text-success">(+{{ $growth }}%)</span>
                                        @elseif ($growth < 0)
                                            <span class="text-danger">({{ $growth }}%)</span>
                                        @else
                                            <span class="text-muted">(0%)</span>
                                        @endif
                                    </p>
                                </div>
                                <small class="mb-0">This Month</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-label-success rounded">
                                    <i class="ti ti-user-check ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Active devices</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $activeDevices }}</h4>
                                </div>
                                <small class="mb-0">This Month</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-label-primary rounded">
                                    <i class="ti ti-cpu ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card table-responsive">
                <h5 class="card-header text-md-start pb-0 text-center">User with Device</h5>
                <div class="card-datatable text-nowrap">
                    <table class="datatables-device-assignments table" id="device-assignment-datatable"
                        data-url="{{ route('api.assign.datatables') }}">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Assigned User</th>
                                <th>Device ID</th>
                                <th>Device Type</th>
                                <th>Assignment Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>


        <!-- Offcanvas Add Assign Device -->
        <div class="offcanvas offcanvas-end" id="offcanvasAssignDevice" aria-labelledby="offcanvasAssignDeviceLabel">
            <div class="offcanvas-header border-bottom">
                <h5 id="offcanvasAssignDeviceLabel" class="offcanvas-title">Assign Device to User</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-6">
                <form class="add-new-assignment pt-0" id="addNewAssignmentForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="mb-6">
                        <label class="form-label" for="user_id">User</label>
                        <select id="user_id" name="user_id" class="form-select" required>
                            <option value="" disabled selected>Loading users...</option>
                            <!-- Options will be loaded via AJAX -->
                        </select>
                        <small class="text-muted">Pilih user yang akan diberi device</small>
                    </div>

                    <div class="mb-6">
                        <label class="form-label" for="device_id">Device</label>
                        <select id="device_id" name="device_id" class="form-select" required>
                            <option value="" disabled selected>Loading available devices...</option>
                            <!-- Options will be loaded via AJAX -->
                        </select>
                        <small class="text-muted">Pilih device yang tersedia (belum di-assign)</small>
                    </div>

                    <div class="mb-6">
                        <label class="form-label" for="assignment_date">Assignment Date</label>
                        <input type="datetime-local" class="form-control" id="assignment_date" name="assignment_date"
                            required />
                    </div>

                    <div class="mb-6">
                        <label class="form-label" for="is_active">Status</label>
                        <select id="is_active" name="is_active" class="form-select" required>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        <small class="text-muted">Catatan tambahan (opsional)</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary data-submit me-3">Assign Device</button>
                        <button type="reset" class="btn btn-label-secondary"
                            data-bs-dismiss="offcanvas">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Offcanvas Edit Assign Device -->
        <div class="offcanvas offcanvas-end" id="offcanvasEditAssignDevice"
            aria-labelledby="offcanvasEditAssignDeviceLabel">
            <div class="offcanvas-header border-bottom">
                <h5 id="offcanvasEditAssignDeviceLabel" class="offcanvas-title">Edit Device to User</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-6">
                <form class="edit-assignment pt-0" id="editAssignmentForm">
                    <input type="hidden" name="id" id="edit_assignment_id">

                    <div class="mb-6">
                        <label class="form-label" for="edit_user_id">User</label>
                        <select id="edit_user_id" name="edit_user_id" class="form-select" required>
                            <option value="" disabled selected>Loading users...</option>
                            <!-- Options will be loaded via AJAX -->
                        </select>
                        <small class="text-muted">Pilih user yang akan diberi device</small>
                    </div>

                    <div class="mb-6">
                        <label class="form-label" for="edit_device_id">Device</label>
                        <select id="edit_device_id" name="edit_device_id" class="form-select" required>
                            <option value="" disabled selected>Loading available devices...</option>
                            <!-- Options will be loaded via AJAX -->
                        </select>
                        <small class="text-muted">Pilih device yang tersedia (belum di-assign)</small>
                    </div>

                    <div class="mb-6">
                        <label class="form-label" for="edit_assignment_date">Assignment Date</label>
                        <input type="datetime-local" class="form-control" id="edit_assignment_date"
                            name="edit_assignment_date" required />
                    </div>

                    <div class="mb-6">
                        <label class="form-label" for="edit_is_active">Status</label>
                        <select id="edit_is_active" name="is_active" class="form-select" required>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="form-label" for="edit_notes">edit_notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                        <small class="text-muted">Catatan tambahan (opsional)</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary data-submit me-3">Assign Device</button>
                        <button type="reset" class="btn btn-label-secondary"
                            data-bs-dismiss="offcanvas">Cancel</button>
                    </div>
                </form>
            </div>
        </div>



        <div class="card table-responsive">
            <h5 class="card-header text-md-start pb-0 text-center">Filter Bulan</h5>
            <div class="card-datatable text-nowrap">
                <div class="dt-action-buttons d-flex text-xl-end ..."> <select id="monthFilter"
                        class="form-select text-capitalize ms-5 mt-2" style="width: 200px;">
                        <option value="">Pilih Bulan</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select> <select id="yearFilter" class="form-select text-capitalize ms-5 mt-2"
                        style="width: 200px;">
                        @for ($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select> </div>
                <h5 class="card-header text-md-start pb-0 text-center">Water Consumption Log</h5>
                <table class="datatables-usage table" id="usage-datatable"
                    data-url="{{ route('api.monitor.datatables') }}">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Tanggal</th>
                            <th>Total Konsumsi (liter)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('demo2/assets/vendor/libs/moment/moment.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/@form-validation/popular.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
        <script src="{{ asset('demo2/assets/js/app-monitor.js') }}"></script>
        <script src="{{ asset('demo2/assets/js/app-deviceassign.js') }}"></script>
    @endpush


@endsection
