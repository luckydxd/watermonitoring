const userListUrl = document.getElementById("table-user").dataset.url;
var assetUrl = "{{ asset('/') }}";
var assetsPath = "{{ asset('demo2/assets/') }}";

function createInitialAvatar(name) {
    if (!name || typeof name !== "string") {
        name = "NN";
    }

    var stateNum = Math.floor(Math.random() * 6);
    var states = [
        "success",
        "danger",
        "warning",
        "info",
        "primary",
        "secondary",
    ];
    var $state = states[stateNum];

    var $initials = name.match(/\b\w/g) || [];
    $initials = (
        ($initials.shift() || "") + ($initials.pop() || "")
    ).toUpperCase();

    if ($initials.length === 0) {
        $initials = name.substring(0, 2).toUpperCase() || "NN";
    }

    return (
        '<span class="avatar-initial rounded-circle bg-label-' +
        $state +
        '">' +
        $initials +
        "</span>"
    );
}

("use strict");

$(function () {
    let borderColor, bodyBg, headingColor;

    if (isDarkStyle) {
        borderColor = config.colors_dark.borderColor;
        bodyBg = config.colors_dark.bodyBg;
        headingColor = config.colors_dark.headingColor;
    } else {
        borderColor = config.colors.borderColor;
        bodyBg = config.colors.bodyBg;
        headingColor = config.colors.headingColor;
    }

    var dt_user_table = $(".datatables-users"),
        select2 = $(".select2"),
        userView = "javascript:;",
        statusObj = {
            1: { title: "Active", class: "bg-label-success" },
            0: { title: "Inactive", class: "bg-label-secondary" },
        };

    if (dt_user_table.length) {
        var dt_user = dt_user_table.DataTable({
            ajax: userListUrl,

            columns: [
                { data: "id" },
                { data: "name" },
                { data: "role" },
                { data: "address" },
                { data: "phone_number" },
                { data: "isActive" },
                { data: "id" },
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

            columnDefs: [
                {
                    searchable: false,
                    orderable: false,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return "";
                    },
                },
                {
                    targets: 1,
                    responsivePriority: 1,
                    render: function (data, type, full, meta) {
                        var $name = full["name"],
                            $email = full["email"],
                            $image = full["image"],
                            $branch = full["branch_name"]; // Ambil data nama cabang

                        var $output;
                        // ... (kode untuk $output avatar tetap sama)
                        if ($image) {
                            var sanitizedName = String($name || "")
                                .replace(/'/g, "\\'")
                                .replace(/"/g, "&quot;");
                            $output =
                                '<img src="' +
                                $image +
                                '" alt="' +
                                $name +
                                '" class="rounded-circle" onerror="this.outerHTML = createInitialAvatar(\'' +
                                sanitizedName +
                                "');\">";
                        } else {
                            $output = createInitialAvatar($name);
                        }

                        // Buat elemen HTML untuk cabang HANYA jika datanya ada
                        var branchInfo = "";
                        if ($branch) {
                            branchInfo =
                                '<div class="text-muted small"><i class="ti ti-building-community me-1"></i>' +
                                $branch +
                                "</div>";
                        }

                        return (
                            '<div class="d-flex justify-content-start align-items-center user-name">' +
                            '<div class="avatar-wrapper">' +
                            '<div class="avatar avatar-sm me-4">' +
                            $output +
                            "</div>" +
                            "</div>" +
                            '<div class="d-flex flex-column">' +
                            '<a href="#" class="text-heading text-truncate"><span class="fw-medium">' +
                            $name +
                            "</span></a>" +
                            "<small>" +
                            $email +
                            "</small>" +
                            branchInfo + // Sisipkan informasi cabang di sini
                            "</div>" +
                            "</div>"
                        );
                    },
                },
                {
                    targets: 2,
                    responsivePriority: 2,
                    render: function (data, type, full, meta) {
                        var $role = full["role"];
                        var roleBadgeObj = {
                            user: '<i class="ti ti-diamond ti-md text-primary me-2"></i>',
                            admin: '<i class="ti ti-device-desktop ti-md text-danger me-2"></i>',
                            teknisi:
                                '<i class="ti ti-tool ti-md text-warning me-2"></i>',
                        };
                        return (
                            "<span class='text-truncate d-flex align-items-center text-heading'>" +
                            roleBadgeObj[$role] +
                            $role +
                            "</span>"
                        );
                    },
                },
                {
                    targets: 5,
                    responsivePriority: 2,
                    render: function (data, type, full, meta) {
                        var status = full["isActive"];

                        return statusObj[status]
                            ? `<span class="badge ${statusObj[status].class} text-capitalized">${statusObj[status].title}</span>`
                            : `<span class="badge bg-label-dark text-capitalized">Unknown</span>`;
                    },
                },
                {
                    targets: -1,
                    searchable: false,
                    orderable: false,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        let buttons = "";

                        // View button untuk semua role
                        buttons +=
                            '<a href="javascript:;" data-id="' +
                            full.id +
                            '" class="btn btn-icon btn-text-secondary view-record mx-1" title="Lihat Detail">' +
                            '<i class="ti ti-eye ti-md"></i>' +
                            "</a>";

                        // Admin buttons
                        if (currentUserRole === "admin") {
                            buttons +=
                                '<a href="javascript:;" data-id="' +
                                full.id +
                                '" class="btn btn-icon btn-text-info edit-record mx-1" data-bs-toggle="offcanvas" data-bs-target="#editUserOffcanvas" title="Edit Pengguna">' +
                                '<i class="ti ti-edit ti-md"></i>' +
                                "</a>";
                            buttons +=
                                '<a href="javascript:;" data-id="' +
                                full.id +
                                '" class="btn btn-icon btn-text-danger delete-record mx-1" title="Hapus Pengguna">' +
                                '<i class="ti ti-trash ti-md"></i>' +
                                "</a>";
                        }

                        // Teknisi buttons
                        if (currentUserRole === "teknisi") {
                            // Button untuk assign device ke user
                            buttons +=
                                '<a href="javascript:;" data-id="' +
                                full.id +
                                '" data-name="' +
                                full.name +
                                '" data-email="' +
                                full.email +
                                '" class="btn btn-icon btn-text-success assign-device-to-user mx-1" title="Daftarkan Perangkat">' +
                                '<i class="ti ti-hexagon-plus ti-md"></i>' +
                                "</a>";
                        }

                        return (
                            '<div class="d-flex align-items-center">' +
                            buttons +
                            "</div>"
                        );
                    },
                },
            ],
            order: [[2, "desc"]],
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

            buttons:
                currentUserRole === "admin"
                    ? [
                          {
                              text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Tambah Data Pengguna</span>',
                              className:
                                  "add-new btn btn-primary waves-effect waves-light mx-4",
                              attr: {
                                  "data-bs-toggle": "offcanvas",
                                  "data-bs-target": "#offcanvasAddUser",
                              },
                          },
                      ]
                    : [],
            // buttons: [
            //     {
            //         extend: "collection",
            //         className:
            //             "btn btn-label-secondary dropdown-toggle mx-4 waves-effect waves-light",
            //         text: '<i class="ti ti-upload me-2 ti-xs"></i>Ekspor',
            //         buttons: [
            //             {
            //                 extend: "print",
            //                 text: '<i class="ti ti-printer me-2" ></i>Cetak',
            //                 className: "dropdown-item",
            //                 exportOptions: {
            //                     columns: [1, 2, 3, 4, 5],
            //                     format: {
            //                         body: function (inner, coldex, rowdex) {
            //                             if (inner.length <= 0) return inner;
            //                             var el = $.parseHTML(inner);
            //                             var result = "";
            //                             $.each(el, function (index, item) {
            //                                 if (
            //                                     item.classList !== undefined &&
            //                                     item.classList.contains(
            //                                         "user-name"
            //                                     )
            //                                 ) {
            //                                     result =
            //                                         result +
            //                                         item.lastChild.firstChild
            //                                             .textContent;
            //                                 } else if (
            //                                     item.innerText === undefined
            //                                 ) {
            //                                     result =
            //                                         result + item.textContent;
            //                                 } else
            //                                     result =
            //                                         result + item.innerText;
            //                             });
            //                             return result;
            //                         },
            //                     },
            //                 },
            //                 customize: function (win) {
            //                     $(win.document.body)
            //                         .css("color", headingColor)
            //                         .css("border-color", borderColor)
            //                         .css("background-color", bodyBg);
            //                     $(win.document.body)
            //                         .find("table")
            //                         .addClass("compact")
            //                         .css("color", "inherit")
            //                         .css("border-color", "inherit")
            //                         .css("background-color", "inherit");
            //                 },
            //             },
            //             // {
            //             //     extend: "csv",
            //             //     text: '<i class="ti ti-file-text me-2" ></i>Csv',
            //             //     className: "dropdown-item",
            //             //     exportOptions: {
            //             //         columns: [1, 2, 3, 4, 5],
            //             //         format: {
            //             //             body: function (inner, coldex, rowdex) {
            //             //                 if (inner.length <= 0) return inner;
            //             //                 var el = $.parseHTML(inner);
            //             //                 var result = "";
            //             //                 $.each(el, function (index, item) {
            //             //                     if (
            //             //                         item.classList !== undefined &&
            //             //                         item.classList.contains(
            //             //                             "user-name"
            //             //                         )
            //             //                     ) {
            //             //                         result =
            //             //                             result +
            //             //                             item.lastChild.firstChild
            //             //                                 .textContent;
            //             //                     } else if (
            //             //                         item.innerText === undefined
            //             //                     ) {
            //             //                         result =
            //             //                             result + item.textContent;
            //             //                     } else
            //             //                         result =
            //             //                             result + item.innerText;
            //             //                 });
            //             //                 return result;
            //             //             },
            //             //         },
            //             //     },
            //             // },
            //             {
            //                 extend: "excel",
            //                 text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
            //                 className: "dropdown-item",
            //                 exportOptions: {
            //                     columns: [1, 2, 3, 4, 5],
            //                     format: {
            //                         body: function (inner, coldex, rowdex) {
            //                             if (inner.length <= 0) return inner;
            //                             var el = $.parseHTML(inner);
            //                             var result = "";
            //                             $.each(el, function (index, item) {
            //                                 if (
            //                                     item.classList !== undefined &&
            //                                     item.classList.contains(
            //                                         "user-name"
            //                                     )
            //                                 ) {
            //                                     result =
            //                                         result +
            //                                         item.lastChild.firstChild
            //                                             .textContent;
            //                                 } else if (
            //                                     item.innerText === undefined
            //                                 ) {
            //                                     result =
            //                                         result + item.textContent;
            //                                 } else
            //                                     result =
            //                                         result + item.innerText;
            //                             });
            //                             return result;
            //                         },
            //                     },
            //                 },
            //             },
            //             {
            //                 extend: "pdf",
            //                 text: '<i class="ti ti-file-code-2 me-2"></i>Pdf',
            //                 className: "dropdown-item",
            //                 exportOptions: {
            //                     columns: [1, 2, 3, 4, 5],
            //                     format: {
            //                         body: function (inner, coldex, rowdex) {
            //                             if (inner.length <= 0) return inner;
            //                             var el = $.parseHTML(inner);
            //                             var result = "";
            //                             $.each(el, function (index, item) {
            //                                 if (
            //                                     item.classList !== undefined &&
            //                                     item.classList.contains(
            //                                         "user-name"
            //                                     )
            //                                 ) {
            //                                     result =
            //                                         result +
            //                                         item.lastChild.firstChild
            //                                             .textContent;
            //                                 } else if (
            //                                     item.innerText === undefined
            //                                 ) {
            //                                     result =
            //                                         result + item.textContent;
            //                                 } else
            //                                     result =
            //                                         result + item.innerText;
            //                             });
            //                             return result;
            //                         },
            //                     },
            //                 },
            //             },
            //             // {
            //             //     extend: "copy",
            //             //     text: '<i class="ti ti-copy me-2" ></i>Copy',
            //             //     className: "dropdown-item",
            //             //     exportOptions: {
            //             //         columns: [1, 2, 3, 4, 5],
            //             //         format: {
            //             //             body: function (inner, coldex, rowdex) {
            //             //                 if (inner.length <= 0) return inner;
            //             //                 var el = $.parseHTML(inner);
            //             //                 var result = "";
            //             //                 $.each(el, function (index, item) {
            //             //                     if (
            //             //                         item.classList !== undefined &&
            //             //                         item.classList.contains(
            //             //                             "user-name"
            //             //                         )
            //             //                     ) {
            //             //                         result =
            //             //                             result +
            //             //                             item.lastChild.firstChild
            //             //                                 .textContent;
            //             //                     } else if (
            //             //                         item.innerText === undefined
            //             //                     ) {
            //             //                         result =
            //             //                             result + item.textContent;
            //             //                     } else
            //             //                         result =
            //             //                             result + item.innerText;
            //             //                 });
            //             //                 return result;
            //             //             },
            //             //         },
            //             //     },
            //             // },
            //         ],
            //     },
            // ],
            // responsive: {
            //     details: {
            //         display: $.fn.dataTable.Responsive.display.modal({
            //             header: function (row) {
            //                 var data = row.data();
            //                 return "Detail " + data["name"];
            //             },
            //         }),
            //         type: "column",
            //         renderer: function (api, rowIdx, columns) {
            //             var data = $.map(columns, function (col, i) {
            //                 return col.title !== ""
            //                     ? '<tr data-dt-row="' +
            //                           col.rowIndex +
            //                           '" data-dt-column="' +
            //                           col.columnIndex +
            //                           '">' +
            //                           "<td>" +
            //                           col.title +
            //                           ":" +
            //                           "</td> " +
            //                           "<td>" +
            //                           col.data +
            //                           "</td>" +
            //                           "</tr>"
            //                     : "";
            //             }).join("");

            //             return data
            //                 ? $('<table class="table"/><tbody />').append(data)
            //                 : false;
            //         },
            //     },
            // },

            initComplete: function () {
                this.api()
                    .columns(2)
                    .every(function () {
                        var column = this;
                        var select = $(
                            '<select id="UserRole" class="form-select text-capitalize"><option value=""> Pilih Peran </option></select>'
                        )
                            .appendTo(".user_role")
                            .on("change", function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
                                column
                                    .search(
                                        val ? "^" + val + "$" : "",
                                        true,
                                        false
                                    )
                                    .draw();
                            });

                        column
                            .data()
                            .unique()
                            .sort()
                            .each(function (d, j) {
                                select.append(
                                    '<option value="' +
                                        d +
                                        '">' +
                                        d +
                                        "</option>"
                                );
                            });
                    });
            },
        });
    }
    $("#addNewUserForm").on("submit", function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.data("url");

        const formData = {
            email: $("#email").val(),
            password: $("#password").val(),
            name: $("#name").val(),
            address: $("#address").val(),
            phone_number: $("#phone_number").val(),
            role: $("#role").val(),
            branch_id: $("#branch_id").val(),
        };

        Notiflix.Loading.standard("Menambahkan user...");
        $.ajax({
            url: url,
            method: "POST",
            contentType: "application/json",
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
            data: JSON.stringify(formData),
            success: function () {
                Notiflix.Loading.remove();

                Notiflix.Notify.success("User berhasil ditambahkan!", {
                    timeout: 3000,
                    showOnlyTheLastOne: true,
                });

                form[0].reset();

                $("#offcanvasAddUser").offcanvas("hide");

                dt_user.ajax.reload();
            },
            error: function (xhr) {
                Notiflix.Loading.remove();

                const errorMessage =
                    xhr.responseJSON?.message || "Gagal menambahkan user!";

                Notiflix.Notify.failure(errorMessage, {
                    timeout: 4000,
                    showOnlyTheLastOne: true,
                });

                console.error(xhr.responseText);
            },
        });
    });

    function fillEditForm(userData) {
        console.log("Mengisi form dengan data:", userData);

        $('#editUserForm input[name="id"]').val(userData.id);
        $('#editUserForm input[name="email"]').val(userData.email || "");
        $('#editUserForm input[name="name"]').val(
            userData.user_data?.name || ""
        );
        $('#editUserForm input[name="address"]').val(
            userData.user_data?.address || ""
        );
        $('#editUserForm input[name="phone_number"]').val(
            userData.user_data?.phone_number || ""
        );

        const userRole = userData.roles?.[0]?.name || "user";
        console.log("Setting role to:", userRole);

        $('#editUserForm select[name="role"]').val(userRole).trigger("change");

        const branchId = userData.branch_id;
        if (branchId) {
            $('#editUserForm select[name="branch_id"]').val(branchId);
        } else {
            $('#editUserForm select[name="branch_id"]').val("");
        }

        $('#editUserForm select[name="isActive"]').val(
            userData.is_active ? "1" : "0"
        );
    }

    $(document).on("click", ".edit-record", function () {
        closePreviousModalsOrOffcanvas();
        const userId = $(this).data("id");
        console.log("Memulai edit user ID:", userId);

        $("#editUserForm")[0].reset();

        $.ajax({
            url: `/api/users/edit/${userId}`,
            method: "GET",
            dataType: "json",
            success: function (response) {
                console.log("Data user diterima:", response);
                fillEditForm(response);
            },
            error: function (xhr) {
                console.error("Error:", xhr);
                if (xhr.status === 404) {
                    alert("User tidak ditemukan");
                } else {
                    alert("Gagal memuat data user");
                }
            },
        });
    });

    $("#editUserForm").submit(function (e) {
        e.preventDefault();
        const userId = $(this).find('input[name="id"]').val();

        const formData = {
            email: $(this).find('input[name="email"]').val(),
            name: $(this).find('input[name="name"]').val(),
            address: $(this).find('input[name="address"]').val(),
            phone_number: $(this).find('input[name="phone_number"]').val(),
            role: $(this).find('select[name="role"]').val(),
            branch_id: $(this).find('select[name="branch_id"]').val(),
            isActive: $(this).find('select[name="isActive"]').val() === "1",
        };

        console.log("Mengupdate user dengan data:", formData);

        Notiflix.Loading.standard("Mengupdate data user...");

        $.ajax({
            url: `/api/users/${userId}`,
            method: "PUT",
            contentType: "application/json",
            data: JSON.stringify(formData),
            success: function (response) {
                Notiflix.Loading.remove();

                Notiflix.Notify.success("Data user berhasil diupdate!", {
                    timeout: 3000,
                    showOnlyTheLastOne: true,
                });

                $("#editUserOffcanvas").offcanvas("hide");

                dt_user.ajax.reload();
            },
            error: function (xhr) {
                Notiflix.Loading.remove();

                const errorMessage =
                    xhr.responseJSON?.message || "Gagal mengupdate data user!";

                Notiflix.Notify.failure(errorMessage, {
                    timeout: 4000,
                    showOnlyTheLastOne: true,
                });

                console.error(xhr.responseText);
            },
        });
    });

    $(document).on("click", ".delete-record", function () {
        const userId = $(this).data("id");
        Notiflix.Confirm.show(
            "Hapus Pengguna",
            "Apakah kamu yakin menghapus pengguna ini?",
            "Ya",
            "Tidak",
            function okCb() {
                Notiflix.Loading.standard("Deleting user...");

                fetch(`/api/users/${userId}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                })
                    .then((res) => {
                        Notiflix.Loading.remove();

                        if (res.ok) {
                            Notiflix.Notify.success(
                                "Berhasil menghapus pengguna."
                            );
                            dt_user.ajax.reload();
                        } else {
                            return res.json().then((data) => {
                                throw new Error(
                                    data.message || "Gagal menghapus pengguna."
                                );
                            });
                        }
                    })
                    .catch((err) => {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.failure(`Error: ${err.message}`);
                    });
            },
            function cancelCb() {
                Notiflix.Notify.info("Delete canceled");
            },
            {
                titleColor: "#ff4c51",
                okButtonBackground: "#ff4c51",
            }
        );
    });

    $(document).on("click", ".view-record", function () {
        const userId = $(this).data("id");
        const baseUrl = $("#users-table").data("show-url");
        if (!baseUrl) {
            console.error(
                "Error: Atribut data-show-url tidak ditemukan pada tabel!"
            );
            alert("Konfigurasi tabel error. Lapor ke administrator.");
            return;
        }
        const url = `${baseUrl}/${userId}`;

        const modal = $("#userDetailModal");

        modal
            .find(
                "#modalUserName, #modalUserEmail, #modalUserPhone, #modalUserAddress, #modalUserRole, #modalUserRegisteredAt"
            )
            .text("...");
        modal
            .find("#modalUserStatus")
            .text("-")
            .removeClass("bg-label-success bg-label-danger")
            .addClass("bg-label-secondary");
        modal
            .find("#modalUserAvatar")
            .html(
                '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
            );
        modal
            .find("#modalUserDevices")
            .html('<p class="text-muted">Memuat data...</p>');

        modal.modal("show");

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success && response.data) {
                    const user = response.data;

                    modal
                        .find("#modalUserName")
                        .text(user.name || "Nama Tidak Tersedia");
                    modal.find("#modalUserEmail").text(user.email || "-");

                    let avatarHtml = user.image
                        ? `<img src="${user.image}" alt="Profile" class="rounded-circle" style="width:70px; height:70px; object-fit: cover;" />`
                        : createInitialAvatar(user.name);
                    modal.find("#modalUserAvatar").html(avatarHtml);

                    modal
                        .find("#modalUserPhone")
                        .text(user.phone_number || "-");
                    modal.find("#modalUserAddress").text(user.address || "-");
                    modal
                        .find("#modalUserRole")
                        .text(
                            user.role
                                ? user.role.charAt(0).toUpperCase() +
                                      user.role.slice(1)
                                : "-"
                        );
                    modal.find("#modalUserRegisteredAt").text(
                        new Date(user.created_at).toLocaleDateString("id-ID", {
                            day: "numeric",
                            month: "long",
                            year: "numeric",
                        })
                    );

                    const statusBadge = modal.find("#modalUserStatus");
                    statusBadge.text(user.isActive ? "Aktif" : "Tidak Aktif");
                    statusBadge
                        .removeClass(
                            "bg-label-secondary bg-label-success bg-label-danger"
                        )
                        .addClass(
                            user.isActive
                                ? "bg-label-success"
                                : "bg-label-danger"
                        );

                    const devicesContainer = modal.find("#modalUserDevices");
                    if (user.devices && user.devices.length > 0) {
                        let devicesHtml =
                            '<ul class="list-group list-group-flush">';
                        user.devices.forEach((device) => {
                            devicesHtml += `<li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <div><h6 class="mb-0">${device.device_unique_id}</h6><small class="text-muted">${device.device_type}</small></div>
                                <span class="badge bg-label-info">${device.created_at}</span>
                            </li>`;
                        });
                        devicesHtml += "</ul>";
                        devicesContainer.html(devicesHtml);
                    } else {
                        devicesContainer.html(
                            '<p class="text-muted">Tidak ada perangkat terpasang.</p>'
                        );
                    }
                } else {
                    modal
                        .find(".modal-body")
                        .html(
                            '<p class="text-center text-danger">Gagal memuat data pengguna: ' +
                                (response.message ||
                                    "Format respons tidak dikenal.") +
                                "</p>"
                        );
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr);
                modal
                    .find(".modal-body")
                    .html(
                        '<p class="text-center text-danger">Terjadi kesalahan saat menghubungi server.</p>'
                    );
            },
        });
    });

    $(document).on("click", ".toggle-status", function () {
        const userId = $(this).data("id");

        Notiflix.Confirm.show(
            "Ubah Status Pengguna",
            "Anda yakin ingin mengubah status pengguna ini?",
            "Ya, Ubah",
            "Batal",
            function okCb() {
                Notiflix.Loading.standard("Mengubah status...");

                fetch(`/api/users/${userId}/toggle-status`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                })
                    .then((res) => {
                        Notiflix.Loading.remove();

                        if (res.ok) {
                            dt_user.ajax.reload(null, false);
                            return res.json();
                        } else {
                            return res.json().then((data) => {
                                throw new Error(
                                    data.message || "Gagal mengubah status"
                                );
                            });
                        }
                    })
                    .then((data) => {
                        Notiflix.Notify.success(
                            data.message || "Status berhasil diubah."
                        );
                    })
                    .catch((err) => {
                        Notiflix.Loading.remove();
                        Notiflix.Notify.failure(`Error: ${err.message}`);
                    });
            },
            function cancelCb() {
                Notiflix.Notify.info("Perubahan dibatalkan.");
            },
            {
                width: "320px",
                borderRadius: "8px",
            }
        );
    });

    function closePreviousModalsOrOffcanvas() {
        var openModal = $(".modal.show");
        if (openModal.length > 0) {
            openModal.modal("hide");
        }

        var openOffcanvas = $(".offcanvas.show");
        if (openOffcanvas.length > 0) {
            const offcanvasInstance = bootstrap.Offcanvas.getInstance(
                openOffcanvas[0]
            );
            offcanvasInstance.hide();
        }
    }

    setTimeout(() => {
        $(".dataTables_filter .form-control").removeClass("form-control-sm");
        $(".dataTables_length .form-select").removeClass("form-select-sm");
    }, 300);
    $(function () {
        let debounceTimer;

        // Show/hide initial meter reading based on device type
        $(document).on("input", "#assign_unique_id", function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const uniqueId = $(this).val().toUpperCase();
                const wrapper = $("#initial-reading-wrapper-tech");
                const input = $("#assign_initial_meter_reading");

                if (uniqueId.includes("F")) {
                    wrapper.slideDown();
                    input.prop("required", true);
                } else {
                    wrapper.slideUp();
                    input.prop("required", false).val("");
                }
            }, 500);
        });

        // Assign device button click - HANYA SATU EVENT HANDLER
        $(document).on("click", ".assign-device-to-user", function () {
            const userId = $(this).data("id");
            const userName = $(this).data("name");
            const userEmail = $(this).data("email");

            $("#assign_user_id").val(userId);
            $("#selected-user-info").html(
                `<strong>${userName}</strong><br><small class="text-muted">${userEmail}</small>`
            );
            $("#technicianAssignDeviceForm")[0].reset();
            $("#assign_user_id").val(userId);
            $("#initial-reading-wrapper-tech").hide();
            $("#assign_initial_meter_reading").prop("required", false);

            const offcanvas = new bootstrap.Offcanvas(
                document.getElementById("offcanvasTechnicianAssignDevice")
            );
            offcanvas.show();
        });

        // Form submission
        $("#technicianAssignDeviceForm").on("submit", function (e) {
            e.preventDefault();

            Notiflix.Loading.standard("Mendaftarkan perangkat...");

            const formData = {
                user_id: $("#assign_user_id").val(),
                unique_id: $("#assign_unique_id").val(),
                initial_meter_reading: $("#assign_initial_meter_reading").val(),
                notes: $("#assign_notes").val(),
            };

            $.ajax({
                url: $(this).data("url"),
                method: "POST",
                headers: { "X-CSRF-TOKEN": $(this).data("token") },
                contentType: "application/json",
                data: JSON.stringify(formData),
                success: function (response) {
                    Notiflix.Loading.remove();
                    Notiflix.Notify.success(
                        response.message || "Perangkat berhasil didaftarkan!"
                    );

                    const offcanvas = bootstrap.Offcanvas.getInstance(
                        document.getElementById(
                            "offcanvasTechnicianAssignDevice"
                        )
                    );
                    offcanvas.hide();

                    if (typeof dt_users !== "undefined") {
                        dt_users.ajax.reload();
                    } else {
                        setTimeout(() => location.reload(), 1500);
                    }
                },
                error: function (xhr) {
                    Notiflix.Loading.remove();
                    let errorMessage = "Terjadi kesalahan. Silakan coba lagi.";

                    if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || errorMessage;
                        if (xhr.responseJSON.errors) {
                            const errors = Object.values(
                                xhr.responseJSON.errors
                            )
                                .flat()
                                .join("<br>");
                            errorMessage += `<br><small class="text-danger">${errors}</small>`;
                        }
                    }

                    Notiflix.Report.failure(
                        "Pendaftaran Gagal",
                        errorMessage,
                        "Tutup",
                        { messageMaxLength: 1500 }
                    );
                },
            });
        });

        // Reset form when offcanvas closed
        $("#offcanvasTechnicianAssignDevice").on(
            "hidden.bs.offcanvas",
            function () {
                $("#technicianAssignDeviceForm")[0].reset();
                $("#selected-user-info").text("Belum ada pengguna dipilih");
                $("#initial-reading-wrapper-tech").hide();
                $("#assign_initial_meter_reading").prop("required", false);
            }
        );
    });
});
