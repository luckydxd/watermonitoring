<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitorManagementController extends Controller
{
    public function index()
    {
        return view('admin.monitor.index');
    }
}
