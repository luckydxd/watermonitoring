<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use Yajra\DataTables\Facades\DataTables;

class DeviceManagementController extends Controller
{
    public function index()
    {
        $totalDevices = Device::count();
        $activeDevices = Device::where('status', 'active')->count();
        $inactiveDevices = Device::where('status', 'inactive')->count();
        $maintenanceDevices = Device::where('status', 'error')->count();

        return view('admin.device.index', compact(
            'totalDevices',
            'activeDevices',
            'inactiveDevices',
            'maintenanceDevices'
        ));
    }
}
