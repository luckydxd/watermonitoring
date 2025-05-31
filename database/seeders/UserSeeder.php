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
        $users = [
            [
                'username' => 'admin123',
                'email' => 'admin@admin.com',
                'password' => 'Admin#123',
                'name' => 'Admin',
                'address' => 'Perumahan Graha Panyindangan No.A8',
                'phone_number' => '0895345990394',
                'role' => 'admin',
            ],
            [
                'username' => 'teknisi1',
                'email' => 'teknisi@dummy.com',
                'password' => 'Teknisi#123',
                'name' => 'Teknisi',
                'address' => 'Perumahan Graha Panyindangan No.A19',
                'phone_number' => '0895345990894',
                'role' => 'teknisi',
            ],
            [
                'username' => 'lucky10',
                'email' => 'lucky@dummy.com',
                'password' => 'Lucky#123',
                'name' => 'Lucky D.',
                'address' => 'Kembang Street No. 10, Whiterun Avenue',
                'phone_number' => '089534598294',
                'role' => 'user',
            ],
            [
                'username' => 'rama123',
                'email' => 'rama@dummy.com',
                'password' => 'Rama#123',
                'name' => 'Rama',
                'address' => 'Perumahan Graha Panyindangan No.A9',
                'phone_number' => '089555230294',
                'role' => 'user',
            ],
            [
                'username' => 'mugni77',
                'email' => 'mugni@dummy.com',
                'password' => 'Mugni#123',
                'name' => 'Mugni',
                'address' => 'Perumahan Graha Panyindangan No.A10',
                'phone_number' => '089555230999',
                'role' => 'user',
            ],
        ];

        foreach ($users as $u) {
            $uuid = Str::uuid();

            $user = User::create([
                'id' => $uuid,
                'username' => $u['username'],
                'email' => $u['email'],
                'password' => Hash::make($u['password']),
                'is_active' => true,
            ]);

            $user->assignRole($u['role']);

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
