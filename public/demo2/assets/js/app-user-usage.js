const ConsumptionUrl = document.getElementById("user-consumption-datatable")
    .dataset.url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#user-consumption-datatable").DataTable({
        processing: true,
        serverSide: true,
        ajax: ConsumptionUrl,
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
        columns: [
            // Kolom ini hanya untuk nomor urut, 'data' bisa null
            { data: null, name: "nomor", orderable: false, searchable: false },
            // Kolom ini akan mengambil data 'usage_date'
            { data: "usage_date", name: "usage_date" },
            // Kolom ini akan mengambil data 'total_consumption'
            { data: "total_consumption", name: "total_consumption" },
        ],
        columnDefs: [
            {
                targets: 0,
                className: "text-center",
                render: function (data, type, full, meta) {
                    // Logika nomor urut tidak berubah
                    return meta.row + 1;
                },
            },
            {
                targets: 1,
                className: "text-center",
                render: function (data, type, full, meta) {
                    // Menggunakan 'full.usage_date' sebagai sumber data
                    return full.usage_date
                        ? new Date(full.usage_date).toLocaleDateString(
                              "id-ID",
                              {
                                  day: "2-digit",
                                  month: "long",
                                  year: "numeric",
                              }
                          )
                        : "-";
                },
            },
            {
                targets: 2,
                className: "text-center",
                render: function (data, type, full, meta) {
                    // Logika ini sudah benar, hanya memastikan sumber datanya benar
                    const consumption = parseFloat(full.total_consumption);
                    return !isNaN(consumption)
                        ? `${consumption.toFixed(2)} Liter`
                        : "-";
                },
            },
        ],
        lengthMenu: [
            [10, 20, 50, 100, 200, -1],
            [10, 20, 50, 100, 200, "Semua"],
        ],
        language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Cari...",
            paginate: {
                next: '<i class="ti ti-chevron-right ti-sm"></i>',
                previous: '<i class="ti ti-chevron-left ti-sm"></i>',
            },
            emptyTable: "Tidak ada data penggunaan air",
            zeroRecords: "Data tidak ditemukan",
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
                            columns: [1, 2],
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
                            columns: [1, 2],
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
                            columns: [0, 1, 2],
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
                            // --- Konfigurasi dan Gaya ---
                            doc.pageMargins = [40, 80, 40, 60];
                            doc.defaultStyle.fontSize = 10;
                            doc.defaultStyle.color = "#333";

                            // Menambahkan gaya baru untuk baris total
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

                            // --- Menambahkan Judul Laporan di Atas Tabel ---
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

                            // --- Menyesuaikan Tabel dan Menghitung Total ---
                            const table = doc.content.find((c) => c.table);
                            if (table) {
                                table.table.widths = [30, "*", "auto"];

                                let totalConsumption = 0;

                                table.table.body.forEach((row, i) => {
                                    if (i === 0) {
                                        row.forEach((cell) => {
                                            cell.style = "tableHeader";
                                            cell.margin = [0, 4, 0, 4];
                                        });
                                        return;
                                    }

                                    const consumptionCell = row[2];
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
                                        // DIUBAH: Kolom ke-2 (indeks 1) ditambahkan untuk di-tengahkan
                                        if (j === 0 || j === 1 || j === 2) {
                                            cell.alignment = "center";
                                        }
                                    });
                                });

                                // Struktur baris total disesuaikan agar sejajar
                                table.table.body.push([
                                    {
                                        text: "",
                                        colSpan: 1,
                                        fillColor: "#f0f0f0",
                                        border: [false, false, false, false],
                                    },
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
            // 1. Inisialisasi datepicker
            // Penting: Beri ID unik pada input datepicker yang dibuat.
            // Dan simpan referensi ke instance datepicker.
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
                    table.column(1).search(selectedDate).draw();

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

            // Function to handle combined month+year filtering
            function applyCombinedMonthYearFilter() {
                var month = $("#monthFilter").val();
                var year = $("#yearFilter").val();

                if (month && year) {
                    var searchTerm = year + "-" + month;
                    table
                        .column(1)
                        .search(searchTerm, true, false, true)
                        .draw();
                } else if (month) {
                    table
                        .column(1)
                        .search("-" + month + "-", true, false, true)
                        .draw();
                } else if (year) {
                    table.column(1).search(year, true, false, true).draw();
                } else {
                    table.column(1).search("").draw();
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
                .insertAfter($(".year_filter")) // Pastikan ini diinsert setelah container year_filter
                .on("click", function () {
                    var $icon = $(this).find("i");

                    // Tambahkan kelas animasi
                    $icon.addClass("rotating");

                    // --- Perbaikan di sini ---
                    // 1. Reset datepicker
                    $datePickerInput.val("").datepicker("clear"); // Menggunakan method 'clear' dari datepicker

                    // 2. Reset filter bulan & tahun
                    $("#monthFilter").val("");
                    $("#yearFilter").val("");

                    // 3. Hapus pencarian DataTables
                    table.column(1).search("").draw();

                    // Hentikan animasi setelah 1 detik
                    setTimeout(function () {
                        $icon.removeClass("rotating");
                    }, 1000);
                });
        },
        // Add this to your DataTables initialization
    });
});
