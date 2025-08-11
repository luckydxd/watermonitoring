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
}
