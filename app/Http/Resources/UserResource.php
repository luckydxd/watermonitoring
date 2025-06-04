<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'role' => $this->getRoleNames()->first(),
            'email' => $this->email,
            'name' => optional($this->userData)->name,
            'address' => optional($this->userData)->address,
            'phone_number' => optional($this->userData)->phone_number,
            'image' => $this->userData ? asset('storage/' . $this->userData->image) : null,
            'isActive' => $this->is_active,
        ];
    }
}
