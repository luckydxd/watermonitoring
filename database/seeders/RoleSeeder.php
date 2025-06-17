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

            // Monitoring
            'view-monitoring',
            'manage-monitoring',

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
        $roles = [
            'admin' => [
                'access-admin-dashboard',
                'view-users',
                'create-users',
                'edit-users',
                'delete-users',
                'toggle-user-status',
                'view-devices',
                'create-devices',
                'edit-devices',
                'delete-devices',
                'view-monitoring',
                'manage-monitoring',
                'view-reports',
                'generate-reports',
                'manage-app-settings',
                'manage-landing-page',
                'manage-roles',
                'manage-permissions',
                'assign-roles'
            ],
            'teknisi' => [
                'access-teknisi-dashboard',
                'view-devices',
                'edit-devices',
                'view-monitoring',
                'manage-monitoring',
                'view-reports'
            ],
            'user' => [
                'access-user-dashboard'
            ]
        ];

        foreach ($roles as $name => $permissions) {
            $role = Role::firstOrCreate(['name' => $name]);
            $role->syncPermissions($permissions);
        }

        // 3. Create a super admin role if needed
        // $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        // $superAdmin->givePermissionTo(Permission::all());
    }
}
