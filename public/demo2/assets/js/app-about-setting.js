const Url = document.getElementById("about-datatable").dataset.url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    let table = $("#about-datatable").DataTable({
        processing: true,
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
                    if (full.image) {
                        // Construct the full image URL
                        const imageUrl = "/storage/" + full.image;
                        return `<img src="${imageUrl}" alt="about Image" class="thumb-lg rounded" 
                            style="width: 100px; height: 100px; object-fit: cover;"
                            onerror="this.onerror=null;this.src='/images/default-about.png'">`;
                    }
                    return '<img src="/images/default-about.png" class="thumb-lg rounded" style="width: 100px; height: 100px;">';
                },
                orderable: false,
                searchable: false,
            },
            {
                targets: -1, // Actions column
                render: function (data, type, full, meta) {
                    return `
                    <button class="btn btn-info btn-edit-about" data-id="${data}">
                        <i class="ti ti-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-delete" data-id="${data}">
                        <i class="ti ti-trash"></i>
                    </button>
                </div> `;
                },
            },
        ],
        columns: [
            { data: "id" }, // No
            { data: "image" }, // Image
            { data: "title" }, // Title
            { data: "description" }, // Description
            { data: "id" }, // Actions
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
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Data About</span>',
                className:
                    "add-new btn btn-primary waves-effect waves-light mx-4",
                attr: {
                    "data-bs-toggle": "offcanvas",
                    "data-bs-target": "#offcanvasAddAbout",
                },
            },
        ],
    });

    $("#addAboutForm").submit(function (e) {
        e.preventDefault();

        // Debug: Cek form data sebelum dikirim
        console.log("Form data:", $(this).serialize());

        // Buat FormData object untuk handle file upload
        const formData = new FormData(this);

        // Debug: Lihat isi FormData
        for (let [key, value] of formData.entries()) {
            console.log(key + ": " + value);
        }
        Notiflix.Loading.standard("Mengirim keluhan...");
        fetch("/api/landing/about", {
            method: "POST",
            body: formData,
        })
            .then((response) => {
                if (!response.ok) {
                    return response.json().then((err) => {
                        throw err;
                    });
                }
                return response.json();
            })
            .then((data) => {
                Notiflix.Loading.remove();
                console.log("Success response:", data);
                Notiflix.Notify.success(
                    data.message || "About berhasil ditambahkan!"
                );

                // Reset form
                $("#addAboutForm")[0].reset();

                // Refresh tabel
                table.ajax.reload(null, false);

                // Tutup offcanvas
                bootstrap.Offcanvas.getInstance(
                    $("#offcanvasAddAbout")[0]
                ).hide();
            })
            .catch((error) => {
                Notiflix.Loading.remove();
                console.error("Error details:", error);

                let errorMessage = "Gagal menambahkan about";
                if (error.message) {
                    errorMessage = error.message;
                }
                if (error.errors) {
                    errorMessage = Object.values(error.errors).join("\n");
                }

                Notiflix.Notify.failure(errorMessage);
            });
    });

    $(document).on("click", ".btn-edit-about", function () {
        const aboutId = $(this).data("id");

        Notiflix.Loading.standard("Memuat data keluhan...");

        $.ajax({
            url: `/api/landing/about/${aboutId}`,
            method: "GET",
            success: function (response) {
                Notiflix.Loading.remove();
                fillEditAboutForm(response);

                new bootstrap.Offcanvas(
                    document.getElementById("offcanvasEditAbout")
                ).show();
            },
            error: function (xhr) {
                Notiflix.Loading.remove();
                Notiflix.Notify.failure("Gagal memuat data about");
            },
        });
    });

    function fillEditAboutForm(data) {
        const about = data.about || data;

        $("#edit_about_id").val(about.id);
        $("#edit_title").val(about.title);
        $("#edit_description").val(about.description);
        // Tampilkan gambar sebelumnya jika ada
        if (about.image) {
            $("#edit_image_preview").html(
                `<img src="/storage/${about.image}" class="img-thumbnail" width="150">`
            );
        } else {
            $("#edit_image_preview").html("<p>Tidak ada gambar</p>");
        }
    }

    $("#editAboutForm").submit(function (e) {
        e.preventDefault();

        const title = $("#edit_title").val();
        const description = $("#edit_description").val();

        if (!title || !description) {
            Notiflix.Notify.failure("Harap isi semua field yang wajib diisi");
            return;
        }

        const aboutId = $("#edit_about_id").val();
        const formData = new FormData(this);

        // Debug: Lihat isi FormData
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        Notiflix.Loading.standard("Menyimpan perubahan...");

        $.ajax({
            url: `/api/landing/about/${aboutId}`,
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
                    $("#offcanvasEditAbout")[0]
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
                    url: `/api/landing/about/${complaintId}`,
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
                width: "320px",
                borderRadius: "8px",
                titleColor: "#ff0000",
            }
        );
    });
});
