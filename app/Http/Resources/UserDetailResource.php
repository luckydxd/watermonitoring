<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
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
            'name' => $this->userData->name ?? $this->name,
            'email' => $this->email,
            'phone_number' => $this->userData->phone_number ?? '-',
            'address' => $this->userData->address ?? '-',
            'image_url' => $this->userData && $this->userData->image ? asset('storage/' . $this->userData->image) : null,
            'status' => $this->status,
            'role' => $this->getRoleNames()->first() ?? 'User',
            'created_at' => $this->created_at,

            // Menambahkan data perangkat yang terhubung
            'devices' => $this->whenLoaded('deviceAssignments', function () {
                return $this->deviceAssignments->map(function ($assignment) {
                    return [
                        'device_unique_id' => $assignment->device->unique_id,
                        'device_type' => $assignment->device->deviceType->name ?? 'N/A',
                        'assignment_date' => $assignment->assignment_date->format('d M Y'),
                    ];
                });
            }),
        ];
    }
}
