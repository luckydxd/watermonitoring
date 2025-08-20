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

    const chartColors = {
        area: {
            series1: "#2196f3",
            series2: "#64b5f6",
            series3: "#90caf9",
            series4: "#e3f2fd",
        },
    };

    // ===================================================================================
    // 2. FUNGSI HELPER UNTUK VALIDASI DATA
    // ===================================================================================
    function validateChartData(data) {
        if (!data || typeof data !== "object") {
            console.error("Chart data is invalid or undefined");
            return false;
        }

        if (!data.series || typeof data.series !== "object") {
            console.error("Chart series data is missing");
            return false;
        }

        if (!data.dates || !Array.isArray(data.dates)) {
            console.error("Chart dates data is missing or invalid");
            return false;
        }

        return true;
    }

    function getDefaultData() {
        return {
            dates: ["Tidak ada data"],
            series: {
                total_consumption: [0],
                average_consumption: [0],
                average_pressure: [0],
                average_flow_rate: [0],
            },
        };
    }

    // ===================================================================================
    // 3. INISIALISASI CHART: LINE AREA CHART (4 METRIK)
    // ===================================================================================
    const areaChartEl = document.querySelector("#lineAreaChart");
    if (areaChartEl) {
        let chartData;

        try {
            const rawData = areaChartEl.dataset.chart;
            if (!rawData) {
                throw new Error("No chart data found in dataset");
            }

            chartData = JSON.parse(rawData);

            if (!validateChartData(chartData)) {
                chartData = getDefaultData();
            }
        } catch (error) {
            console.error("Error parsing chart data:", error);
            chartData = getDefaultData();
        }

        // Pastikan semua array data ada dan tidak undefined
        const safeData = {
            total_consumption: chartData.series.total_consumption || [],
            average_consumption: chartData.series.average_consumption || [],
            average_pressure: chartData.series.average_pressure || [],
            average_flow_rate: chartData.series.average_flow_rate || [],
        };

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
            colors: [
                chartColors.area.series1,
                chartColors.area.series2,
                chartColors.area.series3,
                chartColors.area.series4,
            ],
            // PERBAIKAN: Pastikan urutan series konsisten dengan controller
            series: [
                {
                    name: "Total Konsumsi",
                    data: safeData.total_consumption,
                },
                {
                    name: "Rata-Rata Konsumsi",
                    data: safeData.average_consumption,
                },
                {
                    name: "Rata-Rata Tekanan",
                    data: safeData.average_pressure,
                },
                {
                    name: "Rata-Rata Aliran",
                    data: safeData.average_flow_rate,
                },
            ],
            xaxis: {
                categories: chartData.dates || ["Tidak ada data"],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: labelColor, fontSize: "13px" },
                    formatter: function (value) {
                        if (!value || value === "Tidak ada data") return value;
                        try {
                            return new Date(value).toLocaleDateString("id-ID", {
                                day: "numeric",
                                month: "short",
                            });
                        } catch (error) {
                            return value;
                        }
                    },
                },
            },
            yaxis: {
                labels: {
                    style: { colors: labelColor, fontSize: "13px" },
                },
            },
            tooltip: {
                shared: false,
                y: {
                    formatter: function (value, { seriesIndex }) {
                        const units = [
                            " Liter", // Total Konsumsi
                            " L/Pengguna", // Rata-rata Konsumsi
                            " Bar", // Rata-rata Tekanan
                            " L/min", // Rata-rata Aliran
                        ];
                        const unit = units[seriesIndex] || "";
                        return Math.round(value * 100) / 100 + unit;
                    },
                },
            },
        };

        try {
            areaChart = new ApexCharts(areaChartEl, areaChartConfig);
            areaChart.render();
        } catch (error) {
            console.error("Error rendering chart:", error);
        }

        // ===================================================================================
        // PERBAIKAN FILTER - Ganti bagian event handler dropdown ini saja
        // ===================================================================================

        const dateFilterDropdown = document.querySelector(
            "#dateFilterDropdown"
        );
        if (dateFilterDropdown && areaChart) {
            const apiUrl = dateFilterDropdown.dataset.url;

            dateFilterDropdown.addEventListener("click", function (e) {
                e.preventDefault();

                if (e.target.classList.contains("dropdown-item")) {
                    const selectedRange = e.target.dataset.range;

                    console.log("Filter selected:", selectedRange); // Debug log

                    $.ajax({
                        url: apiUrl,
                        type: "GET",
                        data: { range: selectedRange },
                        dataType: "json",
                        success: function (response) {
                            console.log("Response received:", response); // Debug log

                            // PERBAIKAN UTAMA: Gunakan updateSeries() DAN updateOptions() secara terpisah

                            // 1. Update categories (x-axis) terlebih dahulu
                            areaChart.updateOptions(
                                {
                                    xaxis: {
                                        categories: response.dates || [
                                            "Tidak ada data",
                                        ],
                                        axisBorder: { show: false },
                                        axisTicks: { show: false },
                                        labels: {
                                            style: {
                                                colors: labelColor,
                                                fontSize: "13px",
                                            },
                                            formatter: function (value) {
                                                if (
                                                    !value ||
                                                    value === "Tidak ada data"
                                                )
                                                    return value;
                                                try {
                                                    return new Date(
                                                        value
                                                    ).toLocaleDateString(
                                                        "id-ID",
                                                        {
                                                            day: "numeric",
                                                            month: "short",
                                                        }
                                                    );
                                                } catch (error) {
                                                    return value;
                                                }
                                            },
                                        },
                                    },
                                },
                                false,
                                false
                            ); // Redraw false, animation false

                            // 2. Kemudian update series data
                            const newSeriesData = [
                                {
                                    name: "Total Konsumsi",
                                    data: response.series.total_consumption || [
                                        0,
                                    ],
                                },
                                {
                                    name: "Rata-Rata Konsumsi",
                                    data: response.series
                                        .average_consumption || [0],
                                },
                                {
                                    name: "Rata-Rata Tekanan",
                                    data: response.series.average_pressure || [
                                        0,
                                    ],
                                },
                                {
                                    name: "Rata-Rata Aliran",
                                    data: response.series.average_flow_rate || [
                                        0,
                                    ],
                                },
                            ];

                            areaChart.updateSeries(newSeriesData, true); // Animate true

                            console.log("Chart updated successfully"); // Debug log
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", error);
                            console.error("Status:", status);
                            console.error("Response:", xhr.responseText);

                            // Fallback jika error
                            alert("Gagal memuat data. Silakan coba lagi.");
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
