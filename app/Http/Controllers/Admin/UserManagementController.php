<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Branch;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index()
    {
        $roles = Role::all()->pluck('name');
        $branches = Branch::all();



        $totalUsers = User::count();
        // $totalUsersOnly = User::role('user')->count();

        $currentUser = Auth::user();
        $totalUsersOnly = User::role('user')
            ->where('branch_id', $currentUser->branch_id)
            ->count();


        $activeUsers = User::where('is_active', 1)->count();
        // $activeUsersOnly = User::where('is_active', 1)->role('user')->count();
        $currentUser = Auth::user();

        // Menghitung pengguna aktif dengan role 'user' (pelanggan)
        // HANYA dari cabang tempat teknisi bertugas.
        $activeUsersOnly = User::role('user')
            ->where('is_active', 1)
            ->where('branch_id', $currentUser->branch_id)
            ->count();

        $lastMonth = Carbon::now()->subMonth();
        $lastMonthActive = User::where('is_active', 1)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $lastMonthActiveOnly = User::where('is_active', 1)
            ->role('user')
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $growth = $lastMonthActive > 0
            ? round((($activeUsers - $lastMonthActive) / $lastMonthActive) * 100, 2)
            : 0;
        // Registered This Month
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        // Jumlah user yang terdaftar bulan ini
        $registeredThisMonth = User::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $registeredThisMonthOnly = User::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->role('user')
            ->count();

        // Jumlah user yang terdaftar bulan lalu
        $registeredLastMonth = User::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        // Growth / perubahan persentase
        $registeredGrowth = $registeredLastMonth > 0
            ? round((($registeredThisMonth - $registeredLastMonth) / $registeredLastMonth) * 100, 2)
            : ($registeredThisMonth > 0 ? 100 : 0);
        return view('admin.users.index', compact('roles', 'branches', 'totalUsers', 'activeUsers', 'growth', 'registeredThisMonth', 'registeredLastMonth', 'registeredGrowth', 'totalUsersOnly', 'activeUsersOnly', 'lastMonthActive', 'lastMonthActiveOnly', 'registeredThisMonthOnly'));
    }
}
