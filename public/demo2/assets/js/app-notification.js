// public/demo2/assets/js/app-notification.js
const NotificationApiUrl = document.getElementById("notifications-datatable")
    .dataset.url;

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    const currentUserRoles = window.currentUserRoles || [];

    let table = $("#notifications-datatable").DataTable({
        processing: true,
        serverSide: true,
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
        ajax: {
            url: NotificationApiUrl,
            data: function (d) {
                d.is_read_filter = $("#readStatusFilter").val();
            },
        },
        columnDefs: [
            {
                targets: 0,
                render: function (data, type, full, meta) {
                    return meta.row + 1;
                },
            },
            {
                targets: 1, // Kolom 'Tipe'
                data: "type", // Data mentah dari backend
                render: function (data, type, row) {
                    let badgeClass = "bg-label-secondary";
                    let statusText = "";
                    switch (data) {
                        case "complaint_created":
                            badgeClass = "bg-label-danger";
                            statusText = "Keluhan Baru";
                            break;
                        case "complaint_responded":
                            badgeClass = "bg-label-info";
                            statusText = "Respon Keluhan";
                            break;
                        case "general":
                            badgeClass = "bg-label-primary";
                            statusText = "Umum";
                            break;
                        default:
                            statusText = data
                                .replace(/_/g, " ")
                                .replace(/\b\w/g, (char) => char.toUpperCase()); // Format "snake_case" ke "Title Case"
                    }
                    return `<span class="badge ${badgeClass}">${statusText}</span>`;
                },
                name: "type", // For server-side sorting
            },
            {
                targets: 3, // Kolom 'Konten'
                data: "content", // Data mentah dari backend
                render: function (data, type, row) {
                    // Batasi konten di frontend
                    return data.length > 70
                        ? data.substring(0, 70) + "..."
                        : data;
                },
                name: "content", // For server-side sorting
            },
            {
                targets: 4, // Kolom 'Waktu' (created_at)
                data: "created_at", // Data mentah dari backend
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        if (!data) return "-";
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
                name: "created_at", // For server-side sorting
            },
        ],
        columns: [
            { data: "id" }, // Placeholder for numbering
            { data: "type" }, // Akan dirender oleh columnDefs targets:1
            { data: "title" },
            { data: "content" }, // Akan dirender oleh columnDefs targets:3
            { data: "created_at" }, // Akan dirender oleh columnDefs targets:4
        ],
        order: [[4, "desc"]],
        language: {
            sLengthMenu: "_MENU_",
            search: "",
            searchPlaceholder: "Cari...",
            paginate: {
                next: '<i class="ti ti-chevron-right ti-sm"></i>',
                previous: '<i class="ti ti-chevron-left ti-sm"></i>',
            },
        },
    });

    // Event handler for filter status baca
    $("#readStatusFilter").on("change", function () {
        table.ajax.reload();
    });

    // Event handler for 'Mark as Read' button within DataTables
    // $("#notifications-datatable").on("click", ".mark-as-read-btn", function () {
    //     const notificationId = $(this).data("id");
    //     $.post(`/api/notifications/${notificationId}/mark-as-read`)
    //         .done(function (response) {
    //             Notiflix.Notify.success(response.message);
    //             table.ajax.reload(null, false);
    //             $(document).trigger("notifications:updated");
    //         })
    //         .fail(function (xhr) {
    //             Notiflix.Notify.failure(
    //                 "Gagal menandai notifikasi sebagai sudah dibaca."
    //             );
    //             console.error("Error markAsRead:", xhr.responseText);
    //         });
    // });

    // Event handler for 'Delete' button within DataTables
    $("#notifications-datatable").on(
        "click",
        ".delete-notification-btn",
        function () {
            const notificationId = $(this).data("id");
            Notiflix.Confirm.show(
                "Konfirmasi Hapus",
                "Apakah Anda yakin ingin menghapus notifikasi ini?",
                "Ya",
                "Tidak",
                function okCb() {
                    $.ajax({
                        url: `/api/notifications/${notificationId}`,
                        type: "DELETE",
                        success: function (response) {
                            Notiflix.Notify.success(response.message);
                            table.ajax.reload(null, false);
                            $(document).trigger("notifications:updated");
                        },
                        error: function (xhr) {
                            Notiflix.Notify.failure(
                                "Gagal menghapus notifikasi."
                            );
                            console.error(
                                "Error deleteNotification:",
                                xhr.responseText
                            );
                        },
                    });
                }
            );
        }
    );

    // Event handler for 'Mark All As Read' button on the notification management page
    $("#markAllAsReadBtn").on("click", function () {
        Notiflix.Confirm.show(
            "Konfirmasi",
            "Apakah Anda yakin ingin menandai semua notifikasi sebagai sudah dibaca?",
            "Ya",
            "Tidak",
            function okCb() {
                $.post("/api/notifications/mark-all-as-read")
                    .done(function (response) {
                        Notiflix.Notify.success(response.message);
                        table.ajax.reload(null, false);
                        $(document).trigger("notifications:updated");
                    })
                    .fail(function (xhr) {
                        Notiflix.Notify.failure(
                            "Gagal menandai semua notifikasi sebagai sudah dibaca."
                        );
                        console.error("Error markAllAsRead:", xhr.responseText);
                    });
            }
        );
    });
});
