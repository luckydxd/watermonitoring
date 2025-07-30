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
        // Start with the base query
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

        // Pass the query to DataTables
        $dataTable = DataTables::of($baseQuery);

        // --- Define searchable columns ---
        // This is crucial. Tell DataTables how to search each column.
        $dataTable->filterColumn('usage_date', function ($query, $keyword) {
            // Your custom filtering logic for 'usage_date' (DataTables column index 3)
            // The $keyword is the search term from your JavaScript (date, month, year, or combined)

            // Handle full date filter (yyyy-mm-dd)
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $keyword)) {
                $query->whereDate('flow_pressure_sensors.measured_at', $keyword);
            }
            // Handle month filter (-mm-)
            else if (preg_match('/^-\d{2}-$/', $keyword)) {
                $month = str_replace('-', '', $keyword);
                $query->whereMonth('flow_pressure_sensors.measured_at', $month);
            }
            // Handle year-month filter (yyyy-mm)
            else if (preg_match('/^\d{4}-\d{2}$/', $keyword)) {
                list($year, $month) = explode('-', $keyword);
                $query->whereYear('flow_pressure_sensors.measured_at', $year)
                    ->whereMonth('flow_pressure_sensors.measured_at', $month);
            }
            // Handle year filter (yyyy)
            else if (preg_match('/^\d{4}$/', $keyword)) {
                $query->whereYear('flow_pressure_sensors.measured_at', $keyword);
            }
            // If none of the specific date formats match, let DataTables handle it as a general search
            // Or you can add a default like:
            // else {
            //     $query->where(DB::raw('DATE(flow_pressure_sensors.measured_at)'), 'LIKE', "%{$keyword}%");
            // }
        });

        // Optionally, define how other columns are searched if you implement general search or other column searches
        // For example, for 'user_name':
        $dataTable->filterColumn('user_name', function ($query, $keyword) {
            $query->where('user_datas.name', 'like', "%{$keyword}%");
        });

        // For 'user_email':
        $dataTable->filterColumn('user_email', function ($query, $keyword) {
            $query->where('users.email', 'like', "%{$keyword}%");
        });


        return $dataTable->make(true);
    }
}
