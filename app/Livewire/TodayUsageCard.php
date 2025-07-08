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

        // --- PERBAIKAN #1: Ambil data assignment lengkap, bukan hanya ID ---
        $activeAssignment = $user->deviceAssignments()
            ->join('devices', 'device_assignments.device_id', '=', 'devices.id')
            ->join('device_types', 'devices.device_type_id', '=', 'device_types.id')
            ->where('device_assignments.is_active', true)
            ->where('device_types.name', 'Flow and Pressure Unit')
            ->select(
                'device_assignments.device_id',
                'device_assignments.initial_meter_reading',
                'device_assignments.created_at as assignment_date' // Ambil tanggal assignment
            )
            ->first();

        if (!$activeAssignment) {
            $this->totalUsage = 0;
            return;
        }

        $deviceId = $activeAssignment->device_id;
        $initialMeterReading = (float) $activeAssignment->initial_meter_reading;
        $assignmentDate = Carbon::parse($activeAssignment->assignment_date);

        // --- LOGIKA UTAMA (TIDAK BERUBAH BANYAK) ---

        // Dapatkan pembacaan meteran TERAKHIR dari HARI INI
        $todayLatestReading = FlowPressureSensor::where('device_id', $deviceId)
            ->whereDate('measured_at', Carbon::today())
            ->max('volume');

        $dailyConsumption = 0;

        // Hanya lanjutkan jika ada data hari ini
        if (!is_null($todayLatestReading)) {
            // Dapatkan pembacaan meteran TERAKHIR dari KEMARIN
            $yesterdayLatestReading = FlowPressureSensor::where('device_id', $deviceId)
                ->whereDate('measured_at', Carbon::yesterday())
                ->max('volume');

            if (!is_null($yesterdayLatestReading)) {
                // LOGIKA NORMAL: Jika ada data kemarin, hitung selisihnya
                $dailyConsumption = $todayLatestReading - $yesterdayLatestReading;
            } else {
                // --- PERBAIKAN #2: LOGIKA FALLBACK YANG LEBIH CERDAS ---
                // Cek apakah hari ini adalah hari pertama alat ditugaskan
                if (Carbon::today()->isSameDay($assignmentDate)) {
                    // Jika YA, pemakaian adalah MAX hari ini - METERAN AWAL
                    $dailyConsumption = $todayLatestReading - $initialMeterReading;
                } else {
                    // Jika BUKAN hari pertama (tapi kemarin kosong), gunakan MAX - MIN hari ini
                    $todayEarliestReading = FlowPressureSensor::where('device_id', $deviceId)
                        ->whereDate('measured_at', Carbon::today())
                        ->min('volume');
                    $dailyConsumption = $todayLatestReading - $todayEarliestReading;
                }
            }
        }

        // Pastikan hasil tidak negatif dan format angka
        $this->totalUsage = number_format(max(0, $dailyConsumption), 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.today-usage-card', [
            'userName' => explode(' ', auth()->user()->name)[0]
        ]);
    }
}
