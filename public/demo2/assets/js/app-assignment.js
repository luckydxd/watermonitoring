$(document).ready(function () {
    $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": csrfToken },
    });

    let columns, columnDefs;

    // Tentukan kolom berdasarkan role pengguna
    if (currentUserRole === "admin") {
        columns = [
            { data: "id", name: "id" }, // No
            { data: "assignable", name: "assignable" }, // Jenis & Detail
            { data: "status", name: "status" }, // Status
            { data: "assignable.user" },
            { data: "technician", name: "technician" }, // Teknisi
            { data: "created_at", name: "created_at" }, // Waktu
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            }, // Aksi
        ];
    } else {
        // teknisi
        columns = [
            { data: "id", name: "id" }, // No
            { data: "assignable", name: "assignable" }, // Jenis & Detail
            { data: "status", name: "status" }, // Status
            { data: "assignable.user" },
            { data: "notes", name: "notes" }, // Catatan Admin
            { data: "created_at", name: "created_at" }, // Waktu
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            }, // Aksi
        ];
    }

    columnDefs = [
        {
            // No
            targets: 0,
            render: (data, type, full, meta) => meta.row + 1,
        },
        {
            targets: 1, // Jenis & Detail Tugas
            render: (data, type, full) => {
                if (!full.assignable_type || !full.assignable) {
                    return '<span class="badge bg-label-danger">Error: Data Tugas Tidak Lengkap</span>';
                }

                let taskType = full.assignable_type.includes("Complaint")
                    ? "Keluhan"
                    : "Pemasangan";
                let taskTitle = full.assignable?.title || "Pemasangan Baru";

                // ================== AWAL PERUBAHAN ==================

                const titleLimit = 15; // Tentukan batas karakter di sini
                let displayTitle = taskTitle;

                // Cek jika judul lebih panjang dari batas
                if (taskTitle.length > titleLimit) {
                    // Potong judul dan tambahkan "..."
                    displayTitle = taskTitle.substring(0, titleLimit) + "...";
                }

                // Kita juga akan membatasi deskripsi untuk konsistensi
                let customerName =
                    full.assignable?.user?.user_data?.name ||
                    full.assignable?.customer_name ||
                    "N/A";
                let taskDescription =
                    full.assignable?.description ||
                    `Untuk pelanggan: ${customerName}`;
                let displayDescription = taskDescription;
                if (taskDescription.length > 15) {
                    displayDescription =
                        taskDescription.substring(0, 15) + "...";
                }

                // =================== AKHIR PERUBAHAN ===================

                return `
            <div class="d-flex flex-column">
                
                <span class="text-heading fw-medium" title="${taskTitle}">${displayTitle}</span>
                
                <small class="text-muted" title="${taskDescription}">
                    <span class="badge bg-label-dark me-1">${taskType}</span>
                    ${displayDescription}
                </small>
            </div>`;
            },
        },
        {
            // Status
            targets: 2,
            render: (data, type, full) => {
                let badgeClass = "";
                switch (data) {
                    case "in_progress":
                        badgeClass = "bg-label-info";
                        break;
                    case "completed":
                        badgeClass = "bg-label-success";
                        break;
                    case "cancelled":
                        badgeClass = "bg-label-danger";
                        break;
                    default:
                        badgeClass = "bg-label-secondary";
                }
                return `<span class="badge ${badgeClass}">${data.replace(
                    "_",
                    " "
                )}</span>`;
            },
        },
        {
            targets: 3, // Pelanggan
            orderable: false,
            render: (data, type, full) => {
                // ================== INI PERBAIKANNYA ==================
                // Kita sekarang langsung mengakses data dari 'full.assignable.user.userData'
                // karena backend sudah mengirimkannya dengan benar.
                const customerName =
                    full.assignable?.user?.user_data?.name || "N/A";
                const customerAddress =
                    full.assignable?.user?.user_data?.address || "N/A";
                // ======================================================

                return `
                <div class="d-flex flex-column">
                    <span class="text-heading fw-medium">${customerName}</span>
                    <small class="text-muted">${customerAddress}</small>
                </div>`;
            },
        },
        {
            // Waktu Dibuat
            targets: -2,
            render: (data) =>
                new Date(data).toLocaleDateString("id-ID", {
                    day: "2-digit",
                    month: "short",
                    year: "numeric",
                    hour: "2-digit",
                    minute: "2-digit",
                }),
        },
        {
            // Aksi
            targets: -1,
            render: (data, type, full) => {
                let buttons = "";
                if (
                    currentUserRole === "teknisi" &&
                    full.status === "in_progress"
                ) {
                    buttons += `<button class="btn btn-success btn-sm btn-complete-assignment" data-id="${full.id}" data-bs-toggle="modal" data-bs-target="#completeAssignmentModal" title="Selesaikan Tugas"><i class="ti ti-check"></i></button>`;
                }
                if (currentUserRole === "admin") {
                    buttons += `<button class="btn btn-secondary btn-sm" title="Lihat Detail"><i class="ti ti-eye"></i></button>`;
                }
                return `<div class="btn-list">${buttons}</div>`;
            },
        },
    ];

    // Inisialisasi DataTables
    const table = $("#assignments-datatable").DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: assignmentsUrl },
        columns: columns,
        columnDefs: columnDefs,
        // ... (konfigurasi bahasa, dom, dll dari file complaint.js Anda bisa disalin ke sini)
    });

    // Filter berdasarkan status
    $("#statusFilter").on("change", function () {
        table.column(2).search(this.value).draw();
    });

    // --- Event Listeners untuk Teknisi ---

    // Saat tombol "Selesaikan Tugas" diklik
    $(document).on("click", ".btn-complete-assignment", function () {
        $("#completionForm")[0].reset();
        $("#assignmentIdInput").val($(this).data("id"));
    });

    // Saat tombol "Kirim Laporan" di modal diklik
    $("#submitCompletionBtn").on("click", function () {
        const assignmentId = $("#assignmentIdInput").val();
        const completionNotes = $("#completionNotes").val();

        if (!completionNotes) {
            Notiflix.Notify.warning("Mohon isi catatan penyelesaian.");
            return;
        }

        const formData = new FormData($("#completionForm")[0]); // Ambil semua data form

        Notiflix.Loading.standard("Mengirim laporan...");

        $.ajax({
            url: `/api/complaints/assignments/${assignmentId}/complete`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                Notiflix.Loading.remove();
                $("#completeAssignmentModal").modal("hide");
                Notiflix.Notify.success(response.message);
                table.ajax.reload();
            },
            error: (xhr) => {
                Notiflix.Loading.remove();
                Notiflix.Notify.failure(
                    xhr.responseJSON
                        ? xhr.responseJSON.message
                        : "Terjadi kesalahan."
                );
            },
        });
    });
});
