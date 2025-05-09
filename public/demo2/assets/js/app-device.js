const DeviceUrl = document.getElementById("devices-datatable").dataset.url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#devices-datatable").DataTable({
        processing: true,
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
            },
            {
                targets: 2,
                render: function (data, type, full, meta) {
                    return full.type_id ? full.type : "-";
                },
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
                            badgeClass = "bg-label-danger";
                            break;
                        case "error":
                            badgeClass = "bg-label-warning";
                            break;
                        default:
                            badgeClass = "bg-label-primary";
                    }
                    return `<span class="badge ${badgeClass}">${
                        full.status.charAt(0).toUpperCase() +
                        full.status.slice(1)
                    }</span>`;
                },
            },
            {
                targets: 4,
                render: function (data, type, full, meta) {
                    if (full.latitude && full.longitude) {
                        return `${full.latitude}, ${full.longitude}`;
                    }
                    return "-";
                },
            },
            {
                targets: -1,
                render: function (data, type, full, meta) {
                    return `
            <div class="btn-list">
                <button class="btn btn-info btn-edit-device" data-id="${data}">
                    <i class="ti ti-edit"></i>
                </button>
                <button class="btn btn-danger btn-delete" data-id="${data}">
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
                data: "id",
            },
            {
                data: "unique_id",
            },
            {
                data: "type",
            },
            {
                data: "status",
            },
            {
                data: "location",
            },
            {
                data: "createdAt",
            },
            {
                data: "id",
            },
        ],
        language: {
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
                text: '<i class="ti ti-upload me-2 ti-xs"></i>Export',
                buttons: [
                    {
                        extend: "print",
                        text: '<i class="ti ti-printer me-2" ></i>Print',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5],
                            // prevent avatar to be print
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = "";
                                    $.each(el, function (index, item) {
                                        if (
                                            item.classList !== undefined &&
                                            item.classList.contains("user-name")
                                        ) {
                                            result =
                                                result +
                                                item.lastChild.firstChild
                                                    .textContent;
                                        } else if (
                                            item.innerText === undefined
                                        ) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                },
                            },
                        },
                        customize: function (win) {
                            //customize print view for dark
                            $(win.document.body)
                                .css("color", headingColor)
                                .css("border-color", borderColor)
                                .css("background-color", bodyBg);
                            $(win.document.body)
                                .find("table")
                                .addClass("compact")
                                .css("color", "inherit")
                                .css("border-color", "inherit")
                                .css("background-color", "inherit");
                        },
                    },
                    {
                        extend: "csv",
                        text: '<i class="ti ti-file-text me-2" ></i>Csv',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5],
                            // prevent avatar to be display
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = "";
                                    $.each(el, function (index, item) {
                                        if (
                                            item.classList !== undefined &&
                                            item.classList.contains("user-name")
                                        ) {
                                            result =
                                                result +
                                                item.lastChild.firstChild
                                                    .textContent;
                                        } else if (
                                            item.innerText === undefined
                                        ) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                },
                            },
                        },
                    },
                    {
                        extend: "excel",
                        text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5],
                            // prevent avatar to be display
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = "";
                                    $.each(el, function (index, item) {
                                        if (
                                            item.classList !== undefined &&
                                            item.classList.contains("user-name")
                                        ) {
                                            result =
                                                result +
                                                item.lastChild.firstChild
                                                    .textContent;
                                        } else if (
                                            item.innerText === undefined
                                        ) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                },
                            },
                        },
                    },
                    {
                        extend: "pdf",
                        text: '<i class="ti ti-file-code-2 me-2"></i>Pdf',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5],
                            // prevent avatar to be display
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = "";
                                    $.each(el, function (index, item) {
                                        if (
                                            item.classList !== undefined &&
                                            item.classList.contains("user-name")
                                        ) {
                                            result =
                                                result +
                                                item.lastChild.firstChild
                                                    .textContent;
                                        } else if (
                                            item.innerText === undefined
                                        ) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                },
                            },
                        },
                    },
                    {
                        extend: "copy",
                        text: '<i class="ti ti-copy me-2" ></i>Copy',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5],
                            // prevent avatar to be display
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = "";
                                    $.each(el, function (index, item) {
                                        if (
                                            item.classList !== undefined &&
                                            item.classList.contains("user-name")
                                        ) {
                                            result =
                                                result +
                                                item.lastChild.firstChild
                                                    .textContent;
                                        } else if (
                                            item.innerText === undefined
                                        ) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                },
                            },
                        },
                    },
                ],
            },
            {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Device</span>',
                className: "add-new btn btn-primary waves-effect waves-light",
                attr: {
                    "data-bs-toggle": "offcanvas",
                    "data-bs-target": "#offcanvasAddDevice",
                },
            },
        ],
    });
    // FIlter by status
    $("#statusFilter").on("change", function () {
        const status = $(this).val();
        table.column(3).search(status, true, false, true).draw();
    });

    $(document).ready(function () {
        // Load device types when offcanvas opens
        $("#offcanvasAddDevice").on("show.bs.offcanvas", loadDeviceTypes);

        // Handle form submission
        $("#addNewDeviceForm").submit(function (e) {
            e.preventDefault();

            // Validate device type is selected
            if (!$("#device_type_id").val()) {
                Notiflix.Notify.failure("Silakan pilih tipe device");
                return;
            }

            Notiflix.Loading.standard("Menyimpan device...");

            const formData = {
                unique_id: $("#unique_id").val(),
                device_type_id: $("#device_type_id").val(),
                status: $("#status").val(),
                latitude: $("#latitude").val(),
                longitude: $("#longitude").val(),
            };

            $.ajax({
                url: "/api/devices",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(formData),
                success: function (response) {
                    Notiflix.Loading.remove();
                    Notiflix.Notify.success(response.message);

                    // Refresh DataTable
                    $("#devices-datatable")
                        .DataTable()
                        .ajax.reload(null, false);

                    // Close offcanvas
                    bootstrap.Offcanvas.getInstance(
                        $("#offcanvasAddDevice")[0]
                    ).hide();
                },
                error: function (xhr) {
                    Notiflix.Loading.remove();
                    let errorMessage =
                        xhr.responseJSON?.message || "Gagal menambahkan device";

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
    function loadDeviceTypes() {
        $.ajax({
            url: "/api/device-types",
            method: "GET",
            success: function (response) {
                const select = $("#device_type_id");
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
                $("#device_type_id").html(
                    '<option value="" disabled selected>Gagal memuat tipe device</option>'
                );
            },
        });
    }

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
            url: "/api/device-types",
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
            $("#edit_device_type_id").val(device.device_type_id || "");
            $("#edit_status").val(device.status || "active");
            $("#edit_latitude").val(device.latitude || "");
            $("#edit_longitude").val(device.longitude || "");

            console.log("Form values after setting:", {
                id: $("#edit_id").val(),
                unique_id: $("#edit_unique_id").val(),
                device_type_id: $("#edit_device_type_id").val(),
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

        Notiflix.Loading.standard("Menyimpan perubahan...");

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
                Notiflix.Loading.standard("Deleting device...");

                fetch(`/api/devices/${deviceId}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
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
