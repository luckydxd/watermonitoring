"use strict";

(function () {
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

    // Pastikan data default tersedia
    window.complaintStatusData = window.complaintStatusData || {
        pending: 0,
        processed: 0,
        resolved: 0,
        rejected: 0,
    };

    window.deviceStatusData = window.deviceStatusData || {
        active: 0,
        inactive: 0,
        error: 0,
    };

    const chartColors = {
        column: { series1: "#826af9", series2: "#d2b0ff", bg: "#f8d3ff" },
        donut: {
            series1: "#FFC107", // pending (yellow)
            series2: "#17A2B8", // processed (teal)
            series3: "#28A745", // resolved (green)
            series4: "#DC3545", // rejected (red)
            series5: "#28A745", // active (green)
            series6: "#6C757D", // inactive (gray)
            series7: "#FFC107", // error (yellow)
        },
        bar: { bg: "#1D9FF2" },
        area: {
            series1: "#2196f3",
            series2: "#64b5f6",
            series3: "#90caf9",
            series4: "#e3f2fd",
        },
    };

    const areaChartEl = document.querySelector("#lineAreaChart");
    const complaintLabels = ["Pending", "Processed", "Resolved", "Rejected"];
    const complaintMap = window.complaintStatusData ?? {};
    const complaintSeries = complaintLabels.map((label) => {
        const key = label.toLowerCase();
        return complaintMap[key] ?? 0;
    });

    if (areaChartEl) {
        // Ambil data dari data-chart attribute
        const chartData = JSON.parse(areaChartEl.dataset.chart);

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
            colors: [
                chartColors.area.series1,
                chartColors.area.series2,
                chartColors.area.series3,
                chartColors.area.series4,
            ],
            series: [
                {
                    name: "Pengunjung",
                    data: chartData.visitors,
                },
                {
                    name: "Klik Kontak",
                    data: chartData.contact_clicks,
                },
                {
                    name: "Klik Login",
                    data: chartData.login_clicks,
                },
                {
                    name: "Klik Download",
                    data: chartData.download_clicks,
                },
            ],
            xaxis: {
                categories: chartData.dates,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: labelColor, fontSize: "13px" } },
            },
            yaxis: {
                labels: { style: { colors: labelColor, fontSize: "13px" } },
            },
            fill: { opacity: 1, type: "solid" },
            tooltip: {
                shared: false,
                y: {
                    formatter: function (value) {
                        return value + " aksi";
                    },
                },
            },
        };
        if (areaChartEl) {
            areaChart = new ApexCharts(areaChartEl, areaChartConfig);
            areaChart.render();
        }
    }

    // --------------------------------------------------------------------
    const horizontalBarChartEl = document.querySelector("#horizontalBarChart"),
        horizontalBarChartConfig = {
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
    if (
        typeof horizontalBarChartEl !== undefined &&
        horizontalBarChartEl !== null
    ) {
        const horizontalBarChart = new ApexCharts(
            horizontalBarChartEl,
            horizontalBarChartConfig
        );
        horizontalBarChart.render();
    }

    // Donut Chart
    // --------------------------------------------------------------------

    // Fungsi untuk membuat donut chart
    document.addEventListener("DOMContentLoaded", function () {
        (function () {
            let cardColor, headingColor, labelColor, borderColor, legendColor;

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

            window.complaintStatusData = window.complaintStatusData || {
                pending: 0,
                processed: 0,
                resolved: 0,
                rejected: 0,
            };

            window.deviceStatusData = window.deviceStatusData || {
                active: 0,
                inactive: 0,
                error: 0,
            };

            function initDonutChart(elementId, labels, seriesData, colors) {
                const el = document.getElementById(elementId);
                if (!el) return;

                const total = seriesData.reduce((a, b) => a + b, 0);

                const options = {
                    chart: {
                        type: "donut",
                        height: 350,
                    },
                    series: seriesData,
                    labels: labels,
                    colors: colors,
                    legend: {
                        position: "bottom",
                        labels: {
                            colors: legendColor,
                            useSeriesColors: false,
                        },
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
                                        formatter: function () {
                                            return total;
                                        },
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

                const chart = new ApexCharts(el, options);
                chart.render();
            }

            initDonutChart(
                "donutChart1",
                ["Pending", "Processed", "Resolved", "Rejected"],
                [
                    window.complaintStatusData.pending || 0,
                    window.complaintStatusData.processed || 0,
                    window.complaintStatusData.resolved || 0,
                    window.complaintStatusData.rejected || 0,
                ],
                ["#FFC107", "#17A2B8", "#28A745", "#DC3545"]
            );

            initDonutChart(
                "donutChart2",
                ["Active", "Inactive", "Error"],
                [
                    window.deviceStatusData.active || 0,
                    window.deviceStatusData.inactive || 0,
                    window.deviceStatusData.error || 0,
                ],
                ["#28A745", "#6C757D", "#FFC107"]
            );
        })();
    });

    // Filter Tanggal Chart Line //
    document.querySelectorAll("#dateFilterDropdown a").forEach((item) => {
        item.addEventListener("click", function (e) {
            e.preventDefault();
            const range = this.getAttribute("data-range");
            fetchChartData(range);
        });
    });

    function fetchChartData(range) {
        fetch(`/admin/chart-data?range=${range}`)
            .then((res) => res.json())
            .then((data) => {
                areaChart.updateOptions({
                    series: data.series,
                    xaxis: { categories: data.dates },
                });
            });
    }
})();
