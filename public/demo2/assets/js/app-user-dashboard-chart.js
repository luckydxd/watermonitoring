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
        // DIUBAH: Tambahkan properti 'bar' dan 'donut' untuk mencegah error di masa depan dari chart lain
        bar: { bg: "#f3f3f3" },
        donut: {
            series1: "#00b5b8",
            series2: "#00cfe8",
            series3: "#29dac7",
            series4: "#60f2ca",
        },
    };

    let labels = [];
    let data = [];

    const consumptionChartEl = document.querySelector("#consumptionLineChart");

    if (consumptionChartEl) {
        // 1. Ambil data awal dari atribut data-chart
        const initialData = JSON.parse(consumptionChartEl.dataset.chart);
        let consumptionChart; // Deklarasikan variabel chart

        // 2. Konfigurasi Chart
        const consumptionConfig = {
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
            colors: ["#00cfe8"],
            series: [
                {
                    name: "Penggunaan Air (Liter)",
                    data: initialData.consumption, // Gunakan data awal
                },
            ],
            xaxis: {
                type: "datetime",
                categories: initialData.dates, // Gunakan label tanggal awal
                labels: { style: { colors: labelColor, fontSize: "13px" } },
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
                y: { formatter: (val) => val + " Liter" },
                x: { format: "dd MMMM yyyy" },
            },
        };

        // 3. Render chart awal
        consumptionChart = new ApexCharts(
            consumptionChartEl,
            consumptionConfig
        );
        consumptionChart.render();

        // 4. Fungsi untuk mengambil data baru
        async function fetchConsumptionData(range) {
            try {
                const response = await fetch(
                    `/api/consumption-summary?range=${range}`
                );
                const result = await response.json();

                const newLabels = result.data.map((item) => item.date);
                const newData = result.data.map((item) =>
                    parseFloat(item.total).toFixed(2)
                );

                // 5. Perbarui chart dengan data baru
                consumptionChart.updateOptions({
                    series: [{ data: newData }],
                    xaxis: { categories: newLabels },
                });
            } catch (error) {
                console.error("Error fetching consumption data:", error);
            }
        }

        // 6. Fungsionalitas filter dropdown
        document
            .querySelectorAll("#consumptionDateFilter .time-period-btn")
            .forEach((btn) => {
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    const period = this.dataset.period;
                    fetchConsumptionData(period);
                });
            });
    }

    function getWaterLevelInfo(level) {
        if (level > 90) {
            return {
                label: "Penuh",
                color: "#00B8D9",
                badgeClass: "text-primary",
            }; // Biru
        } else if (level >= 40) {
            return {
                label: "Aman",
                color: "#00B8D9",
                badgeClass: "text-success",
            }; // Hijau
        } else if (level >= 15) {
            return {
                label: "Siaga",
                color: "#FFAB00",
                badgeClass: "text-warning",
            }; // Kuning
        } else {
            return {
                label: "Kritis",
                color: "#EA5455",
                badgeClass: "text-danger",
            }; // Merah
        }
    }

    function getTurbidityInfo(ntu) {
        if (ntu <= 5) {
            return { label: "Bersih", color: "#00B8D9" };
        } else if (ntu <= 25) {
            return { label: "Sedang", color: "#FFAB00" }; //
        } else {
            return { label: "Kotor", color: "#EA5455" }; // Merah
        }
    }

    function mapNtuToDisplayPercentage(ntu) {
        if (ntu <= 5) {
            // Rentang "Bersih" (0-5 NTU) dipetakan ke 0-33% bar
            return (ntu / 5) * 33;
        } else if (ntu <= 25) {
            // Rentang "Sedang" (5-25 NTU) dipetakan ke 34-66% bar
            // Rumus: Awal persentase + (progres di rentang saat ini)
            return 33 + ((ntu - 5) / (25 - 5)) * 33;
        } else {
            // Rentang "Kotor" (>25 NTU) dipetakan ke 67-100% bar
            // Kita batasi nilai maksimal di 100 NTU untuk visualisasi agar tidak berlebihan
            const cappedNtu = Math.min(ntu, 100);
            return 66 + ((cappedNtu - 25) / (100 - 25)) * 34;
        }
    }

    function initializeSensorWidgets() {
        // Cari semua elemen yang dibutuhkan di halaman
        const waterLevelEl = document.querySelector("#waterLevelChart");
        const turbidityEl = document.querySelector("#turbidityChart");

        // Elemen teks untuk diupdate
        const waterLevelValueEl = document.getElementById("waterLevelValue");
        const waterLevelMessageEl =
            document.getElementById("waterLevelMessage");

        const turbidityValueEl = document.getElementById("turbidityValue");
        const turbidityStatusEl = document.getElementById("turbidityStatus");

        if (!waterLevelEl && !turbidityEl) return;

        let waterLevelChart, turbidityChart;

        // --- FUNGSI TUNGGAL UNTUK MENGAMBIL DATA & UPDATE SEMUA WIDGET ---
        async function fetchAndUpdateAllWidgets() {
            try {
                // SATU PANGGILAN API UNTUK SEMUA WIDGET
                const response = await fetch("/api/sensor-latest");

                if (!response.ok) {
                    throw new Error(
                        `Gagal mengambil data: Status ${response.status}`
                    );
                }

                const result = await response.json();
                const data = result.data; // Akses objek 'data' dari respons

                if (waterLevelChart && data.water_level !== undefined) {
                    const waterLevelValue = parseFloat(data.water_level);

                    // 1. Dapatkan informasi kategori (label & warna)
                    const levelInfo = getWaterLevelInfo(waterLevelValue);

                    // 2. Update nilai teks persentase
                    if (waterLevelValueEl)
                        waterLevelValueEl.textContent = `${waterLevelValue.toFixed(
                            1
                        )} %`;

                    // 3. Update teks status dan warnanya
                    if (waterLevelMessageEl) {
                        waterLevelMessageEl.textContent = `Status: ${levelInfo.label}`;
                        // Hapus kelas warna lama dan tambahkan yang baru
                        waterLevelMessageEl.className = `text-muted mt-3 ${levelInfo.badgeClass}`;
                    }

                    // 4. Perbarui chart dengan nilai DAN warna baru
                    waterLevelChart.updateOptions({
                        series: [waterLevelValue],
                        colors: [levelInfo.color],
                    });
                }

                // --- LOGIKA BARU UNTUK WIDGET TURBIDITY ---
                if (turbidityChart && data.turbidity !== undefined) {
                    const turbidityValue = parseFloat(data.turbidity);

                    // 1. Dapatkan informasi kategori (label & warna)
                    const turbidityInfo = getTurbidityInfo(turbidityValue);
                    // 2. Dapatkan nilai persentase untuk "jarum" bar
                    const displayPercentage =
                        mapNtuToDisplayPercentage(turbidityValue);

                    // 3. Update nilai teks NTU di bawah chart
                    if (turbidityValueEl)
                        turbidityValueEl.textContent = `${turbidityValue.toFixed(
                            1
                        )} NTU`;

                    // 4. Perbarui chart dengan persentase, warna, dan label status baru
                    turbidityChart.updateOptions({
                        series: [displayPercentage],
                        colors: [turbidityInfo.color],
                        labels: [turbidityInfo.label],
                    });
                }
            } catch (error) {
                console.error("Gagal memuat data widget sensor:", error);
                if (turbidityValueEl) turbidityValueEl.textContent = "-";
            }
        }

        // --- Inisialisasi Chart Water Level ---
        if (waterLevelEl) {
            const waterLevelConfig = {
                chart: {
                    height: 170,
                    type: "radialBar",
                    sparkline: { enabled: true },
                },
                colors: ["#00B8D9"],
                series: [0],
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        hollow: { size: "65%" },
                        track: { background: borderColor },
                        dataLabels: {
                            name: { show: false },
                            value: {
                                fontSize: "24px",
                                color: labelColor,
                                fontWeight: 500,
                                offsetY: 0,
                            },
                        },
                    },
                },
                stroke: { lineCap: "round" },
                labels: ["Water Level"],
            };
            waterLevelChart = new ApexCharts(waterLevelEl, waterLevelConfig);
            waterLevelChart.render();
        }

        if (turbidityEl) {
            const turbidityConfig = {
                chart: {
                    height: 170,
                    type: "radialBar",
                    sparkline: { enabled: true },
                },
                series: [0],
                colors: ["#8A8D93"], // Warna abu-abu awal
                stroke: { lineCap: "round" },
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        hollow: { size: "65%" },
                        track: { background: borderColor },
                        dataLabels: {
                            name: {
                                // Ini adalah label di tengah (Bersih/Sedang/Kotor)
                                show: true,
                                fontSize: "22px",
                                fontWeight: 600,
                                offsetY: -5,
                                color: labelColor,
                            },
                            value: {
                                // Angka persentase di bawah label, kita sembunyikan
                                show: false,
                            },
                        },
                    },
                },
                // Label awal, akan di-update secara dinamis
                labels: ["-"],
            };
            turbidityChart = new ApexCharts(turbidityEl, turbidityConfig);
            turbidityChart.render();
        }

        // --- Mulai proses pengambilan data ---
        fetchAndUpdateAllWidgets();
        setInterval(fetchAndUpdateAllWidgets, 30000);
    }

    initializeSensorWidgets();
})();
