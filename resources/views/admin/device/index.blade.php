@extends('layouts.app')

@section('title', 'Manajemen Alat')

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
                <div class="col-lg-4 col-sm-6">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial bg-label-primary rounded"><i
                                            class="ti ti-cpu ti-28px"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $totalDevices }}</h4>
                            </div>
                            <p class="mb-1">Jumlah Alat</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card card-border-shadow-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial bg-label-info rounded"><i
                                            class="ti ti-power ti-28px"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $activeDevices }}</h4>
                            </div>
                            <p class="mb-1">Alat Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="card card-border-shadow-danger h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial bg-label-danger rounded"><i
                                            class="ti ti-time-duration-off ti-28px"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $inactiveDevices }}</h4>
                            </div>
                            <p class="mb-1">Alat Tidak Aktif</p>
                        </div>
                    </div>
                </div>

                {{-- error widget --}}
                {{-- <div class="col-lg-3 col-sm-6">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial bg-label-warning rounded"><i
                                            class="ti ti-alert-triangle ti-28px"></i></span>
                                </div>
                                <h4 class="mb-0">{{ $maintenanceDevices }}</h4>
                            </div>
                            <p class="mb-1">Alat Butuh Perbaikan</p>
                        </div>
                    </div>
                </div> --}}

                <!--/ Card Border Shadow -->

                <!-- DataTable Section -->
                <div class="card table-responsive">
                    <h5 class="card-header text-md-start pb-0 text-center">Filter</h5>
                    <div class="card-datatable text-nowrap">
                        <div class="dt-action-buttons d-flex text-xl-end ...">
                            <select id="statusFilter" class="form-select text-capitalize ms-5 mt-2" style="width: 200px;">
                                <!-- Akan diisi otomatis -->
                            </select>
                            <select id="typeFilter" class="form-select text-capitalize ms-5 mt-2" style="width: 200px;">
                                <!-- Akan diisi otomatis -->
                            </select>


                        </div>

                        <h5 class="card-header text-md-start pb-0 text-center">Manajemen Alat</h5>
                        <table class="datatables-device table" id="devices-datatable"
                            data-url="{{ route('api.devices.index') }}">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">ID Unik</th>
                                    <th class="text-center">Jenis Alat</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Waktu Dibuat</th>
                                    @role('admin')
                                        <th class="text-center">Aksi</th>
                                    @endrole
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
                    <h5 id="offcanvasAddDeviceLabel" class="offcanvas-title">Tambah Data Alat</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-6">
                    <form class="add-new-device pt-0" id="addNewDeviceForm" onsubmit="return false">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="mb-6">
                            <label class="form-label" for="device_type_id">Jenis Alat</label>
                            <select id="device_type_id" name="device_type_id" class="form-select" required>
                                <option value="" disabled selected>Memuat...</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="unique_id">ID Unik</label>
                            <input type="text" class="form-control text-muted" id="unique_id" name="unique_id" required
                                readonly />
                            <small class="text-muted">ID unik akan otomatis setelah memilih jenis alat.</small>
                        </div>
                        <div class="mb-6">
                            {{-- <label class="form-label"for="status">Status</label> --}}
                            <select id="status" name="status" class="form-select" hidden>
                                <option value="" disabled>Pilih Status</option>
                                <option value="inactive" selected>Tidak Aktif</option>
                                <option value="active">Aktif</option>
                                <option value="error">Error</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary data-submit me-3">Simpan</button>
                        <button type="reset" class="btn btn-outline-secondary"
                            data-bs-dismiss="offcanvas">Batal</button>
                    </form>
                </div>
            </div>

            <!-- Offcanvas Edit Device -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditDevice"
                aria-labelledby="offcanvasEditDeviceLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasEditDeviceLabel" class="offcanvas-title">Edit Alat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-6">
                    <form id="editDeviceForm">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label for="edit_unique_id" class="form-label">ID Unik</label>
                            <input type="text" class="form-control" id="edit_unique_id" name="unique_id" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_device_type_id" class="form-label">Jenis Alat</label>
                            <select class="form-select" id="edit_device_type_id" name="device_type_id" disabled>
                                {{-- Opsi akan diisi otomatis melalui JavaScript --}}
                                <option value="" disabled selected>Memuat...</option>
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

                        <button type="submit" class="btn btn-primary me-3">Simpan</button>
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="offcanvas">Batal</button>
                    </form>
                </div>

            </div>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasGenerateQRCode"
                aria-labelledby="offcanvasGenerateQRCodeLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasGenerateQRCodeLabel" class="offcanvas-title">QR Code Perangkat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body h-100 mx-0 flex-grow-0 p-6">
                    <div class="mb-3">
                        <label for="qr_unique_id_display" class="form-label">ID Unik Perangkat</label>
                        <input type="text" class="form-control" id="qr_unique_id_display" readonly>
                        <small class="text-muted">Ini adalah ID unik yang akan dienkripsi dalam QR Code.</small>
                    </div>

                    <div class="mb-3 text-center">
                        <div id="qrcode" class="d-flex justify-content-center align-items-center"
                            style="min-height: 200px;">
                            <p class="text-muted">QR Code.</p>
                        </div>
                    </div>


                    <button type="button" class="btn btn-success mt-4" id="downloadQrBtn" style="display: none;">
                        <i class="ti ti-download ti-xs"></i>Unduh QR Code
                    </button>

                    <button type="button" class="btn btn-outline-secondary ms-2 mt-4"
                        data-bs-dismiss="offcanvas">Tutup</button>

                </div>
            </div>

            @push('scripts')
                <script src="{{ asset('demo2/assets/js/app-device.js') }}"></script>
                <script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs/qrcode.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
                <script>
                    const currentUserRole = @json(auth()->user()->getRoleNames()->first());
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const offcanvasGenerateQRCode = document.getElementById('offcanvasGenerateQRCode');
                        const qrUniqueIdDisplay = document.getElementById('qr_unique_id_display');
                        const qrcodeDiv = document.getElementById('qrcode');
                        const downloadQrBtn = document.getElementById('downloadQrBtn');

                        // Fungsi untuk handle klik tombol QR di datatables
                        document.body.addEventListener('click', function(event) {
                            const button = event.target.closest('.btn-qr-code');
                            if (button) {
                                const uniqueId = button.dataset.uniqueId; // Ambil unique_id dari data-attribute

                                qrUniqueIdDisplay.value = uniqueId;
                                qrcodeDiv.innerHTML = '<p class="text-muted">Membuat QR Code...</p>';
                                downloadQrBtn.style.display = 'none';

                                // Langsung generate QR Code saat offcanvas muncul
                                // Memberi sedikit waktu agar offcanvas dapat terbuka dan elemen siap
                                setTimeout(() => {
                                    generateQrCode(uniqueId);
                                }, 100);
                            }
                        });

                        // Fungsi untuk generate QR Code
                        function generateQrCode(uniqueId) {
                            if (!uniqueId) {
                                qrcodeDiv.innerHTML = '<p class="text-danger">ID unik tidak tersedia.</p>';
                                return;
                            }

                            const qrContent = uniqueId;

                            qrcodeDiv.innerHTML = ''; // Bersihkan QR Code sebelumnya
                            new QRCode(qrcodeDiv, {
                                text: qrContent,
                                width: 200,
                                height: 200,
                                colorDark: "#000000",
                                colorLight: "#ffffff",
                                correctLevel: QRCode.CorrectLevel.H
                            });

                            downloadQrBtn.style.display = 'block';

                            // Memberi sedikit waktu agar QR Code ter-render ke canvas
                            setTimeout(() => {
                                const canvas = qrcodeDiv.querySelector('canvas');
                                if (canvas) {
                                    const imgData = canvas.toDataURL('image/png');
                                    const {
                                        jsPDF
                                    } = window.jspdf;
                                    const doc = new jsPDF({
                                        orientation: 'portrait',
                                        unit: 'px',
                                        format: [250, 270]
                                    });

                                    const imgWidth = 200;
                                    const imgHeight = 200;
                                    const pageWidth = doc.internal.pageSize.getWidth();
                                    const pageHeight = doc.internal.pageSize.getHeight();
                                    const x = (pageWidth - imgWidth) / 2;
                                    const y = (pageHeight - imgHeight) / 2;

                                    doc.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);

                                    doc.setFontSize(12);
                                    doc.text(`SR: ${uniqueId}`, pageWidth / 2, y + imgHeight +
                                        20, {
                                            align: 'center'
                                        });

                                    const fileName =
                                        `QR_Device_${uniqueId}.pdf`; // Nama file langsung dari unique_id
                                    doc.save(fileName);
                                } else {
                                    console.error("Elemen canvas QR Code tidak ditemukan.");
                                    downloadQrBtn.style.display = 'none';
                                }
                            }, 100);
                        }
                    });
                </script>
            @endpush
        @endsection
