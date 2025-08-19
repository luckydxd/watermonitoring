<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateDeviceStatus extends Command
{
    protected $signature = 'devices:update-status';

    protected $description = 'Find active devices that are offline for too long and update their status to inactive';

    public function handle()
    {
        $offlineThresholdMinutes = 15;
        $cutoffTime = Carbon::now()->subMinutes($offlineThresholdMinutes);

        $this->info("Memulai pengecekan status perangkat...");
        $this->info("Ambang batas waktu: Perangkat yang tidak aktif sejak " . $cutoffTime->toDateTimeString());

        $devicesToUpdateQuery = Device::query()
            ->where('status', 'active')
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '<', $cutoffTime);

        $count = $devicesToUpdateQuery->count();

        if ($count > 0) {
            $devicesToUpdateQuery->update(['status' => 'inactive']);

            $message = "Selesai. Berhasil mengubah status {$count} perangkat menjadi 'inactive'.";
            $this->info($message);
            Log::info($message);
        } else {
            $this->info("Selesai. Tidak ada perangkat yang perlu diubah statusnya.");
        }

        return 0;
    }
}
