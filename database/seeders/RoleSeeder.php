<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Permissions
        $permissions = [
            'access-admin-dashboard',
            'access-user-dashboard',
            'manage-users',
            'manage-devices',
            'monitor-water-usage',
            'manage-website-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Buat Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $teknisi = Role::firstOrCreate(['name' => 'teknisi']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // Berikan permission ke admin
        $admin->givePermissionTo([
            'access-admin-dashboard',
            'manage-users',
            'manage-devices',
            'monitor-water-usage',
            'manage-website-settings',
        ]);

        // Berikan permission ke teknisi
        $teknisi->givePermissionTo([
            'access-admin-dashboard',
            'manage-devices',
            'monitor-water-usage',
        ]);

        // Berikan permission ke user
        $user->givePermissionTo([
            'access-user-dashboard',
        ]);
    }
}
