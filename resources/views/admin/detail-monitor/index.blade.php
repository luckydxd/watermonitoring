@extends('layouts.app')

@section('title', 'Detail Water Usage')

@section('content')
    @php
        $monitorRoute = auth()->user()->hasRole('admin') ? 'admin.monitor' : 'teknisi.monitor';

    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="mb-4">
                <a href="{{ route($monitorRoute) }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i> Back to Main Page
                </a>
            </div>

            <div class="row g-6">
                {{-- Info User card --}}
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
                {{-- End of Info User card --}}

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
                                            <div class="badge bg-label-info me-4 rounded p-2"><i
                                                    class="ti ti-users ti-lg"></i>
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

                <!-- Line Area Chart -->
                <div class="col-12 row-md-6">
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
                                        <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last
                                            30
                                            Days</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);"
                                            class="dropdown-item d-flex align-items-center">Current
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

                <!-- Water Usage Line Chart -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <h5 class="card-title mb-0">Last Updates</h5>
                                <p class="card-subtitle my-0">Commercial Networks</p>
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
                                        <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last
                                            30
                                            Days</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);"
                                            class="dropdown-item d-flex align-items-center">Current
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
                            <canvas height="280px" id="chartSaya"></canvas>
                        </div>
                    </div>
                </div>
                <!-- /Water Usage Line Chart -->


                <div class="card">
                    <div class="card-header">
                        <h4>Water Usage: {{ $user->name }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table" id="user-usage-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Total Konsumsi (Liter)</th>
                                    <th>Device</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->waterConsumptionLogs as $log)
                                    <tr>
                                        <td>{{ $log->date->format('d M Y') }}</td>
                                        <td>{{ $log->total_consumption }}</td>
                                        <td>{{ $log->device->unique_id ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
        <script src="{{ asset('demo2/assets/vendor/libs/chartjs/chartjs.js') }}"></script>
        <script src="{{ asset('demo2/assets/js/app-detail-monitor-chart.js') }}"></script>
    @endpush
@endsection
