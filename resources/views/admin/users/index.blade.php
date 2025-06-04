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
    @role('admin')
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row g-6 mb-6">
                <div class="col-sm-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span class="text-heading">Pengguna</span>
                                    <div class="d-flex align-items-center my-1">
                                        <h4 class="mb-0 me-2">{{ $totalUsers }}</h4>
                                    </div>
                                    <small class="mb-0">Total Pengguna</small>
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
                                    <span class="text-heading">Pengguna Aktif</span>

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
                                    <small class="mb-0">Bulan Terakhir</small>
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
                                    <span class="text-heading">User Terdaftar</span>
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
                                    <small class="mb-0">Dibandingkan Bulan Kemarin</small>
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
            @endrole

            @role('teknisi')
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row g-6 mb-6">
                        <div class="col-sm-6 col-xl-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div class="content-left">
                                            <span class="text-heading">Pengguna</span>
                                            <div class="d-flex align-items-center my-1">
                                                <h4 class="mb-0 me-2">{{ $totalUsersOnly }}</h4>
                                            </div>
                                            <small class="mb-0">Total Pengguna</small>
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
                                            <span class="text-heading">Pengguna Aktif</span>
                                            <div class="d-flex align-items-center my-1">
                                                <h4 class="mb-0 me-2"> {{ $activeUsersOnly }}</h4>
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
                                            <small class="mb-0">Bulan Terakhir</small>
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
                                            <span class="text-heading">User Terdaftar</span>
                                            <div class="d-flex align-items-center my-1">
                                                <h4 class="mb-0 me-2">{{ $registeredThisMonthOnly }}</h4>
                                                @if ($registeredGrowth > 0)
                                                    <p class="text-success mb-0">(+{{ $registeredGrowth }}%)</p>
                                                @elseif ($registeredGrowth < 0)
                                                    <p class="text-danger mb-0">({{ $registeredGrowth }}%)</p>
                                                @else
                                                    <p class="text-muted mb-0">(0%)</p>
                                                @endif
                                            </div>
                                            <small class="mb-0">Dibandingkan Bulan Kemarin</small>
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
                    @endrole


                    <!-- Users List Table -->
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0">Filter</h5>
                            <div class="d-flex justify-content-between align-items-center row gap-md-0 gap-4 pt-4">
                                <div class="col-md-4 user_role"></div>
                                <div class="col-md-4 user_plan"></div>
                                <div class="col-md-4 user_status"></div>
                            </div>
                        </div>

                        <div class="card-datatable table-responsive" id="table-user"
                            data-url="{{ route('api.users.index') }}">
                            <h5 class="card-header text-md-start pb-0 text-center">Manajemen Pengguna</h5>
                            <table class="datatables-users table">
                                <thead class="border-top">
                                    <tr>
                                        <th></th>
                                        <th>Pengguna</th>
                                        {{-- <th>Nama Pengguna</th> --}}
                                        <th>Peran</th>
                                        <th>Alamat</th>
                                        <th>Nomor Telepon</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
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
                                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Tambah Pengguna</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-6">
                                <form class="add-new-user pt-0" id="addNewUserForm" onsubmit="return false"
                                    data-roles="{{ json_encode($roles) }}" data-url="/api/users">
                                    <div class="mb-6">
                                        <label class="form-label" for="name">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            required />
                                    </div>
                                    {{-- <div class="mb-6">
                                        <label class="form-label" for="username">Nama Pengguna</label>
                                        <input type="text" class="form-control" id="username" name="username"
                                            required />
                                    </div> --}}
                                    <div class="mb-6">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            required />
                                    </div>
                                    <div class="mb-6">
                                        <label class="form-label" for="password">Kata Sandi</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required />
                                    </div>
                                    <div class="mb-6">
                                        <label class="form-label" for="address">Alamat</label>
                                        <input type="text" class="form-control" id="address" name="address" />
                                    </div>
                                    <div class="mb-6">
                                        <label class="form-label" for="phone_number">Nomor Telepon</label>
                                        <input type="text" class="form-control" id="phone_number"
                                            name="phone_number" />
                                    </div>
                                    <div class="mb-6">
                                        <label class="form-label" for="role">Peran</label>
                                        <select id="role" name="role" class="form-select" required>
                                            <option value="" disabled selected>Pilih Peran</option>
                                            @isset($roles)
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary data-submit me-3">Simpan</button>
                                    <button type="reset" class="btn btn-label-danger"
                                        data-bs-dismiss="offcanvas">Batal</button>
                                </form>
                            </div>
                        </div>


                        <!-- Edit User Offcanvas -->
                        <div class="offcanvas offcanvas-end" id="editUserOffcanvas"
                            aria-labelledby="editUserOffcanvasLabel">
                            <div class="offcanvas-header border-bottom">
                                <h5 id="editUserOffcanvasLabel" class="offcanvas-title">Edit Pengguna</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-6">
                                <form class="edit-user pt-0" id="editUserForm" onsubmit="return false"
                                    data-roles="{{ json_encode($roles) }}" data-url="/api/users">
                                    <input type="hidden" id="edit_id" name="id">

                                    <div class="mb-6">
                                        <label class="form-label" for="edit_name">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="edit_name" name="name"
                                            required />
                                    </div>
                                    {{-- 
                                    <div class="mb-6">
                                        <label class="form-label" for="edit_username">Nama Pengguna</label>
                                        <input type="text" class="form-control" id="edit_username" name="username"
                                            required />
                                    </div> --}}

                                    <div class="mb-6">
                                        <label class="form-label" for="edit_email">Email</label>
                                        <input type="email" class="form-control" id="edit_email" name="email"
                                            required />
                                    </div>

                                    <div class="mb-6">
                                        <label class="form-label" for="edit_password">Kata Sandi (Kosongkan jika tidak
                                            ingin
                                            mengubah)</label>
                                        <input type="password" class="form-control" id="edit_password"
                                            name="password" />
                                    </div>

                                    <div class="mb-6">
                                        <label class="form-label" for="edit_address">Alamat</label>
                                        <input type="text" class="form-control" id="edit_address" name="address" />
                                    </div>

                                    <div class="mb-6">
                                        <label class="form-label" for="edit_phone_number">Nomor Telepon</label>
                                        <input type="text" class="form-control" id="edit_phone_number"
                                            name="phone_number" />
                                    </div>

                                    <div class="mb-6">
                                        <label class="form-label" for="edit_role">Peran</label>
                                        <select id="edit_role" name="role" class="form-select" required>
                                            <option value="" disabled selected>Pilih Peran</option>
                                            @isset($roles)
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>

                                    <div class="mb-6">
                                        <label class="form-label" for="edit_isActive">Status Aktif</label>
                                        <select id="edit_isActive" name="isActive" class="form-select" required>
                                            <option value="" disabled selected>Pilih Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Non-Active</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary data-submit me-3">Ubah</button>
                                    <button type="button" class="btn btn-label-danger"
                                        data-bs-dismiss="offcanvas">Batal</button>
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
                <script>
                    const currentUserRole = @json(auth()->user()->getRoleNames()->first());
                </script>
            @endpush
