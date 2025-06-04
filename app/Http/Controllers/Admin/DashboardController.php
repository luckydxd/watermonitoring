<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WaterConsumptionLog;
use App\Models\Complaint;
use App\Models\Device;
use App\Models\VisitorActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;



class DashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        // Format tanggal
        $tanggalHariIni = now()->translatedFormat('l, d F Y');
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();

        $todayUsage = WaterConsumptionLog::whereDate('date', Carbon::today())
            ->sum('total_consumption');

        $totalComplaints = Complaint::where('status', 'pending')->count(); // Statistik baru

        // Total Device Active
        $activeDevices = Device::where('status', 'active')->count();


        // Visitor Line Chart //
        $activities = VisitorActivity::orderBy('date', 'asc')
            ->limit(14)
            ->get();


        $chartData = [
            'dates' => $activities->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('j/n');
            }),
            'visitors' => $activities->pluck('visitors'),
            'contact_clicks' => $activities->pluck('contact_clicks'),
            'login_clicks' => $activities->pluck('login_clicks'),
            'download_clicks' => $activities->pluck('download_clicks')
        ];

        // Donut Chart
        $complaintRaw = Complaint::selectRaw('LOWER(status) as status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => $item->total]);

        $deviceRaw = Device::selectRaw('LOWER(status) as status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => $item->total]);

        $defaultComplaintStatuses = ['pending', 'processed', 'resolved', 'rejected'];
        $defaultDeviceStatuses = ['active', 'inactive', 'error'];

        // Tambahkan key yang tidak ada, isi dengan 0
        $complaintStatusCounts = collect($defaultComplaintStatuses)
            ->mapWithKeys(fn($status) => [$status => $complaintRaw[$status] ?? 0]);

        $deviceStatusCounts = collect($defaultDeviceStatuses)
            ->mapWithKeys(fn($status) => [$status => $deviceRaw[$status] ?? 0]);


        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'activeDevices',
            'totalComplaints',
            'tanggalHariIni',
            'chartData',
            'complaintStatusCounts',
            'deviceStatusCounts'

        ));
    }

    public function getChartData(Request $request)
    {
        $range = $request->get('range');
        $query = VisitorActivity::query();

        switch ($range) {
            case 'today':
                $query->whereDate('date', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('date', Carbon::yesterday());
                break;
            case 'last7':
                $query->whereBetween('date', [Carbon::now()->subDays(6), Carbon::now()]);
                break;
            case 'last30':
                $query->whereBetween('date', [Carbon::now()->subDays(29), Carbon::now()]);
                break;
            case 'thisMonth':
                $query->whereMonth('date', Carbon::now()->month);
                break;
            case 'lastMonth':
                $query->whereMonth('date', Carbon::now()->subMonth()->month);
                break;
            default:
                $query->limit(14); // default fallback
        }

        $activities = $query->orderBy('date', 'asc')->get();

        return response()->json([
            'dates' => $activities->pluck('date')->map(fn($d) => Carbon::parse($d)->format('j/n')),
            'series' => [
                ['name' => 'Pengunjung', 'data' => $activities->pluck('visitors')],
                ['name' => 'Klik Kontak', 'data' => $activities->pluck('contact_clicks')],
                ['name' => 'Klik Login', 'data' => $activities->pluck('login_clicks')],
                ['name' => 'Klik Download', 'data' => $activities->pluck('download_clicks')],
            ],
        ]);
    }
}
