<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeviceAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil beberapa device dan user
        $devices = Device::all();
        $users = User::all();

        // Assign device pertama ke admin
        DeviceAssignment::create([
            'id' => Str::uuid(),
            'user_id' => $users->where('username', 'mugni77')->first()->id,
            'device_id' => $devices[0]->id,
            'assignment_date' => now()->subDays(7),
            'is_active' => true,
            'notes' => 'Device diaktifkan melalui seeder',
        ]);

        // Assign device kedua ke lucky10
        DeviceAssignment::create([
            'id' => Str::uuid(),
            'user_id' => $users->where('username', 'lucky10')->first()->id,
            'device_id' => $devices[1]->id,
            'assignment_date' => now()->subDays(5),
            'is_active' => true,
            'notes' => 'Device untuk user lucky10',
        ]);

        // Assign device keempat ke rama123
        DeviceAssignment::create([
            'id' => Str::uuid(),
            'user_id' => $users->where('username', 'rama123')->first()->id,
            'device_id' => $devices[3]->id,
            'assignment_date' => now()->subDays(3),
            'is_active' => false,
            'notes' => 'Device tidak aktif untuk rama123',
        ]);

        $this->command->info('DeviceAssignmentSeeder berhasil dijalankan!');
        $this->command->info('Total Device Assignments: ' . DeviceAssignment::count());
    }
}
