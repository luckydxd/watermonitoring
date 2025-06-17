<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AppSettingController extends Controller
{
    public function index()
    {
        return view('admin.app-settings.index');
    }

    public function edit()
    {
        $settings = AppSetting::firstOrNew();
        $roles = Role::with(['users', 'permissions'])->get();
        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('-', $item->name)[0]; // Group by permission prefix
        });

        return view('admin.app-setting.index', [
            'settings' => $settings,
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissions' => $roles->mapWithKeys(function ($role) {
                return [$role->id => $role->permissions->pluck('id')->toArray()];
            })
        ]);
    }


    public function update(Request $request)
    {
        $request->validate([
            'name_app' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'secondary_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'no_contact' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'instagram' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'gmap_coordinat' => 'nullable|string',
        ]);

        $settings = AppSetting::firstOrNew();

        if ($request->has('role_permissions')) {
            foreach ($request->role_permissions as $roleId => $permissions) {
                $role = Role::findById($roleId);
                $role->syncPermissions($permissions);
            }
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo) {
                Storage::delete('public/' . $settings->logo);
            }
            $path = $request->file('logo')->store('settings', 'public');
            $settings->logo = $path;
        }

        // Handle secondary logo upload
        if ($request->hasFile('secondary_logo')) {
            // Delete old secondary logo if exists
            if ($settings->secondary_logo) {
                Storage::delete('public/' . $settings->secondary_logo);
            }
            $path = $request->file('secondary_logo')->store('settings', 'public');
            $settings->secondary_logo = $path;
        }

        $settings->name_app = $request->name_app;
        $settings->desc = $request->desc;
        $settings->no_contact = $request->no_contact;
        $settings->email = $request->email;
        $settings->instagram = $request->instagram;
        $settings->alamat = $request->alamat;
        $settings->gmap_coordinat = $request->gmap_coordinat;
        $settings->save();

        return redirect()->route('admin.settings.edit')->with('success', 'Settings updated successfully');
    }

    // Role Management

    // In RoleManagementController


    public function roles()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('-', $item->name)[0]; // Group by permission prefix
        });

        return view('admin.settings.roles', compact('roles', 'permissions'));
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::findOrFail($request->role_id);
        $role->syncPermissions($request->permissions ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Role permissions updated successfully'
        ]);
    }
}
