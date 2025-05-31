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
})();

// Data statis (contoh angka)
const visitorData = [
    120, 150, 200, 180, 220, 250, 300, 280, 320, 310, 330, 350,
];
const projectData = [30, 50, 40, 60, 70, 90, 100, 110, 120, 130, 140, 150];

const ctx = document.getElementById("chartSaya").getContext("2d");
const chartSaya = new Chart(ctx, {
    type: "line",
    data: {
        labels: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ],
        datasets: [
            {
                label: "Visitor",
                data: visitorData,
                borderColor: "rgba(75, 192, 192, 1)",
                borderWidth: 2,
                fill: false,
                tension: 0.3,
            },
            {
                label: "Project",
                data: projectData,
                borderColor: "rgba(255, 99, 132, 1)",
                borderWidth: 2,
                fill: false,
                tension: 0.3,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
            },
        },
        plugins: {
            legend: {
                position: "top",
            },
            tooltip: {
                mode: "index",
                intersect: false,
            },
        },
    },
});
