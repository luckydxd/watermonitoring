// public/demo2/assets/js/app-report-user.js

$(function () {
    // 1. DEKLARASI VARIABEL & STATE
    const tableElement = $("#report-user-datatable");
    const baseUrl = tableElement.data("url");
    const reportTitle = $("#report-title");
    // const backButton = $("#back-to-branches");
    let dt;

    let currentState = {
        level: "branch",
        branchId: null,
        branchName: null,
    };

    const branchColumns = [
        { data: "id", name: "no" },
        { data: "name", name: "name" },
        { data: "total_users", name: "total_users" },
        { data: "active_users", name: "active_users" },
        { data: "inactive_users", name: "inactive_users" },
    ];

    // ==========================================================
    // PERUBAHAN DI SINI: Sesuaikan dengan key baru dari controller
    // ==========================================================
    const userColumns = [
        { data: "id", name: "no" },
        { data: "full_name", name: "full_name" }, // Menggunakan 'full_name'
        { data: "role_name", name: "role_name" }, // Menggunakan 'role_name'
        { data: "address", name: "address" }, // Menggunakan 'address'
        { data: "is_active", name: "is_active" },
    ];

    // 2. FUNGSI UTAMA UNTUK MENGELOLA TABEL
    function setupAndLoadTable() {
        if ($.fn.DataTable.isDataTable(tableElement)) {
            dt.destroy();
            tableElement.empty();
        }

        let currentColumns, currentHeaders, currentAjaxUrl;

        if (currentState.level === "branch") {
            currentColumns = branchColumns;
            currentAjaxUrl = baseUrl;
            currentHeaders = `
                <tr>
                    <th>No</th>
                    <th class="text-center">Nama Cabang</th>
                    <th class="text-center">Total Pengguna</th>
                    <th class="text-center">Pengguna Aktif</th>
                    <th class="text-center">Pengguna Non-Aktif</th>
                </tr>`;
        } else {
            // level 'user'
            currentColumns = userColumns;
            currentAjaxUrl = `${baseUrl}?branch_id=${currentState.branchId}`;
            currentHeaders = `
                <tr>
                    <th>No</th>
                    <th>Pengguna</th>
                    <th class="text-center">Peran</th>
                    <th>Alamat</th>
                    <th class="text-center">Status</th>
                </tr>`;
        }

        tableElement.prepend(`<thead>${currentHeaders}</thead>`);
        dt = tableElement.DataTable({
            processing: true,
            serverSide: true,
            ajax: { url: currentAjaxUrl },
            columns: currentColumns,
            order: [[1, "asc"]],
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
            drawCallback: function (settings) {
                updateUI();
            },
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: (data, type, row, meta) =>
                        meta.row + 1 + meta.settings._iDisplayStart,
                },
                {
                    targets: 1, // Nama Cabang / Pengguna

                    className:
                        currentState.level === "branch"
                            ? "text-primary cursor-pointer text-center"
                            : "",
                    // ==========================================================
                    // PERUBAHAN DI SINI: Sesuaikan dengan data baru
                    // ==========================================================
                    render: (data, type, row) => {
                        // Untuk level 'user', `data` adalah `full_name`. `row` punya `email`.
                        if (currentState.level === "user") {
                            return `${data}<br><small>${row.email}</small>`;
                        }
                        return data;
                    },
                },
                {
                    targets: [2, 3, 4],
                    className: "text-center",
                    // ==========================================================
                    // PERUBAHAN DI SINI: Sesuaikan dengan data baru
                    // ==========================================================
                    render: (data, type, row, meta) => {
                        // Untuk level 'user', `data` adalah nilai dari kolomnya (role_name, address, is_active)
                        if (currentState.level === "user") {
                            if (meta.col === 4) {
                                // Kolom Status
                                return data
                                    ? '<span class="badge bg-label-success">Active</span>'
                                    : '<span class="badge bg-label-secondary">Inactive</span>';
                            }
                            return data; // Tampilkan role_name dan address langsung
                        }
                        return data;
                    },
                },
            ],
            language: {
                sLengthMenu: "_MENU_",
                search: "",
                searchPlaceplace: "Cari...",
                paginate: {
                    next: '<i class="ti ti-chevron-right ti-sm"></i>',
                    previous: '<i class="ti ti-chevron-left ti-sm"></i>',
                },
            },
            buttons: [
                {
                    text: '<i class="ti ti-arrow-up me-1"></i>Kembali ke Laporan Cabang',
                    className: "btn btn-outline-primary btn-sm d-none",
                    attr: { id: "back-to-branches" }, // ID sudah benar
                    action: function () {
                        currentState.level = "branch";
                        currentState.branchId = null;
                        currentState.branchName = null;
                        setupAndLoadTable(); // Panggil fungsi utama lagi
                    },
                },
                {
                    extend: "collection",
                    className:
                        "btn btn-label-secondary dropdown-toggle mx-4 waves-effect waves-light",
                    text: '<i class="ti ti-upload me-2 ti-xs"></i>Ekspor',
                    buttons: [
                        // {
                        //     extend: "print",
                        //     text: '<i class="ti ti-printer me-2"></i>Print',
                        //     className: "dropdown-item",
                        //     autoPrint: false,
                        //     title: "", // Kosongkan title agar customize yang mengatur
                        //     exportOptions: {
                        //         columns: () =>
                        //             currentState.level === "branch"
                        //                 ? [0, 1, 2, 3, 4]
                        //                 : [0, 1, 2, 3, 4],
                        //         format: {
                        //             body: (data, row, column, node) => {
                        //                 if (
                        //                     typeof data !== "string" ||
                        //                     data.length === 0
                        //                 )
                        //                     return data;
                        //                 const plainText = $(node)
                        //                     .text()
                        //                     .replace(/\n/g, " ")
                        //                     .replace(/\s+/g, " ")
                        //                     .trim();
                        //                 if (
                        //                     currentState.level === "user" &&
                        //                     column === 1
                        //                 ) {
                        //                     const name = $(node)
                        //                         .contents()
                        //                         .not("small")
                        //                         .text()
                        //                         .trim();
                        //                     const email = $(node)
                        //                         .find("small")
                        //                         .text()
                        //                         .trim();
                        //                     return `${name} (${email})`;
                        //                 }
                        //                 return plainText;
                        //             },
                        //         },
                        //     },
                        //     // Kustomisasi print agar konsisten dengan contoh Anda
                        //     customize: function (win) {
                        //         const user = window.printUserData || {};
                        //         const userInfo = [
                        //             `Dicetak oleh: ${user.name || "System"}`,
                        //             user.role ? `(${user.role})` : "",
                        //             user.branch ? `Cabang: ${user.branch}` : "",
                        //         ]
                        //             .filter(Boolean)
                        //             .join(" ");

                        //         const options = {
                        //             day: "2-digit",
                        //             month: "long",
                        //             year: "numeric",
                        //             hour: "2-digit",
                        //             minute: "2-digit",
                        //         };
                        //         const printTime = new Date()
                        //             .toLocaleDateString("id-ID", options)
                        //             .replace(/\./g, ":")
                        //             .replace(/ pukul /, ", ");

                        //         $(win.document.body).prepend(
                        //             '<div style="margin-bottom:15px;font-size:10pt;border-bottom:1px solid #ddd;padding-bottom:5px">' +
                        //                 `<div style="float:left">${printTime} WIB</div>` +
                        //                 `<div style="float:right">${userInfo}</div>` +
                        //                 '<div style="clear:both"></div>' +
                        //                 "</div>" +
                        //                 '<h1 style="text-align:center;margin:5px 0 15px 0;font-size:14pt">LAPORAN PENGGUNA</h1>' +
                        //                 `<h2 style="text-align:center;margin:0 0 15px 0;font-size:12pt">${getExportTitle()}</h2>`
                        //         );

                        //         $(win.document.body).append(
                        //             '<div style="text-align:center;margin-top:20px;font-size:9pt;color:#666">' +
                        //                 "Dokumen ini dicetak secara otomatis dari Sistem Monitoring Air" +
                        //                 "</div>"
                        //         );

                        //         $(win.document.body)
                        //             .find("table")
                        //             .addClass("compact")
                        //             .css({
                        //                 "font-size": "10pt",
                        //                 width: "100%",
                        //             });
                        //         $(win.document.head).append(
                        //             "<style>@page { size: auto; margin: 5mm; } body { margin: 0; padding: 0; }</style>"
                        //         );
                        //     },
                        // },
                        {
                            extend: "excel",
                            text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                            className: "dropdown-item",
                            filename: function () {
                                // Nama file dinamis
                                const timestamp = new Date()
                                    .toISOString()
                                    .slice(0, 19)
                                    .replace(/[:-]/g, "");
                                if (currentState.level === "branch") {
                                    return `Laporan_Pengguna_per_Cabang_${timestamp}`;
                                } else {
                                    return `Laporan_Pengguna_Cabang_${currentState.branchName.replace(
                                        /\s+/g,
                                        "_"
                                    )}_${timestamp}`;
                                }
                            },
                            title: function () {
                                return getExportTitle();
                            },
                            exportOptions: {
                                // Menggunakan fungsi yang sama dengan Print
                                columns: function () {
                                    return currentState.level === "branch"
                                        ? [0, 1, 2, 3, 4]
                                        : [0, 1, 2, 3, 4];
                                },
                                format: {
                                    body: function (data, row, column, node) {
                                        if (
                                            typeof data !== "string" ||
                                            data.length === 0
                                        )
                                            return data;
                                        if (
                                            currentState.level === "user" &&
                                            column === 1
                                        ) {
                                            return $(node)
                                                .text()
                                                .replace(/\n/g, " ")
                                                .replace(/\s+/g, " ")
                                                .trim();
                                        }
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
                                let prefix = "Laporan Pengguna";

                                if (currentState.level === "branch") {
                                    prefix += " - Semua Cabang";
                                } else {
                                    prefix += ` - Cabang ${
                                        currentState.branchName || "Unknown"
                                    }`;
                                }
                                return `${prefix} - ${day}-${month}-${year}`;
                            },
                            exportOptions: {
                                columns: () =>
                                    currentState.level === "branch"
                                        ? [0, 1, 2, 3, 4]
                                        : [0, 1, 2, 3, 4],
                                format: {
                                    body: (data, row, column, node) => {
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
                                        if (
                                            currentState.level === "user" &&
                                            column === 1
                                        ) {
                                            const name = $(node)
                                                .contents()
                                                .not("small")
                                                .text()
                                                .trim();
                                            const email = $(node)
                                                .find("small")
                                                .text()
                                                .trim();
                                            return `${name} (${email})`;
                                        }
                                        return plainText;
                                    },
                                },
                            },
                            customize: function (doc) {
                                doc.pageMargins = [40, 90, 40, 60];
                                doc.defaultStyle.fontSize = 10;
                                doc.defaultStyle.color = "#333";

                                const appName = window.pdfExportData.appName;
                                const appAddress =
                                    window.pdfExportData.appAddress;
                                const appUrl = window.pdfExportData.appUrl;
                                const appPhone = window.pdfExportData.appPhone;
                                const currentUser =
                                    window.pdfExportData.userName;
                                const userRole = window.pdfExportData.userRole;

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

                                doc.footer = function (currentPage, pageCount) {
                                    const now = new Date();
                                    const printDate = now.toLocaleDateString(
                                        "id-ID",
                                        {
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                            hour: "2-digit",
                                            minute: "2-digit",
                                        }
                                    );
                                    return {
                                        columns: [
                                            {
                                                text: `Dicetak oleh: ${currentUser} (${userRole}) pada ${printDate} `,
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
                                        margin: [0, 0, 0, 5],
                                    },
                                    reportSubtitle: {
                                        fontSize: 14,
                                        bold: false,
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

                                let reportTitle = "LAPORAN PENGGUNA";
                                let subTitle =
                                    currentState.level === "branch"
                                        ? "SEMUA CABANG"
                                        : `CABANG ${
                                              currentState.branchName?.toUpperCase() ||
                                              ""
                                          }`;

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

                                doc.content.forEach(function (content) {
                                    if (content.table) {
                                        content.table.widths =
                                            currentState.level === "branch"
                                                ? [
                                                      "auto",
                                                      "*",
                                                      "auto",
                                                      "auto",
                                                      "auto",
                                                  ]
                                                : [
                                                      "auto",
                                                      "*",
                                                      "auto",
                                                      "*",
                                                      "auto",
                                                  ];

                                        content.table.body.forEach(function (
                                            row,
                                            i
                                        ) {
                                            row.forEach(function (cell, j) {
                                                if (i === 0) {
                                                    cell.style = "tableHeader";
                                                    cell.margin = [5, 5, 5, 5];
                                                } else {
                                                    cell.style =
                                                        i % 2 === 0
                                                            ? "tableBodyEven"
                                                            : "tableBodyOdd";
                                                    cell.margin = [5, 5, 5, 5];
                                                }
                                                if (j !== 1) {
                                                    cell.alignment = "center";
                                                }
                                                if (j !== 2) {
                                                    cell.alignment = "center";
                                                }
                                            });
                                        });
                                    }
                                });
                            },
                        },
                    ],
                },
            ],
        });
    }

    const getExportTitle = () =>
        currentState.level === "branch"
            ? "Laporan Pengguna per Cabang"
            : `Laporan Pengguna - Cabang ${currentState.branchName}`;

    function updateUI() {
        reportTitle.text(getExportTitle());
        if (dt) {
            // PERBAIKAN: Hapus '-btn' dari selector agar cocok dengan ID tombol Anda
            const backButton = dt.button("#back-to-branches");

            if (currentState.level === "branch") {
                backButton.nodes().addClass("d-none");
            } else {
                backButton.nodes().removeClass("d-none");
            }
        }
    }

    // 3. EVENT LISTENER
    tableElement.on("click", "tbody tr", function () {
        if (currentState.level === "branch") {
            const rowData = dt.row(this).data();
            if (!rowData || rowData.total_users == 0) return;
            currentState.level = "user";
            currentState.branchId = rowData.id;
            currentState.branchName = rowData.name;
            updateUI();
            setupAndLoadTable();
        }
    });

    // 4. PEMUATAN AWAL
    setupAndLoadTable();
});
