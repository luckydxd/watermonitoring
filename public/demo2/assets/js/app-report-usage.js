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
                // Kolom Nomor Urut (tidak berubah)
                targets: 0,
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                },
            },
            {
                // Kolom Nama Pengguna
                targets: 1,
                render: function (data, type, full, meta) {
                    // Mengakses 'user_name' dari hasil query
                    return full.user_name || "-";
                },
            },
            {
                // Kolom Email
                targets: 2,
                render: function (data, type, full, meta) {
                    // Mengakses 'user_email' dari hasil query
                    return full.user_email || "-";
                },
            },
            {
                // Kolom Tanggal Penggunaan
                targets: 3,
                render: function (data, type, full, meta) {
                    // Mengakses 'usage_date' dari hasil query
                    return full.usage_date
                        ? new Date(full.usage_date).toLocaleDateString(
                              "id-ID",
                              {
                                  day: "numeric",
                                  month: "long",
                                  year: "numeric",
                              }
                          )
                        : "-";
                },
            },
            {
                // Kolom Total Konsumsi
                targets: 4,
                render: function (data, type, full, meta) {
                    // Mengakses 'total_consumption' dari hasil query
                    const consumption = parseFloat(full.total_consumption);
                    return !isNaN(consumption)
                        ? `${consumption.toFixed(2)} Liter`
                        : "-";
                },
            },
        ],
        columns: [
            // Sesuaikan 'data' agar cocok dengan nama alias dari query
            { data: "user_name" }, // Untuk nomor urut, bisa diisi null atau nama kolom apa saja
            { data: "user_name" },
            { data: "user_email" },
            { data: "usage_date" },
            { data: "total_consumption" },
        ],
        lengthMenu: [
            [10, 20, 50, 100, 200, -1],
            [10, 20, 50, 100, 200, "Semua"],
        ],
        pageLength: 10,
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
                        extend: "pdfHtml5",
                        text: '<i class="ti ti-file-type-pdf me-2"></i>PDF',
                        className: "dropdown-item",
                        orientation: "portrait",
                        pageSize: "A4",
                        title: "",
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
                            return `Laporan Konsumsi Air - ${day}-${month}-${year}`;
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
                            doc.pageMargins = [40, 80, 40, 60];
                            doc.defaultStyle.fontSize = 10;
                            doc.defaultStyle.color = "#333";

                            doc.styles.totalLabel = {
                                bold: true,
                                fontSize: 10,
                                alignment: "right",
                                fillColor: "#f0f0f0",
                            };
                            doc.styles.totalValue = {
                                bold: true,
                                fontSize: 10,
                                alignment: "center",
                                fillColor: "#f0f0f0",
                            };
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

                            const tableContentIndex = doc.content.findIndex(
                                (c) => c.table
                            );
                            if (tableContentIndex !== -1) {
                                doc.content.splice(tableContentIndex, 0, {
                                    text: "LAPORAN KONSUMSI AIR",
                                    style: "reportTitle",
                                });
                                doc.content[tableContentIndex + 1].margin = [
                                    0, 0, 0, 0,
                                ];
                            }

                            const table = doc.content.find((c) => c.table);
                            if (table) {
                                table.table.widths = [
                                    30,
                                    "*",
                                    "auto",
                                    "auto",
                                    "auto",
                                ];

                                let totalConsumption = 0;

                                table.table.body.forEach((row, i) => {
                                    if (i === 0) {
                                        row.forEach((cell) => {
                                            cell.style = "tableHeader";
                                            cell.margin = [0, 4, 0, 4];
                                        });
                                        return;
                                    }

                                    const consumptionCell = row[4];
                                    if (consumptionCell) {
                                        const numericValue = parseFloat(
                                            consumptionCell.text.replace(
                                                /[^0-9.]/g,
                                                ""
                                            )
                                        );
                                        if (!isNaN(numericValue)) {
                                            totalConsumption += numericValue;
                                        }
                                    }

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

                                        if (j === 0 || j === 3 || j === 4) {
                                            cell.alignment = "center";
                                        }
                                    });
                                });

                                table.table.body.push([
                                    {
                                        text: "",
                                        colSpan: 3,
                                        fillColor: "#f0f0f0",
                                        border: [false, false, false, false],
                                    },
                                    {},
                                    {},
                                    {
                                        text: "Total Konsumsi",
                                        style: "totalLabel",
                                        border: [false, false, false, false],
                                    },
                                    {
                                        text:
                                            Math.round(totalConsumption) +
                                            " Liter",
                                        style: "totalValue",
                                        border: [false, false, false, false],
                                    },
                                ]);

                                table.layout = {
                                    hLineWidth: (i, node) =>
                                        i === 0 ||
                                        i === 1 ||
                                        i === node.table.body.length - 1 ||
                                        i === node.table.body.length
                                            ? 1
                                            : 0,
                                    vLineWidth: (i, node) => 0,
                                    hLineColor: (i, node) => {
                                        if (
                                            i === 0 ||
                                            i === 1 ||
                                            i === node.table.body.length - 1 ||
                                            i === node.table.body.length
                                        ) {
                                            return "#34495e";
                                        }
                                        return "#dfe6e9";
                                    },
                                    paddingTop: (i, node) => 6,
                                    paddingBottom: (i, node) => 6,
                                };
                            }
                        },
                    },
                ],
            },
        ],
        initComplete: function () {
            // 1. DATE PICKER INITIALIZATION
            var $datePickerInput = $(
                '<input type="text" class="form-control" id="datePickerFilter" placeholder="Pilih Tanggal">' // Tambahkan ID di sini
            ).appendTo($(".date_picker"));

            // Inisialisasi datepicker pada elemen yang baru dibuat
            $datePickerInput
                .datepicker({
                    format: "yyyy-mm-dd",
                    autoclose: true,
                    language: "id", // bahasa Indonesia
                    todayHighlight: true,
                })
                .on("changeDate", function (e) {
                    var selectedDate = e.format();
                    table.column(3).search(selectedDate).draw();

                    // Ketika datepicker digunakan, reset filter bulan & tahun
                    $("#monthFilter").val("");
                    $("#yearFilter").val("");
                });

            // 2. MONTH FILTER
            var monthSelect = $(
                '<select id="monthFilter" class="form-select"><option value="">Pilih Bulan</option></select>'
            )
                .appendTo(".month_filter")
                .on("change", function () {
                    applyCombinedMonthYearFilter();
                    // Ketika filter bulan/tahun digunakan, kosongkan datepicker
                    $datePickerInput.val("").datepicker("clear"); // Gunakan .clear() atau .update()
                });

            // 3. YEAR FILTER
            var yearSelect = $(
                '<select id="yearFilter" class="form-select"><option value="">Pilih Tahun</option></select>'
            )
                .appendTo(".year_filter")
                .on("change", function () {
                    applyCombinedMonthYearFilter();
                    // Ketika filter bulan/tahun digunakan, kosongkan datepicker
                    $datePickerInput.val("").datepicker("clear"); // Gunakan .clear() atau .update()
                });
            // COMBINED FILTER FUNCTION (Updated for admin)
            function applyCombinedMonthYearFilter() {
                var month = $("#monthFilter").val();
                var year = $("#yearFilter").val();

                if (month && year) {
                    // Search format: "yyyy-mm" (matches backend expectation)
                    table
                        .column(3)
                        .search(year + "-" + month)
                        .draw();
                } else if (month) {
                    // Search format: "-mm-" (matches backend expectation)
                    table
                        .column(3)
                        .search("-" + month + "-")
                        .draw();
                } else if (year) {
                    // Search format: "yyyy" (matches backend expectation)
                    table.column(3).search(year).draw();
                } else {
                    table.column(3).search("").draw();
                }
            }

            // MONTH OPTIONS (Indonesian)
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

            // YEAR OPTIONS
            for (var y = new Date().getFullYear(); y >= 2020; y--) {
                yearSelect.append(
                    '<option value="' + y + '">' + y + "</option>"
                );
            }

            // 4. RESET BUTTON
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

                    // Reset all filters
                    $datePickerInput.val("").datepicker("clear");
                    $("#monthFilter").val("");
                    $("#yearFilter").val("");
                    table.columns(3).search("").draw();

                    setTimeout(() => $icon.removeClass("rotating"), 1000);
                });
        },
    });
});
