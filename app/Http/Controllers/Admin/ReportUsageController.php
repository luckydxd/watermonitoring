<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\WaterConsumptionLog;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ReportUsageController extends Controller
{
    public function index()
    {
        return view('admin.report-usage.index');
    }



    public function datatables(Request $request)
    {

        $data = WaterConsumptionLog::query()->with(['user.userData']);
        return DataTables::of($data)->make(true);
    }
}
