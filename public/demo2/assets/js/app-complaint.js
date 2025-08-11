const Url = document.getElementById("complaints-datatable").dataset.url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#complaints-datatable").DataTable({
        processing: true,
        dom:
            '<"row"' +
            '<"col-md-2"<"ms-n2"l>>' +
            '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0"f>>' +
            ">" +
            '<"table-responsive"t>' +
            '<"row"' +
            '<"col-sm-12 col-md-6"i>' +
            '<"col-sm-12 col-md-6"p>' +
            ">",
        ajax: Url,
        columnDefs: [
            {
                targets: 0,
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                },
            },
            {
                targets: 1,
                render: function (data, type, full, meta) {
                    return full.user?.user_data?.name || "-";
                },
            },
            {
                targets: 2,
                render: function (data, type, full) {
                    if (data) {
                        const imageUrl = `/storage/${data}`;
                        const complaintTitle = full.title || "Gambar Keluhan";

                        return `
                <a href="${imageUrl}" data-lightbox="complaint-images" data-title="${complaintTitle}">
                    <img src="${imageUrl}" alt="${complaintTitle}" class="img-thumbnail" 
                         style="width: 80px; height: 80px; object-fit: cover;"
                         onerror="this.onerror=null;this.src='/path/to/default-image.png'">
                </a>
            `;
                    }
                    return "<span>Tanpa Gambar</span>";
                },
                orderable: false,
                searchable: false,
            },
            {
                targets: 3,
                render: function (data, type, full, meta) {
                    if (type === "display" && data && data.length > 30) {
                        return data.substring(0, 30) + "...";
                    }
                    return data;
                },
            },
            {
                targets: 4,
                render: function (data, type, full, meta) {
                    if (type === "display" && data && data.length > 30) {
                        return (
                            '<span title="' +
                            data +
                            '">' +
                            data.substring(0, 30) +
                            "...</span>"
                        );
                    }
                    return data;
                },
            },
            {
                targets: 5,
                render: function (data, type, full, meta) {
                    let badgeClass = "";
                    switch (full.status) {
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
                    return `<span class="badge ${badgeClass}">${
                        full.status.charAt(0).toUpperCase() +
                        full.status.slice(1)
                    }</span>`;
                },
            },
            {
                targets: 6,
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
            {
                targets: -1,
                render: function (data, type, full, meta) {
                    let buttons = "";
                    if (
                        currentUserRole === "admin" ||
                        currentUserRole === "teknisi"
                    ) {
                        if (full.status === "pending") {
                            buttons += `
                <button class="btn btn-twitter btn-process" data-id="${data}" title="Proses">
                    <i class="ti ti-refresh"></i>
                </button>
                `;
                        } else if (full.status === "processed") {
                            buttons += `
                <button class="btn btn-success btn-resolve" data-id="${data}" title="Selesaikan">
                    <i class="ti ti-check"></i>
                </button>
                `;
                        }
                    }

                    buttons += `
                    <button class="btn btn-info btn-edit-complaint" data-id="${data}" title="Edit">
                        <i class="ti ti-edit"></i>
                    </button>
                `;

                    if (currentUserRole === "admin") {
                        buttons += `
                        <button class="btn btn-danger btn-delete" data-id="${data}" title="Delete">
                            <i class="ti ti-trash"></i>
                        </button>
                    `;
                    }

                    return `<div class="btn-list">${buttons}</div>`;
                },
            },
        ],
        columns: [
            { data: "id" },
            { data: "user_name" },
            { data: "image" },
            { data: "title" },
            { data: "description" },
            { data: "status" },
            { data: "created_at" },
            { data: "id" },
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
        // buttons: [
        //     {
        //         text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Data Komplain</span>',
        //         className:
        //             "add-new btn btn-primary waves-effect waves-light mx-4",
        //         attr: {
        //             "data-bs-toggle": "offcanvas",
        //             "data-bs-target": "#offcanvasAddComplaint",
        //         },
        //     },
        // ],

        initComplete: function () {
            const api = this.api();

            api.columns(5).every(function () {
                const column = this;
                const select = $("#statusFilter")
                    .empty()
                    .append('<option value="">Semua Status</option>');

                column
                    .data()
                    .unique()
                    .sort()
                    .each(function (d) {
                        if (d) {
                            select.append(
                                '<option value="' +
                                    d +
                                    '">' +
                                    d.charAt(0).toUpperCase() +
                                    d.slice(1) +
                                    "</option>"
                            );
                        }
                    });
            });

            $("#statusFilter").on("change", function () {
                table.column(5).search(this.value).draw();
            });
        },
    });

    $("#addComplaintForm").on("submit", function (e) {
        e.preventDefault();

        const form = $(this)[0];
        const formData = new FormData(form);
        const url = $(this).data("url");
        const token = $('meta[name="csrf-token"]').attr("content");

        Notiflix.Loading.standard("Mengirim keluhan...");
        fetch("/api/complaints", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": token,
                Accept: "application/json",
            },
            body: formData,
        })
            .then(async (response) => {
                const responseData = await response.json();

                if (!response.ok) {
                    const error = new Error(
                        responseData.message || "Terjadi kesalahan"
                    );
                    error.response = responseData;
                    throw error;
                }

                return responseData;
            })
            .then((data) => {
                Notiflix.Loading.remove();
                Notiflix.Notify.success(
                    data.message || "Keluhan berhasil ditambahkan!"
                );

                form.reset();

                if ($.fn.DataTable.isDataTable("#complaints-datatable")) {
                    $("#complaints-datatable").DataTable().ajax.reload();
                }
            })
            .catch((error) => {
                Notiflix.Loading.remove();
                console.error("Error details:", error.response || error);

                let errorMessage =
                    error.message || "Gagal menambahkan keluhan.";

                if (error.response && error.response.errors) {
                    const validationErrors = Object.values(
                        error.response.errors
                    )
                        .flat()
                        .join("<br>");
                    errorMessage = `${error.response.message}<br><br><small class="text-danger">${validationErrors}</small>`;
                }

                Notiflix.Report.failure(
                    "Pendaftaran Gagal",
                    errorMessage,
                    "Tutup",
                    {
                        message_html: true,
                    }
                );
            });
    });
    $(document).on("click", ".btn-edit-complaint", function () {
        const complaintId = $(this).data("id");

        Notiflix.Loading.standard("Memuat data keluhan...");

        $.ajax({
            url: `/api/complaints/${complaintId}`,
            method: "GET",
            success: function (response) {
                Notiflix.Loading.remove();
                fillEditComplaintForm(response);

                new bootstrap.Offcanvas(
                    document.getElementById("offcanvasEditComplaint")
                ).show();
            },
            error: function (xhr) {
                Notiflix.Loading.remove();
                Notiflix.Notify.failure("Gagal memuat data keluhan");
            },
        });
    });

    function fillEditComplaintForm(data) {
        const complaint = data.complaint || data;

        $("#edit_complaint_id").val(complaint.id);
        $("#edit_title").val(complaint.title);
        $("#edit_description").val(complaint.description);
        $("#edit_status").val(complaint.status);

        if (complaint.image) {
            $("#edit_image_preview").html(
                `<img src="/storage/${complaint.image}" class="img-thumbnail" width="150">`
            );
        } else {
            $("#edit_image_preview").html("<p>Tidak ada gambar</p>");
        }
    }

    $("#editComplaintForm").submit(function (e) {
        e.preventDefault();

        const title = $("#edit_title").val();
        const description = $("#edit_description").val();
        const status = $("#edit_status").val();

        if (!title || !description || !status) {
            Notiflix.Notify.failure("Harap isi semua field yang wajib diisi");
            return;
        }

        const complaintId = $("#edit_complaint_id").val();
        const formData = new FormData(this);

        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        Notiflix.Loading.standard("Menyimpan perubahan...");

        $.ajax({
            url: `/api/complaints/${complaintId}`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-HTTP-Method-Override": "PUT",
            },
            success: function (response) {
                Notiflix.Loading.remove();
                Notiflix.Notify.success(
                    response.message || "Perubahan berhasil disimpan"
                );
                table.ajax.reload(null, false);
                bootstrap.Offcanvas.getInstance(
                    $("#offcanvasEditComplaint")[0]
                ).hide();
            },
            error: function (xhr) {
                Notiflix.Loading.remove();
                let errorMessage =
                    xhr.responseJSON?.message || "Gagal menyimpan perubahan";

                if (xhr.responseJSON?.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join(
                        "<br>"
                    );
                }

                Notiflix.Notify.failure(errorMessage);
            },
        });
    });
    $(document).on("click", ".btn-delete", function () {
        const complaintId = $(this).data("id");

        Notiflix.Confirm.show(
            "Hapus Keluhan",
            "Apakah Anda yakin ingin menghapus keluhan ini?",
            "Ya",
            "Tidak",
            function () {
                Notiflix.Loading.standard("Menghapus keluhan...");

                $.ajax({
                    url: `/api/complaints/${complaintId}`,
                    method: "DELETE",
                    success: function (response) {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.success(
                            response.message || "Keluhan berhasil dihapus"
                        );
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.failure(
                            xhr.responseJSON?.message ||
                                "Gagal menghapus keluhan"
                        );
                    },
                });
            },
            function () {
                Notiflix.Notify.info("Penghapusan dibatalkan");
            },
            {
                titleColor: "#ff4c51",
                okButtonBackground: "#ff4c51",
            }
        );
    });

    $(document).on("click", ".btn-process", function () {
        const complaintId = $(this).data("id");

        Notiflix.Confirm.show(
            "Proses Keluhan",
            "Tandai keluhan ini sebagai sedang diproses?",
            "Ya",
            "Tidak",
            function () {
                Notiflix.Loading.standard("Memproses...");

                $.ajax({
                    url: `/api/complaints/${complaintId}/process`,
                    method: "POST",
                    success: function (response) {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.success(
                            response.message || "Status keluhan diperbarui"
                        );
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.failure(
                            xhr.responseJSON?.message ||
                                "Gagal memperbarui status"
                        );
                    },
                });
            }
        );
    });

    $(document).on("click", ".btn-resolve", function () {
        const complaintId = $(this).data("id");

        Notiflix.Confirm.show(
            "Selesaikan Keluhan",
            "Tandai keluhan ini sebagai selesai?",
            "Ya",
            "Tidak",
            function () {
                Notiflix.Loading.standard("Memproses...");

                $.ajax({
                    url: `/api/complaints/${complaintId}/resolve`,
                    method: "POST",
                    success: function (response) {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.success(
                            response.message || "Keluhan berhasil diselesaikan"
                        );
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.failure(
                            xhr.responseJSON?.message ||
                                "Gagal menyelesaikan keluhan"
                        );
                    },
                });
            }
        );
    });

    $("#image, #edit_image").change(function () {
        const previewId =
            $(this).attr("id") === "image"
                ? "#image_preview"
                : "#edit_image_preview";
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                $(previewId).html(
                    `<img src="${e.target.result}" class="img-thumbnail" width="150">`
                );
            };

            reader.readAsDataURL(file);
        }
    });
});
