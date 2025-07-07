const UserDeviceUrl = document.getElementById("user-devices-datatable").dataset
    .url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#user-devices-datatable").DataTable({
        processing: true,
        serverSide: false,
        ajax: UserDeviceUrl,
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
        columnDefs: [
            {
                // Format kolom Status
                targets: 3,
                render: function (data, type, row) {
                    let isOnline = false;
                    if (
                        row.last_seen_at &&
                        data &&
                        data.toLowerCase() === "active"
                    ) {
                        const diffMinutes =
                            (new Date() - new Date(row.last_seen_at)) / 60000;
                        if (diffMinutes <= 20) isOnline = true;
                    }
                    const badgeClass = isOnline
                        ? "bg-label-success"
                        : "bg-label-danger";
                    const statusText = isOnline ? "Online" : "Offline";
                    return `<span class="badge ${badgeClass}">${statusText}</span>`;
                },
            },
            {
                // Format kolom Tanggal
                targets: 4,
                render: function (data, type, row) {
                    return new Date(data).toLocaleDateString("id-ID", {
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                    });
                },
            },
            {
                targets: -1,
                render: function (data, type, row) {
                    const assignmentId = data;
                    const uniqueId = row.unique_id;

                    return `
                    <button class="btn btn-info btn-edit-device"  data-id="${assignmentId}">
                        <i class="ti ti-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-delete-device"  data-id="${assignmentId}" 
                        data-unique-id="${uniqueId}" >
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
                `;
                    return btn;
                },
            },
        ],
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                title: "No",
                orderable: false,
                searchable: false,
            },
            {
                data: "unique_id",
                name: "devices.unique_id",
                title: "Unique ID",
            },
            {
                data: "device_type_name",
                name: "device_types.name",
                title: "Jenis Alat",
            },
            { data: "status", name: "devices.status", title: "Status" },
            {
                data: "created_at",
                name: "device_assignments.created_at",
                title: "Tanggal Terdaftar",
            },
            {
                data: "id",
                name: "aksi",
                title: "Aksi",
                orderable: false,
                searchable: false,
            },
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
                extend: "collection",
                className:
                    "btn btn-label-secondary dropdown-toggle mx-4 waves-effect waves-light",
                text: '<i class="ti ti-upload me-2 ti-xs"></i>Ekspor',
                buttons: [
                    {
                        extend: "pdfHtml5",
                        text: '<i class="ti ti-file-type-pdf me-2"></i>PDF',
                        className: "dropdown-item",
                        orientation: "portrait",
                        pageSize: "A4",
                        filename: function () {
                            const now = new Date();
                            const year = now.getFullYear();
                            const month = (now.getMonth() + 1)
                                .toString()
                                .padStart(2, "0");
                            const day = now
                                .getDate()
                                .toString()
                                .padStart(2, "0");
                            return `Laporan Data Alat - ${day}-${month}-${year}`;
                        },
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4],
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (!inner) return "";
                                    const tempDiv =
                                        document.createElement("div");
                                    tempDiv.innerHTML = inner;
                                    const badge =
                                        tempDiv.querySelector(".badge");
                                    if (badge) return badge.textContent.trim();
                                    return (
                                        tempDiv.textContent ||
                                        tempDiv.innerText ||
                                        ""
                                    ).trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // --- Konfigurasi dan Gaya tidak diubah ---
                            doc.pageMargins = [40, 80, 40, 60];
                            doc.defaultStyle.fontSize = 10;
                            doc.defaultStyle.color = "#333";

                            doc.styles.companyName = {
                                fontSize: 10,
                                bold: true,
                                color: "#2c3e50",
                                alignment: "left",
                            };
                            doc.styles.companyAddress = {
                                fontSize: 9,
                                color: "#7f8c8d",
                                alignment: "left",
                            };
                            doc.styles.reportTitle = {
                                fontSize: 16,
                                bold: true,
                                color: "#34495e",
                                alignment: "center",
                                margin: [0, 15, 0, 15],
                            };
                            doc.styles.tableHeader = {
                                bold: true,
                                fontSize: 10,
                                color: "white",
                                fillColor: "#4a69bd",
                                alignment: "center",
                            };
                            doc.styles.tableBodyOdd = {
                                fontSize: 9,
                            };
                            doc.styles.tableBodyEven = {
                                fillColor: "#f5f6fa",
                                fontSize: 9,
                            };
                            doc.styles.footerText = {
                                fontSize: 8,
                                color: "#7f8c8d",
                                alignment: "center",
                            };

                            // --- Header (Kop Surat) Dokumen ---
                            doc.header = function (
                                currentPage,
                                pageCount,
                                pageSize
                            ) {
                                return {
                                    stack: [
                                        {
                                            text: "Sistem Pemantauan Konsumsi Air Rumah Tangga Berbasis Web",
                                            style: "companyName",
                                        },
                                        {
                                            text: "Perumahan Graha Panyindangan No.8A",
                                            style: "companyAddress",
                                        },
                                        {
                                            text: "https://swmp.024n.my.id/ | (021) 555-1234",
                                            style: "companyAddress",
                                            margin: [0, 0, 0, 15],
                                        },
                                        {
                                            canvas: [
                                                {
                                                    type: "line",
                                                    x1: 0,
                                                    y1: 5,
                                                    x2: pageSize.width - 80,
                                                    y2: 5,
                                                    lineWidth: 1.5,
                                                    lineColor: "#2c3e50",
                                                },
                                            ],
                                        },
                                        {
                                            canvas: [
                                                {
                                                    type: "line",
                                                    x1: 0,
                                                    y1: 2,
                                                    x2: pageSize.width - 80,
                                                    y2: 2,
                                                    lineWidth: 0.5,
                                                    lineColor: "#2c3e50",
                                                },
                                            ],
                                        },
                                    ],
                                    margin: [40, 20, 40, 0],
                                };
                            };

                            // --- Footer Dokumen ---
                            doc.footer = function (currentPage, pageCount) {
                                return {
                                    columns: [
                                        {
                                            text: "Dokumen ini valid dan dibuat oleh sistem secara otomatis.",
                                            alignment: "left",
                                            style: "footerText",
                                            margin: [40, 20, 0, 0],
                                        },
                                        {
                                            text: `Halaman ${currentPage} dari ${pageCount}`,
                                            alignment: "right",
                                            style: "footerText",
                                            margin: [0, 20, 40, 0],
                                        },
                                    ],
                                };
                            };

                            // --- Menyesuaikan Tabel Utama (tidak ada perubahan) ---
                            const table = doc.content.find((c) => c.table);
                            if (table) {
                                table.table.widths = [
                                    30,
                                    "*",
                                    "auto",
                                    "auto",
                                    "auto",
                                ];
                                table.table.body[0].forEach((cell) => {
                                    cell.style = "tableHeader";
                                    cell.margin = [0, 4, 0, 4];
                                });
                                table.table.body.forEach((row, i) => {
                                    if (i === 0) return;
                                    row.forEach((cell, j) => {
                                        cell.style =
                                            i % 2 === 0
                                                ? "tableBodyEven"
                                                : "tableBodyOdd";
                                        cell.border = [
                                            false,
                                            false,
                                            false,
                                            false,
                                        ];
                                        // DIUBAH: Menambahkan j === 1 untuk menengahkan kolom unique_id
                                        if (j === 0 || j === 1 || j === 3) {
                                            cell.alignment = "center";
                                        }
                                    });
                                });
                                table.layout = {
                                    hLineWidth: (i, node) =>
                                        i === 0 ||
                                        i === 1 ||
                                        i === node.table.body.length
                                            ? 1
                                            : 0,
                                    vLineWidth: (i, node) => 0,
                                    hLineColor: (i, node) =>
                                        i === 0 || i === 1
                                            ? "#34495e"
                                            : "#dfe6e9",
                                    hLineColor: (i, node) =>
                                        i === node.table.body.length
                                            ? "#34495e"
                                            : "#dfe6e9",
                                    paddingTop: (i, node) => 6,
                                    paddingBottom: (i, node) => 6,
                                };
                            }
                        },
                    },
                ],
            },
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Alat Baru</span>',
                className: "add-new btn btn-primary waves-effect waves-light",
                attr: {
                    "data-bs-toggle": "offcanvas",
                    "data-bs-target": "#offcanvasRegisterDevice",
                },
            },
        ],
    });

    $(function () {
        // Pastikan jQuery dan Notiflix sudah di-load di halaman Anda

        const registerForm = $("#registerDeviceForm");

        // Jika form tidak ada di halaman ini, hentikan eksekusi skrip
        if (registerForm.length === 0) {
            return;
        }

        const uniqueIdInput = $("#unique_id");
        const initialReadingWrapper = $("#initial-reading-wrapper");
        const offcanvasEl = document.getElementById("offcanvasRegisterDevice");
        const offcanvas = new bootstrap.Offcanvas(offcanvasEl);

        // Ambil URL dan Token dari atribut data- di form
        const registerUrl = registerForm.data("url");
        const csrfToken = registerForm.data("token");

        let debounceTimer;

        // Fungsi untuk menampilkan/menyembunyikan input meteran awal
        uniqueIdInput.on("input", function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const uniqueId = $(this).val().toUpperCase();
                if (uniqueId.includes("F")) {
                    initialReadingWrapper.slideDown();
                    $("#initial_meter_reading").prop("required", true);
                } else {
                    initialReadingWrapper.slideUp();
                    $("#initial_meter_reading").prop("required", false);
                }
            }, 500);
        });

        // Menangani submit form
        registerForm.on("submit", function (e) {
            e.preventDefault();

            Notiflix.Loading.standard("Mendaftarkan perangkat...");

            const formData = {
                unique_id: $("#unique_id").val(),
                initial_meter_reading: $("#initial_meter_reading").val(),
            };

            $.ajax({
                url: registerUrl, // Gunakan URL dari data-attribute
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken, // Gunakan Token dari data-attribute
                },
                contentType: "application/json",
                data: JSON.stringify(formData),
                success: function (response) {
                    Notiflix.Loading.remove();
                    Notiflix.Notify.success(
                        response.message || "Perangkat berhasil didaftarkan!"
                    );

                    offcanvas.hide();

                    // Refresh DataTable jika ada, jika tidak, reload halaman
                    if ($.fn.DataTable.isDataTable("#user-devices-datatable")) {
                        $("#user-devices-datatable")
                            .DataTable()
                            .ajax.reload(null, false);
                    } else {
                        setTimeout(() => location.reload(), 1500);
                    }
                },
                error: function (xhr) {
                    Notiflix.Loading.remove();
                    let errorMessage = "Terjadi kesalahan. Silakan coba lagi.";
                    if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || errorMessage;
                        if (xhr.responseJSON.errors) {
                            const errors = Object.values(
                                xhr.responseJSON.errors
                            )
                                .flat()
                                .join("<br>");
                            errorMessage += `<br><small class="text-danger">${errors}</small>`;
                        }
                    }
                    Notiflix.Report.failure(
                        "Pendaftaran Gagal",
                        errorMessage,
                        "Tutup",
                        { message_html: true }
                    );
                },
            });
        });

        // Reset form saat offcanvas ditutup
        offcanvasEl.addEventListener("hidden.bs.offcanvas", function () {
            registerForm[0].reset();
            initialReadingWrapper.hide();
        });
    });

    const editOffcanvasEl = document.getElementById("offcanvasEditDevice");
    const editOffcanvas = new bootstrap.Offcanvas(editOffcanvasEl);
    const editForm = $("#editDeviceForm");

    $(document).on("click", ".btn-edit-device", function () {
        const assignmentId = $(this).data("id");

        Notiflix.Loading.standard("Memuat data...");

        // Panggil API untuk mendapatkan data device assignment
        $.ajax({
            url: `/api/assign/${assignmentId}/edit`, // Route dari web.php
            method: "GET",
            success: function (response) {
                Notiflix.Loading.remove();

                // Isi form dengan data yang diterima
                $("#edit_assignment_id").val(response.id);
                $("#edit_unique_id_display").text(response.device.unique_id);
                $("#edit_device_type_display").text(
                    response.device?.device_type?.name || "N/A"
                );
                $("#edit_notes").val(response.notes);
                $("#edit_is_active").val(response.is_active ? "1" : "0");

                // Tampilkan offcanvas edit
                editOffcanvas.show();
            },
            error: function (xhr) {
                Notiflix.Loading.remove();
                Notiflix.Notify.failure(
                    xhr.responseJSON?.message || "Gagal memuat data."
                );
            },
        });
    });

    // --- Menangani saat form edit disubmit ---
    editForm.on("submit", function (e) {
        e.preventDefault();
        const assignmentId = $("#edit_assignment_id").val();

        Notiflix.Loading.standard("Menyimpan perubahan...");

        const formData = {
            _token: $('meta[name="csrf-token"]').attr("content"),
            notes: $("#edit_notes").val(),
            is_active: $("#edit_is_active").val(),
        };

        $.ajax({
            url: `/api/assign/${assignmentId}/update`, // Route dari web.php
            method: "PUT", // Gunakan method PUT untuk update
            contentType: "application/json",
            data: JSON.stringify(formData),
            success: function (response) {
                Notiflix.Loading.remove();
                Notiflix.Notify.success(response.message);
                editOffcanvas.hide();
                $("#user-devices-datatable")
                    .DataTable()
                    .ajax.reload(null, false);
            },
            error: function (xhr) {
                Notiflix.Loading.remove();
                let errorMessage =
                    xhr.responseJSON?.message || "Gagal menyimpan perubahan.";
                if (xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("<br>");
                    errorMessage += `<br><small class="text-danger">${errors}</small>`;
                }
                Notiflix.Report.failure("Update Gagal", errorMessage, "Tutup", {
                    message_html: true,
                });
            },
        });
    });

    $(document).on("click", ".btn-delete-device", function () {
        const assignmentId = $(this).data("id");
        const uniqueId = $(this).data("unique-id");
        const url = `/api/assign/${assignmentId}`; // URL dari route web.php
        const token = $('meta[name="csrf-token"]').attr("content"); // Ambil CSRF token dari meta tag

        Notiflix.Confirm.show(
            "Konfirmasi Hapus Alat",
            `Apakah Anda yakin ingin melepas perangkat dengan ID: <strong>${uniqueId}</strong> dari akun Anda?`,
            "Ya, Hapus",
            "Batal",
            function okCb() {
                // Fungsi yang dijalankan jika user klik "Ya"
                Notiflix.Loading.standard("Memproses...");

                $.ajax({
                    url: url,
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": token,
                    },
                    success: function (response) {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.success(response.message);

                        // Refresh DataTable untuk menampilkan perubahan
                        $("#user-devices-datatable")
                            .DataTable()
                            .ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Notiflix.Loading.remove();
                        const errorMessage =
                            xhr.responseJSON?.message ||
                            "Gagal melepas perangkat.";
                        Notiflix.Notify.failure(errorMessage);
                    },
                });
            },
            function cancelCb() {
                // Fungsi jika user klik "Batal"
                // Tidak melakukan apa-apa
            },
            {
                // Opsi Notiflix
                message_html: true,
                title_color: "#DC3545",
                ok_button_background: "#DC3545",
            }
        );
    });

    $(document).on("click", ".btn-view-device", function () {
        const deviceId = $(this).data("id");
        window.location.href = `/devices/${deviceId}`;
    });
});
