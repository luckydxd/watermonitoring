<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WaterConsumptionLog;
use App\Models\FlowPressureSensor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TodayUsageCard extends Component
{
    public $totalUsage = 0;

    public function mount()
    {
        $this->loadTodayUsage();
    }

    public function loadTodayUsage()
    {
        $user = Auth::user();
        if (!$user) {
            $this->totalUsage = 0;
            return;
        }

        // Langkah 1: Dapatkan ID perangkat aktif tipe 'Flow and Pressure Unit' milik pengguna
        $activeAssignment = $user->deviceAssignments()
            ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_assignments.is_active', true)
            ->where('device_types.name', 'Flow and Pressure Unit')
            ->select('device_assignments.device_id')
            ->first();

        // Jika pengguna tidak memiliki perangkat yang relevan, pemakaian adalah 0
        if (!$activeAssignment) {
            $this->totalUsage = 0;
            return;
        }

        $deviceId = $activeAssignment->device_id;

        // Langkah 2: Hitung pemakaian untuk hari ini menggunakan MAX(volume) - MIN(volume)
        $dailyReadings = FlowPressureSensor::where('device_id', $deviceId)
            ->whereDate('measured_at', Carbon::today()) // Filter hanya untuk hari ini
            ->select(
                DB::raw('MAX(volume) as max_vol'),
                DB::raw('MIN(volume) as min_vol')
            )
            ->first();

        $dailyConsumption = 0;
        // Pastikan ada data dan kedua nilai (max dan min) tidak null
        if ($dailyReadings && !is_null($dailyReadings->max_vol) && !is_null($dailyReadings->min_vol)) {
            $dailyConsumption = $dailyReadings->max_vol - $dailyReadings->min_vol;
        }

        // Langkah 3: Set properti dengan nilai yang sudah diformat
        $this->totalUsage = number_format($dailyConsumption, 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.today-usage-card', [
            'userName' => explode(' ', auth()->user()->name)[0]
        ]);
    }
}
