"use strict";

(async function () {
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
        area: { series1: "#29dac7", series2: "#60f2ca", series3: "#a5f8cd" },
    };

    // Fetch data dari backend
    let labels = [];
    let data = [];

    try {
        const response = await fetch("/api/monitoring/usage", {
            headers: {
                Accept: "application/json",
                Authorization: "Bearer YOUR_TOKEN_JIKA_PAKAI_SANCTUM",
            },
        });

        const json = await response.json();

        labels = json.map((item) => item.date);
        data = json.map((item) => parseFloat(item.total_usage));
    } catch (error) {
        console.error("Gagal memuat data monitoring:", error);
    }

    const areaChartEl = document.querySelector("#lineAreaChart"),
        areaChartConfig = {
            chart: {
                height: 400,
                type: "area",
                parentHeightOffset: 0,
                toolbar: { show: false },
                zoom: { enabled: false },
            },
            dataLabels: { enabled: false },
            stroke: { show: true, curve: "smooth", width: 2 },
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
            colors: [chartColors.area.series1],
            series: [
                {
                    name: "Penggunaan Air (Liter)",
                    data: data,
                },
            ],
            xaxis: {
                categories: labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: labelColor, fontSize: "13px" },
                },
            },
            yaxis: {
                labels: { style: { colors: labelColor, fontSize: "13px" } },
                title: { text: "Liter" },
            },
            fill: {
                opacity: 0.8,
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.5,
                    opacityTo: 0.9,
                    stops: [0, 90, 100],
                },
            },
            tooltip: {
                shared: false,
                y: {
                    formatter: function (val) {
                        return val + " Liter";
                    },
                },
            },
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
    // Expenses Radial Bar Chart
    // --------------------------------------------------------------------
    document.addEventListener("DOMContentLoaded", async () => {
        const chartEl = document.querySelector("#waterLevelChart");
        const valueEl = document.getElementById("waterLevelValue");
        const messageEl = document.getElementById("waterLevelMessage");

        const chartOptions = {
            chart: {
                height: 170,
                type: "radialBar",
                sparkline: { enabled: true },
                parentHeightOffset: 0,
            },
            colors: ["#00b8d9"],
            series: [0],
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: "65%" },
                    track: {
                        strokeWidth: "45%",
                        background: "#f0f0f0",
                    },
                    dataLabels: {
                        name: { show: false },
                        value: {
                            fontSize: "24px",
                            color: "#111",
                            fontWeight: 500,
                            offsetY: -5,
                        },
                    },
                },
            },
            stroke: { lineCap: "round" },
            labels: ["Water Level"],
        };

        const chart = new ApexCharts(chartEl, chartOptions);
        chart.render();

        // Fetch data from your endpoint
        try {
            const res = await fetch("/api/sensor-latest", {
                headers: {
                    Accept: "application/json",
                    Authorization: "Bearer YOUR_API_TOKEN", // jika perlu
                },
            });

            const data = await res.json();

            const waterLevel = parseFloat(data.water_level); // Misalnya dalam cm
            chart.updateSeries([waterLevel]);
            valueEl.textContent = `${waterLevel} cm`;
            messageEl.textContent = `Water level terakhir dari perangkat Anda`;
        } catch (err) {
            valueEl.textContent = "-";
            messageEl.textContent = "Gagal mengambil data.";
        }
    });
    // Radial Chart Config
    const turbidityChartEl = document.querySelector("#turbidityChart");

    const turbidityChartConfig = {
        chart: {
            height: 170,
            sparkline: { enabled: true },
            parentHeightOffset: 0,
            type: "radialBar",
        },
        colors: ["#00b5b8"], // Adjust based on turbidity range if needed
        series: [0], // Initial value
        plotOptions: {
            radialBar: {
                offsetY: 0,
                startAngle: -90,
                endAngle: 90,
                hollow: { size: "65%" },
                track: {
                    strokeWidth: "45%",
                    background: "#f0f0f0",
                },
                dataLabels: {
                    name: { show: false },
                    value: {
                        fontSize: "24px",
                        color: "#333",
                        fontWeight: 500,
                        offsetY: -5,
                    },
                },
            },
        },
        stroke: { lineCap: "round" },
        labels: ["Turbidity"],
    };

    // Render chart
    let turbidityChart = new ApexCharts(turbidityChartEl, turbidityChartConfig);
    turbidityChart.render();

    // Fetch real-time data
    async function fetchTurbidity() {
        try {
            const response = await fetch("/api/sensor-latest"); // Sesuaikan endpoint
            const data = await response.json();

            const turbidity = data.turbidity;
            const timestamp = new Date(data.timestamp).toLocaleString("id-ID");

            turbidityChart.updateSeries([parseFloat(turbidity)]);
            document.getElementById(
                "turbidityValue"
            ).textContent = `${turbidity} NTU`;
            document.getElementById("lastUpdated").textContent = timestamp;
        } catch (error) {
            console.error("Failed to fetch turbidity:", error);
        }
    }

    // Update every 30 seconds
    fetchTurbidity();
    setInterval(fetchTurbidity, 30000);
})();
