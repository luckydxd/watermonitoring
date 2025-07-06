const UserDeviceUrl = document.getElementById("user-devices-datatable").dataset
    .url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#user-devices-datatable").DataTable({
        processing: true,
        serverSide: false,
        ajax: UserDeviceUrl,
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
                    return full.device_type?.name || "-";
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
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        const date = new Date(data);
                        return date.toLocaleDateString("id-ID", {
                            day: "2-digit",
                            month: "2-digit",
                            year: "numeric",
                            hour: "2-digit",
                            minute: "2-digit",
                        });
                    }
                    return data;
                },
            },
        ],
        columns: [
            { data: "id" },
            { data: "unique_id" },
            { data: "device_type.name" },
            { data: "status" },
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
                ],
            },
        ],
    });

    $(document).on("click", ".btn-view-device", function () {
        const deviceId = $(this).data("id");
        window.location.href = `/devices/${deviceId}`;
    });
});
