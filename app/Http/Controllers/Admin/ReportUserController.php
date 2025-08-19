<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;


class ReportUserController extends Controller
{
    public function index()
    {
        return view('admin.report-user.index');
    }


    // app/Http/Controllers/ReportUserController.php

    public function datatables(Request $request)
    {
        if ($request->has('branch_id')) {
            // ** DRILL-DOWN LOGIC **
            $data = User::query()
                ->with(['userData', 'roles']) // with() tetap penting untuk performa (hindari N+1 query)
                ->where('branch_id', $request->branch_id);

            return DataTables::of($data)
                // ==========================================================
                // PENDEKATAN BARU: DEFINISIKAN SETIAP KOLOM SECARA EKSPLISIT
                // ==========================================================
                ->addColumn('full_name', function ($user) {
                    return optional($user->userData)->name ?? 'N/A';
                })
                ->addColumn('address', function ($user) {
                    return optional($user->userData)->address ?? '-';
                })
                ->addColumn('role_name', function ($user) {
                    // Ambil nama peran pertama, jika ada
                    $role = $user->roles->first();
                    return $role ? ucfirst($role->name) : '-';
                })
                ->toJson(); // Tetap gunakan toJson()

        } else {
            // ** ROLL-UP LOGIC ** (Tidak perlu diubah)
            $data = Branch::query()
                ->leftJoin('users', 'branches.id', '=', 'users.branch_id')
                ->select(
                    'branches.id',
                    'branches.name',
                    DB::raw('COUNT(users.id) as total_users'),
                    DB::raw('SUM(CASE WHEN users.is_active = 1 THEN 1 ELSE 0 END) as active_users'),
                    DB::raw('SUM(CASE WHEN users.is_active = 0 THEN 1 ELSE 0 END) as inactive_users')
                )
                ->groupBy('branches.id', 'branches.name');

            return DataTables::of($data)->make(true);
        }
    }
}
