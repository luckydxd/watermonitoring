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

        // 1. Dapatkan data penugasan aktif untuk mengambil device_id DAN initial_meter_reading
        $activeAssignment = $user->deviceAssignments()
            ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_assignments.is_active', true)
            ->where('device_types.name', 'Flow and Pressure Unit')
            ->select('device_assignments.device_id', 'device_assignments.initial_meter_reading')
            ->first();

        if (!$activeAssignment) {
            $this->totalUsage = 0;
            return;
        }

        $deviceId = $activeAssignment->device_id;
        $initialMeterReading = (float) $activeAssignment->initial_meter_reading;

        // 2. Dapatkan pembacaan meteran TERAKHIR dari HARI INI
        $todayLatestReading = FlowPressureSensor::where('device_id', $deviceId)
            ->whereDate('measured_at', Carbon::today())
            ->max('volume');

        $dailyConsumption = 0;

        // Hanya lanjutkan jika ada data hari ini
        if (!is_null($todayLatestReading)) {
            // 3. Dapatkan pembacaan meteran TERAKHIR dari KEMARIN untuk titik awal
            $yesterdayLatestReading = FlowPressureSensor::where('device_id', $deviceId)
                ->whereDate('measured_at', Carbon::yesterday())
                ->max('volume');

            if (!is_null($yesterdayLatestReading)) {
                // 4a. LOGIKA NORMAL: Jika ada data kemarin, hitung selisihnya
                $dailyConsumption = $todayLatestReading - $yesterdayLatestReading;
            } else {
                // 4b. LOGIKA FALLBACK (PERBAIKAN): Jika tidak ada data kemarin,
                //     artinya ini adalah hari pertama. Gunakan initial_meter_reading.
                $dailyConsumption = $todayLatestReading - $initialMeterReading;
            }
        }

        // Pastikan hasil tidak negatif (jika meteran direset) dan format angka
        $this->totalUsage = number_format(max(0, $dailyConsumption), 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.today-usage-card', [
            'userName' => explode(' ', auth()->user()->name)[0]
        ]);
    }
}
