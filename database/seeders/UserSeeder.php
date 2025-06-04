<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserData;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => 'Admin#123',
                'address' => 'Perumahan Graha Panyindangan No.A8',
                'phone_number' => '0895345990394',
                'role' => 'admin',
            ],
            [
                'name' => 'Teknisi',
                'email' => 'teknisi@dummy.com',
                'password' => 'Teknisi#123',
                'address' => 'Perumahan Graha Panyindangan No.A19',
                'phone_number' => '0895345990894',
                'role' => 'teknisi',
            ],
            [
                'name' => 'Lucky D.',
                'email' => 'lucky@dummy.com',
                'password' => 'Lucky#123',
                'address' => 'Kembang Street No. 10, Whiterun Avenue',
                'phone_number' => '089534598294',
                'role' => 'user',
            ],
            [
                'name' => 'Rama',
                'email' => 'rama@dummy.com',
                'password' => 'Rama#123',
                'address' => 'Perumahan Graha Panyindangan No.A9',
                'phone_number' => '089555230294',
                'role' => 'user',
            ],
            [
                'name' => 'Mugni',
                'email' => 'mugni@dummy.com',
                'password' => 'Mugni#123',
                'address' => 'Perumahan Graha Panyindangan No.A10',
                'phone_number' => '089555230999',
                'role' => 'user',
            ],
        ];

        foreach ($users as $u) {
            $uuid = Str::uuid();

            $user = User::create([
                'id' => $uuid,
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

            // Generate JWT token for the user
            $token = JWTAuth::fromUser($user);

            // Store the token if needed (optional)
            $user->jwt_token = $token;
            $user->save();
        }
    }
}
