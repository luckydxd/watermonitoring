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
                                <a href="{{ route('admin.report-usage') }}" class="btn btn-primary">Penggunaan</a>
                                <a href="{{ route('admin.complaint') }}" class="btn btn-secondary">Keluhan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Widget Welcome -->

            <!-- Statistics -->
            <div class="col-xl-8 col-md-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">Statistik</h5>
                        <small class="text-muted">Terakhir diperbarui {{ now()->diffForHumans() }}</small>
                    </div>

                    <div class="card-body d-flex align-items-end">
                        <div class="w-100">
                            <div class="row gy-3">
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-primary me-4 rounded p-4">
                                            <i class="ti ti-users ti-lg"></i>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">{{ $totalUsers }}</h5>
                                            <small>Total Pengguna</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-success me-3 rounded p-4">
                                            <i class="ti ti-user-check ti-lg"></i>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">{{ $activeUsers }}</h5>
                                            <small>Pelanggan Aktif</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-info me-3 rounded p-4">
                                            <i class="ti ti-power ti-lg"></i>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">{{ $activeDevices }} </h5>
                                            <small><span style="margin-right: 2px;">Alat</span> Aktif</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-label-warning me-3 rounded p-4">
                                            <i class="ti ti-alert-circle ti-lg"></i>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="mb-0">{{ $totalComplaints }}</h5>
                                            <small>Total Keluhan</small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Statistics -->

            <!-- Widget Konsumsi Bulan Ini -->
            <div class="col-xl-4 col-md-12">
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

            <div class="col-xl-4 col-md-12">
                <div class="card" style="height: 160px">
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

            <div class="col-xl-4 col-md-12">
                <div class="card" style="height: 160px">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Penggunaan Tertinggi</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">
                                        {{ strlen($topUser->name) > 15 ? substr($topUser->name, 0, 7) . '...' : $topUser->name }}
                                        <small class="text-muted">({{ $topUser->total_consumption }} L)</small>
                                    </h4>
                                    <p class="mb-0">
                                        @if (isset($topUser->percentage))
                                            @if ($topUser->percentage > 0)
                                                <span class="text-danger">(+{{ $topUser->percentage }}%)</span>
                                            @elseif($topUser->percentage < 0)
                                                <span class="text-success">({{ $topUser->percentage }}%)</span>
                                            @else
                                                <span class="text-muted">(0%)</span>
                                            @endif
                                        @else
                                            <span class="text-muted">(-)</span>
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

            {{-- <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Update Terkini</h5>
                            <p class="card-subtitle my-0">Monitor Landingpage</p>
                        </div>

                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-calendar"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" id="dateFilterDropdown">
                                <li><a href="#" class="dropdown-item" data-range="last7">7 Hari Terakhir</a></li>
                                <li><a href="#" class="dropdown-item" data-range="last30">30 Hari Terakhir</a></li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li><a href="#" class="dropdown-item" data-range="thisMonth">Bulan Ini</a></li>
                                <li><a href="#" class="dropdown-item" data-range="lastMonth">Bulan Lalu</a></li>
                            </ul>

                        </div>
                    </div>
                    <div class="card-body">
                        <div id="lineAreaChart"data-chart='@json($chartData)'></div>
                    </div>
                </div>
            </div> --}}
            <!-- /Line Area Chart Monitor Landingpage -->

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Pemantauan Konsumsi Air</h5>
                            <p class="card-subtitle my-0">Total pemakaian air seluruh pelanggan</p>
                        </div>
                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-calendar"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" id="dateFilterDropdown"
                                data-url="{{ route('api.admin.water_usage.data') }}">
                                <li><a href="#" class="dropdown-item" data-range="last7">7 Hari Terakhir</a></li>
                                <li><a href="#" class="dropdown-item" data-range="last30">30 Hari Terakhir</a></li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li><a href="#" class="dropdown-item" data-range="thisMonth">Bulan Ini</a></li>
                                <li><a href="#" class="dropdown-item" data-range="lastMonth">Bulan Lalu</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="lineAreaChart" data-chart='@json($chartData)'></div>
                    </div>
                </div>
            </div>


            <!-- Donut Chart Card Gabungan -->
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Statistik Sistem</h5>
                            <p class="card-subtitle my-0">Distribusi status keluhan & perangkat</p>
                        </div>
                        <div class="dropdown d-none d-sm-flex">
                            {{-- <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-calendar"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="javascript:void(0);" class="dropdown-item">Today</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item">Yesterday</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item">Last 7 Days</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item">Last 30 Days</a></li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                                <li><a href="javascript:void(0);" class="dropdown-item">Current Month</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item">Last Month</a></li>
                            </ul> --}}
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <h6 class="mb-2 text-center">Status Keluhan</h6>
                                <div id="donutChart1" data-chart='@json($complaintStatusCounts)'></div>
                            </div>

                            <div class="col-md-6 col-12">
                                <h6 class="mb-2 text-center">Status Perangkat</h6>
                                <div id="donutChart2" data-chart='@json($deviceStatusCounts)'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Donut Chart Card Gabungan -->


            {{-- <!-- Bar Chart -->
            <div class="col-md-6 col-12 mb-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-subtitle mb-1 mt-0">Balance</p>
                            <h5 class="card-title mb-0">9999</h5>
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
                                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last
                                        7
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
                        <div id="horizontalBarChart"></div>
                    </div>
                </div>
            </div>
            <!-- /Bar Chart --> --}}


            <!-- Activity Timeline -->
            <div class="col-xxl-6 order-xl-0 order-2 mb-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title d-flex align-items-center m-0 mb-2 me-2 pt-1">
                            <i class="ti ti-list-details me-3"></i> Log Aktivitas
                        </h5>
                    </div>
                    <div class="card-body pb-0">
                        <ul class="timeline mb-0">
                            @forelse ($latestActivities as $activity)
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-primary"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-3">
                                            <h6 class="mb-0">
                                                {{-- Jika causer ada, gunakan namanya. Jika tidak, tampilkan 'Sistem'. --}}
                                                {{ optional($activity->causer)->name ?? 'Sistem' }}
                                            </h6>
                                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                        </div>

                                        <p class="mb-2">{{ $activity->description }}</p>

                                        {{-- Tampilkan detail pelaku HANYA JIKA causer-nya ADA --}}
                                        @if ($activity->causer)
                                            <div class="d-flex align-items-center flex-wrap">

                                                {{-- ================= AWAL LOGIKA AVATAR ANDA ================= --}}
                                                <div class="avatar avatar-sm me-2">
                                                    @if ($activity->causer->userData && $activity->causer->userData->image)
                                                        {{-- JIKA USER PUNYA GAMBAR --}}
                                                        <img class="rounded-circle"
                                                            src="{{ asset('storage/' . $activity->causer->userData->image) }}"
                                                            alt="Avatar">
                                                    @else
                                                        {{-- JIKA USER TIDAK PUNYA GAMBAR, BUAT INISIAL --}}
                                                        @php
                                                            $name =
                                                                optional($activity->causer->userData)->name ??
                                                                $activity->causer->name;
                                                            $words = explode(' ', trim($name));
                                                            $initials = '';
                                                            if (isset($words[0]) && !empty($words[0])) {
                                                                $initials .= strtoupper(substr($words[0], 0, 1));
                                                            }
                                                            if (count($words) > 1) {
                                                                $initials .= strtoupper(substr(end($words), 0, 1));
                                                            }
                                                            if (empty($initials)) {
                                                                $initials = 'NN';
                                                            }
                                                        @endphp
                                                        {{-- Tampilkan inisial dengan warna acak --}}
                                                        <span
                                                            class="avatar-initial rounded-circle bg-label-{{ ['success', 'danger', 'warning', 'info', 'primary'][array_rand(['success', 'danger', 'warning', 'info', 'primary'])] }}">
                                                            {{ $initials }}
                                                        </span>
                                                    @endif
                                                </div>
                                                {{-- ================= AKHIR LOGIKA AVATAR ANDA ================= --}}

                                                <div>
                                                    <p class="small fw-medium mb-0">
                                                        {{ optional($activity->causer->userData)->name ?? $activity->causer->name }}
                                                    </p>
                                                    <small>{{ $activity->causer->getRoleNames()->first() ?? 'User' }}</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-secondary"></span>
                                    <div class="timeline-event">
                                        <h6 class="mb-0">Tidak Ada Aktivitas</h6>
                                        <p class="mb-2">Belum ada aktivitas yang tercatat di sistem.</p>
                                    </div>
                                </li>
                            @endforelse

                            <li class="timeline-end-indicator">
                                <i class="ti ti-check"></i>
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
            <script src="{{ asset('demo2/assets/js/app-dashboard-chart.js') }}"></script>
            {{-- <script>
                window.complaintStatusData = @json($complaintStatusCounts);
                window.deviceStatusData = @json($deviceStatusCounts);
            </script> --}}


            <script>
                // Di bagian footer atau layout utama
                document.addEventListener('DOMContentLoaded', function() {
                    // Track contact clicks
                    document.querySelectorAll('.contact-button').forEach(button => {
                        button.addEventListener('click', function() {
                            fetch('/track-activity/contact', {
                                method: 'POST'
                            });
                        });
                    });

                    // Track login clicks
                    document.querySelectorAll('.login-button').forEach(button => {
                        button.addEventListener('click', function() {
                            fetch('/track-activity/login', {
                                method: 'POST'
                            });
                        });
                    });

                    // Track download clicks
                    document.querySelectorAll('.download-button').forEach(button => {
                        button.addEventListener('click', function() {
                            fetch('/track-activity/download', {
                                method: 'POST'
                            });
                        });
                    });
                });
            </script>
        @endpush
