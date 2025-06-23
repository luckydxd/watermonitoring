<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SensorData;
use App\Models\WaterConsumptionLog;
use App\Models\DeviceAssignment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessSensorDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deviceId;
    protected $pressure;
    protected $flowRate;
    protected $ultrasonicDistance;
    protected $turbidity;
    protected $volume;
    protected $timestamp;

    public function __construct(
        string $deviceId,
        float $pressure,
        float $flowRate,
        float $ultrasonicDistance,
        float $turbidity,
        float $volume,
        string $timestamp
    ) {
        $this->deviceId = $deviceId;
        $this->pressure = $pressure;
        $this->flowRate = $flowRate;
        $this->ultrasonicDistance = $ultrasonicDistance;
        $this->turbidity = $turbidity;
        $this->volume = $volume;
        $this->timestamp = $timestamp;
    }

    public function handle(): void
    {
        DB::beginTransaction();
        try {
            SensorData::create([
                'id' => Str::uuid(),
                'device_id' => $this->deviceId,
                'pressure' => $this->pressure,
                'flow_rate' => $this->flowRate,
                'water_level' => $this->ultrasonicDistance,
                'turbidity' => $this->turbidity,
                'timestamp' => $this->timestamp,
            ]);

            $deviceAssignment = DeviceAssignment::where('device_id', $this->deviceId)
                ->where('is_active', true)
                ->first();

            if ($deviceAssignment) {
                $userId = $deviceAssignment->user_id;
                $logDate = \Carbon\Carbon::parse($this->timestamp)->toDateString();

                $consumptionLog = WaterConsumptionLog::firstOrNew([
                    'user_id' => $userId,
                    'date' => $logDate,
                ]);

                $consumptionLog->total_consumption += $this->volume;
                $consumptionLog->save();
                Log::info("Volume " . $this->volume . "L ditambahkan ke log konsumsi user " . $userId . " pada tanggal " . $logDate);
            } else {
                Log::warning("Sensor data received for device ID " . $this->deviceId . " but no active user assignment found.");
            }

            DB::commit();
            Log::info("Sensor data and consumption log processed successfully for device: " . $this->deviceId);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to process sensor data for device " . $this->deviceId . ": " . $e->getMessage());
            $this->fail($e);
        }
    }
}
