const DeviceAssignmentUrl = document.getElementById(
    "device-assignment-datatable"
).dataset.url;
const DeleteUrl =
    document.getElementById("device-assignment-datatable").dataset.deleteUrl ||
    DeviceAssignmentUrl;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#device-assignment-datatable").DataTable({
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
        ajax: {
            url: DeviceAssignmentUrl,
            type: "GET",
        },

        columnDefs: [
            {
                targets: 0,
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                orderable: false,
            },
            {
                targets: 1,
                render: function (data, type, full, meta) {
                    return full.user?.user_data?.name || "-";
                },
            },
            {
                targets: 2,
                render: function (data, type, full, meta) {
                    return full.device?.unique_id || "-";
                },
            },
            {
                targets: 3,
                render: function (data, type, full, meta) {
                    return full.device?.device_type?.name || "-";
                },
            },
            {
                targets: 4,
                render: function (data, type, full, meta) {
                    return full.assignment_date
                        ? moment(full.assignment_date).format(
                              "DD/MM/YYYY HH:mm"
                          )
                        : "-";
                },
            },
            {
                targets: 5,
                render: function (data) {
                    return data
                        ? '<span class="badge bg-label-success">Active</span>'
                        : '<span class="badge bg-label-secondary">Inactive</span>';
                },
            },
            {
                targets: -1,
                data: "id",
                render: function (data, type, row) {
                    return `
                        <div class="btn-list">
                            <button class="btn btn-info btn-detail" data-id="${data}" data-user-id="${row.user?.id}">
                                <i class="ti ti-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-edit" data-id="${data}">
                                <i class="ti ti-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-delete" data-id="${data}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    `;
                },
                orderable: false,
            },
        ],

        columns: [
            {
                data: "id",
            },
            {
                data: "user_datas.name",
            },
            {
                data: "unique_id",
            },
            {
                data: "device_type.name",
            },
            {
                data: "assignment_date",
            },
            {
                data: "is_active",
            },
            {
                data: "id",
            },
        ],
        order: [[1, "asc"]],
        language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Search",
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
                            columns: [1, 2, 3, 4],
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
                        filename: function () {
                            var base = "User_with_Assigned_Device";
                            var date = new Date();
                            var timestamp =
                                date.getFullYear() +
                                "-" +
                                String(date.getMonth() + 1).padStart(2, "0") +
                                "-" +
                                String(date.getDate()).padStart(2, "0") +
                                "_" +
                                String(date.getHours()).padStart(2, "0") +
                                "-" +
                                String(date.getMinutes()).padStart(2, "0") +
                                "-" +
                                String(date.getSeconds()).padStart(2, "0");

                            return base + "_" + timestamp;
                        },

                        exportOptions: {
                            columns: [1, 2, 3, 4],
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
                        filename: function () {
                            var base = "User_with_Assigned_Device";
                            var date = new Date();
                            var timestamp =
                                date.getFullYear() +
                                "-" +
                                String(date.getMonth() + 1).padStart(2, "0") +
                                "-" +
                                String(date.getDate()).padStart(2, "0") +
                                "_" +
                                String(date.getHours()).padStart(2, "0") +
                                "-" +
                                String(date.getMinutes()).padStart(2, "0") +
                                "-" +
                                String(date.getSeconds()).padStart(2, "0");

                            return base + "_" + timestamp;
                        },
                        exportOptions: {
                            columns: [1, 2, 3, 4],
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
                        filename: function () {
                            var base = "User_with_Assigned_Device";
                            var date = new Date();
                            var timestamp =
                                date.getFullYear() +
                                "-" +
                                String(date.getMonth() + 1).padStart(2, "0") +
                                "-" +
                                String(date.getDate()).padStart(2, "0") +
                                "_" +
                                String(date.getHours()).padStart(2, "0") +
                                "-" +
                                String(date.getMinutes()).padStart(2, "0") +
                                "-" +
                                String(date.getSeconds()).padStart(2, "0");

                            return base + "_" + timestamp;
                        },
                        exportOptions: {
                            columns: [1, 2, 3, 4],
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
                            columns: [1, 2, 3, 4],
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
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Assignment</span>',
                className: "add-new btn btn-primary waves-effect waves-light",
                attr: {
                    "data-bs-toggle": "offcanvas",
                    "data-bs-target": "#offcanvasAssignDevice",
                },
            },
        ],
    });

    // Handle row actions
    $("#device-assignment-datatable").on("click", ".btn-detail", function () {
        const userId = $(this).data("user-id");
        if (userId) {
            window.location.href = `/admin/detail-monitor/${userId}`;
        }
    });

    $(document).ready(function () {
        // Load users and devices when offcanvas opens
        $("#offcanvasAssignDevice").on("show.bs.offcanvas", function () {
            loadUsers();
            loadAvailableDevices();
        });

        // Function to load users
        function loadUsers() {
            $.get("/api/monitor/assign/users", function (data) {
                $("#user_id")
                    .empty()
                    .append(
                        '<option value="" disabled selected>Select User</option>'
                    );
                $.each(data, function (key, user) {
                    $("#user_id").append(
                        `<option value="${user.id}">${user.name} (${user.email})</option>`
                    );
                });
            });
        }

        // Function to load available devices
        function loadAvailableDevices() {
            $("#device_id").html(
                '<option value="" disabled selected>Loading devices...</option>'
            );

            $.get("/api/monitor/assign/devices")
                .done(function (data) {
                    $("#device_id")
                        .empty()
                        .append(
                            '<option value="" disabled selected>Select Device</option>'
                        );

                    if (data.length === 0) {
                        $("#device_id").append(
                            '<option value="" disabled>No available devices</option>'
                        );
                        return;
                    }

                    $.each(data, function (key, device) {
                        // Handle missing device_type
                        const typeName = device.device_type?.name || "No Type";
                        const deviceText = `${
                            device.unique_id || "No ID"
                        } (${typeName})`;

                        $("#device_id").append(
                            `<option value="${device.id}">${deviceText}</option>`
                        );
                    });
                })
                .fail(function (xhr) {
                    console.error("Error loading devices:", xhr.responseText);
                    $("#device_id").html(
                        '<option value="" disabled selected>Error loading devices</option>'
                    );
                });
        }
        // Handle form submission
        $(document).ready(function () {
            // Load users and devices when offcanvas opens
            $("#offcanvasAssignDevice").on("show.bs.offcanvas", function () {
                Notiflix.Loading.standard("Memuat data...");
                Promise.all([loadUsers(), loadAvailableDevices()])
                    .then(() => Notiflix.Loading.remove())
                    .catch(() => Notiflix.Loading.remove());
            });

            // Handle form submission
            $("#addNewAssignmentForm").submit(function (e) {
                e.preventDefault();

                // Validasi form
                if (!$("#user_id").val()) {
                    Notiflix.Notify.failure("Silakan pilih user");
                    return;
                }

                if (!$("#device_id").val()) {
                    Notiflix.Notify.failure("Silakan pilih device");
                    return;
                }

                Notiflix.Loading.standard("Memproses assignment...");

                const formData = {
                    user_id: $("#user_id").val(),
                    device_id: $("#device_id").val(),
                    assignment_date: $("#assignment_date").val(),
                    is_active: $("#is_active").val(),
                    notes: $("#notes").val(),
                };

                $.ajax({
                    url: "/api/monitor/assign",
                    method: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(formData),
                    success: function (response) {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.success("Device berhasil di-assign!");

                        // Reset form
                        $("#addNewAssignmentForm")[0].reset();

                        // Refresh DataTable
                        $("#device-assignment-datatable")
                            .DataTable()
                            .ajax.reload();

                        // Close offcanvas
                        bootstrap.Offcanvas.getInstance(
                            $("#offcanvasAssignDevice")[0]
                        ).hide();
                    },
                    error: function (xhr) {
                        Notiflix.Loading.remove();
                        let errorMessage =
                            xhr.responseJSON?.message ||
                            "Gagal melakukan assignment device";

                        if (xhr.responseJSON?.errors) {
                            errorMessage = Object.values(
                                xhr.responseJSON.errors
                            ).join("<br>");
                        }

                        Notiflix.Report.failure("Error", errorMessage, "Okay");
                    },
                });
            });
        });
    });

    // Fungsi untuk mengisi form edit
    function fillEditAssignmentForm(data) {
        console.log("Mengisi form edit dengan data:", data);

        // Set nilai form
        $("#edit_user_id").val(data.assignment.user_id);
        $("#edit_device_id").val(data.assignment.device_id);
        $("#edit_assignment_date").val(
            formatDateTimeForInput(data.assignment.assignment_date)
        );
        $("#edit_is_active").val(data.assignment.is_active ? "1" : "0");
        $("#edit_notes").val(data.assignment.notes || "");

        // Load users dropdown
        const userSelect = $("#edit_user_id");
        userSelect
            .empty()
            .append('<option value="" disabled>Pilih User</option>');
        data.users.forEach((user) => {
            userSelect.append(
                `<option value="${user.id}">
                ${user.user_data?.name || "No Name"} (${user.email})
            </option>`
            );
        });

        // Load devices dropdown
        const deviceSelect = $("#edit_device_id");
        deviceSelect
            .empty()
            .append('<option value="" disabled>Pilih Device</option>');
        data.available_devices.forEach((device) => {
            const typeName = device.device_type?.name || "Unknown Type";
            deviceSelect.append(
                `<option value="${device.id}">
                ${device.unique_id} (${typeName})
            </option>`
            );
        });

        // Set nilai yang dipilih
        userSelect.val(data.assignment.user_id);
        deviceSelect.val(data.assignment.device_id);
    }

    // Format datetime untuk input
    function formatDateTimeForInput(dateString) {
        if (!dateString) return "";
        const date = new Date(dateString);
        return date.toISOString().slice(0, 16);
    }

    // Handle klik tombol edit
    $(document).on("click", ".btn-edit", function () {
        const assignmentId = $(this).data("id");
        console.log("Edit assignment ID:", assignmentId);

        Notiflix.Loading.standard("Memuat data assignment...");

        $.ajax({
            url: `/api/monitor/${assignmentId}/edit`,
            method: "GET",
            success: function (response) {
                Notiflix.Loading.remove();
                console.log("Data assignment diterima:", response);

                if (!response.assignment) {
                    Notiflix.Notify.failure("Data assignment tidak valid");
                    return;
                }

                fillEditAssignmentForm(response);

                // Tampilkan offcanvas
                const editOffcanvas = new bootstrap.Offcanvas(
                    document.getElementById("offcanvasEditAssignDevice")
                );
                editOffcanvas.show();
            },
            error: function (xhr) {
                Notiflix.Loading.remove();
                console.error("Error:", xhr.responseText);
                Notiflix.Notify.failure("Gagal memuat data assignment");
            },
        });
    });

    // Handle submit form edit
    $("#editAssignmentForm").on("submit", function (e) {
        e.preventDefault();
        const assignmentId = $(this).find('input[name="id"]').val();

        const formData = {
            user_id: $("#edit_user_id").val(),
            device_id: $("#edit_device_id").val(),
            assignment_date: $("#edit_assignment_date").val(),
            is_active: $("#edit_is_active").val(),
            notes: $("#edit_notes").val(),
        };

        // Validasi
        if (
            !formData.user_id ||
            !formData.device_id ||
            !formData.assignment_date
        ) {
            Notiflix.Notify.failure("Harap lengkapi semua field wajib");
            return;
        }

        Notiflix.Loading.standard("Menyimpan perubahan...");

        $.ajax({
            url: `/api/monitor/${assignmentId}`,
            method: "PUT",
            contentType: "application/json",
            data: JSON.stringify(formData),
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                Notiflix.Loading.remove();
                Notiflix.Notify.success(
                    response.message || "Assignment berhasil diperbarui!"
                );

                // Tutup offcanvas
                bootstrap.Offcanvas.getInstance(
                    document.getElementById("offcanvasEditAssignDevice")
                ).hide();

                // Refresh DataTable
                $("#device-assignment-datatable")
                    .DataTable()
                    .ajax.reload(null, false);
            },
            error: function (xhr) {
                Notiflix.Loading.remove();
                let errorMessage = xhr.responseJSON?.message || "Update gagal!";

                if (xhr.responseJSON?.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join(
                        "<br>"
                    );
                }

                Notiflix.Report.failure("Error", errorMessage, "Okay");
                console.error(xhr.responseText);
            },
        });
    });

    // Delete functionality
    $(document).on("click", ".btn-delete", function () {
        const deleteId = $(this).data("id");
        const dataTable = $("#device-assignment-datatable").DataTable(); // Get DataTable instance

        Notiflix.Confirm.show(
            "Delete Usage Record",
            "Are you sure you want to delete this record?",
            "Yes",
            "No",
            function okCb() {
                Notiflix.Loading.standard("Deleting Record...");

                fetch(`${DeleteUrl}/${deleteId}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                })
                    .then(async (res) => {
                        const response = await res.json();

                        if (!res.ok) {
                            throw new Error(
                                response.message || "Delete failed"
                            );
                        }

                        return response;
                    })
                    .then(() => {
                        Notiflix.Notify.success("Usage deleted successfully.", {
                            onClose: () => {
                                table.ajax.reload(); // Reload with current pagination
                            },
                        });
                    })
                    .catch((err) => {
                        Notiflix.Notify.failure(`Error: ${err.message}`);
                    })
                    .finally(() => {
                        Notiflix.Loading.remove();
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
