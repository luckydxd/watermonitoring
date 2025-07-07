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

        // DIUBAH: Mengambil semua ID perangkat aktif yang relevan untuk mendukung multi-perangkat
        $activeDeviceIds = $user->deviceAssignments()
            ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_assignments.is_active', true)
            ->where('device_types.name', 'Flow and Pressure Unit') // Pastikan nama ini sesuai
            ->pluck('device_assignments.device_id')
            ->toArray();

        if (empty($activeDeviceIds)) {
            $this->totalUsage = 0;
            return;
        }

        // --- LOGIKA BARU YANG BENAR UNTUK MENGHITUNG PEMAKAIAN HARI INI ---

        // 1. Dapatkan pembacaan meteran TERAKHIR dari HARI INI
        $todayLatestReading = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
            ->whereDate('measured_at', Carbon::today())
            ->max('volume');

        $dailyConsumption = 0;

        // Hanya lanjutkan jika ada data hari ini
        if (!is_null($todayLatestReading)) {
            // 2. Dapatkan pembacaan meteran TERAKHIR dari KEMARIN
            $yesterdayLatestReading = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
                ->whereDate('measured_at', Carbon::yesterday())
                ->max('volume');

            if (!is_null($yesterdayLatestReading)) {
                // 3. Hitung selisihnya (logika utama)
                $dailyConsumption = $todayLatestReading - $yesterdayLatestReading;
            } else {
                // 4. FALLBACK: Jika tidak ada data kemarin (misal hari pertama penggunaan)
                // Gunakan logika MAX - MIN untuk hari ini saja
                $todayEarliestReading = FlowPressureSensor::whereIn('device_id', $activeDeviceIds)
                    ->whereDate('measured_at', Carbon::today())
                    ->min('volume');

                $dailyConsumption = $todayLatestReading - $todayEarliestReading;
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
