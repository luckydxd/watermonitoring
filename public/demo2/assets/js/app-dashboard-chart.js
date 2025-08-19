"use strict";

(function () {
    // ===================================================================================
    // 1. DEKLARASI VARIABEL BERSAMA
    // ===================================================================================
    let cardColor, headingColor, labelColor, borderColor, legendColor;
    let areaChart;

    if (isDarkStyle) {
        cardColor = config.colors_dark.cardColor;
        headingColor = config.colors_dark.headingColor;
        labelColor = config.colors_dark.textMuted;
        legendColor = config.colors_dark.bodyColor;
        borderColor = config.colors_dark.borderColor;
    } else {
        cardColor = config.colors.cardColor;
        headingColor = config.colors.headingColor;
        labelColor = config.colors.textMuted;
        legendColor = config.colors.bodyColor;
        borderColor = config.colors.borderColor;
    }

    // DIUBAH: Tambahkan semua warna yang dibutuhkan
    const chartColors = {
        area: {
            series1: "#2196f3",
            series2: "#64b5f6",
            series3: "#90caf9",
            series4: "#e3f2fd",
        },
    };

    // ===================================================================================
    // 2. INISIALISASI CHART: LINE AREA CHART (4 METRIK)
    // ===================================================================================
    const areaChartEl = document.querySelector("#lineAreaChart");
    if (areaChartEl) {
        const initialChartData = JSON.parse(areaChartEl.dataset.chart);
        const areaChartConfig = {
            chart: {
                height: 400,
                type: "area",
                parentHeightOffset: 0,
                toolbar: { show: false },
                zoom: { enabled: false },
            },
            dataLabels: { enabled: false },
            stroke: { show: false, curve: "straight" },
            legend: {
                show: true,
                position: "top",
                horizontalAlign: "start",
                labels: { colors: legendColor, useSeriesColors: false },
            },
            grid: {
                borderColor: borderColor,
                xaxis: { lines: { show: true } },
            },
            fill: { opacity: 1, type: "solid" },

            // DIUBAH: Gunakan 4 warna
            colors: [
                chartColors.area.series1,
                chartColors.area.series2,
                chartColors.area.series3,
                chartColors.area.series4,
            ],
            // DIUBAH: Definisikan 4 series data
            series: [
                {
                    name: "Total Konsumsi",
                    data: initialChartData.series.total_consumption,
                },
                {
                    name: "Rata-Rata Konsumsi",
                    data: initialChartData.series.average_consumption,
                },

                {
                    name: "Rata-Rata Aliran",
                    data: initialChartData.series.average_flow_rate,
                },
                {
                    name: "Rata-Rata Tekanan",
                    data: initialChartData.series.average_pressure,
                },
            ],
            xaxis: {
                categories: initialChartData.dates,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: labelColor, fontSize: "13px" },
                    formatter: function (value) {
                        if (!value) return "";
                        return new Date(value).toLocaleDateString("id-ID", {
                            day: "numeric",
                            month: "short",
                        });
                    },
                },
            },
            // DIUBAH: Y-Axis tidak menampilkan unit karena unitnya berbeda-beda
            yaxis: {
                labels: {
                    style: { colors: labelColor, fontSize: "13px" },
                },
            },
            // DIUBAH: Tooltip cerdas yang menampilkan unit berbeda per series
            tooltip: {
                shared: false,
                y: {
                    formatter: function (value, { seriesIndex }) {
                        const units = [
                            " Liter",
                            " L/Pengguna",
                            " Bar",
                            " L/min",
                        ];
                        // Ambil unit yang sesuai berdasarkan indeks series
                        const unit = units[seriesIndex] || "";
                        return Math.round(value * 100) / 100 + unit; // Pembulatan 2 desimal
                    },
                },
            },
        };

        areaChart = new ApexCharts(areaChartEl, areaChartConfig);
        areaChart.render();

        const dateFilterDropdown = document.querySelector(
            "#dateFilterDropdown"
        );
        if (dateFilterDropdown) {
            const apiUrl = dateFilterDropdown.dataset.url;
            dateFilterDropdown.addEventListener("click", function (e) {
                e.preventDefault();
                if (e.target.classList.contains("dropdown-item")) {
                    const selectedRange = e.target.dataset.range;
                    $.ajax({
                        url: apiUrl,
                        type: "GET",
                        data: { range: selectedRange },
                        success: function (response) {
                            areaChart.updateOptions({
                                xaxis: { categories: response.dates },
                                // Update semua 4 series
                                series: [
                                    { data: response.series.total_consumption },
                                    {
                                        data: response.series
                                            .average_consumption,
                                    },
                                    { data: response.series.average_pressure },
                                    { data: response.series.average_flow_rate },
                                ],
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error("Gagal mengambil data chart:", error);
                        },
                    });
                }
            });
        }
    }

    const horizontalBarChartEl = document.querySelector("#horizontalBarChart");
    if (horizontalBarChartEl) {
        const horizontalBarChartConfig = {
            chart: {
                height: 400,
                type: "bar",
                toolbar: {
                    show: false,
                },
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: "30%",
                    startingShape: "rounded",
                    borderRadius: 8,
                },
            },
            grid: {
                borderColor: borderColor,
                xaxis: {
                    lines: {
                        show: false,
                    },
                },
                padding: {
                    top: -20,
                    bottom: -12,
                },
            },
            colors: chartColors.bar.bg,
            dataLabels: {
                enabled: false,
            },
            series: [
                {
                    data: [700, 350, 480, 600, 210, 550, 150],
                },
            ],
            xaxis: {
                categories: [
                    "MON, 11",
                    "THU, 14",
                    "FRI, 15",
                    "MON, 18",
                    "WED, 20",
                    "FRI, 21",
                    "MON, 23",
                ],
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: "13px",
                    },
                },
            },
            yaxis: {
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: "13px",
                    },
                },
            },
        };
        const horizontalBarChart = new ApexCharts(
            horizontalBarChartEl,
            horizontalBarChartConfig
        );
        horizontalBarChart.render();
    }

    // Donut Chart
    // --------------------------------------------------------------------

    function initDonutChart(elementId, labels, seriesData, colors) {
        const el = document.getElementById(elementId);
        if (!el) return;

        const validSeriesData = seriesData.map((val) => Number(val) || 0);
        const total = validSeriesData.reduce((a, b) => a + b, 0);

        const options = {
            chart: { type: "donut", height: 350 },
            series: validSeriesData,
            labels: labels,
            colors: colors,
            legend: {
                position: "bottom",
                labels: { colors: legendColor, useSeriesColors: false },
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return Math.round(val) + "%";
                },
            },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: "Total",
                                color: headingColor,
                                formatter: () => total,
                            },
                        },
                    },
                },
            },
        };
        const chart = new ApexCharts(el, options);
        chart.render();
    }

    // Inisialisasi Donut Chart 1 (Status Keluhan)
    const complaintDataEl = document.getElementById("donutChart1");
    if (complaintDataEl) {
        const complaintStatusData = JSON.parse(complaintDataEl.dataset.chart);
        initDonutChart(
            "donutChart1",
            ["Tertunda", "Diproses", "Selesai", "Ditolak"],
            [
                complaintStatusData.pending,
                complaintStatusData.processed,
                complaintStatusData.resolved,
                complaintStatusData.rejected,
            ],
            ["#FFC107", "#17A2B8", "#28A745", "#DC3545"]
        );
    }

    // Inisialisasi Donut Chart 2 (Status Perangkat)
    const deviceDataEl = document.getElementById("donutChart2");
    if (deviceDataEl) {
        const deviceStatusData = JSON.parse(deviceDataEl.dataset.chart);
        initDonutChart(
            "donutChart2",
            ["Active", "Inactive"],
            [deviceStatusData.active, deviceStatusData.inactive],
            ["#28A745", "#6C757D"]
        );
    }
})();
