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
                    // {
                    //     extend: "print",
                    //     text: '<i class="ti ti-printer me-2" ></i>Print',
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
                    //     customize: function (win) {
                    //         $(win.document.body)
                    //             .css("color", headingColor)
                    //             .css("border-color", borderColor)
                    //             .css("background-color", bodyBg);
                    //         $(win.document.body)
                    //             .find("table")
                    //             .addClass("compact")
                    //             .css("color", "inherit")
                    //             .css("border-color", "inherit")
                    //             .css("background-color", "inherit");
                    //     },
                    // },
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

                        // 1. Judul di dalam file Excel
                        title: "Laporan Data Alat",

                        // 2. Nama file yang sederhana dengan tanggal
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
                            return `Laporan_Data_Alat_${day}-${month}-${year}`;
                        },

                        exportOptions: {
                            // 3. Ekspor semua kolom yang terlihat di tabel
                            columns: ":visible",

                            // 4. Fungsi untuk membersihkan HTML dari sel
                            format: {
                                body: function (data, row, column, node) {
                                    // Jika data bukan string (misal: angka), kembalikan apa adanya
                                    if (
                                        typeof data !== "string" ||
                                        data.length === 0
                                    ) {
                                        return data;
                                    }
                                    // Cara aman untuk menghapus tag HTML (seperti badge status)
                                    // dan hanya menyisakan teksnya saja.
                                    return $("<div>").html(data).text().trim();
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
                        title: "", // Dikosongkan agar diatur oleh customize

                        // 1. Nama file sederhana dengan tanggal
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
                            return `Laporan_Data_Alat_${day}-${month}-${year}`;
                        },

                        exportOptions: {
                            // Ekspor semua kolom yang terlihat
                            columns: ":visible",
                            format: {
                                body: function (data, type, row, column, node) {
                                    // Membersihkan HTML dari sel (misal: badge status)
                                    if (
                                        typeof data !== "string" ||
                                        data.length === 0
                                    ) {
                                        return data;
                                    }
                                    return $("<div>").html(data).text().trim();
                                },
                            },
                        },

                        // 2. Kustomisasi dokumen agar sesuai dengan gaya referensi Anda
                        customize: function (doc) {
                            // --- Konfigurasi dan Gaya Umum ---
                            doc.pageMargins = [40, 90, 40, 60];
                            doc.defaultStyle.fontSize = 10;
                            doc.defaultStyle.color = "#333";

                            // --- Ambil Data dari Blade ---
                            const pdfData = window.pdfExportData || {};
                            const appName =
                                pdfData.appName || "Sistem Monitoring";
                            const appAddress = pdfData.appAddress || "";
                            const appUrl = pdfData.appUrl || "";
                            const appPhone = pdfData.appPhone || "";
                            const currentUser = pdfData.userName || "System";
                            const userRole = pdfData.userRole || "";

                            // --- Header (Kop Surat) Dokumen ---
                            doc.header = function (
                                currentPage,
                                pageCount,
                                pageSize
                            ) {
                                return {
                                    stack: [
                                        {
                                            text: appName,
                                            style: "companyName",
                                            margin: [0, 0, 0, 2],
                                        },
                                        {
                                            text: appAddress,
                                            style: "companyAddress",
                                            margin: [0, 0, 0, 2],
                                        },
                                        {
                                            text: `${appUrl} | ${appPhone}`,
                                            style: "companyAddress",
                                            margin: [0, 0, 0, 5],
                                        },
                                        {
                                            canvas: [
                                                {
                                                    type: "line",
                                                    x1: 0,
                                                    y1: 5,
                                                    x2: pageSize.width - 80,
                                                    y2: 5,
                                                    lineWidth: 2,
                                                    lineColor: "#34495e",
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
                                                    lineColor: "#bdc3c7",
                                                },
                                            ],
                                            margin: [0, 0, 0, 10],
                                        },
                                    ],
                                    margin: [40, 20, 40, 0],
                                };
                            };

                            // --- Footer Dokumen ---
                            doc.footer = function (currentPage, pageCount) {
                                const printDate = new Date().toLocaleDateString(
                                    "id-ID",
                                    {
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    }
                                );
                                return {
                                    columns: [
                                        {
                                            text: `Dicetak oleh: ${currentUser} (${userRole}) pada ${printDate}`,
                                            style: "footerText",
                                            alignment: "left",
                                        },
                                        {
                                            text: `Halaman ${currentPage} dari ${pageCount}`,
                                            style: "footerText",
                                            alignment: "right",
                                        },
                                    ],
                                    margin: [40, 10, 40, 0],
                                };
                            };

                            // --- Definisi Gaya ---
                            doc.styles = {
                                companyName: {
                                    fontSize: 12,
                                    bold: true,
                                    color: "#2c3e50",
                                    alignment: "left",
                                },
                                companyAddress: {
                                    fontSize: 9,
                                    color: "#7f8c8d",
                                    alignment: "left",
                                },
                                reportTitle: {
                                    fontSize: 16,
                                    bold: true,
                                    color: "#34495e",
                                    alignment: "center",
                                    margin: [0, 15, 0, 15],
                                },
                                tableHeader: {
                                    bold: true,
                                    fontSize: 10,
                                    color: "white",
                                    fillColor: "#34495e",
                                    alignment: "center",
                                },
                                tableBodyOdd: {
                                    fontSize: 9,
                                    color: "#2c3e50",
                                    fillColor: "#ffffff",
                                },
                                tableBodyEven: {
                                    fontSize: 9,
                                    color: "#2c3e50",
                                    fillColor: "#f8f9fa",
                                },
                                footerText: { fontSize: 8, color: "#7f8c8d" },
                            };

                            // --- Sisipkan Judul Laporan ---
                            const tableIndex = doc.content.findIndex(
                                (item) => item.table
                            );
                            if (tableIndex !== -1) {
                                doc.content.splice(tableIndex, 0, {
                                    text: "LAPORAN DATA ALAT",
                                    style: "reportTitle",
                                });
                            }

                            // --- Penyesuaian Tabel ---
                            doc.content.forEach(function (content) {
                                if (content.table) {
                                    // Atur lebar kolom agar otomatis
                                    content.table.widths = Array(
                                        content.table.body[0].length + 1
                                    )
                                        .join("*")
                                        .split("");

                                    // Terapkan gaya pada setiap sel
                                    content.table.body.forEach(function (
                                        row,
                                        i
                                    ) {
                                        row.forEach(function (cell) {
                                            cell.style =
                                                i === 0
                                                    ? "tableHeader"
                                                    : i % 2 === 0
                                                    ? "tableBodyEven"
                                                    : "tableBodyOdd";
                                            cell.margin = [5, 5, 5, 5];
                                            cell.alignment = "center";
                                        });
                                    });
                                }
                            });
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
