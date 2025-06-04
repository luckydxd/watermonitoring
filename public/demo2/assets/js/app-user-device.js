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
                render: function (data, type, full, meta) {
                    if (full.latitude && full.longitude) {
                        return `${full.latitude}, ${full.longitude}`;
                    }
                    return "-";
                },
            },
            {
                targets: 5,
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
            // {
            //     targets: -1,
            //     render: function (data, type, full, meta) {
            //         return `
            //             <div class="btn-list">
            //                 <button class="btn btn-info btn-view-device" data-id="${data}">
            //                     <i class="ti ti-eye"></i>
            //                 </button>
            //             </div>
            //         `;
            //     },
            // },
        ],
        columns: [
            { data: "id" },
            { data: "unique_id" },
            { data: "device_type.name" },
            { data: "status" },
            { data: null }, // Location (handled in render)
            { data: "created_at" },
            // { data: "id" },
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
    });

    // Handle view button click
    $(document).on("click", ".btn-view-device", function () {
        const deviceId = $(this).data("id");
        window.location.href = `/devices/${deviceId}`;
    });
});
