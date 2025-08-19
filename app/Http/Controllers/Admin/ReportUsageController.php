<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\FlowPressureSensor;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Auth;

class ReportUsageController extends Controller
{
    public function index()
    {
        $appSettings = AppSetting::first();

        // Ambil data pengguna yang sedang login
        $currentUser = Auth::user();
        return view('admin.report-usage.index', compact('appSettings', 'currentUser'));
    }



    public function datatables(Request $request)
    {
        $baseQuery = FlowPressureSensor::query()
            ->select(
                'user_datas.name as user_name',
                'users.email as user_email',
                DB::raw('DATE(flow_pressure_sensors.measured_at) as usage_date'),
                DB::raw('MAX(flow_pressure_sensors.volume) - MIN(flow_pressure_sensors.volume) as total_consumption')
            )
            ->join('devices', 'flow_pressure_sensors.device_id', '=', 'devices.id')
            ->join('device_assignments', 'devices.id', '=', 'device_assignments.device_id')
            ->join('users', 'device_assignments.user_id', '=', 'users.id')
            ->leftJoin('user_datas', 'users.id', '=', 'user_datas.user_id')
            ->groupBy('users.id', 'user_datas.name', 'users.email', 'usage_date')
            ->having('total_consumption', '>', 0);

        $dataTable = DataTables::of($baseQuery);

        $dataTable->filterColumn('usage_date', function ($query, $keyword) {

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $keyword)) {
                $query->whereDate('flow_pressure_sensors.measured_at', $keyword);
            } else if (preg_match('/^-\d{2}-$/', $keyword)) {
                $month = str_replace('-', '', $keyword);
                $query->whereMonth('flow_pressure_sensors.measured_at', $month);
            } else if (preg_match('/^\d{4}-\d{2}$/', $keyword)) {
                list($year, $month) = explode('-', $keyword);
                $query->whereYear('flow_pressure_sensors.measured_at', $year)
                    ->whereMonth('flow_pressure_sensors.measured_at', $month);
            } else if (preg_match('/^\d{4}$/', $keyword)) {
                $query->whereYear('flow_pressure_sensors.measured_at', $keyword);
            }
        });

        $dataTable->filterColumn('user_name', function ($query, $keyword) {
            $query->where('user_datas.name', 'like', "%{$keyword}%");
        });

        $dataTable->filterColumn('user_email', function ($query, $keyword) {
            $query->where('users.email', 'like', "%{$keyword}%");
        });


        return $dataTable->make(true);
    }

    public function getAdminUsageData(Request $request)
    {
        Carbon::setLocale('id');

        $period = $request->input('period', 'branch');
        $branchId = $request->input('branch_id');
        $userId = $request->input('user_id');
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        // Subquery untuk konsumsi harian
        $dailyConsumption = DB::table('flow_pressure_sensors')
            ->select(
                'device_id',
                DB::raw('DATE(measured_at) as date'),
                DB::raw('MAX(volume) - MIN(volume) as daily_consumption')
            )
            ->groupBy('device_id', 'date');

        $query = DB::table('device_assignments')
            ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
            ->join('users', 'device_assignments.user_id', '=', 'users.id')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
            ->leftJoin('user_datas', 'users.id', '=', 'user_datas.user_id')
            ->leftJoinSub($dailyConsumption, 'daily', function ($join) {
                $join->on('devices.id', '=', 'daily.device_id');
            })
            ->where('device_assignments.is_active', true);

        // Level Branch
        if ($period === 'branch') {
            $query->groupBy('branches.id', 'branches.name')
                ->select(
                    'branches.id',
                    'branches.name as period_label',
                    DB::raw('IFNULL(SUM(daily.daily_consumption), 0) as total_consumption'),
                    DB::raw("'branch' as period_type")
                );
        }
        // Level User
        elseif ($period === 'user') {
            $query->where('branches.id', $branchId)
                ->groupBy('users.id', 'user_datas.name', 'branches.name')
                ->select(
                    'users.id',
                    DB::raw('CONCAT(COALESCE(user_datas.name), " (", branches.name, ")") as period_label'),
                    DB::raw('IFNULL(SUM(daily.daily_consumption), 0) as total_consumption'),
                    DB::raw("'user' as period_type")
                );
        }
        // Level Daily
        elseif ($period === 'daily') {
            $query->where('users.id', $userId)
                ->whereNotNull('daily.date')
                ->whereYear('daily.date', $year)
                ->whereMonth('daily.date', $month)
                ->select(
                    'daily.date',
                    // TAMBAHAN: Menyertakan informasi user untuk export
                    'user_datas.name as user_name',
                    'users.email as user_email',
                    'branches.name as branch_name',
                    DB::raw('CONCAT(
                    CASE DAYOFWEEK(daily.date)
                        WHEN 1 THEN "Minggu"
                        WHEN 2 THEN "Senin"
                        WHEN 3 THEN "Selasa"
                        WHEN 4 THEN "Rabu"
                        WHEN 5 THEN "Kamis"
                        WHEN 6 THEN "Jumat"
                        WHEN 7 THEN "Sabtu"
                    END,
                    ", ",
                    DAY(daily.date), " ",
                    CASE MONTH(daily.date)
                        WHEN 1 THEN "Januari"
                        WHEN 2 THEN "Februari"
                        WHEN 3 THEN "Maret"
                        WHEN 4 THEN "April"
                        WHEN 5 THEN "Mei"
                        WHEN 6 THEN "Juni"
                        WHEN 7 THEN "Juli"
                        WHEN 8 THEN "Agustus"
                        WHEN 9 THEN "September"
                        WHEN 10 THEN "Oktober"
                        WHEN 11 THEN "November"
                        WHEN 12 THEN "Desember"
                    END,
                    " ", YEAR(daily.date)
                ) as period_label'),
                    'daily.daily_consumption as total_consumption',
                    DB::raw("'daily' as period_type")
                );
        }

        return DataTables::of($query)
            ->addColumn('formatted_consumption', function ($row) {
                return number_format($row->total_consumption, 2, ',', '.') . ' Liter';
            })
            ->rawColumns(['formatted_consumption'])
            ->make(true);
    }
}
