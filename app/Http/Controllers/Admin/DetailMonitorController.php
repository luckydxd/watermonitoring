<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DetailMonitorController extends Controller
{
    public function index($id)
    {
        $user = User::with([
            'waterConsumptionLogs' => function ($q) {
                $q->orderBy('date', 'desc');
            },
            'deviceAssignments.device'
        ])
            ->findOrFail($id);

        return view('admin.detail-monitor.index', compact('user'));
    }
}
