"use strict";

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

    const chartColors = {
        column: { series1: "#826af9", series2: "#d2b0ff", bg: "#f8d3ff" },
        donut: {
            series1: "#fee802",
            series2: "#F1F0F2",
            series3: "#826bf8",
            series4: "#3fd0bd",
        },
        area: { series1: "#29dac7", series2: "#60f2ca", series3: "#a5f8cd" },
        bar: { bg: "#1D9FF2" },
    };

    const areaChartEl = document.querySelector("#lineAreaChart"),
        areaChartConfig = {
            chart: {
                height: 400,
                type: "area",
                parentHeightOffset: 0,
                toolbar: { show: false },
                zoom: {
                    enabled: false,
                },
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
                chartColors.area.series3,
                chartColors.area.series2,
                chartColors.area.series1,
            ],
            series: [
                {
                    name: "Visits",
                    data: [
                        100, 120, 90, 170, 130, 160, 140, 240, 220, 180, 270,
                        280, 375,
                    ],
                },
                {
                    name: "Clicks",
                    data: [
                        60, 80, 70, 110, 80, 100, 90, 180, 160, 140, 200, 220,
                        275,
                    ],
                },
                {
                    name: "Sales",
                    data: [
                        20, 40, 30, 70, 40, 60, 50, 140, 120, 100, 140, 180,
                        220,
                    ],
                },
            ],
            xaxis: {
                categories: [
                    "7/12",
                    "8/12",
                    "9/12",
                    "10/12",
                    "11/12",
                    "12/12",
                    "13/12",
                    "14/12",
                    "15/12",
                    "16/12",
                    "17/12",
                    "18/12",
                    "19/12",
                    "20/12",
                ],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: labelColor, fontSize: "13px" } },
            },
            yaxis: {
                labels: { style: { colors: labelColor, fontSize: "13px" } },
            },
            fill: { opacity: 1, type: "solid" },
            tooltip: { shared: false },
        };

    if (areaChartEl) {
        const areaChart = new ApexCharts(areaChartEl, areaChartConfig);
        areaChart.render();
    }

    // Horizontal Bar Chart
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
    const donutChartEl = document.querySelector("#donutChart"),
        donutChartConfig = {
            chart: {
                height: 390,
                type: "donut",
            },
            labels: ["Operational", "Networking", "Hiring", "R&D"],
            series: [42, 7, 25, 25],
            colors: [
                chartColors.donut.series1,
                chartColors.donut.series4,
                chartColors.donut.series3,
                chartColors.donut.series2,
            ],
            stroke: {
                show: false,
                curve: "straight",
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, opt) {
                    return parseInt(val, 10) + "%";
                },
            },
            legend: {
                show: true,
                position: "bottom",
                markers: { offsetX: -3 },
                itemMargin: {
                    vertical: 3,
                    horizontal: 10,
                },
                labels: {
                    colors: legendColor,
                    useSeriesColors: false,
                },
            },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: {
                                fontSize: "2rem",
                                fontFamily: "Public Sans",
                            },
                            value: {
                                fontSize: "1.2rem",
                                color: legendColor,
                                fontFamily: "Public Sans",
                                formatter: function (val) {
                                    return parseInt(val, 10) + "%";
                                },
                            },
                            total: {
                                show: true,
                                fontSize: "1.5rem",
                                color: headingColor,
                                label: "Operational",
                                formatter: function (w) {
                                    return "42%";
                                },
                            },
                        },
                    },
                },
            },
            responsive: [
                {
                    breakpoint: 992,
                    options: {
                        chart: {
                            height: 380,
                        },
                        legend: {
                            position: "bottom",
                            labels: {
                                colors: legendColor,
                                useSeriesColors: false,
                            },
                        },
                    },
                },
                {
                    breakpoint: 576,
                    options: {
                        chart: {
                            height: 320,
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        show: true,
                                        name: {
                                            fontSize: "1.5rem",
                                        },
                                        value: {
                                            fontSize: "1rem",
                                        },
                                        total: {
                                            fontSize: "1.5rem",
                                        },
                                    },
                                },
                            },
                        },
                        legend: {
                            position: "bottom",
                            labels: {
                                colors: legendColor,
                                useSeriesColors: false,
                            },
                        },
                    },
                },
                {
                    breakpoint: 420,
                    options: {
                        chart: {
                            height: 280,
                        },
                        legend: {
                            show: false,
                        },
                    },
                },
                {
                    breakpoint: 360,
                    options: {
                        chart: {
                            height: 250,
                        },
                        legend: {
                            show: false,
                        },
                    },
                },
            ],
        };
    if (typeof donutChartEl !== undefined && donutChartEl !== null) {
        const donutChart = new ApexCharts(donutChartEl, donutChartConfig);
        donutChart.render();
    }
})();
