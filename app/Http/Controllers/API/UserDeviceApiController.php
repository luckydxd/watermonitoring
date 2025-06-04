<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Yajra\DataTables\Facades\DataTables;

class UserDeviceApiController extends Controller
{
    public function getUserDevices()
    {
        $devices = Device::query()
            ->whereHas('deviceAssignments', function ($query) {
                $query->where('user_id', auth()->id())
                    ->where('is_active', true);
            })
            ->with(['deviceType']);

        return DataTables::of($devices)->make(true);
    }
}
