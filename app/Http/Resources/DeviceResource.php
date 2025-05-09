<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'unique_id' => $this->unique_id,
            'type' => optional($this->deviceType)->name,
            'type_id' => $this->device_type_id,
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at->format('Y-m-d H:i:s'),

            // Jika perlu menambahkan relasi lain
            'sensorData' => $this->whenLoaded('sensorDatas', function () {
                return $this->sensorDatas->map(function ($data) {
                    return [
                        'timestamp' => $data->timestamp->format('Y-m-d H:i:s'),
                        'pressure' => $data->pressure,
                        'flowRate' => $data->flow_rate,
                        'waterLevel' => $data->water_level,
                        'turbidity' => $data->turbidity
                    ];
                });
            }),

            'assignments' => $this->whenLoaded('deviceAssignments', function () {
                return $this->deviceAssignments->map(function ($assignment) {
                    return [
                        'userId' => $assignment->user_id,
                        'userName' => optional($assignment->user)->username,
                        'assignmentDate' => $assignment->assignment_date->format('Y-m-d'),
                        'isActive' => $assignment->is_active
                    ];
                });
            })
        ];
    }
}
