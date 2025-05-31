<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Device;
use Yajra\DataTables\DataTables;

class ReportDeviceController extends Controller
{
    public function index()
    {
        return view('admin.report-device.index');
    }
    public function datatables(Request $request)
    {

        $data = Device::query()->with(['deviceType']);
        return DataTables::of($data)->make(true);
    }
}
