const UsageUrl = document.getElementById("usage-datatable").dataset.url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#usage-datatable").DataTable({
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
        ajax: {
            url: UsageUrl,
            data: function (d) {
                d.month = $("#monthFilter").val();
                d.year = $("#yearFilter").val();
            },
        },
        columnDefs: [
            {
                targets: 0,
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                },
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
                    return full.user?.email || "-";
                },
            },
            {
                targets: 3,
                render: function (data, type, full, meta) {
                    return full.date
                        ? new Date(full.date).toLocaleDateString("id-ID", {
                              day: "numeric",
                              month: "long",
                              year: "numeric",
                          })
                        : "-";
                },
            },
            {
                targets: 4,
                render: function (data, type, full, meta) {
                    return full.total_consumption
                        ? `${full.total_consumption.toFixed(2)} liter`
                        : "-";
                },
            },
            {
                targets: -1,
                render: function (data, type, full, meta) {
                    return `
                            <div class="btn-list">
                                <button class="btn btn-danger btn-delete" data-id="${full.id}">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        `;
                },
            },
        ],
        columns: [
            { data: "id" },
            { data: "user.userData.name" },
            { data: "user.email" },
            { data: "date" },
            { data: "total_consumption" },
            { data: "id" },
        ],
        language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Search ...",
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
                            var base = "Water_Consumption_Log_Report";
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
                            var base = "Water_Consumption_Report";
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
                            var base = "Water_Consumption_Report";
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
            // {
            //     text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Usage</span>',
            //     className: "add-new btn btn-primary waves-effect waves-light",
            //     attr: {
            //         "data-bs-toggle": "offcanvas",
            //         "data-bs-target": "#offcanvasAddDevice",
            //     },
            // },
        ],
        initComplete: function () {
            // Add month filter event
            $("#monthFilter, #yearFilter").change(function () {
                table.ajax.reload();
            });

            // If you want to add dynamic filters like the device example:
            const api = this.api();

            // Example for filtering by consumption range (if you add such filters)
            /*
                api.columns(4).every(function () {
                    const column = this;
                    
                    const select = $("#consumptionFilter")
                        .empty()
                        .append('<option value="">All Consumption</option>')
                        .append('<option value="0-50">0-50 liter</option>')
                        .append('<option value="50-100">50-100 liter</option>')
                        .append('<option value="100+">100+ liter</option>');

                    $("#consumptionFilter").on("change", function () {
                        const val = $(this).val();
                        if (val) {
                            const [min, max] = val.includes('+') ? 
                                [val.replace('+', ''), null] : 
                                val.split('-').map(Number);
                            
                            column.search('').draw(); // Clear previous search
                            
                            $.fn.dataTable.ext.search.push(
                                function(settings, data, dataIndex) {
                                    const consumption = parseFloat(data[4]) || 0;
                                    if (max === null) return consumption >= min;
                                    return consumption >= min && consumption <= max;
                                }
                            );
                        } else {
                            $.fn.dataTable.ext.search.pop();
                        }
                        table.draw();
                    });
                });
                */
        },
    });

    // Add button actions
    $("#usage-datatable").on("click", ".btn-detail", function () {
        const id = $(this).data("id");
        // Handle detail action
        console.log("Detail clicked for ID:", id);
    });

    // Delete Device with Notiflix
    $(document).on("click", ".btn-delete", function () {
        let deleteId = $(this).data("id");
        Notiflix.Confirm.show(
            "Delete Usage Record",
            "Are you sure you want to delete this record?",
            "Yes",
            "No",
            function okCb() {
                Notiflix.Loading.standard("Deleting Record...");

                fetch(`/api/monitor/${deleteId}`, {
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
                                "Usage deleted successfully."
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
