/**
 * Page User List
 */
const userListUrl = document.getElementById("table-user").dataset.url;

("use strict");

// Datatable (jquery)
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

    // Variable declaration for table
    var dt_user_table = $(".datatables-users"),
        select2 = $(".select2"),
        userView = "app-user-view-account.html",
        statusObj = {
            1: { title: "Active", class: "bg-label-success" },
            0: { title: "Inactive", class: "bg-label-secondary" },
        };

    if (select2.length) {
        var $this = select2;
        $this.wrap('<div class="position-relative"></div>').select2({
            placeholder: "Select Country",
            dropdownParent: $this.parent(),
        });
    }
    // Users datatable
    if (dt_user_table.length) {
        var dt_user = dt_user_table.DataTable({
            ajax: userListUrl,
            // JSON file to add data
            columns: [
                { data: "id" },
                { data: "id" },
                { data: "name" },
                { data: "username" },
                { data: "role" },
                { data: "address" },
                { data: "phone_number" },
                { data: "isActive" },
                { data: "action" },
            ],

            columnDefs: [
                {
                    className: "control",
                    searchable: false,
                    orderable: false,
                    responsivePriority: 1,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return "";
                    },
                },
                {
                    // For Checkboxes
                    targets: 1,
                    orderable: false,
                    checkboxes: {
                        selectAllRender:
                            '<input type="checkbox" class="form-check-input">',
                    },
                    render: function () {
                        return '<input type="checkbox" class="dt-checkboxes form-check-input" >';
                    },
                    searchable: false,
                },
                {
                    // User full name and email
                    targets: 2,
                    responsivePriority: 1,
                    render: function (data, type, full, meta) {
                        var $name = full["name"],
                            $email = full["email"],
                            $image = full["image"];
                        if ($image) {
                            // For Avatar image
                            var $output =
                                '<img src="' +
                                assetsPath +
                                "img/avatars/" +
                                $image +
                                '" alt="Avatar" class="rounded-circle">';
                        } else {
                            // For Avatar badge
                            var stateNum = Math.floor(Math.random() * 6);
                            var states = [
                                "success",
                                "danger",
                                "warning",
                                "info",
                                "primary",
                                "secondary",
                            ];
                            var $state = states[stateNum],
                                $name = full["name"],
                                $initials = $name.match(/\b\w/g) || [];
                            $initials = (
                                ($initials.shift() || "") +
                                ($initials.pop() || "")
                            ).toUpperCase();
                            $output =
                                '<span class="avatar-initial rounded-circle bg-label-' +
                                $state +
                                '">' +
                                $initials +
                                "</span>";
                        }
                        // Creates full output for row
                        var $row_output =
                            '<div class="d-flex justify-content-start align-items-center user-name">' +
                            '<div class="avatar-wrapper">' +
                            '<div class="avatar avatar-sm me-4">' +
                            $output +
                            "</div>" +
                            "</div>" +
                            '<div class="d-flex flex-column">' +
                            '<a href="' +
                            userView +
                            '" class="text-heading text-truncate"><span class="fw-medium">' +
                            $name +
                            "</span></a>" +
                            "<small>" +
                            $email +
                            "</small>" +
                            "</div>" +
                            "</div>";
                        return $row_output;
                    },
                },
                {
                    // User Role
                    targets: 4,
                    responsivePriority: 2,
                    render: function (data, type, full, meta) {
                        var $role = full["role"];
                        var roleBadgeObj = {
                            user: '<i class="ti ti-crown ti-md text-primary me-2"></i>',
                            admin: '<i class="ti ti-device-desktop ti-md text-danger me-2"></i>',
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
                    // User Status
                    targets: 7,
                    responsivePriority: 2,
                    render: function (data, type, full, meta) {
                        var status = full["isActive"];

                        return statusObj[status]
                            ? `<span class="badge ${statusObj[status].class} text-capitalized">${statusObj[status].title}</span>`
                            : `<span class="badge bg-label-dark text-capitalized">Unknown</span>`;
                    },
                },
                {
                    // Actions
                    targets: -1,
                    title: "Actions",
                    searchable: false,
                    orderable: false,
                    render: function (data, type, full, meta) {
                        return (
                            '<div class="d-flex align-items-center">' +
                            `<a href="javascript:;" data-id="${full.id}" class="btn btn-icon btn-text-danger waves-effect waves-light rounded-pill delete-record"><i class="ti ti-trash ti-md"></i></a>` +
                            '<a href="' +
                            '<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect waves-light rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-md"></i></a>' +
                            '<div class="dropdown-menu dropdown-menu-end m-0">' +
                            '<a href="javascript:;" class="dropdown-item edit-record" >Edit</a>' +
                            '<a href="javascript:;" class="dropdown-item">Suspend</a>' +
                            "</div>" +
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
                ">t" +
                '<"row"' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                ">",
            language: {
                sLengthMenu: "_MENU_",
                search: "",
                searchPlaceholder: "Search User",
                paginate: {
                    next: '<i class="ti ti-chevron-right ti-sm"></i>',
                    previous: '<i class="ti ti-chevron-left ti-sm"></i>',
                },
            },
            // Buttons with Dropdown
            buttons: [
                {
                    extend: "collection",
                    className:
                        "btn btn-label-secondary dropdown-toggle mx-4 waves-effect waves-light",
                    text: '<i class="ti ti-upload me-2 ti-xs"></i>Export',
                    buttons: [
                        {
                            extend: "print",
                            text: '<i class="ti ti-printer me-2" ></i>Print',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5],
                                // prevent avatar to be print
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = "";
                                        $.each(el, function (index, item) {
                                            if (
                                                item.classList !== undefined &&
                                                item.classList.contains(
                                                    "user-name"
                                                )
                                            ) {
                                                result =
                                                    result +
                                                    item.lastChild.firstChild
                                                        .textContent;
                                            } else if (
                                                item.innerText === undefined
                                            ) {
                                                result =
                                                    result + item.textContent;
                                            } else
                                                result =
                                                    result + item.innerText;
                                        });
                                        return result;
                                    },
                                },
                            },
                            customize: function (win) {
                                //customize print view for dark
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
                            extend: "csv",
                            text: '<i class="ti ti-file-text me-2" ></i>Csv',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5],
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = "";
                                        $.each(el, function (index, item) {
                                            if (
                                                item.classList !== undefined &&
                                                item.classList.contains(
                                                    "user-name"
                                                )
                                            ) {
                                                result =
                                                    result +
                                                    item.lastChild.firstChild
                                                        .textContent;
                                            } else if (
                                                item.innerText === undefined
                                            ) {
                                                result =
                                                    result + item.textContent;
                                            } else
                                                result =
                                                    result + item.innerText;
                                        });
                                        return result;
                                    },
                                },
                            },
                        },
                        {
                            extend: "excel",
                            text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5],
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = "";
                                        $.each(el, function (index, item) {
                                            if (
                                                item.classList !== undefined &&
                                                item.classList.contains(
                                                    "user-name"
                                                )
                                            ) {
                                                result =
                                                    result +
                                                    item.lastChild.firstChild
                                                        .textContent;
                                            } else if (
                                                item.innerText === undefined
                                            ) {
                                                result =
                                                    result + item.textContent;
                                            } else
                                                result =
                                                    result + item.innerText;
                                        });
                                        return result;
                                    },
                                },
                            },
                        },
                        {
                            extend: "pdf",
                            text: '<i class="ti ti-file-code-2 me-2"></i>Pdf',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5],
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = "";
                                        $.each(el, function (index, item) {
                                            if (
                                                item.classList !== undefined &&
                                                item.classList.contains(
                                                    "user-name"
                                                )
                                            ) {
                                                result =
                                                    result +
                                                    item.lastChild.firstChild
                                                        .textContent;
                                            } else if (
                                                item.innerText === undefined
                                            ) {
                                                result =
                                                    result + item.textContent;
                                            } else
                                                result =
                                                    result + item.innerText;
                                        });
                                        return result;
                                    },
                                },
                            },
                        },
                        {
                            extend: "copy",
                            text: '<i class="ti ti-copy me-2" ></i>Copy',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5],
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = "";
                                        $.each(el, function (index, item) {
                                            if (
                                                item.classList !== undefined &&
                                                item.classList.contains(
                                                    "user-name"
                                                )
                                            ) {
                                                result =
                                                    result +
                                                    item.lastChild.firstChild
                                                        .textContent;
                                            } else if (
                                                item.innerText === undefined
                                            ) {
                                                result =
                                                    result + item.textContent;
                                            } else
                                                result =
                                                    result + item.innerText;
                                        });
                                        return result;
                                    },
                                },
                            },
                        },
                    ],
                },
                {
                    text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New User</span>',
                    className:
                        "add-new btn btn-primary waves-effect waves-light",
                    attr: {
                        "data-bs-toggle": "offcanvas",
                        "data-bs-target": "#offcanvasAddUser",
                    },
                },
            ],
            // For responsive popup
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + data["name"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.title !== "" // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                      col.rowIndex +
                                      '" data-dt-column="' +
                                      col.columnIndex +
                                      '">' +
                                      "<td>" +
                                      col.title +
                                      ":" +
                                      "</td> " +
                                      "<td>" +
                                      col.data +
                                      "</td>" +
                                      "</tr>"
                                : "";
                        }).join("");

                        return data
                            ? $('<table class="table"/><tbody />').append(data)
                            : false;
                    },
                },
            },
            initComplete: function () {
                // Adding role filter once table initialized
                this.api()
                    .columns(4)
                    .every(function () {
                        var column = this;
                        var select = $(
                            '<select id="UserRole" class="form-select text-capitalize"><option value=""> Select Role </option></select>'
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
    // Add New User
    $("#addNewUserForm").on("submit", function (e) {
        e.preventDefault();

        const form = $(this);
        const url = form.data("url"); // ambil dari data-url

        const formData = {
            username: $("#username").val(),
            email: $("#email").val(),
            password: $("#password").val(),
            name: $("#name").val(),
            address: $("#address").val(),
            phone_number: $("#phone_number").val(),
            role: $("#role").val(),
        };

        // Tampilkan loading indicator
        Notiflix.Loading.standard("Menambahkan user...");

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            success: function (response) {
                Notiflix.Loading.remove();

                // Notifikasi sukses
                Notiflix.Notify.success("User berhasil ditambahkan!", {
                    timeout: 3000,
                    showOnlyTheLastOne: true,
                });

                // Reset form
                form[0].reset();

                // Tutup offcanvas
                $("#offcanvasAddUser").offcanvas("hide");

                // Reload datatable
                dt_user.ajax.reload();
            },
            error: function (xhr) {
                Notiflix.Loading.remove();

                // Ambil pesan error jika ada
                const errorMessage =
                    xhr.responseJSON?.message || "Gagal menambahkan user!";

                // Notifikasi error lebih detail
                Notiflix.Notify.failure(errorMessage, {
                    timeout: 4000,
                    showOnlyTheLastOne: true,
                });

                console.error(xhr.responseText);
            },
        });
    });

    // Delete Record
    $(document).on("click", ".delete-record", function () {
        const userId = $(this).data("id");

        Notiflix.Confirm.show(
            "Delete User",
            "Are you sure you want to delete this user?",
            "Yes",
            "No",
            function okCb() {
                // Tampilkan loading indicator
                Notiflix.Loading.standard("Deleting user...");

                fetch(`/api/users/${userId}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        // Jika kamu pakai auth, tambahkan Authorization Bearer token
                        // 'Authorization': 'Bearer <token>'
                    },
                })
                    .then((res) => {
                        Notiflix.Loading.remove();

                        if (res.ok) {
                            Notiflix.Notify.success(
                                "User deleted successfully."
                            );
                            dt_user.ajax.reload(); // Reload datatable setelah delete
                        } else {
                            return res.json().then((data) => {
                                throw new Error(
                                    data.message || "Delete failed"
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
                // Aksi ketika user memilih No
                Notiflix.Notify.info("Delete canceled");
            },
            {
                width: "320px",
                borderRadius: "8px",
                // Anda bisa menambahkan opsi styling lainnya
            }
        );
    });
    // Delete Record
    $(".datatables-users tbody").on("click", ".delete-record", function () {
        dt_user.row($(this).parents("tr")).remove().draw();
    });

    // Filter form control to default size
    // ? setTimeout used for multilingual table initialization
    setTimeout(() => {
        $(".dataTables_filter .form-control").removeClass("form-control-sm");
        $(".dataTables_length .form-select").removeClass("form-select-sm");
    }, 300);
});

// // Validation & Phone mask
// (function () {
//     const phoneMaskList = document.querySelectorAll(".phone-mask"),
//         addNewUserForm = document.getElementById("addNewUserForm");

//     // Phone Number - Format Indonesia +62
//     if (phoneMaskList) {
//         phoneMaskList.forEach(function (phoneMask) {
//             new Cleave(phoneMask, {
//                 prefix: "+62",
//                 blocks: [3, 4, 4, 4],
//                 delimiters: [" ", "-", "-"],
//                 numericOnly: true,
//             });
//         });
//     }

//     // Add New User Form Validation
//     const fv = FormValidation.formValidation(addNewUserForm, {
//         fields: {
//             userFullname: {
//                 validators: {
//                     notEmpty: {
//                         message: "Please enter fullname",
//                     },
//                 },
//             },
//             userEmail: {
//                 validators: {
//                     notEmpty: {
//                         message: "Please enter your email",
//                     },
//                     emailAddress: {
//                         message: "The value is not a valid email address",
//                     },
//                 },
//             },
//             userContact: {
//                 validators: {
//                     notEmpty: {
//                         message: "Please enter your phone number",
//                     },
//                     regexp: {
//                         regexp: /^\+62\s?8[1-9][0-9]{1,2}-[0-9]{3,4}-[0-9]{3,4}$/,
//                         message:
//                             "Please enter a valid Indonesian phone number (e.g. +62 812-3456-7890)",
//                     },
//                 },
//             },
//         },
//         plugins: {
//             trigger: new FormValidation.plugins.Trigger(),
//             bootstrap5: new FormValidation.plugins.Bootstrap5({
//                 eleValidClass: "",
//                 rowSelector: function (field, ele) {
//                     return ".mb-6";
//                 },
//             }),
//             submitButton: new FormValidation.plugins.SubmitButton(),
//             autoFocus: new FormValidation.plugins.AutoFocus(),
//         },
//     });
// })();
