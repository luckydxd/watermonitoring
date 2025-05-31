<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;

class ReportComplaintController extends Controller
{
    public function index()
    {
        return view('admin.report-complaint.index');
    }
    public function datatables()
    {
        $data = Complaint::query()->with(['user.userData']);
        return DataTables::of($data)->make(true);
    }
}
