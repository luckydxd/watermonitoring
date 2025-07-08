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
                targets: 0,
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
                name: "deviceType.name",
            },
            {
                targets: 3,
                render: function (data, type, full, meta) {
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
                name: "status",
            },
            {
                targets: 4,
                render: function (data, type, full, meta) {
                    const timestamp = full.created_at;
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
                name: "created_at",
            },
            {
                targets: -1,
                render: function (data, type, full, meta) {
                    const deviceId = full.id;
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
            { data: "id" },
            { data: "unique_id", name: "unique_id" },
            { data: "device_type.name", name: "deviceType.name" },
            { data: "status" },
            { data: "created_at" },
            { data: "id" },
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
        $.ajax({
            url: "/api/devices/types-datatables",
            type: "GET",
            success: function (data) {
                const select = $("#typeFilter")
                    .empty()
                    .append('<option value="">Pilih Jenis Alat</option>');

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

    initTypeFilter();

    $("#typeFilter").on("change", function () {
        const val = $(this).val();

        table.column(2).search(val).draw();
    });

    $(document).ready(function () {
        const offcanvasGenerateQRCode = $("#offcanvasGenerateQRCode");
        const qrUniqueIdDisplay = $("#qr_unique_id_display");
        const qrcodeDiv = $("#qrcode");
        const downloadQrBtn = $("#downloadQrBtn");

        const offcanvasAddDevice = $("#offcanvasAddDevice");
        const addNewDeviceForm = $("#addNewDeviceForm");
        const addDeviceIdInput = $("#unique_id");
        const addDeviceTypeIdSelect = $("#device_type_id");
        const addDeviceStatusSelect = $("#status");

        function loadDeviceTypesForAddForm() {
            $.ajax({
                url: "/api/devices/types",
                method: "GET",
                dataType: "json",
                success: function (data) {
                    addDeviceTypeIdSelect
                        .empty()
                        .append(
                            '<option value="" disabled selected>Pilih Jenis Alat</option>'
                        );
                    data.forEach(function (type) {
                        const option = `<option value="${type.id}" data-code="${type.code}">${type.name}</option>`;
                        addDeviceTypeIdSelect.append(option);
                    });
                },
                error: function () {
                    Notiflix.Notify.failure("Gagal memuat jenis alat.");
                },
            });
        }

        function generateUniqueIdPreview() {
            const selectedOption =
                addDeviceTypeIdSelect.find("option:selected");

            if (selectedOption.length && selectedOption.val()) {
                const deviceTypeCode = selectedOption.data("code");

                if (deviceTypeCode) {
                    const now = new Date();
                    const year = String(now.getFullYear()).slice(-2);
                    const month = String(now.getMonth() + 1).padStart(2, "0");
                    const deviceVersion = "1";
                    const previewSerial = "XXX";

                    const previewUniqueId = `${year}${month}${deviceTypeCode}${deviceVersion}${previewSerial}`;
                    addDeviceIdInput.val(previewUniqueId);
                } else {
                    addDeviceIdInput.val("Gagal membuat preview ID");
                }
            } else {
                addDeviceIdInput.val(
                    "Pilih jenis alat untuk menghasilkan Unik ID..."
                );
            }
        }

        function generateQrCode(uniqueId) {
            if (!uniqueId) {
                qrcodeDiv.html(
                    '<p class="text-danger">ID unik tidak tersedia.</p>'
                );
                return;
            }

            const qrContent = uniqueId;

            qrcodeDiv.empty();
            new QRCode(qrcodeDiv[0], {
                text: qrContent,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H,
            });

            downloadQrBtn.css("display", "block");

            setTimeout(() => {
                const canvas = qrcodeDiv.find("canvas")[0];
                if (canvas) {
                    const imgData = canvas.toDataURL("image/png");
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF({
                        orientation: "portrait",
                        unit: "px",
                        format: [250, 350],
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
                        doc.save(fileName);
                    });
                } else {
                    console.error("Elemen canvas QR Code tidak ditemukan.");
                    downloadQrBtn.css("display", "none");
                }
            }, 100);
        }

        $(document).on("click", ".btn-qr-code", function () {
            const button = $(this);
            const uniqueId = button.data("unique-id");

            qrUniqueIdDisplay.val(uniqueId);

            qrcodeDiv.html('<p class="text-muted">Membuat QR Code...</p>');
            downloadQrBtn.css("display", "none");

            setTimeout(() => {
                generateQrCode(uniqueId);
            }, 100);
        });

        offcanvasAddDevice.on("show.bs.offcanvas", function () {
            addNewDeviceForm[0].reset();
            loadDeviceTypesForAddForm();
            addDeviceIdInput.val(
                "Pilih jenis alat untuk menghasilkan ID unik..."
            );
            addDeviceIdInput.prop("readonly", true);
            addDeviceStatusSelect.val("inactive");

            addDeviceTypeIdSelect
                .off("change", generateUniqueIdPreview)
                .on("change", generateUniqueIdPreview);
        });

        addNewDeviceForm.submit(function (e) {
            e.preventDefault();

            if (!addDeviceTypeIdSelect.val()) {
                Notiflix.Notify.failure("Silakan pilih jenis alat.");
                return;
            }

            Notiflix.Loading.standard("Menyimpan perangkat...");

            const formData = {
                device_type_id: addDeviceTypeIdSelect.val(),
                status: addDeviceStatusSelect.val(),
            };

            $.ajax({
                url: "/api/devices",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(formData),
                success: function (response) {
                    Notiflix.Loading.remove();
                    Notiflix.Notify.success(
                        response.message || "Perangkat berhasil ditambahkan."
                    );
                    $("#devices-datatable")
                        .DataTable()
                        .ajax.reload(null, false);
                    bootstrap.Offcanvas.getInstance(
                        offcanvasAddDevice[0]
                    ).hide();
                },
                error: function (xhr) {
                    Notiflix.Loading.remove();
                    let errorMessage =
                        xhr.responseJSON?.message ||
                        "Gagal menambahkan perangkat.";
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

    $(document).on("click", ".btn-edit-device", function () {
        const deviceId = $(this).data("id");
        console.log("Edit device ID:", deviceId);

        $("#editDeviceForm")[0].reset();

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

    function fillEditDeviceForm(response) {
        console.log("Mengisi form edit dengan response:", response);

        const device = response.device || response;
        console.log("Device data:", device);

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

        if (!data.device_type_id) {
            Notiflix.Notify.failure("Silakan pilih tipe device");
            return;
        }

        $.ajax({
            url: `/api/devices/${id}`,
            method: "PUT",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function (res) {
                Notiflix.Loading.remove();
                Notiflix.Notify.success(res.message || "Berhasil diperbarui!");

                bootstrap.Offcanvas.getInstance(
                    document.getElementById("offcanvasEditDevice")
                ).hide();

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
    $(document).on("click", ".btn-delete", function () {
        const deviceId = $(this).data("id");

        Notiflix.Confirm.show(
            "Delete Device",
            "Are you sure you want to delete this device?",
            "Yes",
            "No",
            function okCb() {
                fetch(`/api/devices/${deviceId}`, {
                    method: "DELETE",
                    headers: {
                        Authorization:
                            "Bearer " + localStorage.getItem("token"),
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                })
                    .then((res) => {
                        Notiflix.Loading.remove();

                        if (res.ok) {
                            Notiflix.Notify.success(
                                "Device deleted successfully."
                            );
                            table.ajax.reload();
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
