const UsageUrl = document.getElementById("report-usage-datatable").dataset.url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#report-usage-datatable").DataTable({
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
        ],
        columns: [
            { data: "id" },
            { data: "user.userData.name" },
            { data: "user.email" },
            { data: "date" },
            { data: "total_consumption" },
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
                text: '<i class="ti ti-upload me-2 ti-xs"></i>Export',
                buttons: [
                    {
                        extend: "print",
                        text: '<i class="ti ti-printer me-2" ></i>Print',
                        className: "dropdown-item",
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
            // Inisialisasi datepicker
            var dateInput = $(
                '<input type="text" class="form-control" placeholder="Pilih Tanggal">'
            )
                .appendTo($(".date_picker"))
                .datepicker({
                    format: "yyyy-mm-dd",
                    autoclose: true,
                    language: "id", // bahasa Indonesia
                    todayHighlight: true,
                })
                .on("changeDate", function (e) {
                    // Format tanggal: yyyy-mm-dd
                    var selectedDate = e.format();
                    table.column(3).search(selectedDate).draw();
                });

            // 2. MONTH FILTER (updated for combined filtering)
            var monthSelect = $(
                '<select id="monthFilter" class="form-select"><option value="">Pilih Bulan</option></select>'
            )
                .appendTo(".month_filter")
                .on("change", function () {
                    applyCombinedMonthYearFilter();
                    $(".date_filter input").val("").datepicker("update");
                });

            // 3. YEAR FILTER (updated for combined filtering)
            var yearSelect = $(
                '<select id="yearFilter" class="form-select"><option value="">Pilih Tahun</option></select>'
            )
                .appendTo(".year_filter")
                .on("change", function () {
                    applyCombinedMonthYearFilter();
                    $(".date_filter input").val("").datepicker("update");
                });

            // Function to handle combined month+year filtering
            function applyCombinedMonthYearFilter() {
                var month = $("#monthFilter").val();
                var year = $("#yearFilter").val();

                if (month && year) {
                    // Combined month+year search (format: YYYY-MM)
                    var searchTerm = year + "-" + month;
                    table
                        .column(5)
                        .search(searchTerm, true, false, true)
                        .draw();
                } else if (month) {
                    // Month-only search (format: -MM-)
                    table
                        .column(5)
                        .search("-" + month + "-", true, false, true)
                        .draw();
                } else if (year) {
                    // Year-only search (format: YYYY)
                    table.column(5).search(year, true, false, true).draw();
                } else {
                    // Clear search if both are empty
                    table.column(5).search("").draw();
                }
            }

            // Month options (Indonesian names)
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

            // Year options
            for (var y = new Date().getFullYear(); y >= 2020; y--) {
                yearSelect.append(
                    '<option value="' + y + '">' + y + "</option>"
                );
            }
            // 4. TOMBOL RESET FILTER
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

                    // Tambahkan kelas animasi
                    $icon.addClass("rotating");

                    // Reset semua filter
                    $(".date_filter input").val("").datepicker("update");
                    $("#monthFilter, #yearFilter").val("");
                    table.column(5).search("").draw();

                    // Hentikan animasi setelah 1 detik
                    setTimeout(function () {
                        $icon.removeClass("rotating");
                    }, 1000);
                });
        },
        // Add this to your DataTables initialization
    });
});
