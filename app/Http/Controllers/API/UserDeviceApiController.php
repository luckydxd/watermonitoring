<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceAssignment;

use Yajra\DataTables\Facades\DataTables;

class UserDeviceApiController extends Controller
{
    public function getUserDevices()
    {
        $assignments = DeviceAssignment::query()
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->with(['device.deviceType']); // Eager load relasi

        // Langsung kirim data mentah ke DataTables
        return DataTables::of($assignments)->make(true);
    }
}
