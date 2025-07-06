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

    const waterUsageColors = {
        series1: "#00cfe8",
        series2: "#ff9f43",
        series3: "#28c76f",
    };

    const waterUsageChartEl = document.querySelector("#waterUsageChart");

    if (waterUsageChartEl) {
        const initialData = JSON.parse(waterUsageChartEl.dataset.chart);

        if (initialData && initialData.dates && initialData.dates.length > 0) {
            const waterUsageConfig = {
                chart: {
                    height: 400,
                    type: "area",
                    parentHeightOffset: 0,
                    toolbar: {
                        show: true,
                        tools: {
                            download: false,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true,
                        },
                    },
                    zoom: { enabled: true, type: "x", autoScaleYaxis: true },
                },
                dataLabels: { enabled: false },
                stroke: { width: 2, curve: "smooth" },
                legend: {
                    show: true,
                    position: "top",
                    horizontalAlign: "left",
                    labels: { colors: legendColor, useSeriesColors: false },
                    markers: { radius: 0, offsetX: -5 },
                },
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 5,
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: true } },
                    padding: { top: 0, right: 0, bottom: 0, left: 0 },
                },
                colors: [
                    waterUsageColors.series1,
                    waterUsageColors.series2,
                    waterUsageColors.series3,
                ],
                series: [
                    {
                        name: "Penggunaan Air (Liter)",
                        data: initialData.consumption,
                    },
                ],
                xaxis: {
                    type: "datetime",
                    categories: initialData.dates,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: labelColor, fontSize: "13px" },
                        datetimeFormatter: {
                            year: "yyyy",
                            month: "MMM 'yy",
                            day: "dd MMM",
                            hour: "HH:mm",
                        },
                    },
                    tooltip: { enabled: false },
                },
                yaxis: {
                    labels: {
                        style: { colors: labelColor, fontSize: "13px" },
                        formatter: function (value) {
                            if (typeof value === "number") {
                                // Bulatkan menjadi bilangan bulat (0 angka di belakang koma)
                                return value.toFixed(0) + " L";
                            }
                            return value;
                        },
                    },
                    title: {
                        text: "Liter",
                        style: { color: labelColor, fontSize: "13px" },
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
                    x: { format: "dd MMM yyyy" },
                },
                markers: {
                    size: 5,
                    strokeWidth: 0,
                    hover: { size: 7 },
                },
            };

            waterUsageChart = new ApexCharts(
                waterUsageChartEl,
                waterUsageConfig
            );
            waterUsageChart.render();
        } else {
            waterUsageChartEl.innerHTML =
                '<div class="d-flex justify-content-center align-items-center h-100">Tidak ada data penggunaan untuk ditampilkan pada periode ini.</div>';
        }

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

                if (!waterUsageChart) {
                }

                if (waterUsageChart) {
                    waterUsageChart.updateOptions({
                        series: [{ data: data.consumption }],
                        xaxis: { categories: data.dates },
                    });
                } else if (data.dates && data.dates.length > 0) {
                    waterUsageChartEl.innerHTML = "";
                    const newConfig = {};
                    newConfig.series[0].data = data.consumption;
                    newConfig.xaxis.categories = data.dates;
                    waterUsageChart = new ApexCharts(
                        waterUsageChartEl,
                        newConfig
                    );
                    waterUsageChart.render();
                }
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
            labels: ["Aktif", "Tidak Aktif", "Bermasalah"],
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
    let complaintBarChart;
    let labelColor, borderColor;

    if (typeof isDarkStyle !== "undefined" && isDarkStyle) {
        labelColor = config.colors_dark.textMuted;
        borderColor = config.colors_dark.borderColor;
    } else {
        labelColor = config.colors.textMuted;
        borderColor = config.colors.borderColor;
    }

    const statusColors = {
        resolved: "#28c76f",
        processed: "#00cfe8",
        pending: "#ff9f43",
        rejected: "#ea5455",
    };

    const translatedStatusMap = {
        resolved: "Selesai",
        processed: "Diproses",
        pending: "Menunggu",
        rejected: "Ditolak",
    };

    function initComplaintBarChart() {
        const chartEl = document.getElementById("complaintBarChart");
        if (!chartEl) return;

        const initialData = JSON.parse(chartEl.dataset.chart);

        document.getElementById(
            "complaint-total"
        ).textContent = `${initialData.total} Keluhan`;

        const mappedSeriesData = initialData.series.map((s) => ({
            ...s,
            name: translatedStatusMap[s.name.toLowerCase()] || s.name,
        }));

        const options = {
            chart: {
                height: 330,
                type: "bar",
                stacked: true,
                toolbar: {
                    show: true,
                    tools: {
                        download: false,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true,
                    },
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "70%",
                    endingShape: "rounded",
                    borderRadius: 6,
                    dataLabels: { position: "top" },
                },
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val > 0 ? val : "";
                },
                offsetY: -20,
                style: { fontSize: "12px", colors: ["#6c757d"] },
            },
            colors: mappedSeriesData.map(
                (s) =>
                    statusColors[
                        Object.keys(translatedStatusMap)
                            .find((key) => translatedStatusMap[key] === s.name)
                            .toLowerCase()
                    ] || "#ccc"
            ),
            series: mappedSeriesData,
            xaxis: {
                categories: initialData.dates,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: labelColor, fontSize: "13px" },
                    rotate:
                        initialData.period === "today" ||
                        initialData.period === "yesterday"
                            ? 0
                            : -45,
                },
                tooltip: { enabled: false },
            },
            yaxis: {
                labels: {
                    style: { colors: labelColor, fontSize: "13px" },
                    formatter: function (val) {
                        return Math.round(val);
                    },
                },
                title: {
                    text: "Jumlah Keluhan",
                    style: { color: labelColor, fontSize: "13px" },
                },
            },
            grid: {
                borderColor: borderColor,
                strokeDashArray: 7,
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } },
            },
            tooltip: {
                enabled: true,
                x: {
                    formatter: function (
                        val,
                        { series, seriesIndex, dataPointIndex, w }
                    ) {
                        return val;
                    },
                },
                y: {
                    formatter: function (
                        val,
                        { series, seriesIndex, dataPointIndex, w }
                    ) {
                        if (typeof val === "number" && val > 0) {
                            return val + " keluhan";
                        }
                        return "";
                    },
                    title: {
                        formatter: function (seriesName) {
                            return seriesName;
                        },
                    },
                },
                marker: {
                    show: true,
                },
            },
            states: {
                hover: { filter: { type: "darken", value: 0.8 } },
            },
            legend: {
                show: true,
                position: "bottom",
                horizontalAlign: "right",
                labels: {
                    colors: labelColor,
                },
                markers: {
                    radius: 2,
                },
                formatter: function (seriesName) {
                    return seriesName;
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 0,
                },
                onItemClick: {
                    toggleDataSeries: true,
                },
                onItemHover: {
                    highlightDataSeries: true,
                },
            },
        };

        complaintBarChart = new ApexCharts(chartEl, options);
        complaintBarChart.render();
    }

    initComplaintBarChart();

    document.querySelectorAll(".period-filter").forEach((item) => {
        item.addEventListener("click", function () {
            const period = this.dataset.period;
            updateComplaintBarChart(period);
        });
    });

    async function updateComplaintBarChart(period) {
        try {
            const response = await fetch(
                `/api/complaint-bar-data?period=${period}`
            );
            const data = await response.json();

            const updatedMappedSeriesData = data.series.map((s) => ({
                ...s,
                name: translatedStatusMap[s.name.toLowerCase()] || s.name,
            }));

            complaintBarChart.updateOptions({
                series: updatedMappedSeriesData,
                xaxis: {
                    categories: data.dates,
                    labels: {
                        rotate:
                            data.period === "today" ||
                            data.period === "yesterday"
                                ? 0
                                : -45,
                    },
                },
                colors: updatedMappedSeriesData.map(
                    (s) =>
                        statusColors[
                            Object.keys(translatedStatusMap)
                                .find(
                                    (key) => translatedStatusMap[key] === s.name
                                )
                                .toLowerCase()
                        ] || "#ccc"
                ),
            });

            document.getElementById(
                "complaint-total"
            ).textContent = `${data.total} Keluhan`;
        } catch (error) {
            console.error("Error fetching complaint bar data:", error);
        }
    }
});
