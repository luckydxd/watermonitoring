<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateDeviceStatus extends Command
{
    protected $signature = 'devices:update-status';
    protected $description = 'Updates device statuses to active or inactive based on operational criteria.';

    public function handle()
    {
        $this->info("Memulai pengecekan status perangkat...");
        $offlineThresholdMinutes = 15;
        $cutoffTime = Carbon::now()->subMinutes($offlineThresholdMinutes);

        // --- PROSES 1: MENONAKTIFKAN PERANGKAT YANG SEHARUSNYA OFFLINE ---
        // Cari perangkat yang statusnya 'active' TAPI:
        // 1. last_seen_at nya lebih lama dari batas waktu, ATAU
        // 2. Tidak punya penugasan yang aktif sama sekali.
        $devicesToDeactivateQuery = Device::query()
            ->where('status', 'active')
            ->where(function ($query) use ($cutoffTime) {
                $query->where('last_seen_at', '<', $cutoffTime)
                    ->orWhereNull('last_seen_at') // Juga nonaktifkan jika belum pernah kirim data
                    ->orWhereDoesntHave('activeAssignment'); // Cek relasi ke device_assignment
            });

        $deactivatedCount = $devicesToDeactivateQuery->count();
        if ($deactivatedCount > 0) {
            $devicesToDeactivateQuery->update(['status' => 'inactive']);
            $message = "Selesai. Menonaktifkan {$deactivatedCount} perangkat.";
            $this->info($message);
            Log::info($message);
        } else {
            $this->info("Tidak ada perangkat yang perlu dinonaktifkan.");
        }

        // --- PROSES 2: MENGAKTIFKAN KEMBALI PERANGKAT YANG SUDAH ONLINE ---
        // Cari perangkat yang statusnya 'inactive' TAPI SEKARANG memenuhi SEMUA syarat untuk aktif:
        // 1. Punya penugasan aktif
        // 2. last_seen_at nya ada
        // 3. last_seen_at nya DALAM rentang waktu 15 menit
        $devicesToActivateQuery = Device::query()
            ->where('status', 'inactive')
            ->whereHas('activeAssignment') // Harus punya penugasan aktif
            ->whereNotNull('last_seen_at') // Harus sudah pernah kirim data
            ->where('last_seen_at', '>=', $cutoffTime); // Harus online dalam 15 menit terakhir

        $activatedCount = $devicesToActivateQuery->count();
        if ($activatedCount > 0) {
            $devicesToActivateQuery->update(['status' => 'active']);
            $message = "Selesai. Mengaktifkan kembali {$activatedCount} perangkat.";
            $this->info($message);
            Log::info($message);
        } else {
            $this->info("Tidak ada perangkat yang perlu diaktifkan kembali.");
        }

        $this->info("Pengecekan status perangkat selesai.");
        return 0;
    }
}
