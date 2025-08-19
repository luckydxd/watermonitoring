$(function () {
    const tableElement = $("#report-complaint-datatable");
    const baseUrl = tableElement.data("url");
    const reportTitle = $("#report-title");
    let dt;

    let currentState = {
        level: "branch",
        branchId: null,
        branchName: null,
    };

    const getReportTitle = () =>
        currentState.level === "branch"
            ? "Laporan Keluhan per Cabang"
            : `Detail Keluhan - Cabang ${currentState.branchName}`;

    const branchColumns = [
        { data: "branch_name", name: "branch_name" },
        { data: "total_complaints", name: "total_complaints" },
        { data: "pending_complaints", name: "pending_complaints" },
        { data: "processed_complaints", name: "processed_complaints" },
        { data: "resolved_complaints", name: "resolved_complaints" },
    ];
    const complaintColumns = [
        { data: "id", name: "id" },
        { data: "user_info", name: "user.userData.name" },
        { data: "title", name: "title" },
        { data: "status", name: "status" },
        { data: "created_at", name: "created_at" },
    ];

    function setupAndLoadTable() {
        if ($.fn.DataTable.isDataTable(tableElement)) {
            dt.destroy();
            tableElement.empty();
        }

        let currentColumns =
            currentState.level === "branch" ? branchColumns : complaintColumns;
        let currentAjaxUrl =
            currentState.level === "branch"
                ? baseUrl
                : `${baseUrl}?branch_id=${currentState.branchId}`;
        let currentHeaders =
            currentState.level === "branch"
                ? `<tr><th class="text-center">Nama Cabang</th><th class="text-center">Total Keluhan</th><th class="text-center">Pending</th><th class="text-center">Diproses</th><th class="text-center">Selesai</th></tr>`
                : `<tr><th class="text-center">No</th><th class="text-center">Pelapor</th><th class="text-center">Judul</th><th class="text-center">Status</th><th class="text-center">Waktu Lapor</th></tr>`;

        tableElement.prepend(`<thead>${currentHeaders}</thead>`);

        dt = tableElement.DataTable({
            processing: true,
            serverSide: true,
            ajax: { url: currentAjaxUrl },
            columns: currentColumns,
            order: [],
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
            buttons: [
                {
                    text: '<i class="ti ti-arrow-left me-1"></i>Kembali ke Per Cabang',
                    className: "btn btn-outline-primary btn-sm",
                    attr: { id: "back-to-branches-btn" },
                    action: function () {
                        currentState.level = "branch";
                        currentState.branchId = null;
                        currentState.branchName = null;
                        setupAndLoadTable();
                    },
                },

                {
                    extend: "collection",
                    className: "btn btn-label-secondary dropdown-toggle mx-2",
                    text: '<i class="ti ti-upload me-2 ti-xs"></i>Ekspor',
                    buttons: [
                        {
                            extend: "excel",
                            text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                            className: "dropdown-item",
                            title: function () {
                                // Menggunakan helper function untuk judul dinamis
                                return getReportTitle();
                            },
                            filename: function () {
                                // Membuat nama file yang dinamis berdasarkan tampilan
                                const timestamp = new Date()
                                    .toISOString()
                                    .slice(0, 19)
                                    .replace(/[:-]/g, "");
                                if (currentState.level === "branch") {
                                    return `Laporan_Keluhan_per_Cabang_${timestamp}`;
                                } else {
                                    return `Laporan_Keluhan_Cabang_${currentState.branchName.replace(
                                        /\s+/g,
                                        "_"
                                    )}_${timestamp}`;
                                }
                            },
                            exportOptions: {
                                // Tentukan kolom yang akan diekspor untuk setiap level
                                columns: function () {
                                    // Untuk kedua level, kita ekspor 5 kolom pertama
                                    return [0, 1, 2, 3, 4];
                                },
                                format: {
                                    body: function (data, row, column, node) {
                                        // Fungsi ini membersihkan HTML dari sel sebelum diekspor
                                        if (
                                            typeof data !== "string" ||
                                            data.length === 0
                                        ) {
                                            return data;
                                        }

                                        // Untuk level detail keluhan, tangani kolom "Pelapor" dan "Status"
                                        if (
                                            currentState.level === "complaint"
                                        ) {
                                            // Kolom Pelapor (indeks 1)
                                            if (column === 1) {
                                                // Ubah 'Nama<br><small>Cabang</small>' menjadi 'Nama (Cabang)'
                                                return $(node)
                                                    .text()
                                                    .replace(/\n/g, " ")
                                                    .replace(/\s+/g, " ")
                                                    .trim();
                                            }
                                            // Kolom Status (indeks 3)
                                            if (column === 3) {
                                                // Ambil teks dari dalam badge, misal: "Pending"
                                                return $(node)
                                                    .find(".badge")
                                                    .text()
                                                    .trim();
                                            }
                                        }

                                        // Untuk kasus lainnya, cukup ambil teksnya
                                        return $(node).text().trim();
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
                            filename: function () {
                                const timestamp = new Date()
                                    .toISOString()
                                    .slice(0, 19)
                                    .replace(/[:-]/g, "");
                                if (currentState.level === "branch") {
                                    return `Laporan_Keluhan_per_Cabang_${timestamp}`;
                                } else {
                                    return `Laporan_Keluhan_Cabang_${currentState.branchName.replace(
                                        /\s+/g,
                                        "_"
                                    )}_${timestamp}`;
                                }
                            },
                            exportOptions: {
                                columns: () =>
                                    currentState.level === "branch"
                                        ? [0, 1, 2, 3, 4]
                                        : [0, 1, 2, 3, 4],
                                format: {
                                    body: function (data, row, column, node) {
                                        if (
                                            typeof data !== "string" ||
                                            data.length === 0
                                        )
                                            return data;

                                        const plainText = $(node)
                                            .text()
                                            .replace(/\n/g, " ")
                                            .replace(/\s+/g, " ")
                                            .trim();

                                        // Format kolom "Pelapor" di level detail
                                        if (
                                            currentState.level ===
                                                "complaint" &&
                                            column === 1
                                        ) {
                                            const name = $(node)
                                                .find(".fw-medium")
                                                .text()
                                                .trim();
                                            const branch = $(node)
                                                .find(".text-muted")
                                                .text()
                                                .trim();
                                            return `${name} (${branch})`;
                                        }
                                        return plainText;
                                    },
                                },
                            },
                            customize: function (doc) {
                                // --- Konfigurasi dan Gaya Umum ---
                                doc.pageMargins = [40, 90, 40, 60];
                                doc.defaultStyle.fontSize = 10;
                                doc.defaultStyle.color = "#333";

                                // --- Ambil Data dari Blade ---
                                const pdfData = window.pdfExportData || {}; // Gunakan data dari blade
                                const appName =
                                    pdfData.appName || "Sistem Monitoring";
                                const appAddress = pdfData.appAddress || "";
                                const appUrl = pdfData.appUrl || "";
                                const appPhone = pdfData.appPhone || "";
                                const currentUser =
                                    pdfData.userName || "System";
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
                                    const printDate =
                                        new Date().toLocaleDateString("id-ID", {
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                        });
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
                                        margin: [0, 15, 0, 5],
                                    },
                                    reportSubtitle: {
                                        fontSize: 14,
                                        color: "#34495e",
                                        alignment: "center",
                                        margin: [0, 0, 0, 20],
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
                                    footerText: {
                                        fontSize: 8,
                                        color: "#7f8c8d",
                                    },
                                };

                                // --- Judul Laporan (Dinamis) ---
                                let reportTitle = "LAPORAN KELUHAN";
                                let subTitle =
                                    currentState.level === "branch"
                                        ? "SEMUA CABANG"
                                        : `CABANG ${
                                              currentState.branchName?.toUpperCase() ||
                                              ""
                                          }`;

                                // Sisipkan judul sebelum tabel
                                const tableIndex = doc.content.findIndex(
                                    (item) => item.table
                                );
                                if (tableIndex !== -1) {
                                    doc.content.splice(
                                        tableIndex,
                                        0,
                                        {
                                            text: reportTitle,
                                            style: "reportTitle",
                                        },
                                        {
                                            text: subTitle,
                                            style: "reportSubtitle",
                                        }
                                    );
                                }

                                // --- Penyesuaian Tabel ---
                                doc.content.forEach(function (content) {
                                    if (content.table) {
                                        // Set lebar kolom dinamis
                                        content.table.widths =
                                            currentState.level === "branch"
                                                ? [
                                                      "*",
                                                      "auto",
                                                      "auto",
                                                      "auto",
                                                      "auto",
                                                  ] // Cabang, Total, Pending, Proses, Selesai
                                                : [
                                                      "auto",
                                                      "*",
                                                      "*",
                                                      "auto",
                                                      "auto",
                                                  ]; // No, Pelapor, Judul, Status, Waktu

                                        // Style untuk setiap baris
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
                    ],
                },
            ],
            drawCallback: function (settings) {
                updateUI();
            },
            columnDefs: [
                { targets: "_all", className: "text-center" },
                {
                    targets: 0,
                    render: function (data, type, row, meta) {
                        if (currentState.level === "branch") {
                            return `<span class="text-primary cursor-pointer">${data}</span>`;
                        }
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, row, meta) {
                        if (currentState.level === "branch") return data;
                        return `<div class="d-flex flex-column">
                                    <span class="text-heading fw-medium">${data.name}</span>
                                    <small class="text-muted">${data.branch_name}</small>
                                </div>`;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row, meta) {
                        if (currentState.level === "branch") return data;
                        let badgeClass = "";
                        switch (data) {
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
                        return `<span class="badge ${badgeClass} text-capitalize">${data}</span>`;
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row, meta) {
                        if (currentState.level === "branch") return data;
                        return new Date(data).toLocaleDateString("id-ID", {
                            day: "numeric",
                            month: "long",
                            year: "numeric",
                        });
                    },
                },
            ],
            language: {},
        });
    }

    function updateUI() {
        reportTitle.text(getReportTitle());
        if (dt) {
            const backButton = dt.button("#back-to-branches-btn");
            if (currentState.level === "branch") {
                backButton.nodes().addClass("d-none");
            } else {
                backButton.nodes().removeClass("d-none");
            }
        }
    }

    tableElement.on("click", "tbody tr", function () {
        if (currentState.level === "branch") {
            const rowData = dt.row(this).data();
            if (!rowData || rowData.total_complaints == 0) return;
            currentState.level = "complaint";
            currentState.branchId = rowData.branch_id;
            currentState.branchName = rowData.branch_name;
            setupAndLoadTable();
        }
    });

    setupAndLoadTable();
});
