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
            <!-- View sales -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-7">
                            <div class="card-body text-nowrap">
                                <h5 class="card-title mb-0">Congratulations John! 🎉</h5>
                                <p class="mb-2">Best seller of the month</p>
                                <h4 class="text-primary mb-1">$48.9k</h4>
                                <a href="javascript:;" class="btn btn-primary">View Sales</a>
                            </div>
                        </div>
                        <div class="col-5 text-sm-left text-center">
                            <div class="card-body px-md-4 px-0 pb-0">
                                <img src="../../assets/img/illustrations/card-advance-sale.png" height="140"
                                    alt="view sales" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- View sales -->

            <!-- Statistics -->
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
            <!--/ Statistics -->

            <div class="col-12 mb-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Last updates</h5>
                            <p class="card-subtitle my-0">Commercial networks</p>
                        </div>
                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-calendar"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center">Yesterday</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7
                                        Days</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30
                                        Days</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Current
                                        Month</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last
                                        Month</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="lineAreaChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Line Area Chart -->

            <!-- Donut Chart -->
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Expense Ratio</h5>
                            <p class="card-subtitle my-0">Spending on various categories</p>
                        </div>
                        <div class="dropdown d-none d-sm-flex">
                            <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-calendar"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center">Yesterday</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7
                                        Days</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30
                                        Days</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Current
                                        Month</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last
                                        Month</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="donutChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Donut Chart -->

            <!-- Bar Chart -->
            <div class="col-md-6 col-12 mb-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-subtitle mb-1 mt-0">Balance</p>
                            <h5 class="card-title mb-0">$74,382.72</h5>
                        </div>
                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-calendar"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center">Today</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center">Yesterday</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7
                                        Days</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30
                                        Days</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Current
                                        Month</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last
                                        Month</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="horizontalBarChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Bar Chart -->

            <!-- Activity Timeline -->
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
            <!--/ Activity Timeline -->




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
            <script src="{{ asset('demo2/assets/js/charts-apex.js') }}"></script>
        @endpush
