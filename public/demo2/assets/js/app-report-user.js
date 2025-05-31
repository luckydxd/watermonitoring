const UserUrl = document.getElementById("report-user-datatable").dataset.url;

const statusObj = {
    0: { title: "Inactive", class: "bg-label-secondary" },
    1: { title: "Active", class: "bg-label-success" },
};

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#report-user-datatable").DataTable({
        processing: true,
        serverside: true,
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
            url: UserUrl,
        },
        columnDefs: [
            {
                targets: 0,
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                },
            },
            {
                targets: 3,
                render: function (data, type, full, meta) {
                    return full.roles?.[0]?.name ?? "-";
                },
            },
            {
                targets: 3,
                responsivePriority: 2,
                render: function (data, type, full, meta) {
                    var roles = full["roles"];
                    if (!roles || roles.length === 0) {
                        return "<span>-</span>";
                    }
                    var roleName = roles[0].name;
                    var roleBadgeObj = {
                        user: '<i class="ti ti-diamond ti-md text-primary me-2"></i>',
                        admin: '<i class="ti ti-device-desktop ti-md text-danger me-2"></i>',
                        teknisi:
                            '<i class="ti ti-tool ti-md text-warning me-2"></i>',
                    };

                    var badge = roleBadgeObj[roleName] || "";

                    return (
                        "<span class='text-truncate d-flex align-items-center text-heading'>" +
                        badge +
                        roleName.charAt(0).toUpperCase() +
                        roleName.slice(1) +
                        "</span>"
                    );
                },
            },

            {
                targets: 6,
                render: function (data, type, full, meta) {
                    const isActive = full.is_active;
                    const status = statusObj[isActive] || {
                        title: "Unknown",
                        class: "bg-label-dark",
                    };

                    return `
      <span class="badge ${status.class} text-capitalize">
        ${status.title}
      </span>
    `;
                },
            },
            {
                targets: 7,
                render: function (data, type, full, meta) {
                    if (type === "display") {
                        return full.created_at
                            ? new Date(full.created_at).toLocaleDateString(
                                  "id-ID",
                                  {
                                      day: "numeric",
                                      month: "long",
                                      year: "numeric",
                                  }
                              )
                            : "-";
                    }
                    return data;
                },
            },
        ],
        columns: [
            { data: "id" },
            { data: "user_data.name" },
            { data: "username" },
            { data: "role" },
            { data: "user_data.address" },
            { data: "user_data.phone_number" },
            { data: "is_active" },
            { data: "created_at" },
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
                        extend: "print",
                        text: '<i class="ti ti-printer me-2" ></i>Print',
                        className: "dropdown-item",
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7],
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
                            var base = "Devices_List";
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
                            columns: [1, 2, 3, 4, 5, 6, 7],
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
                            columns: [1, 2, 3, 4, 5, 6, 7],
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
                            columns: [1, 2, 3, 4, 5, 6, 7],
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
                            columns: [1, 2, 3, 4, 5, 6, 7],
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
        ],
        initComplete: function () {
            var dateInput = $(
                '<input type="text" class="form-control" placeholder="Pilih Tanggal">'
            )
                .appendTo($(".date_filter"))
                .datepicker({
                    format: "yyyy-mm-dd",
                    autoclose: true,
                    language: "id",
                    todayHighlight: true,
                })
                .on("changeDate", function (e) {
                    var selectedDate = e.format();
                    table
                        .column(7)
                        .search("^" + selectedDate, true, false, true)
                        .draw();
                    $("#monthFilter, #yearFilter").val("");
                });

            var monthSelect = $(
                '<select id="monthFilter" class="form-select"><option value="">Pilih Bulan</option></select>'
            )
                .appendTo(".month_filter")
                .on("change", function () {
                    applyCombinedMonthYearFilter();
                    $(".date_filter input").val("").datepicker("update");
                });

            var yearSelect = $(
                '<select id="yearFilter" class="form-select"><option value="">Pilih Tahun</option></select>'
            )
                .appendTo(".year_filter")
                .on("change", function () {
                    applyCombinedMonthYearFilter();
                    $(".date_filter input").val("").datepicker("update");
                });

            function applyCombinedMonthYearFilter() {
                var month = $("#monthFilter").val();
                var year = $("#yearFilter").val();

                if (month && year) {
                    var searchTerm = year + "-" + month;
                    table
                        .column(7)
                        .search(searchTerm, true, false, true)
                        .draw();
                } else if (month) {
                    table
                        .column(7)
                        .search("-" + month + "-", true, false, true)
                        .draw();
                } else if (year) {
                    table.column(7).search(year, true, false, true).draw();
                } else {
                    table.column(7).search("").draw();
                }
            }

            const monthNames = [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember",
            ];

            for (var m = 1; m <= 12; m++) {
                var monthStr = m.toString().padStart(2, "0");
                monthSelect.append(
                    '<option value="' +
                        monthStr +
                        '">' +
                        monthNames[m - 1] +
                        "</option>"
                );
            }

            for (var y = new Date().getFullYear(); y >= 2020; y--) {
                yearSelect.append(
                    '<option value="' + y + '">' + y + "</option>"
                );
            }
            $(
                '<div class="reset-filter-container" style="width: 40px; margin-left: 10px; margin-top: 8px">' +
                    '<button class="btn btn-outline-secondary p-0 d-flex align-items-center justify-content-center" ' +
                    'id="resetFilter" style="width: 38px; height: 38px" title="Reset Filter">' +
                    '<i class="ti ti-restore" style="font-size: 1.2rem"></i>' +
                    "</button>" +
                    "</div>"
            )
                .insertAfter($(".year_filter"))
                .on("click", function () {
                    var $icon = $(this).find("i");

                    $icon.addClass("rotating");

                    $(".date_filter input").val("").datepicker("update");
                    $("#monthFilter, #yearFilter").val("");
                    table.column(7).search("").draw();

                    setTimeout(function () {
                        $icon.removeClass("rotating");
                    }, 1000);
                });
        },
    });
});
