const consumptionUrl = $("#report-usage-datatable").data("url");
let chartColumn;

$(document).ready(function () {
    let currentFilter = {
        period: "branch",
    };

    let navigationHistory = [];
    function initChart() {
        var optionsColumn = {
            chart: {
                height: 350,
                type: "bar",
                foreColor: "#444050",
                animations: {
                    enabled: false,
                },
                events: {
                    dataPointSelection: function (event, chartContext, config) {
                        const dataPointIndex = config.dataPointIndex;
                        const tableRows = table.rows().data().toArray();
                        if (tableRows[dataPointIndex]) {
                            drillDownFromChart(tableRows[dataPointIndex]);
                        }
                    },
                    animationEnd: function (chartCtx, opts) {
                        const newData =
                            chartCtx.w.config.series[0].data.slice();
                        window.setTimeout(function () {
                            chartCtx.updateOptions(
                                {
                                    series: [
                                        {
                                            data: newData,
                                        },
                                    ],
                                    subtitle: {
                                        text: "Klik bar untuk melihat detail",
                                    },
                                },
                                false,
                                false
                            );
                        }, 300);
                    },
                },
                toolbar: {
                    show: false,
                },
                zoom: {
                    enabled: false,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: 0,
            },
            grid: {
                borderColor: "#e6e6e8",
            },
            series: [
                {
                    name: "Total Konsumsi",
                    data: [],
                },
            ],
            xaxis: {
                categories: [],
                axisTicks: {
                    color: "#333",
                },
                axisBorder: {
                    color: "#333",
                },
            },
            yaxis: {
                decimalsInFloat: 2,
                opposite: false,
                labels: {
                    offsetX: -10,
                },
                title: {
                    text: "Liter",
                },
            },
            title: {
                text: "Konsumsi Air - Semua Cabang",
                align: "left",
                style: {
                    fontSize: "12px",
                },
            },
            subtitle: {
                text: "Klik bar untuk melihat detail",
                floating: true,
                align: "right",
                offsetY: 0,
                style: {
                    fontSize: "18px",
                },
            },
            fill: {
                type: "gradient",
                gradient: {
                    shade: "dark",
                    type: "vertical",
                    shadeIntensity: 0.5,
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 0.8,
                    stops: [0, 100],
                    gradientToColors: ["#90caf9"],
                },
            },
            colors: ["#64b5f6"],
            tooltip: {
                theme: "light",
                y: {
                    formatter: function (val) {
                        return (
                            parseFloat(val).toLocaleString("id-ID", {
                                maximumFractionDigits: 2,
                            }) + " Liter"
                        );
                    },
                },
            },
            legend: {
                show: true,
            },
        };

        chartColumn = new ApexCharts(
            document.querySelector("#columnchart"),
            optionsColumn
        );
        chartColumn.render();
    }

    // Update Chart berdasarkan data dari table
    function updateChart() {
        const tableData = table.rows().data().toArray();
        const categories = [];
        const data = [];

        tableData.forEach((row) => {
            categories.push(row.period_label);
            data.push(parseFloat(row.total_consumption) || 0);
        });

        // Update chart title berdasarkan current filter
        let chartTitle = "Konsumsi Air - ";
        let chartSubtitle = "Klik bar untuk melihat detail";

        switch (currentFilter.period) {
            case "branch":
                chartTitle += "Semua Cabang";
                break;
            case "user":
                chartTitle += `Cabang ${
                    currentFilter.branch_name || "Tidak Diketahui"
                }`;
                break;
            case "daily":
                const userInfo = getCurrentUserInfo();
                if (userInfo) {
                    chartTitle += `${userInfo.user_name}`;
                    chartSubtitle = `${currentFilter.year} - ${getMonthName(
                        currentFilter.month
                    )}`;
                } else {
                    chartTitle += "Konsumsi Harian";
                }
                break;
        }

        // Update chart
        chartColumn.updateOptions({
            series: [
                {
                    name: "Total Konsumsi",
                    data: data,
                },
            ],
            xaxis: {
                categories: categories,
            },
            title: {
                text: chartTitle,
            },
            subtitle: {
                text: chartSubtitle,
            },
        });
    }

    // Fungsi drill down dari chart
    function drillDownFromChart(rowData) {
        if (!rowData) return;

        navigationHistory.push(JSON.parse(JSON.stringify(currentFilter)));

        switch (currentFilter.period) {
            case "branch":
                currentFilter = {
                    period: "user",
                    branch_id: rowData.id,
                    branch_name: rowData.period_label,
                };
                break;

            case "user":
                currentFilter = {
                    period: "daily",
                    user_id: rowData.id,
                    user_name: rowData.period_label.split(" (")[0],
                    user_email: rowData.user_email || "",
                    branch_id: currentFilter.branch_id,
                    branch_name: currentFilter.branch_name,
                    year: new Date().getFullYear(),
                    month: new Date().getMonth() + 1,
                };
                break;
        }

        reloadTable();
    }

    // Helper function untuk nama bulan
    function getMonthName(monthNumber) {
        const months = [
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
        return months[monthNumber - 1] || "";
    }

    function getPeriodHeaderLabel() {
        switch (currentFilter.period) {
            case "branch":
                return "Cabang";
            case "user":
                return "Pelanggan";
            case "daily":
                return "Tanggal";
            case "yearly":
                return "Tahun";
            case "monthly":
                return "Bulan";
            default:
                return "Periode";
        }
    }

    function getCurrentPeriodLabel() {
        let label = "LAPORAN KONSUMSI AIR - ";

        if (currentFilter.period === "branch") {
            label += "SEMUA CABANG";
        } else if (currentFilter.period === "user") {
            label += `CABANG ${
                currentFilter.branch_name?.toUpperCase() || "TIDAK DIKETAHUI"
            }`;
        } else if (currentFilter.period === "daily") {
            const userInfo = getCurrentUserInfo();
            if (userInfo) {
                label += `PELANGGAN ${userInfo.user_name.toUpperCase()}`;
                if (userInfo.branch_name) {
                    label += ` - CABANG ${userInfo.branch_name.toUpperCase()}`;
                }
            } else {
                label += "KONSUMSI HARIAN";
            }
        }
    }

    function getCurrentUserInfo() {
        if (currentFilter.period === "daily") {
            // Pertama, coba ambil dari currentFilter jika tersimpan
            if (currentFilter.user_name && currentFilter.branch_name) {
                return {
                    user_name: currentFilter.user_name,
                    user_email: currentFilter.user_email || "",
                    branch_name: currentFilter.branch_name,
                };
            }

            // Jika tidak ada di filter, coba ambil dari tabel
            const tableData = table.rows().data().toArray();
            if (tableData.length > 0) {
                const firstRow = tableData[0];
                return {
                    user_name: firstRow.user_name || "Tidak Diketahui",
                    user_email: firstRow.user_email || "",
                    branch_name: firstRow.branch_name || "",
                };
            }
        }
        return null;
    }

    // Fungsi untuk mengupdate header tabel
    function updateTableHeaders() {
        const periodLabel = getPeriodHeaderLabel();
        const $header = $("#report-usage-datatable thead tr");

        $header.html(`
            <th width="5%">No</th>
            <th class="text-center">${periodLabel}</th>
            <th class="text-center">Total Konsumsi (liter)</th>
        `);
    }

    const table = $("#report-usage-datatable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: consumptionUrl,
            type: "GET",
            data: function (d) {
                d.period = currentFilter.period;
                d.branch_id = currentFilter.branch_id;
                d.user_id = currentFilter.user_id;
                d.year = currentFilter.year;
                d.month = currentFilter.month;
            },
            error: function (xhr, error, thrown) {
                console.error("Error:", xhr.responseText);
                Notiflix.Notify.failure("Terjadi kesalahan saat memuat data");
            },
            dataSrc: function (json) {
                // Update chart setelah data table dimuat
                setTimeout(() => {
                    updateChart();
                }, 100);
                return json.data;
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
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                orderable: false,
                searchable: false,
            },
            {
                data: "period_label",
                className: "drill-down-cell cursor-pointer text-center",
                render: function (data, type, row) {
                    return data || "-";
                },
            },
            {
                data: "total_consumption",
                className: "text-center",

                render: function (data, type, row) {
                    return (
                        parseFloat(data).toLocaleString("id-ID", {
                            // minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        }) + " Liter"
                    );
                },
            },
            {
                data: "user_name",
                visible: false,
                defaultContent: "",
                render: function (data) {
                    return data || "";
                },
            },
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
                text: '<i class="ti ti-arrow-up me-1"></i>Roll Up',
                className: "btn btn-outline-primary me-2 roll-up-btn",
                action: function (e, dt, node, config) {
                    e.preventDefault();
                    rollUp();
                },
                init: function (api, node, config) {
                    // Sembunyikan tombol saat inisialisasi jika period = branch
                    $(node).toggleClass(
                        "d-none",
                        currentFilter.period === "branch"
                    );
                },
            },
            {
                extend: "collection",
                className:
                    "btn btn-label-secondary dropdown-toggle mx-2 waves-effect waves-light",
                text: '<i class="ti ti-upload me-2 ti-xs"></i>Ekspor',
                buttons: [
                    // {
                    //     extend: "print",
                    //     title: "",
                    //     text: '<i class="ti ti-printer me-2"></i>Print',
                    //     className: "dropdown-item",
                    //     autoPrint: true,
                    //     customize: function (win) {
                    //         // 1. Hapus about:blank
                    //         $(win.document.body)
                    //             .find('img[src="about:blank"]')
                    //             .remove();

                    //         // 2. Data pencetak
                    //         const user = window.printUserData || {};
                    //         const userInfo = [
                    //             `Dicetak oleh: ${user.name || "System"}`,
                    //             user.role ? `(${user.role})` : "",
                    //             user.branch ? `Cabang: ${user.branch}` : "",
                    //         ]
                    //             .filter(Boolean)
                    //             .join(" ");

                    //         // 3. Format tanggal Indonesia yang benar
                    //         const options = {
                    //             day: "2-digit",
                    //             month: "long",
                    //             year: "numeric",
                    //             hour: "2-digit",
                    //             minute: "2-digit",
                    //         };
                    //         const printTime = new Date()
                    //             .toLocaleDateString("id-ID", options)
                    //             .replace(/\./g, ":") // Ganti titik menjadi titik dua pada jam
                    //             .replace(/ pukul /, ", ");

                    //         // 4. Header dokumen
                    //         $(win.document.body).prepend(
                    //             '<div style="margin-bottom:15px;font-size:10pt;border-bottom:1px solid #ddd;padding-bottom:5px">' +
                    //                 `<div style="float:left">${printTime}</div>` +
                    //                 `<div style="float:right">${userInfo}</div>` +
                    //                 '<div style="clear:both"></div>' +
                    //                 "</div>" +
                    //                 '<h1 style="text-align:center;margin:5px 0 15px 0;font-size:14pt">LAPORAN KONSUMSI AIR</h1>' +
                    //                 `<h2 style="text-align:center;margin:0 0 15px 0;font-size:12pt">${getCurrentPeriodLabel()}</h2>`
                    //         );

                    //         // 5. Footer dokumen
                    //         $(win.document.body).append(
                    //             '<div style="text-align:center;margin-top:20px;font-size:9pt;color:#666">' +
                    //                 "Dokumen ini dicetak secara otomatis dari Sistem Monitoring Air" +
                    //                 "</div>"
                    //         );

                    //         // 6. Style tabel
                    //         $(win.document.body)
                    //             .find("table")
                    //             .addClass("compact")
                    //             .css({
                    //                 "font-size": "10pt",
                    //                 width: "100%",
                    //             });

                    //         // 7. Hapus margin default browser
                    //         $(win.document.head).append(
                    //             "<style>" +
                    //                 "@page { size: auto; margin: 5mm; }" +
                    //                 "body { margin: 0; padding: 0; }" +
                    //                 "</style>"
                    //         );
                    //     },
                    // },
                    {
                        extend: "excel",
                        text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                        className: "dropdown-item",
                        filename: function () {
                            let base = "Laporan_Konsumsi_Air";
                            const timestamp = new Date()
                                .toISOString()
                                .slice(0, 19)
                                .replace(/[:-]/g, "");

                            if (currentFilter.period === "branch") {
                                return `${base}_Semua_Cabang_${timestamp}`;
                            } else if (currentFilter.period === "user") {
                                return `${base}_Cabang_${
                                    currentFilter.branch_name || "Unknown"
                                }_${timestamp}`;
                            } else if (currentFilter.period === "daily") {
                                return `${base}_Pelanggan_${
                                    currentFilter.user_id || "Unknown"
                                }_${timestamp}`;
                            }

                            return `${base}_${getCurrentPeriodLabel()}_${timestamp}`;
                        },
                        exportOptions: {
                            columns: [0, 1, 2],
                            modifier: {
                                page: "all",
                            },
                        },
                        customize: function (xlsx) {
                            var sheet = xlsx.xl.worksheets["sheet1.xml"];
                            $("row c", sheet).attr("s", "50"); // Set column width
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
                            let prefix = "Laporan Konsumsi Air";

                            if (currentFilter.period === "branch") {
                                prefix += " - Semua Cabang";
                            } else if (currentFilter.period === "user") {
                                prefix += ` - ${
                                    currentFilter.branch_name || "Cabang"
                                }`;
                            } else if (currentFilter.period === "daily") {
                                // PERBAIKAN: Gunakan fungsi untuk mendapatkan user info
                                const userInfo = getCurrentUserInfo();
                                if (userInfo) {
                                    prefix += ` - ${userInfo.user_name}`;
                                } else {
                                    prefix += " - Harian";
                                }
                            }

                            return `${prefix} - ${day}-${month}-${year}.pdf`;
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

                            // Data dari window.pdfExportData
                            const appName = window.pdfExportData.appName;
                            const appAddress = window.pdfExportData.appAddress;
                            const appUrl = window.pdfExportData.appUrl;
                            const appPhone = window.pdfExportData.appPhone;
                            const currentUser = window.pdfExportData.userName;
                            const userRole = window.pdfExportData.userRole;

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
                                    }
                                );

                                return {
                                    stack: [
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
                                        {
                                            columns: [
                                                {
                                                    width: "*",
                                                    stack: [
                                                        {
                                                            text: `Dicetak oleh: ${currentUser} (${userRole})`,
                                                            style: "footerText",
                                                            alignment: "left",
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

                            // --- Gaya Dokumen ---
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
                                    fontSize: 11,
                                    color: "white",
                                    fillColor: "#34495e",
                                    alignment: "center",
                                },
                                tableBodyOdd: {
                                    fontSize: 10,
                                    color: "#2c3e50",
                                    fillColor: "#ffffff",
                                },
                                tableBodyEven: {
                                    fontSize: 10,
                                    color: "#2c3e50",
                                    fillColor: "#f8f9fa",
                                },
                                footerText: {
                                    fontSize: 8,
                                    color: "#7f8c8d",
                                },
                                totalLabel: {
                                    bold: true,
                                    fontSize: 10,
                                    alignment: "right",
                                    fillColor: "#f8f9fa",
                                    color: "#2c3e50",
                                },
                                totalValue: {
                                    bold: true,
                                    fontSize: 10,
                                    alignment: "center",
                                    fillColor: "#f8f9fa",
                                    color: "#2c3e50",
                                },
                            };

                            let reportTitle = "LAPORAN KONSUMSI AIR";
                            let subTitle = "";

                            if (currentFilter.period === "branch") {
                                subTitle = "SEMUA CABANG";
                            } else if (currentFilter.period === "user") {
                                subTitle = `CABANG ${
                                    currentFilter.branch_name?.toUpperCase() ||
                                    ""
                                }`;
                            } else if (currentFilter.period === "daily") {
                                // PERBAIKAN: Gunakan data user yang sudah diperbaiki
                                const userInfo = getCurrentUserInfo();
                                if (userInfo) {
                                    subTitle = `PELANGGAN ${userInfo.user_name.toUpperCase()}`;
                                    if (userInfo.branch_name) {
                                        subTitle += ` - CABANG ${userInfo.branch_name.toUpperCase()}`;
                                    }
                                } else {
                                    subTitle = "KONSUMSI HARIAN";
                                }

                                // Tambahkan informasi periode
                                if (currentFilter.year && currentFilter.month) {
                                    const monthNames = [
                                        "JANUARI",
                                        "FEBRUARI",
                                        "MARET",
                                        "APRIL",
                                        "MEI",
                                        "JUNI",
                                        "JULI",
                                        "AGUSTUS",
                                        "SEPTEMBER",
                                        "OKTOBER",
                                        "NOVEMBER",
                                        "DESEMBER",
                                    ];
                                    subTitle += ` - ${
                                        monthNames[
                                            parseInt(currentFilter.month) - 1
                                        ]
                                    } ${currentFilter.year}`;
                                }
                            }

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
                                        style: "reportTitle",
                                        fontSize: 14,
                                        margin: [0, 0, 0, 20],
                                    }
                                );
                            }

                            // --- Penyesuaian Tabel ---
                            doc.content.forEach(function (content) {
                                if (content.table) {
                                    // Set lebar kolom
                                    content.table.widths = ["auto", "*", 100];

                                    // Hitung total konsumsi
                                    let totalConsumption = 0;

                                    // Style untuk setiap baris
                                    content.table.body.forEach(function (
                                        row,
                                        i
                                    ) {
                                        // Header row
                                        if (i === 0) {
                                            row.forEach(function (cell) {
                                                cell.style = "tableHeader";
                                                cell.margin = [8, 8, 8, 8];
                                            });
                                            return;
                                        }

                                        // Data rows
                                        row.forEach(function (cell, j) {
                                            cell.style =
                                                i % 2 === 0
                                                    ? "tableBodyEven"
                                                    : "tableBodyOdd";
                                            cell.margin = [8, 6, 8, 6];

                                            // Alignment kolom
                                            if (j === 0 || j === 2) {
                                                cell.alignment = "center";
                                            } else {
                                                cell.alignment = "center";
                                            }

                                            // Hitung total dari kolom konsumsi (kolom 2)
                                            if (j === 2) {
                                                const valueStr = cell.text
                                                    .toString()
                                                    .replace(/[^\d,]/g, "")
                                                    .replace(",", ".");
                                                const value =
                                                    parseFloat(valueStr);
                                                if (!isNaN(value)) {
                                                    totalConsumption += value;
                                                }
                                            }
                                        });
                                    });

                                    // Tambahkan baris total
                                    content.table.body.push([
                                        {
                                            text: "",
                                            colSpan: 1,
                                            style: "tableBodyOdd",
                                            border: [true, true, false, true],
                                        },
                                        {
                                            text: "TOTAL KONSUMSI",
                                            style: "totalLabel",
                                            border: [false, true, false, true],
                                        },
                                        {
                                            text:
                                                totalConsumption.toLocaleString(
                                                    "id-ID"
                                                ) + " Liter",
                                            style: "totalValue",
                                            border: [false, true, true, true],
                                        },
                                    ]);

                                    // Konfigurasi border tabel
                                    content.layout = {
                                        hLineWidth: function (i, node) {
                                            if (
                                                i === 0 ||
                                                i === node.table.body.length - 1
                                            )
                                                return 1;
                                            return 0.5;
                                        },
                                        vLineWidth: function (i, node) {
                                            if (
                                                i === 0 ||
                                                i === node.table.widths.length
                                            )
                                                return 1;
                                            return 0.5;
                                        },
                                        hLineColor: function (i, node) {
                                            return "#e3e6ea";
                                        },
                                        vLineColor: function (i, node) {
                                            return "#e3e6ea";
                                        },
                                        paddingTop: function (i, node) {
                                            return 6;
                                        },
                                        paddingBottom: function (i, node) {
                                            return 6;
                                        },
                                    };
                                }
                            });
                        },
                    },
                ],
            },
        ],
        initComplete: function () {
            updateTableHeaders();
            initChart();
        },
    });

    function reloadTable() {
        table.page(0);
        table.ajax.reload(null, false);
        updateTableHeaders();
        updateUI();
    }

    function updateUI() {
        updateBreadcrumb();
        updateRollUpButton();
        updateFilterControls();
    }

    function updateBreadcrumb() {
        let breadcrumb = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';

        breadcrumb += `<li class="breadcrumb-item"><a href="#" class="breadcrumb-link" data-level="branch">Total</a></li>`;

        if (
            currentFilter.period === "user" ||
            currentFilter.period === "daily"
        ) {
            breadcrumb += `<li class="breadcrumb-item"><a href="#" class="breadcrumb-link" data-level="user">${
                currentFilter.branch_name || "Cabang"
            }</a></li>`;
        }

        if (currentFilter.period === "daily") {
            breadcrumb += `<li class="breadcrumb-item active">Pelanggan</li>`;
        }

        breadcrumb += "</ol></nav>";
        $("#report-breadcrumb").html(breadcrumb);
    }

    function updateRollUpButton() {
        const $rollUpBtn = $(".roll-up-btn");

        if (currentFilter.period === "branch") {
            // Sembunyikan tombol Roll Up
            $rollUpBtn.addClass("d-none");
        } else {
            // Tampilkan tombol Roll Up dan update teks
            $rollUpBtn.removeClass("d-none");
            const buttonText =
                currentFilter.period === "daily"
                    ? '<i class="ti ti-arrow-up me-1"></i>Kembali ke Pelanggan'
                    : '<i class="ti ti-arrow-up me-1"></i>Kembali ke Cabang';

            // Update teks tombol di DataTables dan di DOM
            table.button('.roll-up-btn:contains("Roll Up")').text(buttonText);
            $rollUpBtn.html(buttonText);
        }
    }

    function updateFilterControls() {
        $("#yearFilter, #monthFilter").prop(
            "disabled",
            currentFilter.period !== "daily"
        );
    }

    $("#report-usage-datatable").on("click", ".drill-down-cell", function () {
        const rowData = table.row($(this).closest("tr")).data();
        drillDownFromChart(rowData);
    });

    $("#rollUpBtn").click(rollUp);

    function rollUp() {
        switch (currentFilter.period) {
            case "daily":
                // PERBAIKAN: Pastikan branch_id dan branch_name tetap tersimpan
                currentFilter = {
                    period: "user",
                    branch_id: currentFilter.branch_id,
                    branch_name: currentFilter.branch_name,
                };

                // Jika branch_id tidak ada, coba ambil dari history
                if (!currentFilter.branch_id && navigationHistory.length > 0) {
                    const lastState =
                        navigationHistory[navigationHistory.length - 1];
                    if (lastState.branch_id) {
                        currentFilter.branch_id = lastState.branch_id;
                        currentFilter.branch_name = lastState.branch_name;
                    }
                }
                break;

            case "user":
                currentFilter = { period: "branch" };
                break;
        }

        // Bersihkan navigation history jika kembali ke branch
        if (currentFilter.period === "branch") {
            navigationHistory = [];
        }

        reloadTable();
    }

    $("#report-breadcrumb").on("click", ".breadcrumb-link", function (e) {
        e.preventDefault();
        const level = $(this).data("level");

        if (level === "branch") {
            currentFilter = { period: "branch" };
            navigationHistory = [];
        } else if (level === "user") {
            if (!currentFilter.branch_id && navigationHistory.length > 0) {
                for (let i = navigationHistory.length - 1; i >= 0; i--) {
                    if (navigationHistory[i].branch_id) {
                        currentFilter.branch_id =
                            navigationHistory[i].branch_id;
                        currentFilter.branch_name =
                            navigationHistory[i].branch_name;
                        break;
                    }
                }
            }

            currentFilter = {
                period: "user",
                branch_id: currentFilter.branch_id,
                branch_name: currentFilter.branch_name,
            };
        }

        reloadTable();
    });

    $("#yearFilter, #monthFilter").change(function () {
        if (currentFilter.period === "daily") {
            currentFilter.year = $("#yearFilter").val();
            currentFilter.month = $("#monthFilter").val();
            reloadTable();
        }
    });

    window.debugCurrentFilter = function () {
        console.log("Current Filter:", currentFilter);
        console.log("Navigation History:", navigationHistory);
    };
});
