@extends('layouts.app')

@section('title', 'Dashboard')

@push('css')
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('demo2/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
            <!-- Widget Welcome -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-7">
                            <div class="card-body text-nowrap">
                                <h5 class="card-title mb-4">Selamat Datang {{ explode(' ', $currentUserName)[0] }}! 👋</h5>
                                <p class="mb-3">Hari ini, {{ $tanggalHariIni }}</p>
                                <h4 class="text-primary mb-1"></h4>
                                <a href="{{ route('teknisi.device') }}" class="btn btn-primary">Alat</a>
                                <a href="{{ route('teknisi.complaint') }}" class="btn btn-secondary">Keluhan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Widget Welcome -->

            {{-- <!-- Statistics -->
            <div class="col-xl-8 col-md-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">Statistics</h5>
                        <small class="text-muted">Updated 1 month ago</small>
                    </div>


                    <div class="card-body d-flex align-items-end">
                        <div class="w-100">
                            <div class="row gy-3">
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-primary me-4 rounded p-2">
                                            <i class="ti ti-chart-pie-2 ti-lg"></i>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">230k</h5>
                                            <small>Sales</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-info me-4 rounded p-2"><i class="ti ti-users ti-lg"></i>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">8.549k</h5>
                                            <small>Customers</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-danger me-4 rounded p-2">
                                            <i class="ti ti-shopping-cart ti-lg"></i>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">1.423k</h5>
                                            <small>Products</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-success me-4 rounded p-2">
                                            <i class="ti ti-currency-dollar ti-lg"></i>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">$9745</h5>
                                            <small>Revenue</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Statistics --> --}}
            <div class="col-xl-8 col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Penggunaan Tertinggi</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">
                                        {{ strlen($topUser->name) > 15 ? substr($topUser->name, 0, 20) . '...' : $topUser->name }}
                                        <small class="text-muted">({{ $topUser->total_consumption }} L)</small>
                                    </h4>
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
                                <small class="text-muted mb-4">Bulan Ini &nbsp;-&nbsp; Dibandingkan Bulan Terakhir</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-label-danger rounded">
                                    <i class="ti ti-droplet-dollar ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Widget Konsumsi Bulan Ini -->
            <div class="col-sm-6 col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Penggunaan</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $currentMonthTotal }} <small class="text-muted">Liter</small>
                                    </h4>
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
                                <small class="text-muted mb-4">Bulan Ini &nbsp;-&nbsp; Dibandingkan Bulan Terakhir</small>

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
                                <span class="text-heading">Rata Rata Penggunaan Harian</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $currentMonthAvg }} <small
                                            class="text-muted">Liter/Hari</small>
                                    </h4>
                                </div>
                                <small class="text-muted mb-0">Bulan Ini</small>
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
                                <small class="text-muted mb-0">Bulan Ini</small>
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
                                <span class="text-heading">Alat Aktif</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $activeDevices }}</h4>
                                </div>
                                <small class="text-muted mb-0">Bulan Ini</small>
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

            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Keluhan Tertunda</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ $totalComplaints }}</h4>
                                </div>
                                <small class="text-muted mb-0">Bulan Ini</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-label-warning rounded">
                                    <i class="ti ti-bubble-text ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Water Consumption Monitoring</h5>
                            <p class="card-subtitle my-0">Daily water usage trends</p>
                        </div>

                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-calendar"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center time-period-btn"
                                        data-period="today">Today</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center time-period-btn"
                                        data-period="yesterday">Yesterday</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center time-period-btn"
                                        data-period="week">Last 7 Days</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center time-period-btn"
                                        data-period="month">Last 30 Days</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center time-period-btn"
                                        data-period="current_month">Current Month</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center time-period-btn"
                                        data-period="last_month">Last Month</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="waterUsageChart" data-chart='@json($waterUsageData ?? [])'></div>
                    </div>
                </div>
            </div>
            <!-- /Line Area Chart -->

            <!-- Bar Chart -->
            <div class="col-md-6 col-12 mb-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0" id="complaint-total">0 Keluhan</h5>
                            <p class="card-subtitle mb-1 mt-0">Statistik Keluhan</p>
                        </div>
                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-calendar"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center period-filter"
                                        data-period="today">Today</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center period-filter"
                                        data-period="yesterday">Yesterday</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center period-filter"
                                        data-period="week">Last 7 Days</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center period-filter"
                                        data-period="month">Last 30 Days</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center period-filter"
                                        data-period="current_month">Current Month</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center period-filter"
                                        data-period="last_month">Last Month</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="complaintBarChart" data-chart='@json($complaintBarData)'></div>
                    </div>
                </div>
            </div>
            <!-- /Bar Chart -->


            <!-- Donut Chart Card Gabungan -->
            <div class="col-md-6 col-12 mb-6">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Statistik Sistem</h5>
                            <p class="card-subtitle my-0">Distribusi status keluhan & perangkat</p>
                        </div>
                        <div class="dropdown d-flex">

                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Donut Chart 2: Device Status -->
                        <h6 class="mb-2 text-center">Status Perangkat</h6>
                        <div id="donutChart2" data-chart='@json($deviceStats)'></div>
                    </div>
                </div>
            </div>

            {{-- <!-- Activity Timeline -->
            <div class="col-xxl-6 order-xl-0 order-2 mb-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title d-flex align-items-center m-0 mb-2 me-2 pt-1">
                            <i class="ti ti-list-details me-3"></i> Activity Timeline
                        </h5>
                        <div class="dropdown">
                            <button class="btn btn-text-secondary rounded-pill text-muted me-n1 border-0 p-2"
                                type="button" id="timelineWapper" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="ti ti-dots-vertical ti-md text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="timelineWapper">
                                <a class="dropdown-item" href="javascript:void(0);">Download</a>
                                <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                <a class="dropdown-item" href="javascript:void(0);">Share</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-0">
                        <ul class="timeline mb-0">
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-primary"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">12 Invoices have been paid</h6>
                                        <small class="text-muted">12 min ago</small>
                                    </div>
                                    <p class="mb-2">Invoices have been paid to the company</p>
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="badge bg-lighter rounded-3">
                                            <img src="../../assets//img/icons/misc/pdf.png" alt="img" width="15"
                                                class="me-2" />
                                            <span class="h6 text-body mb-0">invoices.pdf</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-success"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">Client Meeting</h6>
                                        <small class="text-muted">45 min ago</small>
                                    </div>
                                    <p class="mb-2">Project meeting with john @10:15am</p>
                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                        <div class="d-flex align-items-center flex-wrap">
                                            <div class="avatar avatar-sm me-2">
                                                <img src="../../assets/img/avatars/1.png" alt="Avatar"
                                                    class="rounded-circle" />
                                            </div>
                                            <div>
                                                <p class="small fw-medium mb-0">Lester McCarthy (Client)</p>
                                                <small>CEO of Pixinvent</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-info"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">Create a new project for client</h6>
                                        <small class="text-muted">2 Day Ago</small>
                                    </div>
                                    <p class="mb-2">6 team members in a project</p>
                                    <ul class="list-group list-group-flush">
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap p-0">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <ul
                                                    class="list-unstyled users-list d-flex align-items-center avatar-group m-0 me-2">
                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top" title="Vinnie Mostowy"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="../../assets/img/avatars/5.png"
                                                            alt="Avatar" />
                                                    </li>
                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top" title="Allen Rieske"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="../../assets/img/avatars/12.png"
                                                            alt="Avatar" />
                                                    </li>
                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top" title="Julee Rossignol"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="../../assets/img/avatars/6.png"
                                                            alt="Avatar" />
                                                    </li>
                                                    <li class="avatar">
                                                        <span class="avatar-initial rounded-circle pull-up text-heading"
                                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                            title="3 more">+3</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-info"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-3">
                                        <h6 class="mb-0">Create a new project for client</h6>
                                        <small class="text-muted">2 Day Ago</small>
                                    </div>
                                    <p class="mb-2">6 team members in a project</p>
                                    <ul class="list-group list-group-flush">
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center flex-wrap p-0">
                                            <div class="d-flex align-items-center flex-wrap">
                                                <ul
                                                    class="list-unstyled users-list d-flex align-items-center avatar-group m-0 me-2">
                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top" title="Vinnie Mostowy"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="../../assets/img/avatars/5.png"
                                                            alt="Avatar" />
                                                    </li>
                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top" title="Allen Rieske"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="../../assets/img/avatars/12.png"
                                                            alt="Avatar" />
                                                    </li>
                                                    <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-bs-placement="top" title="Julee Rossignol"
                                                        class="avatar pull-up">
                                                        <img class="rounded-circle" src="../../assets/img/avatars/6.png"
                                                            alt="Avatar" />
                                                    </li>
                                                    <li class="avatar">
                                                        <span class="avatar-initial rounded-circle pull-up text-heading"
                                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                            title="3 more">+3</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--/ Activity Timeline --> --}}




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
            <script src="{{ asset('demo2/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
            <script src="{{ asset('demo2/assets/js/app-teknisi-dashboard-chart.js') }}"></script>
        @endpush
