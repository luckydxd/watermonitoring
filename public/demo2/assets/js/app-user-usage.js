$(document).ready(function () {
    const consumptionUrl = $("#user-consumption-datatable").data("url");

    let table, yearSelect, monthSelect, datePickerInput;

    const defaultFilter = {
        period: "yearly",
        year: new Date().getFullYear(),
        month: new Date().getMonth() + 1,
    };

    let currentFilter = { ...defaultFilter };

    function reloadTable(callback) {
        console.log("=== RELOAD TABLE ===");
        console.log(
            "Sending filter to server:",
            JSON.stringify(currentFilter, null, 2)
        );

        // Build URL dengan parameter
        const params = new URLSearchParams();

        if (currentFilter.period) params.append("period", currentFilter.period);
        if (currentFilter.year) params.append("year", currentFilter.year);
        if (currentFilter.month) params.append("month", currentFilter.month);
        if (currentFilter.date) params.append("date", currentFilter.date);

        const fullUrl = consumptionUrl + "?" + params.toString();
        console.log("Full URL:", fullUrl);

        table.ajax.url(fullUrl).load(function (json) {
            console.log("Server response:", json);
            if (callback) callback(json);
        });
    }

    function updateUI() {
        if (table) {
            const header = table.column(1).header();
            let newTitle = "Periode";

            switch (currentFilter.period) {
                case "yearly":
                    newTitle = "Tahun";
                    break;
                case "monthly":
                    newTitle = "Bulan";
                    break;
                case "daily":
                    newTitle = "Tanggal";
                    break;
                default:
                    if (currentFilter.date) {
                        newTitle = "Tanggal";
                    }
            }
            $(header).html(newTitle);

            // Sinkronkan nilai dropdown filter dengan state saat ini
            if (yearSelect && monthSelect) {
                yearSelect.val(currentFilter.year || "");
                monthSelect.val(currentFilter.month || "");
            }
        }
    }

    // Inisialisasi DataTables
    table = $("#user-consumption-datatable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: consumptionUrl,
            data: function (d) {
                // Kirim parameter filter ke server setiap request
                return $.extend({}, d, currentFilter);
            },
            error: function (xhr, error, code) {
                console.error("DataTables Error:", xhr.responseText);
            },
        },
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
            { data: null, name: "nomor", orderable: false, searchable: false },
            { data: "formatted_date", name: "formatted_date" },
            { data: "formatted_consumption", name: "total_consumption" },
            {
                data: "date",
                visible: false,
                defaultContent: "",
            },
        ],
        order: [[3, "desc"]],
        columnDefs: [
            {
                targets: 0,
                className: "text-center",
                render: (data, type, row, meta) =>
                    meta.row + 1 + meta.settings._iDisplayStart,
            },
            {
                targets: 1,
                className: "text-center cursor-pointer drill-down-cell",
                createdCell: function (td, cellData, rowData, row, col) {
                    // Tambahkan style untuk menunjukkan bisa diklik (kecuali untuk daily dan date specific)
                    if (
                        currentFilter.period !== "daily" &&
                        !currentFilter.date
                    ) {
                        $(td)
                            .addClass("text-primary")
                            .attr("title", "Klik untuk drill down");
                        $(td).append(
                            ' <i class="ti ti-chevrons-down ti-xs"></i>'
                        );
                    }
                },
            },
            {
                targets: 2,
                className: "text-center",
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
            processing: "Memuat data...",
        },
        buttons: [
            {
                text: '<i class="ti ti-arrow-up me-0  me-sm-1 ti-xs"></i>Roll Up',
                className: "btn btn-outline-primary me-2 roll-up-btn",
                action: function (e, dt, node, config) {
                    e.preventDefault();
                    console.log(
                        "Roll Up button clicked from DataTables button"
                    );
                    rollUp();
                },
            },
            {
                extend: "collection",
                className:
                    "btn btn-label-secondary dropdown-toggle mx-2 waves-effect waves-light",
                text: '<i class="ti ti-upload me-2 ti-xs"></i>Ekspor',
                buttons: [
                    {
                        extend: "print",
                        text: '<i class="ti ti-printer me-2"></i>Print',
                        className: "dropdown-item",
                        exportOptions: { columns: [1, 2] },
                        title: function () {
                            return `Laporan Konsumsi Air - ${getCurrentPeriodLabel()}`;
                        },
                    },
                    {
                        extend: "excel",
                        text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                        className: "dropdown-item",
                        filename: function () {
                            const base = "Water_Consumption_Report";
                            const timestamp = new Date()
                                .toISOString()
                                .slice(0, 19)
                                .replace(/[:-]/g, "");
                            return `${base}_${getCurrentPeriodLabel()}_${timestamp}`;
                        },
                        exportOptions: { columns: [1, 2] },
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
                            doc.pageMargins = [40, 90, 40, 60];
                            doc.defaultStyle.fontSize = 10;
                            doc.defaultStyle.color = "#333";

                            const appName = window.pdfExportData.appName;
                            const appAddress = window.pdfExportData.appAddress;
                            const appUrl = window.pdfExportData.appUrl;
                            const appPhone = window.pdfExportData.appPhone;
                            const currentUser = window.pdfExportData.userName;
                            const userRole = window.pdfExportData.userRole;

                            // Gaya untuk informasi pencetak
                            doc.styles.printInfo = {
                                fontSize: 8,
                                color: "#7f8c8d",
                                alignment: "left",
                                margin: [0, 10, 0, 5],
                            };

                            doc.styles.totalLabel = {
                                bold: true,
                                fontSize: 10,
                                alignment: "right",
                                fillColor: "#f8f9fa",
                                color: "#2c3e50",
                            };
                            doc.styles.totalValue = {
                                bold: true,
                                fontSize: 10,
                                alignment: "center",
                                fillColor: "#f8f9fa",
                                color: "#2c3e50",
                            };
                            doc.styles.companyName = {
                                fontSize: 12,
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
                                fontSize: 11,
                                color: "white",
                                fillColor: "#34495e",
                                alignment: "center",
                            };
                            doc.styles.tableBodyOdd = {
                                fontSize: 10,
                                color: "#2c3e50",
                            };
                            doc.styles.tableBodyEven = {
                                fillColor: "#f8f9fa",
                                fontSize: 10,
                                color: "#2c3e50",
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
                                        { text: appName, style: "companyName" },
                                        {
                                            text: appAddress,
                                            style: "companyAddress",
                                        },
                                        {
                                            text: `${appUrl} | ${appPhone}`,
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
                                        },
                                    ],
                                    margin: [40, 20, 40, 0],
                                };
                            };

                            // --- Footer Dokumen ---
                            doc.footer = function (currentPage, pageCount) {
                                // Mendapatkan informasi user dan tanggal
                                const now = new Date();
                                const printDate = now.toLocaleDateString(
                                    "id-ID",
                                    {
                                        weekday: "long",
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    }
                                );
                                const printTime = now.toLocaleTimeString(
                                    "id-ID",
                                    {
                                        hour: "2-digit",
                                        minute: "2-digit",
                                        second: "2-digit",
                                    }
                                );

                                return {
                                    stack: [
                                        // Garis pemisah
                                        {
                                            canvas: [
                                                {
                                                    type: "line",
                                                    x1: 0,
                                                    y1: 0,
                                                    x2: 515,
                                                    y2: 0,
                                                    lineWidth: 0.5,
                                                    lineColor: "#bdc3c7",
                                                },
                                            ],
                                            margin: [40, 0, 40, 10],
                                        },
                                        // Informasi footer dalam tiga kolom
                                        {
                                            columns: [
                                                {
                                                    width: "*",
                                                    stack: [
                                                        {
                                                            alignment: "left",
                                                            text: `Dicetak oleh: ${currentUser} (${userRole})`, // Menambahkan role
                                                            style: "footerText",
                                                        },
                                                        {
                                                            text: `Tanggal: ${printDate}`,
                                                            style: "footerText",
                                                            alignment: "left",
                                                            margin: [
                                                                0, 2, 0, 0,
                                                            ],
                                                        },
                                                        {
                                                            text: `Waktu: ${printTime}`,
                                                            style: "footerText",
                                                            alignment: "left",
                                                            margin: [
                                                                0, 2, 0, 0,
                                                            ],
                                                        },
                                                    ],
                                                },
                                                {
                                                    width: "*",
                                                    text: "Dokumen ini valid dan dibuat oleh sistem secara otomatis.",
                                                    style: "footerText",
                                                    alignment: "center",
                                                },
                                                {
                                                    width: "*",
                                                    text: `Halaman ${currentPage} dari ${pageCount}`,
                                                    style: "footerText",
                                                    alignment: "right",
                                                },
                                            ],
                                            margin: [40, 0, 40, 15],
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
                                table.table.widths = [40, "*", 100];

                                let totalConsumption = 0;
                                table.table.dontBreakRows = true;

                                table.table.body.forEach((row, i) => {
                                    if (i === 0) {
                                        // Header row
                                        row.forEach((cell) => {
                                            cell.style = "tableHeader";
                                            cell.margin = [8, 8, 8, 8];
                                            cell.border = [
                                                true,
                                                true,
                                                true,
                                                true,
                                            ];
                                            cell.borderColor = [
                                                "#e3e6ea",
                                                "#e3e6ea",
                                                "#e3e6ea",
                                                "#e3e6ea",
                                            ];
                                        });
                                        return;
                                    }

                                    // Ganti bagian perhitungan total di dalam customize function:

                                    const consumptionCell = row[2];
                                    if (consumptionCell) {
                                        // Mengambil teks dari cell
                                        let textValue =
                                            consumptionCell.text.toString();
                                        console.log("Raw text:", textValue); // Debug log

                                        // Hapus kata "Liter" dan karakter non-numeric kecuali angka, titik, dan koma
                                        let numericText = textValue
                                            .replace(/\s*Liter\s*/gi, "")
                                            .replace(/[^\d.,]/g, "");
                                        console.log(
                                            "After removing Liter:",
                                            numericText
                                        ); // Debug log

                                        // Jika menggunakan format Indonesia (titik sebagai pemisah ribuan, koma sebagai desimal)
                                        if (
                                            numericText.includes(".") &&
                                            numericText.includes(",")
                                        ) {
                                            // Format: 1.234,56
                                            numericText = numericText
                                                .replace(/\./g, "")
                                                .replace(",", ".");
                                        } else if (
                                            numericText.includes(".") &&
                                            !numericText.includes(",")
                                        ) {
                                            // Bisa jadi format: 1234.56 atau 1.234 (ribuan)
                                            let parts = numericText.split(".");
                                            if (
                                                parts.length === 2 &&
                                                parts[1].length <= 2
                                            ) {
                                                // Format desimal: 1234.56
                                                numericText = numericText;
                                            } else {
                                                // Format ribuan: 1.234 atau 1.234.567
                                                numericText =
                                                    numericText.replace(
                                                        /\./g,
                                                        ""
                                                    );
                                            }
                                        } else if (numericText.includes(",")) {
                                            // Format: 1234,56
                                            numericText = numericText.replace(
                                                ",",
                                                "."
                                            );
                                        }

                                        console.log(
                                            "Final numeric text:",
                                            numericText
                                        ); // Debug log
                                        const numericValue =
                                            parseFloat(numericText);
                                        console.log(
                                            "Parsed value:",
                                            numericValue
                                        ); // Debug log

                                        if (!isNaN(numericValue)) {
                                            totalConsumption += numericValue;
                                        }
                                    }

                                    // Data rows
                                    row.forEach((cell, j) => {
                                        cell.style =
                                            i % 2 === 0
                                                ? "tableBodyEven"
                                                : "tableBodyOdd";
                                        cell.margin = [8, 6, 8, 6];
                                        cell.border = [true, true, true, true];
                                        cell.borderColor = [
                                            "#e3e6ea",
                                            "#e3e6ea",
                                            "#e3e6ea",
                                            "#e3e6ea",
                                        ];

                                        // Alignment berdasarkan kolom
                                        if (j === 0 || j === 2) {
                                            // No dan Konsumsi - center
                                            cell.alignment = "center";
                                        } else if (j === 1) {
                                            // Nama - left
                                            cell.alignment = "left";
                                        }
                                    });
                                });

                                // Baris total
                                // Bagian untuk mengganti di customize function:

                                // Baris total - PERBAIKAN BORDER
                                table.table.body.push([
                                    {
                                        text: "",
                                        colSpan: 1,
                                        fillColor: "#f8f9fa",
                                        border: [true, true, false, true],
                                        borderColor: [
                                            "#e3e6ea",
                                            "#e3e6ea",
                                            "#e3e6ea",
                                            "#e3e6ea",
                                        ],
                                        margin: [8, 8, 8, 8],
                                    },
                                    {
                                        text: "TOTAL KONSUMSI",
                                        style: "totalLabel",
                                        border: [false, true, false, true],
                                        borderColor: [
                                            "#e3e6ea",
                                            "#e3e6ea",
                                            "#e3e6ea",
                                            "#e3e6ea",
                                        ],
                                        margin: [8, 8, 8, 8],
                                    },
                                    // Dan ganti bagian display total:
                                    {
                                        text:
                                            totalConsumption.toLocaleString(
                                                "id-ID",
                                                {
                                                    // minimumFractionDigits: 2,
                                                    // maximumFractionDigits: 2,
                                                }
                                            ) + " Liter",
                                        style: "totalValue",
                                        border: [false, true, true, true],
                                        borderColor: [
                                            "#e3e6ea",
                                            "#e3e6ea",
                                            "#e3e6ea",
                                            "#e3e6ea",
                                        ],
                                        margin: [8, 8, 8, 8],
                                    },
                                ]);

                                // Layout tabel yang diperbaiki - PERBAIKAN BORDER BAWAH
                                table.layout = {
                                    hLineWidth: function (i, node) {
                                        if (i === 0 || i === 1) return 2; // Header
                                        if (i === node.table.body.length)
                                            return 2; // Border bawah tabel (setelah total)
                                        return 0.5; // Data rows
                                    },
                                    vLineWidth: function (i, node) {
                                        if (
                                            i === 0 ||
                                            i === node.table.widths.length
                                        )
                                            return 2; // Outer borders
                                        return 0.5; // Inner borders
                                    },
                                    hLineColor: function (i, node) {
                                        if (i === 0 || i === 1)
                                            return "#34495e"; // Header
                                        if (i === node.table.body.length)
                                            return "#34495e"; // Border bawah tabel
                                        return "#e3e6ea"; // Data rows
                                    },
                                    vLineColor: function (i, node) {
                                        if (
                                            i === 0 ||
                                            i === node.table.widths.length
                                        )
                                            return "#34495e"; // Outer borders
                                        return "#e3e6ea"; // Inner borders
                                    },
                                    paddingTop: function (i, node) {
                                        return i === 0 ? 8 : 6;
                                    },
                                    paddingBottom: function (i, node) {
                                        return i === 0 ? 8 : 6;
                                    },
                                    paddingLeft: function (i, node) {
                                        return 8;
                                    },
                                    paddingRight: function (i, node) {
                                        return 8;
                                    },
                                };

                                // Menambahkan margin untuk tabel
                                table.margin = [0, 10, 0, 20];
                            }
                        },
                    },
                ],
            },
        ],

        initComplete: function () {
            // Inisialisasi elemen filter
            initializeFilters();

            // Update visibility tombol roll up
            updateRollUpButton();

            console.log("DataTable initialized successfully");
        },
        drawCallback: function () {
            updateUI();
            updateRollUpButton();
        },
    });

    // ===================================================================================
    // 3. FUNGSI INISIALISASI FILTER
    // ===================================================================================
    function initializeFilters() {
        // Date Picker
        datePickerInput = $(
            '<input type="text" class="form-control" placeholder="Pilih Tanggal Spesifik" readonly>'
        ).appendTo($(".date_picker"));

        // Month Filter
        monthSelect = $(
            '<select id="monthFilter" class="form-select text-capitalize">' +
                '<option value="">Pilih Bulan</option></select>'
        ).appendTo($(".month_filter"));

        // Year Filter
        yearSelect = $(
            '<select id="yearFilter" class="form-select text-capitalize">' +
                '<option value="">Pilih Tahun</option></select>'
        ).appendTo($(".year_filter"));

        // Setup DatePicker sebagai fungsi terpisah
        initDatePicker();

        // Year Filter Handler
        yearSelect.on("change", function () {
            const year = $(this).val();
            const month = monthSelect.val();

            // Clear date picker
            datePickerInput.val("").datepicker("clearDates");

            if (year && month) {
                // Drill down ke daily jika ada year dan month
                currentFilter = {
                    period: "daily",
                    year: parseInt(year),
                    month: parseInt(month),
                };
            } else if (year) {
                // Drill down ke monthly jika hanya ada year
                currentFilter = {
                    period: "monthly",
                    year: parseInt(year),
                };
            } else {
                // Kembali ke yearly jika tidak ada selection
                currentFilter = { period: "yearly" };
            }
            reloadTable();
        });

        // Month Filter Handler
        monthSelect.on("change", function () {
            const month = $(this).val();
            const year = yearSelect.val();

            datePickerInput.val("").datepicker("clearDates");

            if (year && month) {
                currentFilter = {
                    period: "daily",
                    year: parseInt(year),
                    month: parseInt(month),
                };
                reloadTable();
            } else if (!year && month) {
                // Me-reset bulan, memberi peringatan, DAN memuat data tahunan
                $(this).val("");
                Notiflix.Notify.warning("Silakan pilih tahun terlebih dahulu.");

                // Atur filter untuk menampilkan data tahunan
                currentFilter = { period: "yearly" };
                // Muat ulang tabel
                reloadTable();
            }
        });

        // Populate filter options
        populateFilterOptions();

        // Reset Button
        createResetButton();
    }

    // Fungsi terpisah untuk inisialisasi date picker
    function initDatePicker() {
        datePickerInput
            .datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                language: "id",
                todayHighlight: true,
            })
            .on("changeDate", function (e) {
                // Cek apakah sedang dalam proses update UI
                if (window.updatingFiltersUI) {
                    console.log("Ignoring date change during UI update");
                    return;
                }

                const selectedDate = e.format("yyyy-mm-dd");
                console.log("Date picker changed to:", selectedDate);
                currentFilter = { date: selectedDate };
                reloadTable();
                // Clear other filters visually
                monthSelect.val("");
                yearSelect.val("");
            });
    }

    // Populate dropdown options
    function populateFilterOptions() {
        // Month options
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

        monthNames.forEach((name, index) => {
            monthSelect.append(`<option value="${index + 1}">${name}</option>`);
        });

        // Year options (last 10 years)
        const currentYear = new Date().getFullYear();
        for (let y = currentYear; y >= currentYear - 10; y--) {
            yearSelect.append(`<option value="${y}">${y}</option>`);
        }
    }

    // Create reset button
    function createResetButton() {
        $(
            '<button class="btn btn-outline-secondary p-0 d-flex align-items-center justify-content-center" ' +
                'id="resetFilter" style="width: 38px; height: 38px" title="Reset ke Tampilan Tahunan">' +
                '<i class="ti ti-restore" style="font-size: 1.2rem"></i></button>'
        )
            .appendTo($(".reset_filter"))
            .on("click", function () {
                resetFilters();
            });
    }

    // ===================================================================================
    // 4. FUNGSI DRILL DOWN & ROLL UP
    // ===================================================================================

    // Drill Down - Handle click pada baris tabel
    $("#user-consumption-datatable tbody").on(
        "click",
        ".drill-down-cell",
        function () {
            const data = table.row($(this).closest("tr")).data();
            if (!data) return;

            // Prevent drill down jika sudah di level daily atau date specific
            if (currentFilter.period === "daily" || currentFilter.date) return;

            if (currentFilter.period === "yearly") {
                // Drill down dari yearly ke monthly
                currentFilter = {
                    period: "monthly",
                    year: parseInt(data.year),
                };
            } else if (currentFilter.period === "monthly") {
                // Drill down dari monthly ke daily
                currentFilter = {
                    period: "daily",
                    year: parseInt(data.year),
                    month: parseInt(data.month),
                };
            }

            reloadTable();
        }
    );

    // FIXED Roll Up function
    function rollUp() {
        console.log("=== ROLL UP FUNCTION CALLED ===");
        console.log(
            "Current filter before roll up:",
            JSON.stringify(currentFilter, null, 2)
        );

        let newFilter = {};

        // Tentukan filter baru berdasarkan kondisi saat ini
        if (currentFilter.date) {
            // Dari date specific ke daily berdasarkan bulan dari tanggal
            const date = new Date(currentFilter.date);
            newFilter = {
                period: "daily",
                year: date.getFullYear(),
                month: date.getMonth() + 1,
            };
            console.log("Rolling up from DATE SPECIFIC to DAILY");
        } else if (currentFilter.period === "daily") {
            // Dari daily ke monthly - hapus month, pertahankan year
            newFilter = {
                period: "monthly",
                year: currentFilter.year,
            };
            console.log("Rolling up from DAILY to MONTHLY");
        } else if (currentFilter.period === "monthly") {
            // Dari monthly ke yearly - hapus semua filter kecuali period
            newFilter = {
                period: "yearly",
            };
            console.log("Rolling up from MONTHLY to YEARLY");
        } else {
            console.log(
                "Already at highest level (yearly) - cannot roll up further"
            );
            Notiflix.Notify.info(
                "Sudah berada pada level tampilan tertinggi (Tahunan)"
            );
            return;
        }

        // Set filter baru
        currentFilter = newFilter;
        console.log(
            "New filter after roll up:",
            JSON.stringify(currentFilter, null, 2)
        );

        // Reload tabel dengan filter baru TANPA memanggil updateFiltersUI terlebih dahulu
        reloadTable(() => {
            console.log("Table reloaded successfully after roll up");
            // Update UI filters SETELAH table berhasil reload
            updateFiltersUI();
        });
    }

    // Fungsi untuk update filter UI sesuai dengan current state
    function updateFiltersUI() {
        console.log(
            "Updating filters UI with:",
            JSON.stringify(currentFilter, null, 2)
        );

        // Temporary flag untuk mencegah event handler mengubah currentFilter
        window.updatingFiltersUI = true;

        // Clear semua input terlebih dahulu
        datePickerInput.val("");
        // Gunakan destroy dan reinit untuk menghindari event handler
        if (datePickerInput.data("datepicker")) {
            datePickerInput.datepicker("destroy");
        }

        yearSelect.val("");
        monthSelect.val("");

        // Set nilai sesuai dengan current filter - HANYA jika ada nilai
        if (currentFilter.year && currentFilter.year !== "") {
            yearSelect.val(currentFilter.year);
            console.log("Set year dropdown to:", currentFilter.year);
        }
        if (currentFilter.month && currentFilter.month !== "") {
            monthSelect.val(currentFilter.month);
            console.log("Set month dropdown to:", currentFilter.month);
        }
        if (currentFilter.date && currentFilter.date !== "") {
            datePickerInput.val(currentFilter.date);
            console.log("Set date picker to:", currentFilter.date);
        }

        // Reinitialize datepicker setelah set nilai
        initDatePicker();

        // Clear flag
        window.updatingFiltersUI = false;
    }

    // Update visibility tombol Roll Up
    function updateRollUpButton() {
        const rollUpBtn = $(".roll-up-btn");
        const canRollUp =
            currentFilter.date ||
            currentFilter.period === "daily" ||
            currentFilter.period === "monthly";

        if (canRollUp) {
            rollUpBtn.show().removeClass("d-none");
            console.log(
                "Roll up button enabled for period:",
                currentFilter.period
            );
        } else {
            rollUpBtn.hide().addClass("d-none");
            console.log(
                "Roll up button disabled for period:",
                currentFilter.period
            );
        }

        // Update button text based on current state
        let buttonText = '<i class="ti ti-arrow-up me-1"></i>Roll Up';
        if (currentFilter.date) {
            buttonText = '<i class="ti ti-arrow-up me-1"></i>Ke Harian';
        } else if (currentFilter.period === "daily") {
            buttonText = '<i class="ti ti-arrow-up me-1"></i>Ke Bulanan';
        } else if (currentFilter.period === "monthly") {
            buttonText = '<i class="ti ti-arrow-up me-1"></i>Ke Tahunan';
        }

        rollUpBtn.html(buttonText);
    }

    // Reset semua filter
    function resetFilters() {
        const $icon = $("#resetFilter i");
        $icon.addClass("rotating");

        console.log("Resetting filters to default");

        // Set flag untuk mencegah event handler
        window.updatingFiltersUI = true;

        // Reset ke state default
        currentFilter = { period: "yearly" };

        // Clear UI elements dengan aman
        datePickerInput.val("");
        if (datePickerInput.data("datepicker")) {
            datePickerInput.datepicker("destroy");
        }
        yearSelect.val("");
        monthSelect.val("");

        // Reinit date picker
        initDatePicker();

        // Clear flag
        window.updatingFiltersUI = false;

        // Reload table
        reloadTable();

        setTimeout(() => {
            $icon.removeClass("rotating");
        }, 1000);
    }

    // Helper function untuk mendapatkan label periode saat ini
    function getCurrentPeriodLabel() {
        if (currentFilter.date) {
            return currentFilter.date;
        }

        switch (currentFilter.period) {
            case "yearly":
                return "Tahunan";
            case "monthly":
                return `Bulanan_${currentFilter.year}`;
            case "daily":
                return `Harian_${currentFilter.month}_${currentFilter.year}`;
            default:
                return "Unknown";
        }
    }

    // Add CSS for rotating animation and cursor pointer
    if (!$("#custom-datatable-styles").length) {
        $(
            '<style id="custom-datatable-styles">' +
                ".rotating { animation: spin 1s linear infinite; } " +
                "@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } } " +
                ".cursor-pointer { cursor: pointer; } " +
                ".drill-down-cell:hover { background-color: #f8f9fa; } " +
                "</style>"
        ).appendTo("head");
    }
});
