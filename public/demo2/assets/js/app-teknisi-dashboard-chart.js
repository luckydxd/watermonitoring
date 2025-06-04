"use strict";

(function () {
    let cardColor, headingColor, labelColor, borderColor, legendColor;
    let waterUsageChart;

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

    // Water usage chart colors
    const waterUsageColors = {
        series1: "#00cfe8", // Teal for main consumption
        series2: "#ff9f43", // Orange for comparison/alert
        series3: "#28c76f", // Green for target
        series4: "#ea5455", // Red for threshold
    };

    const waterUsageChartEl = document.querySelector("#waterUsageChart");

    if (waterUsageChartEl) {
        // Get initial data from data attribute
        const initialData = JSON.parse(waterUsageChartEl.dataset.chart);

        const waterUsageConfig = {
            chart: {
                height: 400,
                type: "area",
                parentHeightOffset: 0,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true,
                    },
                },
                zoom: {
                    enabled: true,
                    type: "x",
                    autoScaleYaxis: true,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: 2,
                curve: "smooth",
            },
            legend: {
                show: true,
                position: "top",
                horizontalAlign: "left",
                labels: {
                    colors: legendColor,
                    useSeriesColors: false,
                },
                markers: {
                    radius: 0,
                    offsetX: -5,
                },
            },
            grid: {
                borderColor: borderColor,
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true,
                    },
                },
                yaxis: {
                    lines: {
                        show: true,
                    },
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0,
                },
            },
            colors: [
                waterUsageColors.series1,
                waterUsageColors.series2,
                waterUsageColors.series3,
            ],
            series: [
                {
                    name: "Water Consumption (Liters)",
                    data: initialData.consumption,
                },
                {
                    name: "Average Consumption",
                    data: initialData.average,
                },
                {
                    name: "Threshold Alert",
                    data: initialData.threshold,
                },
            ],
            xaxis: {
                type: "datetime",
                categories: initialData.dates,
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
                    datetimeFormatter: {
                        year: "yyyy",
                        month: "MMM 'yy",
                        day: "dd MMM",
                        hour: "HH:mm",
                    },
                },
                tooltip: {
                    enabled: false,
                },
            },
            yaxis: {
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: "13px",
                    },
                    formatter: function (value) {
                        return value + " L";
                    },
                },
                title: {
                    text: "Liters",
                    style: {
                        color: labelColor,
                        fontSize: "13px",
                    },
                },
            },
            fill: {
                type: "gradient",
                gradient: {
                    shade: "dark",
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: [waterUsageColors.series1],
                    inverseColors: true,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 100],
                },
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (value) {
                        return value + " Liters";
                    },
                },
                x: {
                    format: "dd MMM yyyy HH:mm",
                },
            },
            markers: {
                size: 5,
                strokeWidth: 0,
                hover: {
                    size: 7,
                },
            },
        };

        waterUsageChart = new ApexCharts(waterUsageChartEl, waterUsageConfig);
        waterUsageChart.render();

        // Time period selector functionality
        document.querySelectorAll(".time-period-btn").forEach((btn) => {
            btn.addEventListener("click", function () {
                const period = this.dataset.period;
                fetchWaterUsageData(period);
            });
        });

        async function fetchWaterUsageData(period) {
            try {
                const response = await fetch(
                    `/api/teknisi/water-usage?period=${period}`
                );
                const data = await response.json();

                waterUsageChart.updateOptions({
                    series: [
                        { data: data.consumption },
                        { data: data.average },
                        { data: data.threshold },
                    ],
                    xaxis: {
                        categories: data.dates,
                    },
                });
            } catch (error) {
                console.error("Error fetching water usage data:", error);
            }
        }
    }
})();

document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi variabel chart
    let donutChart2;

    // Warna chart
    const chartColors = {
        device: {
            active: "#28A745",
            inactive: "#6C757D",
            error: "#FFC107",
        },
    };

    // Fungsi inisialisasi donut chart
    function initDonutCharts() {
        // Ambil data dari atribut HTML

        const deviceData = JSON.parse(
            document.getElementById("donutChart2").dataset.chart
        );

        const totalDevices = Object.values(deviceData).reduce(
            (a, b) => a + b,
            0
        );

        // Konfigurasi chart perangkat
        const deviceOptions = {
            series: Object.values(deviceData),
            chart: {
                type: "donut",
                height: 350,
            },
            labels: ["Active", "Inactive", "Error"],
            colors: [
                chartColors.device.active,
                chartColors.device.inactive,
                chartColors.device.error,
            ],
            legend: {
                position: "bottom",
            },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: "Total",
                                formatter: () => totalDevices,
                            },
                        },
                    },
                },
            },
            responsive: [
                {
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200,
                        },
                        legend: {
                            position: "bottom",
                        },
                    },
                },
            ],
        };

        // Inisialisasi chart

        donutChart2 = new ApexCharts(
            document.querySelector("#donutChart2"),
            deviceOptions
        );
        donutChart2.render();
    }

    // Panggil fungsi inisialisasi
    initDonutCharts();

    // Handler untuk filter periode
    document.querySelectorAll(".period-filter").forEach((item) => {
        item.addEventListener("click", function () {
            const period = this.dataset.period;
            updateComplaintStats(period);
        });
    });

    // Fungsi untuk update data keluhan berdasarkan periode
    async function updateComplaintStats(period) {
        try {
            const response = await fetch(
                `/api/teknisi/complaint-stats?period=${period}`
            );
            const data = await response.json();

            // Update chart keluhan
            donutChart1.updateSeries([
                data.pending || 0,
                data.processed || 0,
                data.resolved || 0,
                data.rejected || 0,
            ]);

            // Update total di label
            const total = Object.values(data).reduce((a, b) => a + b, 0);
            donutChart1.updateOptions({
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                total: {
                                    formatter: () => total,
                                },
                            },
                        },
                    },
                },
            });
        } catch (error) {
            console.error("Error fetching complaint stats:", error);
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi variabel chart
    let complaintBarChart;

    // Define labelColor and borderColor based on theme
    let labelColor, borderColor;
    if (typeof isDarkStyle !== "undefined" && isDarkStyle) {
        labelColor = config.colors_dark.textMuted;
        borderColor = config.colors_dark.borderColor;
    } else {
        labelColor = config.colors.textMuted;
        borderColor = config.colors.borderColor;
    }

    // Warna chart
    const chartColors = {
        bar: {
            bg: "#1D9FF2",
            hover: "#0d8ae3",
        },
    };

    // Fungsi inisialisasi bar chart
    function initComplaintBarChart() {
        const chartEl = document.getElementById("complaintBarChart");
        if (!chartEl) return;

        const initialData = JSON.parse(chartEl.dataset.chart);

        // Update total complaints
        document.getElementById(
            "complaint-total"
        ).textContent = `${initialData.total} Keluhan`;

        const options = {
            chart: {
                height: 330,
                type: "bar",
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true,
                    },
                },
                events: {
                    dataPointSelection: function (event, chartContext, config) {
                        // Handle click on bar if needed
                    },
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "70%",
                    endingShape: "rounded",
                    borderRadius: 6,
                    distributed: false,
                    dataLabels: {
                        position: "top",
                    },
                },
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val;
                },
                offsetY: -20,
                style: {
                    fontSize: "12px",
                    colors: ["#6c757d"],
                },
            },
            colors: [chartColors.bar.bg],
            series: [
                {
                    name: "Complaints",
                    data: initialData.counts,
                },
            ],
            xaxis: {
                categories: initialData.dates,
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
                    rotate:
                        initialData.period === "today" ||
                        initialData.period === "yesterday"
                            ? 0
                            : -45,
                },
                tooltip: {
                    enabled: false,
                },
            },
            yaxis: {
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: "13px",
                    },
                    formatter: function (val) {
                        return Math.round(val);
                    },
                },
                title: {
                    text: "Jumlah Keluhan",
                    style: {
                        color: labelColor,
                        fontSize: "13px",
                    },
                },
            },
            grid: {
                borderColor: borderColor,
                strokeDashArray: 7,
                xaxis: {
                    lines: {
                        show: false,
                    },
                },
                yaxis: {
                    lines: {
                        show: true,
                    },
                },
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " complaints";
                    },
                },
            },
            states: {
                hover: {
                    filter: {
                        type: "darken",
                        value: 0.8,
                    },
                },
            },
        };

        complaintBarChart = new ApexCharts(chartEl, options);
        complaintBarChart.render();
    }

    // Panggil fungsi inisialisasi
    initComplaintBarChart();

    // Handler untuk filter periode
    document.querySelectorAll(".period-filter").forEach((item) => {
        item.addEventListener("click", function () {
            const period = this.dataset.period;
            updateComplaintBarChart(period);
        });
    });

    // Fungsi untuk update data berdasarkan periode
    async function updateComplaintBarChart(period) {
        try {
            const response = await fetch(
                `/api/complaint-bar-data?period=${period}`
            );
            const data = await response.json();

            // Update chart
            complaintBarChart.updateOptions({
                series: [
                    {
                        data: data.counts,
                    },
                ],
                xaxis: {
                    categories: data.dates,
                    labels: {
                        rotate:
                            period === "today" || period === "yesterday"
                                ? 0
                                : -45,
                    },
                },
            });

            // Update total
            document.getElementById(
                "complaint-total"
            ).textContent = `${data.total} Complaints`;
        } catch (error) {
            console.error("Error fetching complaint bar data:", error);
        }
    }
});
