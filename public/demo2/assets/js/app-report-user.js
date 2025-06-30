const UserUrl = document.getElementById("report-user-datatable").dataset.url;

const statusObj = {
    0: { title: "Inactive", class: "bg-label-secondary" },
    1: { title: "Active", class: "bg-label-success" },
};

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#report-user-datatable").DataTable({
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
            url: UserUrl,
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
                responsivePriority: 2,
                render: function (data, type, full, meta) {
                    var roles = full["roles"];
                    if (!roles || roles.length === 0) {
                        return "<span>-</span>";
                    }
                    var roleName = roles[0].name;
                    var roleBadgeObj = {
                        user: '<i class="ti ti-diamond ti-md text-primary me-2"></i>',
                        admin: '<i class="ti ti-device-desktop ti-md text-danger me-2"></i>',
                        teknisi:
                            '<i class="ti ti-tool ti-md text-warning me-2"></i>',
                    };

                    var badge = roleBadgeObj[roleName] || "";

                    return (
                        "<span class='text-truncate d-flex align-items-center text-heading'>" +
                        badge +
                        roleName.charAt(0).toUpperCase() +
                        roleName.slice(1) +
                        "</span>"
                    );
                },
            },

            {
                targets: 5,
                render: function (data, type, full, meta) {
                    const isActive = full.is_active;
                    const status = statusObj[isActive] || {
                        title: "Unknown",
                        class: "bg-label-dark",
                    };

                    return `
      <span class="badge ${status.class} text-capitalize">
        ${status.title}
      </span>
    `;
                },
            },
            {
                targets: 6,
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
            { data: "id" },
            { data: "user_data.name" },
            { data: "role" },
            { data: "user_data.address" },
            { data: "user_data.phone_number" },
            { data: "is_active" },
            { data: "created_at" },
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
                            columns: [1, 2, 3, 4, 5, 6],
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
                            return `Laporan_Data_Alat_${year}-${month}-${day}_${hours}-${minutes}-${seconds}`;
                        },
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6],
                            format: {
                                body: function (data, coldex, rowdex) {
                                    if (
                                        typeof data !== "string" ||
                                        data.length === 0
                                    ) {
                                        return data;
                                    }

                                    const tempDiv =
                                        document.createElement("div");
                                    tempDiv.innerHTML = data;

                                    let cleanedText = "";
                                    if (coldex === 5) {
                                        const badgeElement =
                                            tempDiv.querySelector(".badge");
                                        if (badgeElement) {
                                            cleanedText =
                                                badgeElement.textContent.trim();
                                        } else {
                                            cleanedText =
                                                tempDiv.textContent.trim();
                                        }
                                    } else if (
                                        tempDiv.querySelector(".user-name")
                                    ) {
                                        cleanedText = tempDiv
                                            .querySelector(".user-name")
                                            .textContent.trim();
                                    } else {
                                        cleanedText =
                                            tempDiv.textContent.trim();
                                    }
                                    return cleanedText;
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
                            return `Laporan Pengguna - ${day}-${month}-${year}`;
                        },
                        exportOptions: {
                            // SESUAIKAN (1): Diubah menjadi 7 kolom
                            columns: [0, 1, 2, 3, 4, 5, 6],
                            format: {
                                body: function (inner, coldex, rowdex) {
                                    if (!inner) return "";

                                    // DIUBAH: Pindahkan deklarasi tempDiv ke atas
                                    const tempDiv =
                                        document.createElement("div");
                                    tempDiv.innerHTML = inner;

                                    // SOLUSI: Potong teks jika kolom adalah kolom ke-4 (indeks 3) dan panjangnya > 15
                                    if (coldex === 3) {
                                        const text = (
                                            tempDiv.textContent ||
                                            tempDiv.innerText ||
                                            ""
                                        ).trim();
                                        if (text.length > 15) {
                                            // Return teks yang sudah dipotong dan hentikan fungsi untuk sel ini
                                            return (
                                                text.substring(0, 15) + "..."
                                            );
                                        }
                                    }

                                    // Lanjutkan proses normal untuk sel lain atau jika teks tidak perlu dipotong
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
                            // --- Bagian Gaya (Styles) tidak perlu banyak diubah ---
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
                            doc.styles.tableBodyOdd = { fontSize: 9 };
                            doc.styles.tableBodyEven = {
                                fillColor: "#f5f6fa",
                                fontSize: 9,
                            };
                            doc.styles.footerText = {
                                fontSize: 8,
                                color: "#7f8c8d",
                                alignment: "center",
                            };

                            // --- Header (Kop Surat) ---
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

                            // --- Footer ---
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

                            // --- Menambahkan Judul Laporan ---
                            const tableContentIndex = doc.content.findIndex(
                                (c) => c.table
                            );
                            if (tableContentIndex !== -1) {
                                // SESUAIKAN (2): Ganti judul laporan di sini.
                                doc.content.splice(tableContentIndex, 0, {
                                    text: "LAPORAN DATA PENGGUNA",
                                    style: "reportTitle",
                                });
                                doc.content[tableContentIndex + 1].margin = [
                                    0, 0, 0, 0,
                                ];
                            }

                            // --- Menyesuaikan Tabel Utama ---
                            const table = doc.content.find((c) => c.table);
                            if (table) {
                                // SESUAIKAN (3): Definisikan lebar untuk 7 kolom.
                                table.table.widths = [
                                    30,
                                    "*",
                                    "auto",
                                    "auto",
                                    "auto",
                                    "auto",
                                    "auto",
                                ];

                                // Menerapkan gaya ke header
                                table.table.body[0].forEach((cell) => {
                                    cell.style = "tableHeader";
                                    cell.margin = [0, 4, 0, 4];
                                });

                                // Menerapkan gaya ke body
                                table.table.body.forEach((row, i) => {
                                    if (i === 0) return; // Lewati header
                                    row.forEach((cell, j) => {
                                        if (cell) {
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

                                            // SESUAIKAN (4): Tentukan kolom yang rata tengah.
                                            // Contoh: No(0), Role(4), Status(5)
                                            if (j === 0 || j === 4 || j === 5) {
                                                cell.alignment = "center";
                                            }
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
                                    hLineColor: (i, node) => {
                                        if (
                                            i === 0 ||
                                            i === 1 ||
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

                    // {
                    //     extend: "copy",
                    //     text: '<i class="ti ti-copy me-2" ></i>Copy',
                    //     className: "dropdown-item",
                    //     exportOptions: {
                    //         columns: [1, 2, 3, 4, 5, 6],
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
                '<input type="text" class="form-control" placeholder="Pilih Tanggal">'
            )
                .appendTo($(".date_filter"))
                .datepicker({
                    format: "yyyy-mm-dd",
                    autoclose: true,
                    language: "id",
                    todayHighlight: true,
                })
                .on("changeDate", function (e) {
                    var selectedDate = e.format();
                    table
                        .column(6)
                        .search("^" + selectedDate, true, false, true)
                        .draw();
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
                        .column(6)
                        .search(searchTerm, true, false, true)
                        .draw();
                } else if (month) {
                    table
                        .column(6)
                        .search("-" + month + "-", true, false, true)
                        .draw();
                } else if (year) {
                    table.column(6).search(year, true, false, true).draw();
                } else {
                    table.column(6).search("").draw();
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
                    table.column(6).search("").draw();

                    setTimeout(function () {
                        $icon.removeClass("rotating");
                    }, 1000);
                });
        },
    });
});
