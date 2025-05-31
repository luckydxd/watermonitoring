const Url = document.getElementById("complaints-datatable").dataset.url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#complaints-datatable").DataTable({
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
        ajax: Url,
        columnDefs: [
            {
                targets: 0,
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                },
            },
            {
                targets: 1, // User name column
                render: function (data, type, full, meta) {
                    // Access the nested user data
                    return full.user?.user_data?.name || "-";
                },
            },
            {
                targets: 2, // Image column
                render: function (data, type, full, meta) {
                    if (full.image) {
                        // Construct the full image URL
                        const imageUrl = "/storage/" + full.image;
                        return `<img src="${imageUrl}" alt="Complaint Image" class="thumb-lg rounded" 
                            style="width: 100px; height: 100px; object-fit: cover;"
                            onerror="this.onerror=null;this.src='/images/default-complaint.png'">`;
                    }
                    return '<img src="/images/default-complaint.png" class="thumb-lg rounded" style="width: 100px; height: 100px;">';
                },
                orderable: false,
                searchable: false,
            },
            {
                targets: 5, // Status column (now index 4 because we added image column)
                render: function (data, type, full, meta) {
                    let badgeClass = "";
                    switch (full.status) {
                        case "pending":
                            badgeClass = "bg-label-warning";
                            break;
                        case "processed":
                            badgeClass = "bg-label-info";
                            break;
                        case "resolved":
                            badgeClass = "bg-label-success";
                            break;
                        case "rejected":
                            badgeClass = "bg-label-danger";
                            break;
                        default:
                            badgeClass = "bg-label-secondary";
                    }
                    return `<span class="badge ${badgeClass}">${
                        full.status.charAt(0).toUpperCase() +
                        full.status.slice(1)
                    }</span>`;
                },
            },
            {
                targets: 6, // Timestamp column (now index 5)
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        const date = new Date(data);
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
            },
        ],
        columns: [
            { data: "id" }, // No
            { data: "user_name" },
            { data: "image" }, // Image
            { data: "title" }, // Title
            { data: "description" }, // Description
            { data: "status" }, // Status
            { data: "created_at" }, // Timestamp
        ],
        language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Search Complaints",
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
                        text: '<i class="ti ti-printer me-2"></i>Print',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6],
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;
                                    var el = $.parseHTML(inner);
                                    var result = "";
                                    $.each(el, function (index, item) {
                                        if (item.innerText === undefined) {
                                            result = result + item.textContent;
                                        } else result = result + item.innerText;
                                    });
                                    return result;
                                },
                            },
                        },
                        customize: function (win) {
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
                        text: '<i class="ti ti-file-text me-2"></i>Csv',
                        className: "dropdown-item",
                        filename: function () {
                            var base = "Complaints_List";
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
                            columns: [1, 2, 3, 4, 5, 6],

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
                            columns: [1, 2, 3, 4, 5, 6],
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
                            columns: [1, 2, 3, 4, 5, 6],
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
                        text: '<i class="ti ti-copy me-2"></i>Copy',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6],
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
            // {
            //     text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Complaint</span>',
            //     className: "add-new btn btn-primary waves-effect waves-light",
            //     attr: {
            //         "data-bs-toggle": "offcanvas",
            //         "data-bs-target": "#offcanvasAddComplaint",
            //     },
            // },
        ],
        initComplete: function () {
            const api = this.api();

            // Initialize status filter
            api.columns(6).every(function () {
                const column = this;
                const select = $("#statusFilter")
                    .empty()
                    .append('<option value="">All Status</option>');

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
            });

            // Apply filter when status changes
            $("#statusFilter").on("change", function () {
                table.column(6).search(this.value).draw();
            });

            // Optional: Initialize device filter if needed
        },
    });

    //
});
