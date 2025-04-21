@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

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
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Users</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $totalUsers }}</h4>
                                </div>
                                <small class="mb-0">Total Users</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-label-primary rounded">
                                    <i class="ti ti-users ti-26px"></i>
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
                                <small class="mb-0">Last Month</small>
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
                                <span class="text-heading">Users Registered</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $registeredThisMonth }}</h4>
                                    @if ($registeredGrowth > 0)
                                        <p class="text-success mb-0">(+{{ $registeredGrowth }}%)</p>
                                    @elseif ($registeredGrowth < 0)
                                        <p class="text-danger mb-0">({{ $registeredGrowth }}%)</p>
                                    @else
                                        <p class="text-muted mb-0">(0%)</p>
                                    @endif
                                </div>
                                <small class="mb-0">Compared to Last Month</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-label-danger rounded">
                                    <i class="ti ti-user-plus ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Users List Table -->
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Filters</h5>
                    <div class="d-flex justify-content-between align-items-center row gap-md-0 gap-4 pt-4">
                        <div class="col-md-4 user_role"></div>
                        <div class="col-md-4 user_plan"></div>
                        <div class="col-md-4 user_status"></div>
                    </div>
                </div>
                <div class="card-datatable table-responsive" id="table-user" data-url="{{ route('api.users.index') }}">
                    <table class="datatables-users table">
                        <thead class="border-top">
                            <tr>
                                <th></th>
                                <th></th>
                                <th>User</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Address</th>
                                <th>Phone Number</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <!-- Offcanvas to add new user -->
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser"
                    aria-labelledby="offcanvasAddUserLabel">
                    <div class="offcanvas-header border-bottom">
                        <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add User</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-6">
                        <form class="add-new-user pt-0" id="addNewUserForm" onsubmit="return false"
                            data-url="{{ route('api.users.store') }}">
                            <div class="mb-6">
                                <label class="form-label" for="name">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required />
                            </div>
                            <div class="mb-6">
                                <label class="form-label" for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required />
                            </div>
                            <div class="mb-6">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required />
                            </div>
                            <div class="mb-6">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required />
                            </div>
                            <div class="mb-6">
                                <label class="form-label" for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" />
                            </div>
                            <div class="mb-6">
                                <label class="form-label" for="phone_number">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" />
                            </div>
                            <div class="mb-6">
                                <label class="form-label" for="role">Role</label>
                                <select id="role" name="role" class="form-select" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary data-submit me-3">Submit</button>
                            <button type="reset" class="btn btn-label-danger"
                                data-bs-dismiss="offcanvas">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- / Content -->
    @endsection

    @push('scripts')
        <script src="{{ asset('demo2/assets/vendor/libs/moment/moment.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/@form-validation/popular.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
        <script src="{{ asset('demo2/assets/js/app-user-list.js') }}"></script>
    @endpush
