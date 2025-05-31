<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;


class ReportUserController extends Controller
{
    public function index()
    {
        return view('admin.report-user.index');
    }

    public function datatables(Request $request)
    {

        $data = User::query()->with(['userData', 'roles']);
        return DataTables::of($data)->make(true);
    }
}
