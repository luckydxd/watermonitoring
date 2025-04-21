<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    public function index()
    {


        $totalUsers = User::count();

        $activeUsers = User::where('is_active', 1)->count();
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthActive = User::where('is_active', 1)
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

        // Jumlah user yang terdaftar bulan lalu
        $registeredLastMonth = User::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        // Growth / perubahan persentase
        $registeredGrowth = $registeredLastMonth > 0
            ? round((($registeredThisMonth - $registeredLastMonth) / $registeredLastMonth) * 100, 2)
            : ($registeredThisMonth > 0 ? 100 : 0);
        return view('admin.users.index', compact('totalUsers', 'activeUsers', 'growth', 'registeredThisMonth', 'registeredLastMonth', 'registeredGrowth'));
    }
}
