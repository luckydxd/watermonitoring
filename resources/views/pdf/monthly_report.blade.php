<!DOCTYPE html>
<html lang="id">

<head>
    <title>Ringkasan Laporan Bulanan - {{ $monthName }} {{ $year }}</title>
    {{-- CSS dari jawaban sebelumnya bisa disalin ke sini --}}
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .details {
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h3 {
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Ringkasan Laporan Monitoring Bulanan</h1>
        <p>Periode: {{ $monthName }} {{ $year }}</p>
    </div>
    <div class="details">
        <strong>Pengguna:</strong> {{ optional($user->userData)->name ?? $user->username }}
    </div>

    <h3>Ringkasan Data Harian (Flow Rate & Pressure)</h3> {{-- Tambahkan detail untuk kejelasan --}}
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Rata-rata Flow Rate (L/min)</th> {{-- Ubah dari Konsumsi Harian jika ini flow/pressure --}}
                <th>Rata-rata Pressure (Bar)</th>
            </tr>
        </thead>
        <tbody>
            {{-- Perbaikan di sini: Gunakan $flowSummary, bukan $reportData --}}
            @forelse ($flowSummary as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>
                    <td>{{ number_format($row->avg_flow_rate, 2) }}</td>
                    <td>{{ number_format($row->avg_pressure, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Tidak ada data flow atau pressure pada periode ini.
                    </td> {{-- Sesuaikan colspan --}}
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="margin-top: 30px;">Ringkasan Kualitas Air</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Rata-rata Kekeruhan (NTU)</th>
                <th>Rata-rata Level Air (%)</th>
            </tr>
        </thead>
        <tbody>
            {{-- Perbaikan di sini: Pastikan ini juga menggunakan $qualitySummary --}}
            @forelse ($qualitySummary as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>
                    <td>{{ number_format($row->avg_turbidity, 2) }}</td>
                    <td>{{ number_format($row->avg_water_level, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Tidak ada data kualitas air pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
