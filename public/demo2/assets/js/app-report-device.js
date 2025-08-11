const DeviceUrl = document.getElementById("report-devices-datatable").dataset
    .url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var filterDateStr = $("#dateFilterInput").val();

        if (!filterDateStr) {
            return true;
        }

        var filterDate = new Date(filterDateStr + "T00:00:00");

        var rowData = data[4] || "";
        if (!rowData) {
            return false;
        }
        var rowDate = new Date(rowData);

        if (
            filterDate.getFullYear() === rowDate.getFullYear() &&
            filterDate.getMonth() === rowDate.getMonth() &&
            filterDate.getDate() === rowDate.getDate()
        ) {
            return true;
        }

        return false;
    });

    let table = $("#report-devices-datatable").DataTable({
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
            url: DeviceUrl,
        },
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
                    return full.device_type?.name ?? "-";
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
            {
                data: "id",
            },
            {
                data: "unique_id",
            },
            {
                data: "device_type_id",
            },
            {
                data: "status",
            },
            {
                data: "created_at",
            },
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
                    // {
                    //     extend: "csv",
                    //     text: '<i class="ti ti-file-text me-2" ></i>Csv',
                    //     className: "dropdown-item",
                    //     filename: function () {
                    //         var base = "Devices_List";
                    //         var date = new Date();
                    //         var timestamp =
                    //             date.getFullYear() +
                    //             "-" +
                    //             String(date.getMonth() + 1).padStart(2, "0") +
                    //             "-" +
                    //             String(date.getDate()).padStart(2, "0") +
                    //             "_" +
                    //             String(date.getHours()).padStart(2, "0") +
                    //             "-" +
                    //             String(date.getMinutes()).padStart(2, "0") +
                    //             "-" +
                    //             String(date.getSeconds()).padStart(2, "0");

                    //         return base + "_" + timestamp;
                    //     },

                    //     exportOptions: {
                    //         columns: [1, 2, 3, 4],
                    //         format: {
                    //             body: function (inner, coldex, rowdex) {
                    //                 if (inner.length <= 0) return inner;
                    //                 var el = $.parseHTML(inner);
                    //                 var result = "";
                    //                 $.each(el, function (index, item) {
                    //                     if (
                    //                         item.classList !== undefined &&
                    //                         item.classList.contains("user-name")
                    //                     ) {
                    //                         result =
                    //                             result +
                    //                             item.lastChild.firstChild
                    //                                 .textContent;
                    //                     } else if (
                    //                         item.innerText === undefined
                    //                     ) {
                    //                         result = result + item.textContent;
                    //                     } else result = result + item.innerText;
                    //                 });
                    //                 return result;
                    //             },
                    //         },
                    //     },
                    // },
                    {
                        extend: "excel",
                        text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                        className: "dropdown-item",
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
                            const hours = now
                                .getHours()
                                .toString()
                                .padStart(2, "0");
                            const minutes = now
                                .getMinutes()
                                .toString()
                                .padStart(2, "0");
                            const seconds = now
                                .getSeconds()
                                .toString()
                                .padStart(2, "0");
                            // Menggunakan format yang mirip dengan contoh PDF Anda: Laporan_Data_Alat_YYYY-MM-DD_HH-MM-SS.xlsx
                            return `Laporan_Data_Alat_${year}-${month}-${day}_${hours}-${minutes}-${seconds}`;
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
                    // {
                    //     extend: "copy",
                    //     text: '<i class="ti ti-copy me-2" ></i>Copy',
                    //     className: "dropdown-item",
                    //     exportOptions: {
                    //         columns: [1, 2, 3, 4],
                    //         format: {
                    //             body: function (inner, coldex, rowdex) {
                    //                 if (inner.length <= 0) return inner;
                    //                 var el = $.parseHTML(inner);
                    //                 var result = "";
                    //                 $.each(el, function (index, item) {
                    //                     if (
                    //                         item.classList !== undefined &&
                    //                         item.classList.contains("user-name")
                    //                     ) {
                    //                         result =
                    //                             result +
                    //                             item.lastChild.firstChild
                    //                                 .textContent;
                    //                     } else if (
                    //                         item.innerText === undefined
                    //                     ) {
                    //                         result = result + item.textContent;
                    //                     } else result = result + item.innerText;
                    //                 });
                    //                 return result;
                    //             },
                    //         },
                    //     },
                    // },
                ],
            },
        ],
        initComplete: function () {
            var dateInput = $(
                '<input type="text" id="dateFilterInput" class="form-control" placeholder="Pilih Tanggal">'
            )
                .appendTo($(".date_filter"))
                .datepicker({
                    format: "yyyy-mm-dd",
                    autoclose: true,
                    language: "id",
                    todayHighlight: true,
                })
                .on("changeDate", function (e) {
                    table.draw();

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
                        .column(4)
                        .search(searchTerm, true, false, true)
                        .draw();
                } else if (month) {
                    table
                        .column(4)
                        .search("-" + month + "-", true, false, true)
                        .draw();
                } else if (year) {
                    table.column(4).search(year, true, false, true).draw();
                } else {
                    table.column(4).search("").draw();
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
                    table.column(4).search("").draw();

                    setTimeout(function () {
                        $icon.removeClass("rotating");
                    }, 1000);
                });
        },
    });
});
