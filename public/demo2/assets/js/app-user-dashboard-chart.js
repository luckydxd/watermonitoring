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

    // Ganti blok if (consumptionChartEl) yang lama dengan yang ini
    if (consumptionChartEl) {
        // 1. Ambil data awal dari atribut data-chart
        const initialData = JSON.parse(
            consumptionChartEl.dataset.chart || "{}"
        );
        let consumptionChart;

        // --- FUNGSI UNTUK MENGAMBIL SEMUA DATA SEKALIGUS (TETAP DIPERLUKAN) ---
        async function fetchAllChartData(range) {
            if (consumptionChart) consumptionChart.showLoading();
            try {
                const [consumptionRes, flowRes, pressureRes] =
                    await Promise.all([
                        fetch(
                            `/user/dashboard/api/consumption-summary?range=${range}`
                        ),
                        fetch(
                            `/user/dashboard/api/history/flow_rate?range=${range}`
                        ),
                        fetch(
                            `/user/dashboard/api/history/pressure?range=${range}`
                        ),
                    ]);
                if (!consumptionRes.ok || !flowRes.ok || !pressureRes.ok)
                    throw new Error("Gagal mengambil data");

                const consumptionResult = await consumptionRes.json();
                const flowResult = await flowRes.json();
                const pressureResult = await pressureRes.json();

                // Format data
                const consumptionSeries = consumptionResult.data.map(
                    (item) => ({
                        x: item.date,
                        y: parseFloat(item.value || item.total).toFixed(2),
                    })
                );
                const flowSeries = flowResult.data.map((item) => ({
                    x: item.date,
                    y: parseFloat(item.value).toFixed(2),
                }));
                const pressureSeries = pressureResult.data.map((item) => ({
                    x: item.date,
                    y: parseFloat(item.value).toFixed(2),
                }));

                // Perbarui chart dengan 3 series data
                consumptionChart.updateSeries([
                    { data: consumptionSeries },
                    { data: flowSeries },
                    { data: pressureSeries },
                ]);
            } catch (error) {
                console.error("Gagal mengambil data multi-series:", error);
                if (consumptionChart)
                    consumptionChart.updateOptions({
                        series: [],
                        noData: { text: "Gagal memuat data." },
                    });
            } finally {
                if (consumptionChart) consumptionChart.hideLoading();
            }
        }

        // --- KONFIGURASI CHART DENGAN GAYA AWAL + SERIES TERSEMBUNYI ---
        const consumptionConfig = {
            // Tipe chart utama adalah 'area' sesuai permintaan Anda
            chart: {
                height: 400,
                type: "area",
                stacked: false,
                toolbar: { show: true },
            },
            stroke: { curve: "smooth", width: [3, 2, 2] },
            colors: ["#00cfe8", "#28c76f", "#ff9f43"],

            // Definisikan 3 series, namun 2 di antaranya akan disembunyikan
            series: [
                {
                    name: "Konsumsi Air (L)",
                    data: initialData.consumption || [],
                },
                {
                    name: "Flow Rate (L/min)",
                    data: initialData.flowRate || [],
                },
                {
                    name: "Pressure (Bar)",
                    data: initialData.pressure || [],
                },
            ],

            dataLabels: { enabled: false },
            fill: {
                // Konfigurasi fill hanya untuk tipe 'area'
                type: "gradient",
                gradient: { opacityFrom: 0.7, opacityTo: 0.1 },
            },
            xaxis: {
                type: "datetime",
                labels: { style: { colors: labelColor } },
            },
            // KEMBALI KE SUMBU-Y TUNGGAL (hanya untuk Konsumsi Air)
            yaxis: {
                title: { text: "Konsumsi Air (Liter)" },
                labels: {
                    style: { colors: labelColor },
                    formatter: (val) => `${val ? val.toFixed(0) : 0} L`,
                },
            },
            tooltip: {
                x: { format: "dd MMM yyyy" },
                // Tooltip tetap cerdas, menampilkan unit yang benar
                y: {
                    formatter: function (value, { seriesIndex }) {
                        if (typeof value === "undefined" || value === null)
                            return "N/A";
                        if (seriesIndex === 0) return `${value} Liter`;
                        if (seriesIndex === 1) return `${value} L/min`;
                        if (seriesIndex === 2) return `${value} Bar`;
                        return value;
                    },
                },
            },
            legend: {
                position: "top",
                horizontalAlign: "center",
                // Ini memungkinkan pengguna mengklik legenda untuk menampilkan/menyembunyikan series
                onItemClick: { toggleDataSeries: true },
                onItemHover: { highlightDataSeries: true },
            },
            grid: { borderColor: borderColor },
        };

        // Render chart
        consumptionChart = new ApexCharts(
            consumptionChartEl,
            consumptionConfig
        );
        consumptionChart.render();

        // Sembunyikan series Flow Rate dan Pressure secara default setelah chart dirender
        consumptionChart.hideSeries("Flow Rate (L/min)");
        consumptionChart.hideSeries("Pressure (Bar)");

        // Fungsionalitas filter dropdown
        document
            .querySelectorAll("#consumptionDateFilter .time-period-btn")
            .forEach((btn) => {
                btn.addEventListener("click", function (e) {
                    e.preventDefault();
                    fetchAllChartData(this.dataset.period);
                });
            });

        // Panggil fetch awal jika data dari blade tidak ada
        if (!initialData.consumption) {
            fetchAllChartData("last7");
        }
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
