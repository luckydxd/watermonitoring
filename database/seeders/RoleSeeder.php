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

        // 1. Create all possible permissions
        $permissions = [
            // Dashboard Access
            'access-admin-dashboard',
            'access-teknisi-dashboard',
            'access-user-dashboard',

            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'toggle-user-status',

            // Device Management
            'view-devices',
            'create-devices',
            'edit-devices',
            'delete-devices',
            'view-own-devices', // BARU: Untuk role user

            // Monitoring
            'view-monitoring',
            'manage-monitoring',

            // Complaints
            'view-complaints', // BARU
            'manage-complaints', // BARU: Untuk membalas atau mengubah status keluhan

            // Usage
            'view-own-usage', // BARU: Untuk role user

            // Reports
            'view-reports',
            'generate-reports',

            // Settings
            'manage-app-settings',
            'manage-landing-page',

            // Role & Permission Management
            'manage-roles',
            'manage-permissions',
            'assign-roles'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create default roles with their permissions
        // Role Admin (semua akses)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all()); // Berikan semua permission kepada admin

        // Role Teknisi
        $teknisiRole = Role::firstOrCreate(['name' => 'teknisi']);
        $teknisiRole->syncPermissions([
            'access-teknisi-dashboard',
            'view-devices',
            'edit-devices',
            'view-monitoring',
            'manage-monitoring',
            'view-reports',
            'view-users', // PENYESUAIAN: Tambahkan ini agar cocok dengan route teknisi.user
            'view-complaints', // PENYESUAIAN: Tambahkan akses untuk melihat keluhan
            'manage-complaints', // PENYESUAIAN: Tambahkan akses untuk mengelola keluhan
        ]);

        // Role User
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'access-user-dashboard',
            'view-own-devices', // PENYESUAIAN: Gunakan permission yang lebih spesifik
            'view-own-usage', // PENYESUAIAN: Tambahkan permission untuk halaman usage
        ]);
    }
}
