const DeviceUrl = document.getElementById("devices-datatable").dataset.url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#devices-datatable").DataTable({
        processing: true,
        serverSide: true,
        dom:
            '<"row"' +
            '<"col-md-2"<"ms-n2"l>>' +
            '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0"fB>>' +
            ">" +
            '<"table-responsive"t>' +
            '<"row"' +
            '<"col-sm-12 col-md-6"i>' +
            '<"col-sm-12 col-md-6"p>' +
            ">",
        ajax: DeviceUrl,
        columnDefs: [
            {
                targets: 0, // Kolom "No"
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                },
                orderable: false,
                searchable: false,
            },
            {
                targets: 2,
                render: (data, type, full, meta) =>
                    full.device_type?.name ?? "N/A",
                name: "deviceType.name", // Pastikan ini benar untuk filtering relasi
            },
            {
                targets: 3, // Kolom "status" (indeks 3)
                render: function (data, type, full, meta) {
                    // Logika rendering badge status
                    let badgeClass = "";
                    switch (full.status) {
                        case "active":
                            badgeClass = "bg-label-success";
                            break;
                        case "inactive":
                            badgeClass = "bg-label-warning";
                            break;
                        case "error":
                            badgeClass = "bg-label-danger";
                            break;
                        default:
                            badgeClass = "bg-label-secondary";
                            break;
                    }
                    return `<span class="badge ${badgeClass}">${
                        full.status.charAt(0).toUpperCase() +
                        full.status.slice(1)
                    }</span>`;
                },
                name: "status", // Penting untuk searching/sorting berdasarkan kolom status
            },
            {
                targets: 4, // Kolom "createdAt" (indeks 4)
                render: function (data, type, full, meta) {
                    // Format timestamp created_at atau last_seen_at
                    const timestamp = full.created_at; // Atau full.last_seen_at
                    if (type === "display" || type === "filter") {
                        if (!timestamp) return "-";
                        const date = new Date(timestamp);
                        return date.toLocaleDateString("id-ID", {
                            day: "2-digit",
                            month: "2-digit",
                            year: "numeric",
                            hour: "2-digit",
                            minute: "2-digit",
                        });
                    }
                    return data;
                },
                name: "created_at", // Penting untuk searching/sorting
            },
            {
                targets: -1,
                render: function (data, type, full, meta) {
                    const deviceId = full.id; // Assuming 'id' is the primary key (UUID)
                    const uniqueId = full.unique_id;
                    return `
              
                    <div class="btn-list">
                          <button class="btn btn-outline-dark btn-qr-code"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasGenerateQRCode"
                            data-unique-id="${uniqueId}">
                        <i class="ti ti-qrcode"></i>
                    </button>
                    <button class="btn btn-info btn-edit-device" data-id="${deviceId}">
                        <i class="ti ti-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-delete" data-id="${deviceId}">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
                `;
                    return btn;
                },
            },
        ],
        columns: [
            { data: "id" }, // 0: Akan dirender oleh targets: 0
            { data: "unique_id", name: "unique_id" }, // 1: Ini adalah kolom unique_id dari tabel devices
            { data: "device_type.name", name: "deviceType.name" },
            { data: "status" }, // 3: Akan dirender oleh targets: 3 (badge status)
            { data: "created_at" }, // 4: Akan dirender oleh targets: 4 (formatted timestamp)
            { data: "id" }, // 5: Akan dirender oleh targets: 5 (actions buttons)
        ],

        language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Cari...",
            paginate: {
                next: '<i class="ti ti-chevron-right ti-sm"></i>',
                previous: '<i class="ti ti-chevron-left ti-sm"></i>',
            },
        },
        buttons: [
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block mx-4">Tambah Data Alat</span>',
                className:
                    "add-new btn btn-primary mx-4 waves-effect waves-light",
                attr: {
                    "data-bs-toggle": "offcanvas",
                    "data-bs-target": "#offcanvasAddDevice",
                },
            },
        ],
        initComplete: function () {
            const api = this.api();

            api.columns(2).every(function () {
                const column = this;

                const select = $("#typeFilter")
                    .empty()
                    .append('<option value="">Jenis Alat</option>');

                column
                    .data()
                    .unique()
                    .sort()
                    .each(function (d) {
                        if (d) {
                            select.append(
                                '<option value="' + d + '">' + d + "</option>"
                            );
                        }
                    });

                $("#typeFilter").on("change", function () {
                    const val = $.fn.dataTable.util.escapeRegex($(this).val());
                    column
                        .search(val ? "^" + val + "$" : "", true, false)
                        .draw();
                });
            });
            // ===== Filter untuk STATUS (kolom ke-3) =====
            api.columns(3).every(function () {
                const column = this;

                const select = $("#statusFilter")
                    .empty()
                    .append('<option value="">Pilih Status</option>');

                column
                    .data()
                    .unique()
                    .sort()
                    .each(function (d) {
                        if (d) {
                            select.append(
                                '<option value="' +
                                    d +
                                    '">' +
                                    d.charAt(0).toUpperCase() +
                                    d.slice(1) +
                                    "</option>"
                            );
                        }
                    });

                $("#statusFilter").on("change", function () {
                    const val = $.fn.dataTable.util.escapeRegex($(this).val());
                    column
                        .search(val ? "^" + val + "$" : "", true, false)
                        .draw();
                });
            });
        },
    });

    function initTypeFilter() {
        // Ambil data jenis device dari server
        $.ajax({
            url: "/api/devices/types-datatables", // Endpoint baru yang akan kita buat
            type: "GET",
            success: function (data) {
                const select = $("#typeFilter")
                    .empty()
                    .append('<option value="">Pilih Jenis Alat</option>');

                // Urutkan data sebelum menambahkannya ke select
                data.sort().forEach(function (d) {
                    if (d) {
                        select.append(`<option value="${d}">${d}</option>`);
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error("Gagal memuat jenis alat:", error);
            },
        });
    }

    // Panggil fungsi untuk inisialisasi filter
    initTypeFilter();

    // Event listener untuk filter
    $("#typeFilter").on("change", function () {
        const val = $(this).val();

        // Gunakan API DataTables untuk mencari di kolom ke-2 (indeks) dan gambar ulang tabel
        table.column(2).search(val).draw();
    });

    $(document).ready(function () {
        // --- Referensi Elemen Global ---
        // Offcanvas QR Code
        const offcanvasGenerateQRCode = $("#offcanvasGenerateQRCode");
        const qrUniqueIdDisplay = $("#qr_unique_id_display");
        const qrcodeDiv = $("#qrcode");
        const downloadQrBtn = $("#downloadQrBtn");

        // Offcanvas Add Device
        const offcanvasAddDevice = $("#offcanvasAddDevice");
        const addNewDeviceForm = $("#addNewDeviceForm");
        const addDeviceIdInput = $("#unique_id"); // Input ID Unik di form tambah
        const addDeviceTypeIdSelect = $("#device_type_id"); // Select Jenis Alat di form tambah
        const addDeviceStatusSelect = $("#status"); // Select Status di form tambah

        // --- FUNGSI-FUNGSI UTAMA ---

        // Fungsi untuk memuat jenis alat ke dropdown di form tambah
        function loadDeviceTypesForAddForm() {
            $.ajax({
                url: "/api/devices/types", // Pastikan URL ini benar
                method: "GET",
                dataType: "json",
                success: function (data) {
                    addDeviceTypeIdSelect
                        .empty()
                        .append(
                            '<option value="" disabled selected>Pilih Jenis Alat</option>'
                        );
                    // Loop melalui setiap tipe dan tambahkan data-code
                    data.forEach(function (type) {
                        // --- PERBAIKAN KUNCI #1 ---
                        // Menambahkan atribut data-code="${type.code}" ke dalam elemen <option>
                        const option = `<option value="${type.id}" data-code="${type.code}">${type.name}</option>`;
                        addDeviceTypeIdSelect.append(option);
                    });
                },
                error: function () {
                    Notiflix.Notify.failure("Gagal memuat jenis alat.");
                },
            });
        }

        // Fungsi untuk menggenerate preview unique_id di form tambah
        function generateUniqueIdPreview() {
            const selectedOption =
                addDeviceTypeIdSelect.find("option:selected");

            if (selectedOption.length && selectedOption.val()) {
                // --- PERBAIKAN KUNCI #2 ---
                // Mengambil kode dari atribut data-code yang sudah kita sisipkan
                const deviceTypeCode = selectedOption.data("code");

                // Pastikan deviceTypeCode tidak undefined sebelum membuat ID
                if (deviceTypeCode) {
                    const now = new Date();
                    const year = String(now.getFullYear()).slice(-2);
                    const month = String(now.getMonth() + 1).padStart(2, "0");
                    const deviceVersion = "1";
                    const previewSerial = "XXX"; // Biarkan ini sebagai placeholder visual

                    const previewUniqueId = `${year}${month}${deviceTypeCode}${deviceVersion}${previewSerial}`;
                    addDeviceIdInput.val(previewUniqueId);
                } else {
                    addDeviceIdInput.val(""); // Kosongkan jika kode tidak ditemukan
                }
            } else {
                addDeviceIdInput.val(""); // Kosongkan jika tidak ada yang dipilih
            }
        }

        // Fungsi untuk generate QR Code dan unduh PDF
        function generateQrCode(uniqueId) {
            if (!uniqueId) {
                qrcodeDiv.html(
                    '<p class="text-danger">ID unik tidak tersedia.</p>'
                );
                return;
            }

            const qrContent = uniqueId;

            qrcodeDiv.empty(); // Bersihkan QR Code sebelumnya
            new QRCode(qrcodeDiv[0], {
                // Perhatikan: jQuery object perlu diubah ke DOM element [0]
                text: qrContent,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H,
            });

            downloadQrBtn.css("display", "block"); // Tampilkan tombol download

            // Memberi sedikit waktu agar QR Code ter-render ke canvas
            setTimeout(() => {
                const canvas = qrcodeDiv.find("canvas")[0]; // Dapatkan elemen canvas
                if (canvas) {
                    const imgData = canvas.toDataURL("image/png");
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF({
                        orientation: "portrait",
                        unit: "px",
                        format: [250, 350], // Sesuaikan ukuran halaman jika perlu
                    });

                    const imgWidth = 200;
                    const imgHeight = 200;
                    const pageWidth = doc.internal.pageSize.getWidth();
                    const pageHeight = doc.internal.pageSize.getHeight();
                    const x = (pageWidth - imgWidth) / 2;
                    const y = (pageHeight - imgHeight) / 2;

                    doc.addImage(imgData, "PNG", x, y, imgWidth, imgHeight);

                    doc.setFontSize(12);
                    doc.text(
                        `ID Unik Perangkat: ${uniqueId}`,
                        pageWidth / 2,
                        y + imgHeight + 20,
                        { align: "center" }
                    );
                    doc.text(
                        "Scan QR Code ini untuk mendaftarkan perangkat.",
                        pageWidth / 2,
                        y + imgHeight + 40,
                        { align: "center" }
                    );

                    const fileName = `QR_Device_${uniqueId}.pdf`;
                    downloadQrBtn.off("click").on("click", function () {
                        // Pastikan event handler hanya dipasang sekali
                        doc.save(fileName);
                    });
                } else {
                    console.error("Elemen canvas QR Code tidak ditemukan.");
                    downloadQrBtn.css("display", "none");
                }
            }, 100);
        }

        // --- EVENT HANDLERS ---

        // Event listener untuk tombol QR di datatables (menggunakan event delegation)
        $(document).on("click", ".btn-qr-code", function () {
            const button = $(this); // jQuery object for the clicked button
            const uniqueId = button.data("unique-id"); // Ambil unique_id dari data-attribute

            // Set unique_id ke input display di offcanvas QR
            qrUniqueIdDisplay.val(uniqueId);

            // Reset QR code area dan sembunyikan tombol download
            qrcodeDiv.html('<p class="text-muted">Membuat QR Code...</p>');
            downloadQrBtn.css("display", "none");

            // Langsung generate QR Code saat offcanvas muncul
            // Bootstrap's data-bs-toggle will open the offcanvas
            // We use a small timeout to ensure the offcanvas is visually ready
            setTimeout(() => {
                generateQrCode(uniqueId);
            }, 100);
        });

        // Event listener saat offcanvas "Add Device" ditampilkan
        offcanvasAddDevice.on("show.bs.offcanvas", function () {
            addNewDeviceForm[0].reset();
            loadDeviceTypesForAddForm(); // Panggil fungsi yang sudah diperbaiki
            addDeviceIdInput.val("Pilih jenis alat untuk melihat preview..."); // Beri instruksi
            addDeviceIdInput.prop("readonly", true);
            addDeviceStatusSelect.val("inactive");

            // Gunakan .off() sebelum .on() untuk menghindari multiple event handlers
            addDeviceTypeIdSelect
                .off("change", generateUniqueIdPreview)
                .on("change", generateUniqueIdPreview);
        });

        // --- Handle Form Submission (Add New Device) ---
        addNewDeviceForm.submit(function (e) {
            e.preventDefault(); // Mencegah submit default form

            // Validasi minimal: pastikan jenis alat dan status terpilih
            if (!addDeviceTypeIdSelect.val()) {
                Notiflix.Notify.failure("Silakan pilih jenis alat.");
                return;
            }
            if (!addDeviceStatusSelect.val()) {
                Notiflix.Notify.failure("Silakan pilih status alat.");
                return;
            }

            Notiflix.Loading.standard("Menyimpan perangkat...");

            // unique_id tidak perlu dikirim karena akan digenerate di backend
            const formData = {
                device_type_id: addDeviceTypeIdSelect.val(),
                status: addDeviceStatusSelect.val(),
                unique_id: addDeviceIdInput.val(),
            };

            $.ajax({
                url: "/api/devices",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(formData),
                success: function (response) {
                    Notiflix.Loading.remove();
                    Notiflix.Notify.success(response.message);
                    addNewDeviceForm[0].reset(); // Reset form

                    // Refresh DataTable
                    $("#devices-datatable")
                        .DataTable()
                        .ajax.reload(null, false);

                    // Tutup offcanvas
                    bootstrap.Offcanvas.getInstance(
                        offcanvasAddDevice[0]
                    ).hide();
                },
                error: function (xhr) {
                    Notiflix.Loading.remove();
                    let errorMessage =
                        xhr.responseJSON?.message ||
                        "Gagal menambahkan perangkat. Silakan coba lagi.";

                    if (xhr.responseJSON?.errors) {
                        errorMessage = Object.values(
                            xhr.responseJSON.errors
                        ).join("<br>");
                    }

                    Notiflix.Notify.failure(errorMessage);
                },
            });
        });
    });
    // Device types loader function
    // function loadDeviceTypes() {
    //     $.ajax({
    //         url: "/api/devices/types",
    //         method: "GET",
    //         success: function (response) {
    //             const select = $("#device_type_id");
    //             select
    //                 .empty()
    //                 .append('<option value="" >Pilih Jenis Alat</option>');

    //             response.forEach(function (type) {
    //                 select.append(
    //                     $("<option>", {
    //                         value: type.id,
    //                         text: type.name,
    //                         "data-name": type.name,
    //                     })
    //                 );
    //             });
    //         },
    //         error: function () {
    //             $(selectId).html(
    //                 '<option value="" disabled selected>Fail to load device type</option>'
    //             );
    //         },
    //     });
    // }

    $(document).on("click", ".btn-edit-device", function () {
        const deviceId = $(this).data("id");
        console.log("Edit device ID:", deviceId);

        // Reset dulu form-nya
        $("#editDeviceForm")[0].reset();

        // Fetch data device
        $.ajax({
            url: `/api/devices/${deviceId}`,
            method: "GET",
            dataType: "json",
            success: function (response) {
                console.log("Data user diterima:", response);
                fillEditDeviceForm(response);
            },
            error: function (xhr) {
                alert("Gagal memuat data device");
                console.error(xhr.responseText);
            },
        });
    });

    let editOffcanvasInstance = null;
    // Fungsi untuk memuat tipe device ke select box
    function loadDeviceTypesForEdit() {
        return $.ajax({
            url: "/api/devices/types",
            method: "GET",
            success: function (response) {
                const select = $("#edit_device_type_id");
                select
                    .empty()
                    .append(
                        '<option value="" disabled selected>Pilih Tipe Device</option>'
                    );

                response.forEach(function (type) {
                    select.append(
                        $("<option>", {
                            value: type.id,
                            text: type.name,
                        })
                    );
                });
            },
            error: function () {
                $("#edit_device_type_id").html(
                    '<option value="" disabled selected>Gagal memuat tipe device</option>'
                );
            },
        });
    }

    // Fungsi untuk mengisi form edit dengan data device
    function fillEditDeviceForm(response) {
        console.log("Mengisi form edit dengan response:", response);

        // Ekstrak device data (handle nested response)
        const device = response.device || response;
        console.log("Device data:", device);

        // Pastikan select box device types sudah dimuat
        loadDeviceTypesForEdit().then(function () {
            console.log("Mengisi nilai form dengan data device:", device);

            $("#edit_id").val(device.id);
            $("#edit_unique_id").val(device.unique_id || "");
            $("#edit_device_type_id").val(device.type_id || "");
            $("#edit_status").val(device.status || "active");
            $("#edit_latitude").val(device.latitude || "");
            $("#edit_longitude").val(device.longitude || "");

            console.log("Form values after setting:", {
                id: $("#edit_id").val(),
                unique_id: $("#edit_unique_id").val(),
                type: $("#edit_device_type_id").val(),
                status: $("#edit_status").val(),
                latitude: $("#edit_latitude").val(),
                longitude: $("#edit_longitude").val(),
            });
        });
    }

    $(document).on("click", ".btn-edit-device", function () {
        const deviceId = $(this).data("id");
        console.log("Edit device ID:", deviceId);

        Notiflix.Loading.standard("Memuat data device...");

        $.ajax({
            url: `/api/devices/${deviceId}`,
            method: "GET",
            success: function (response) {
                Notiflix.Loading.remove();
                console.log("Data device diterima:", response);

                if (!response || (!response.device && !response.id)) {
                    console.error("Invalid device data structure:", response);
                    Notiflix.Notify.failure("Struktur data device tidak valid");
                    return;
                }

                fillEditDeviceForm(response);

                const editOffcanvas = new bootstrap.Offcanvas(
                    document.getElementById("offcanvasEditDevice")
                );
                editOffcanvas.show();
            },
            error: function (xhr) {
                Notiflix.Loading.remove();
                console.error("Error:", xhr.responseText);
                Notiflix.Notify.failure("Gagal memuat data device");
            },
        });
    });

    // Handle form submission
    $("#editDeviceForm").on("submit", function (e) {
        e.preventDefault();
        const id = $("#edit_id").val();

        const data = {
            unique_id: $("#edit_unique_id").val(),
            device_type_id: $("#edit_device_type_id").val(),
            status: $("#edit_status").val(),
            latitude: $("#edit_latitude").val(),
            longitude: $("#edit_longitude").val(),
        };

        // Validasi
        if (!data.device_type_id) {
            Notiflix.Notify.failure("Silakan pilih tipe device");
            return;
        }

        // Notiflix.Loading.standard("Menyimpan perubahan...");

        $.ajax({
            url: `/api/devices/${id}`,
            method: "PUT",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function (res) {
                Notiflix.Loading.remove();
                Notiflix.Notify.success(res.message || "Berhasil diperbarui!");

                // Tutup offcanvas
                bootstrap.Offcanvas.getInstance(
                    document.getElementById("offcanvasEditDevice")
                ).hide();

                // Refresh DataTable
                $("#devices-datatable").DataTable().ajax.reload(null, false);
            },
            error: function (xhr) {
                Notiflix.Loading.remove();
                let errorMessage = xhr.responseJSON?.message || "Update gagal!";

                if (xhr.responseJSON?.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join(
                        "<br>"
                    );
                }

                Notiflix.Notify.failure(errorMessage);
                console.error(xhr.responseText);
            },
        });
    });
    // Delete Device with Notiflix
    $(document).on("click", ".btn-delete", function () {
        const deviceId = $(this).data("id");

        Notiflix.Confirm.show(
            "Delete Device",
            "Are you sure you want to delete this device?",
            "Yes",
            "No",
            function okCb() {
                // Notiflix.Loading.standard("Deleting device...");

                fetch(`/api/devices/${deviceId}`, {
                    method: "DELETE",
                    headers: {
                        Authorization:
                            "Bearer " + localStorage.getItem("token"), // Pastikan ini mengambil token yang valid
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ), // Untuk web routes
                    },
                })
                    .then((res) => {
                        Notiflix.Loading.remove();

                        if (res.ok) {
                            Notiflix.Notify.success(
                                "Device deleted successfully."
                            );
                            table.ajax.reload(); // reload datatable
                        } else {
                            return res.json().then((data) => {
                                throw new Error(
                                    data.message || "Delete failed"
                                );
                            });
                        }
                    })
                    .catch((err) => {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.failure(`Error: ${err.message}`);
                    });
            },
            function cancelCb() {
                Notiflix.Notify.info("Delete canceled.");
            },
            {
                width: "300px",
                borderRadius: "6px",
            }
        );
    });
});

// Load device types for edit form
function loadDeviceTypes(selectedId = null) {
    return fetch("/api/device-types")
        .then((res) => res.json())
        .then((deviceTypes) => {
            const select = $("#edit_device_type_id");
            select
                .empty()
                .append(`<option value="">Pilih Tipe Perangkat</option>`);
            deviceTypes.forEach((type) => {
                const selected = type.id == selectedId ? "selected" : "";
                select.append(
                    `<option value="${type.id}" ${selected}>${type.name}</option>`
                );
            });
        })
        .catch(() => {
            Notiflix.Notify.failure("Gagal memuat device type");
        });
}
