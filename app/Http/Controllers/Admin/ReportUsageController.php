<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\FlowPressureSensor;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;


class ReportUsageController extends Controller
{
    public function index()
    {
        return view('admin.report-usage.index');
    }



    public function datatables(Request $request)
    {

        $data = FlowPressureSensor::query()
            // 2. Pilih kolom yang dibutuhkan dan hitung konsumsi harian
            ->select(
                'user_datas.name as user_name',
                'users.email as user_email',
                // Mengambil tanggal dari measured_at
                DB::raw('DATE(flow_pressure_sensors.measured_at) as usage_date'),
                // Menghitung selisih MAX dan MIN volume untuk setiap grup sebagai total_consumption
                DB::raw('MAX(flow_pressure_sensors.volume) - MIN(flow_pressure_sensors.volume) as total_consumption')
            )
            // 3. Lakukan JOIN untuk menghubungkan ke data pengguna
            ->join('devices', 'flow_pressure_sensors.device_id', '=', 'devices.id')
            ->join('device_assignments', 'devices.id', '=', 'device_assignments.device_id')
            ->join('users', 'device_assignments.user_id', '=', 'users.id')
            ->leftJoin('user_datas', 'users.id', '=', 'user_datas.user_id')
            // 4. Kelompokkan hasilnya per hari per pengguna
            ->groupBy('users.id', 'user_datas.name', 'users.email', 'usage_date')
            // 5. Hanya ambil baris yang memiliki konsumsi (opsional, tapi bagus)
            ->having('total_consumption', '>', 0);

        // 6. Kirim ke DataTables
        return DataTables::of($data)->make(true);
    }
}
