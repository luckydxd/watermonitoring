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
            <!-- Statistics -->
            <div class="col-sm-6 col-xl-8">
                <div class="card h-100">
                    <div class="card-body" style="height: 170px;">

                        {{-- =================================================================== --}}
                        {{-- KONDISI 1: JIKA USER MEMILIKI SETIDAKNYA SATU PERANGKAT TERDAFTAR --}}
                        {{-- =================================================================== --}}
                        @if ($hasDevice)
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span class="text-heading">Status Perangkat Anda</span>
                                    <div class="d-flex align-items-center my-1">
                                        {{-- Judul utama widget, menampilkan total perangkat --}}
                                        <h3 class="mb-0 me-2">{{ $totalDevicesCount }}</h3>
                                        <span class="text-heading">Perangkat Terdaftar</span>

                                        <small class="text-muted">&nbsp;( {{ $onlineDevicesCount }} online)</small>
                                    </div>
                                    {{-- Tampilkan jumlah yang online sebagai sub-teks --}}
                                </div>
                                <div class="avatar">
                                    {{-- Ikon berubah berdasarkan status online/offline --}}
                                    @if ($onlineDevicesCount > 0)
                                        <span class="avatar-initial bg-label-success rounded">
                                            <i class="ti ti-cpu ti-30px"></i>
                                        </span>
                                    @else
                                        <span class="avatar-initial bg-label-danger rounded">
                                            <i class="ti ti-cpu-off ti-30px"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Alert notifikasi yang dinamis di bagian bawah widget --}}
                            @if ($onlineDevicesCount > 0)
                                {{-- Alert jika ada perangkat yang online --}}
                                <div class="alert alert-outline-success d-flex align-items-center mt-3" role="alert">
                                    <span class="alert-icon me-2 rounded"><i class="ti ti-check"></i></span>
                                    <span>
                                        Anda memiliki <strong>{{ $onlineDevicesCount }} dari
                                            {{ $totalDevicesCount }}</strong> perangkat yang terhubung dan aktif.
                                    </span>
                                </div>
                            @else
                                {{-- Alert jika SEMUA perangkat offline --}}
                                <div class="alert alert-solid-danger d-flex align-items-center mt-3" role="alert">
                                    <span class="alert-icon me-2 rounded"><i class="ti ti-alert-triangle"></i></span>
                                    <span>
                                        Peringatan! Semua (<strong>{{ $totalDevicesCount }}</strong>) perangkat Anda sedang
                                        offline.
                                    </span>
                                </div>
                            @endif

                            {{-- =================================================================== --}}
                            {{-- KONDISI 2: JIKA USER TIDAK MEMILIKI PERANGKAT SAMA SEKALI --}}
                            {{-- =================================================================== --}}
                        @else
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="content-left">
                                    <span class="text-heading">Status Perangkat</span>
                                    <div class="d-flex align-items-center my-1">
                                        <h3 class="mb-0 me-2">0</h3>
                                        <span class="text-heading">Perangkat Terdaftar</span>
                                    </div>
                                    <small class="text-muted">Tidak ada perangkat ditemukan</small>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial bg-label-secondary rounded">
                                        <i class="ti ti-device-desktop-off ti-30px"></i>
                                    </span>
                                </div>
                            </div>

                            {{-- Alert notifikasi untuk user tanpa perangkat --}}
                            <div class="alert alert-solid-warning d-flex align-items-center mt-3" role="alert">
                                <span class="alert-icon me-2 rounded"><i class="ti ti-info-circle"></i></span>
                                <span>Anda belum memiliki perangkat. Hubungi administrator untuk registrasi.</span>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            <!--/ Line Area Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Pembaruan Terakhir</h5>
                            <p class="card-subtitle my-0">Monitor Penggunaan</p>
                        </div>
                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-calendar"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" id="dateFilterDropdown">
                                <li><a href="#" class="dropdown-item" data-range="today">Hari Ini</a></li>
                                <li><a href="#" class="dropdown-item" data-range="yesterday">Kemarin</a></li>
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
