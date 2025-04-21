<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserData;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Data seed pengguna
        $users = [
            [
                'username' => 'admin123',
                'email' => 'admin@gmail.com',
                'password' => 'Admin#123',
                'name' => 'Admin',
                'address' => 'Perumahan Graha Panyindangan No.A8',
                'phone_number' => '0895345990294',
                'role' => 'admin',
            ],
            [
                'username' => 'lucky10',
                'email' => 'lucky@gmail.com',
                'password' => 'Lucky#123',
                'name' => 'Lucky D.',
                'address' => 'Kembang Street No. 10, Whiterun Avenue',
                'phone_number' => '089534598294',
                'role' => 'user',
            ],
            [
                'username' => 'rama123',
                'email' => 'rama@gmail.com',
                'password' => 'Rama#123',
                'name' => 'Rama',
                'address' => 'Perumahan Graha Panyindangan No.A9',
                'phone_number' => '089555230294',
                'role' => 'user',
            ],
            [
                'username' => 'mugni77',
                'email' => 'mugni@gmail.com',
                'password' => 'Mugni#123',
                'name' => 'Mugni',
                'address' => 'Perumahan Graha Panyindangan No.A10',
                'phone_number' => '089555230999',
                'role' => 'user',
            ],
        ];

        foreach ($users as $u) {
            $uuid = Str::uuid(); // generate UUID

            $user = User::create([
                'id' => $uuid,
                'username' => $u['username'],
                'email' => $u['email'],
                'password' => Hash::make($u['password']),
                'is_active' => true,
            ]);

            // Assign role
            $user->assignRole($u['role']);

            // Buat data user terkait
            UserData::create([
                'id' => Str::uuid(),
                'user_id' => $uuid,
                'name' => $u['name'],
                'address' => $u['address'],
                'phone_number' => $u['phone_number'],
                'image' => null,
            ]);
        }
    }
}
