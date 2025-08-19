<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;

use Illuminate\Http\Request;

class ReportComplaintController extends Controller
{
    public function index()
    {
        return view('admin.report-complaint.index');
    }
    public function datatables(Request $request)
    {
        if ($request->has('branch_id')) {
            // ** DRILL-DOWN LOGIC: Tampilkan detail keluhan untuk cabang yang dipilih **
            $query = Complaint::query()
                ->whereHas('user', function ($q) use ($request) {
                    $q->where('branch_id', $request->branch_id);
                });
        } else {
            // ** ROLL-UP LOGIC: Tampilkan agregat keluhan per cabang **
            $query = Branch::query()
                ->leftJoin('users', 'branches.id', '=', 'users.branch_id')
                ->leftJoin('complaints', 'users.id', '=', 'complaints.user_id')
                ->select(
                    'branches.id as branch_id',
                    'branches.name as branch_name',
                    DB::raw('COUNT(complaints.id) as total_complaints'),
                    DB::raw("SUM(CASE WHEN complaints.status = 'pending' THEN 1 ELSE 0 END) as pending_complaints"),
                    DB::raw("SUM(CASE WHEN complaints.status = 'processed' THEN 1 ELSE 0 END) as processed_complaints"),
                    DB::raw("SUM(CASE WHEN complaints.status = 'resolved' THEN 1 ELSE 0 END) as resolved_complaints")
                )
                ->groupBy('branches.id', 'branches.name');
        }

        // Terapkan Global Scope untuk teknisi (jika ada)
        // Global scope akan otomatis memfilter hasil akhir jika yang login adalah teknisi
        return DataTables::of($query)
            ->addColumn('user_info', function ($data) {
                // Kolom ini hanya relevan untuk drill-down
                if ($data instanceof Complaint) {
                    return [
                        'name' => optional($data->user->userData)->name ?? 'N/A',
                        'branch_name' => optional($data->user->branch)->name ?? 'N/A',
                    ];
                }
                return null;
            })
            ->toJson();
    }
}
