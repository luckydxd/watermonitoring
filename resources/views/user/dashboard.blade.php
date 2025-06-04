@extends('layouts.app')

@section('title', 'Dashboard')


@section('content')


    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">

            <!-- Welcome User -->
            @livewire('today-usage-card')
            <!-- Welcome User -->

            <!-- Statistics -->
            <div class="col-sm-6 col-xl-8">
                <div class="card h-100">
                    <div class="card-body" style="height: 170px">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Status Alat Anda Saat Ini:</span>
                                <div class="d-flex align-items-center my-1">
                                    @if ($isOffline)
                                        <span class="badge bg-danger">Offline</span>
                                    @else
                                        <span class="badge bg-success">Online</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    Data terakhir diperbarui:
                                    {{ $lastUpdated ?? 'Belum ada data' }}
                                </small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-label-primary rounded">
                                    <i class="ti ti-cpu ti-30px"></i>
                                </span>
                            </div>
                        </div>
                        @if ($offlineTooLong)
                            <div class="alert alert-solid-warning d-flex align-items-center mt-3" role="alert">
                                <span class="alert-icon me-2 rounded">
                                    <i class="ti ti-alert-triangle"></i>
                                </span>
                                <span>Perangkat Anda tidak aktif selama lebih dari 24 jam.</span>
                            </div>
                        @elseif ($isOffline)
                            <div class="alert alert-outline-danger d-flex align-items-center mt-3" role="alert">
                                <span class="alert-icon me-2 rounded">
                                    <i class="ti ti-alert-triangle"></i>
                                </span>
                                <span>Perangkat Anda sedang offline.</span>
                            </div>
                        @else
                            <div class="alert alert-outline-success d-flex align-items-center mt-3" role="alert">
                                <span class="alert-icon me-2 rounded">
                                    <i class="ti ti-check"></i>
                                </span>
                                <span>Perangkat Anda terhubung dan aktif.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!--/ Statistics -->

            <!--/ Line Area Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Last updates</h5>
                            <p class="card-subtitle my-0">Monitor Penggunaan</p>
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
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last
                                        30
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

            <!-- Water Level Widget -->
            <div class="col-xl-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-header pb-2">
                        <h5 class="card-title mb-1" id="waterLevelValue">-</h5>
                        <p class="card-subtitle">Water Level</p>
                    </div>
                    <div class="card-body">
                        <div id="waterLevelChart"></div>
                        <div class="mt-3 text-center">
                            <small class="text-muted mt-3" id="waterLevelMessage">Memuat data...</small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Water Level Widget -->

            <!-- Turbidity Widget -->
            <div class="col-xl-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-header pb-2">
                        <h5 id="turbidityValue" class="card-title mb-1">Loading...</h5>
                        <p class="card-subtitle">Turbidity (NTU)</p>
                    </div>
                    <div class="card-body">
                        <div id="turbidityChart"></div>
                        <div class="mt-3 text-center">
                            <small class="text-muted mt-3">Terakhir diperbarui <span id="lastUpdated">-</span></small>
                        </div>
                    </div>
                </div>
            </div>



            <!--/ Content -->


        @endsection

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="{{ asset('demo2/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
            <script src="{{ asset('demo2/assets/vendor/libs/chartjs/chartjs.js') }}"></script>
            <script src="{{ asset('demo2/assets/js/app-user-dashboard-chart.js') }}"></script>
        @endpush
