$(document).ready(function () {
    const currentUserRoles = window.currentUserRoles || [];

    // Helper function for timeAgo
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        let seconds = Math.floor((now - date) / 1000);

        let suffix = "lalu"; // Default suffix
        if (seconds < 0) {
            seconds = Math.abs(seconds); // Ambil nilai absolut jika negatif
            suffix = "yang lalu"; // Atau "di masa depan"
            // Seharusnya tidak terjadi untuk notifikasi aktual
            console.warn(
                "Notification timestamp is in the future:",
                dateString
            );
        }

        let interval = seconds / 31536000; // Tahun
        if (interval > 1) {
            return Math.floor(interval) + " tahun " + suffix;
        }
        interval = seconds / 2592000; // Bulan
        if (interval > 1) {
            return Math.floor(interval) + " bulan " + suffix;
        }
        interval = seconds / 86400; // Hari
        if (interval > 1) {
            return Math.floor(interval) + " hari " + suffix;
        }
        interval = seconds / 3600; // Jam
        if (interval > 1) {
            return Math.floor(interval) + " jam " + suffix;
        }
        interval = seconds / 60; // Menit
        if (interval > 1) {
            return Math.floor(interval) + " menit " + suffix;
        }
        return Math.floor(seconds) + " detik " + suffix;
    }

    // Fungsi untuk memuat dan memperbarui notifikasi di navbar
    function loadNavbarNotifications() {
        $.get("/api/notifications/unread-count")
            .done(function (data) {
                const unreadCount = data.unread_count;
                const badgeElement = $("#notification-badge-count");
                const newCountElement = $("#notification-new-count");

                if (unreadCount > 0) {
                    badgeElement.text(unreadCount).show();
                    newCountElement.text(`${unreadCount} Baru`).show();
                } else {
                    badgeElement.hide().text("");
                    newCountElement.hide().text("");
                }
            })
            .fail(function (xhr) {
                console.error(
                    "Error fetching unread notification count:",
                    xhr.responseText
                );
                // Optional: show an error to user if Notiflix is available
                // Notiflix.Report.failure('Gagal mengambil jumlah notifikasi.', 'Silakan coba refresh halaman.', 'OK');
            });

        $.get("/api/notifications/latest")
            .done(function (data) {
                const notificationList = $("#navbar-notification-list");
                notificationList.empty(); // Kosongkan daftar yang ada

                if (data.notifications.length > 0) {
                    data.notifications.forEach(function (notification) {
                        let iconClass = "ti-bell-ringing"; // Default icon
                        let avatarBgClass = "bg-label-primary"; // Default background
                        let linkHref = "#"; // Default link

                        // Sesuaikan ikon, warna, dan link berdasarkan tipe notifikasi
                        switch (notification.type) {
                            case "complaint_created":
                                iconClass = "ti-alert-circle";
                                avatarBgClass = "bg-label-danger";
                                if (notification.related_complaint_id) {
                                    if (currentUserRoles.includes("admin")) {
                                        linkHref = `/admin/complaint/`;
                                    } else if (
                                        currentUserRoles.includes("teknisi")
                                    ) {
                                        linkHref = `/teknisi/complaint/`;
                                    } else {
                                        linkHref = ``;
                                    }
                                }
                                break;
                            case "complaint_responded":
                                iconClass = "ti-alert-circle";
                                avatarBgClass = "bg-label-info";
                                // Untuk 'complaint_responded', biasanya terkait dengan complaint yang direspon
                                // Jadi link akan mengarah ke detail keluhan user yang membuat
                                if (notification.related_complaint_id) {
                                    linkHref = ``;
                                }
                                break;
                            case "general":
                                iconClass = "ti-info-circle";
                                avatarBgClass = "bg-label-success";
                                linkHref = "#"; // Atau arahkan ke dashboard
                                break;
                            default:
                                iconClass = "ti-bell-ringing";
                                avatarBgClass = "bg-label-secondary";
                        }

                        // Determine if notification is read to apply class and show/hide badge dot
                        const isReadClass = notification.is_read
                            ? "marked-as-read"
                            : "";
                        const badgeDot = notification.is_read
                            ? ""
                            : '<span class="badge badge-dot"></span>';

                        const notificationItem = `
                            <li class="list-group-item list-group-item-action dropdown-notifications-item waves-effect ${isReadClass}" data-id="${
                            notification.id
                        }">
                                <a href="${linkHref}" class="d-flex align-items-center text-decoration-none">
                                    <div class="me-3 flex-shrink-0">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded-circle ${avatarBgClass}"><i class="ti ${iconClass}"></i></span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small mb-1">${
                                            notification.title
                                        }</h6>
                                        <small class="d-block text-body mb-1">${
                                            notification.content
                                        }</small>
                                        <small class="text-muted">${timeAgo(
                                            notification.created_at
                                        )}</small>
                                    </div>
                                </a>
                                <div class="dropdown-notifications-actions flex-shrink-0">
                                    <a href="javascript:void(0)" class="dropdown-notifications-read">${badgeDot}</a>
                                    <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                                </div>
                            </li>
                        `;
                        notificationList.append(notificationItem);
                    });
                } else {
                    notificationList.append(`
                        <li class="list-group-item">
                            <div class="text-center text-muted py-3">Tidak ada notifikasi terbaru.</div>
                        </li>
                    `);
                }

                // Setelah list dimuat, inisialisasi Perfect Scrollbar (jika digunakan)
                const scrollableContainer = document
                    .querySelector("#navbar-notification-list")
                    ?.closest(".scrollable-container"); // Use optional chaining for safety
                if (
                    typeof PerfectScrollbar !== "undefined" &&
                    scrollableContainer
                ) {
                    if (
                        scrollableContainer._ps_ ||
                        scrollableContainer.classList.contains("ps--active-y")
                    ) {
                        PerfectScrollbar.from(scrollableContainer).update();
                    } else {
                        new PerfectScrollbar(scrollableContainer);
                    }
                }
            })
            .fail(function (xhr) {
                console.error(
                    "Error fetching latest notifications:",
                    xhr.responseText
                );
                // Optional: show an error to user if Notiflix is available
                // Notiflix.Report.failure('Gagal mengambil notifikasi terbaru.', 'Silakan coba refresh halaman.', 'OK');
            });
    }

    // Panggil fungsi saat dokumen siap untuk pertama kali
    loadNavbarNotifications();

    // Opsional: Perbarui notifikasi setiap beberapa detik
    // setInterval(loadNavbarNotifications, 60000); // Perbarui setiap 60 detik

    // --- EVENT LISTENER BARU: Tandai notifikasi sebagai dibaca saat link diklik ---
    $(document).on(
        "click",
        "a.d-flex.align-items-center.text-decoration-none",
        function (e) {
            const listItem = $(this).closest(".list-group-item");
            const notificationId = listItem.data("id"); // Ambil ID dari data-id attribute pada li

            if (notificationId) {
                // Send AJAX request to mark as read
                $.post(`/api/notifications/${notificationId}/mark-as-read`)
                    .done(function (response) {
                        console.log(
                            `Notification ${notificationId} marked as read:`,
                            response.message
                        );
                        // No need to preventDefault() here.
                        // The link will navigate while the AJAX runs in the background.
                        loadNavbarNotifications(); // Refresh notifications in navbar
                    })
                    .fail(function (xhr) {
                        console.error(
                            `Error marking notification ${notificationId} as read:`,
                            xhr.responseText
                        );
                        // Optionally show an error to the user
                        // Notiflix.Notify.failure("Gagal menandai notifikasi sebagai sudah dibaca.");
                    });
            }
            // Allow the default link behavior (navigation to href)
        }
    );
    // --- AKHIR EVENT LISTENER BARU ---

    // Event handler untuk tombol 'Mark all as read' di navbar
    $("#mark-all-navbar-read-btn").on("click", function () {
        // Pastikan Notiflix sudah diinisialisasi atau hapus baris ini jika tidak digunakan
        if (typeof Notiflix !== "undefined" && Notiflix.Confirm) {
            Notiflix.Confirm.show(
                "Konfirmasi",
                "Apakah Anda yakin ingin menandai semua notifikasi sebagai sudah dibaca?",
                "Ya",
                "Tidak",
                function okCb() {
                    $.post("/api/notifications/mark-all-as-read")
                        .done(function (response) {
                            if (
                                typeof Notiflix !== "undefined" &&
                                Notiflix.Notify
                            ) {
                                Notiflix.Notify.success(response.message);
                            } else {
                                console.log(response.message);
                            }
                            loadNavbarNotifications();
                            if (
                                $.fn.DataTable.isDataTable(
                                    "#notifications-datatable"
                                )
                            ) {
                                $("#notifications-datatable")
                                    .DataTable()
                                    .ajax.reload(null, false);
                            }
                        })
                        .fail(function (xhr) {
                            if (
                                typeof Notiflix !== "undefined" &&
                                Notiflix.Notify
                            ) {
                                Notiflix.Notify.failure(
                                    "Gagal menandai semua notifikasi sebagai sudah dibaca."
                                );
                            } else {
                                console.error(
                                    "Error markAllAsRead navbar:",
                                    xhr.responseText
                                );
                            }
                        });
                }
            );
        } else {
            // Fallback jika Notiflix tidak ada
            if (
                confirm(
                    "Apakah Anda yakin ingin menandai semua notifikasi sebagai sudah dibaca?"
                )
            ) {
                $.post("/api/notifications/mark-all-as-read")
                    .done(function (response) {
                        console.log(response.message);
                        loadNavbarNotifications();
                        if (
                            $.fn.DataTable.isDataTable(
                                "#notifications-datatable"
                            )
                        ) {
                            $("#notifications-datatable")
                                .DataTable()
                                .ajax.reload(null, false);
                        }
                    })
                    .fail(function (xhr) {
                        console.error(
                            "Error markAllAsRead navbar:",
                            xhr.responseText
                        );
                    });
            }
        }
    });

    // Event delegation for marking individual notification as read from dropdown (badge dot click)
    // This event handler remains for the dot badge specifically
    $("#navbar-notification-list").on(
        "click",
        ".dropdown-notifications-read", // This targets the badge dot link
        function (e) {
            e.preventDefault(); // Prevent default navigation if this element is a link
            e.stopPropagation(); // Stop propagation to prevent the parent link from being clicked

            const listItem = $(this).closest(".list-group-item");
            const notificationId = listItem.data("id");
            if (!notificationId) return;

            $.post(`/api/notifications/${notificationId}/mark-as-read`)
                .done(function (response) {
                    if (typeof Notiflix !== "undefined" && Notiflix.Notify) {
                        Notiflix.Notify.success(response.message);
                    } else {
                        console.log(response.message);
                    }
                    listItem.addClass("marked-as-read");
                    listItem.find(".badge-dot").remove(); // Remove dot badge
                    loadNavbarNotifications(); // Update unread count
                    if (
                        $.fn.DataTable.isDataTable("#notifications-datatable")
                    ) {
                        $("#notifications-datatable")
                            .DataTable()
                            .ajax.reload(null, false);
                    }
                })
                .fail(function (xhr) {
                    if (typeof Notiflix !== "undefined" && Notiflix.Notify) {
                        Notiflix.Notify.failure(
                            "Gagal menandai notifikasi sebagai sudah dibaca."
                        );
                    } else {
                        console.error(
                            "Error markAsRead dropdown:",
                            xhr.responseText
                        );
                    }
                });
        }
    );

    // Event delegation for archiving (removing from dropdown) individual notification
    $("#navbar-notification-list").on(
        "click",
        ".dropdown-notifications-archive",
        function (e) {
            e.preventDefault();
            e.stopPropagation(); // Mencegah klik menyebar ke link parent
            const listItem = $(this).closest(".list-group-item");
            const notificationId = listItem.data("id");
            if (!notificationId) return;

            if (typeof Notiflix !== "undefined" && Notiflix.Confirm) {
                Notiflix.Confirm.show(
                    "Konfirmasi Hapus",
                    "Apakah Anda yakin ingin menghapus notifikasi ini dari daftar?",
                    "Ya",
                    "Tidak",
                    function okCb() {
                        $.ajax({
                            url: `/api/notifications/${notificationId}`,
                            type: "DELETE",
                            success: function (response) {
                                if (
                                    typeof Notiflix !== "undefined" &&
                                    Notiflix.Notify
                                ) {
                                    Notiflix.Notify.success(response.message);
                                } else {
                                    console.log(response.message);
                                }
                                listItem.remove();
                                loadNavbarNotifications();
                                if (
                                    $.fn.DataTable.isDataTable(
                                        "#notifications-datatable"
                                    )
                                ) {
                                    $("#notifications-datatable")
                                        .DataTable()
                                        .ajax.reload(null, false);
                                }
                            },
                            error: function (xhr) {
                                if (
                                    typeof Notiflix !== "undefined" &&
                                    Notiflix.Notify
                                ) {
                                    Notiflix.Notify.failure(
                                        "Gagal menghapus notifikasi."
                                    );
                                } else {
                                    console.error(
                                        "Error deleteNotification dropdown:",
                                        xhr.responseText
                                    );
                                }
                            },
                        });
                    }
                );
            }
        }
    );

    // Mendengarkan custom event dari app-notification.js untuk memperbarui navbar
    $(document).on("notifications:updated", function () {
        loadNavbarNotifications();
    });
});
