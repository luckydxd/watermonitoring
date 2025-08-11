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
        $roles = Role::withCount('users')->with(['users.userData'])->get();
        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('-', $item->name)[0];
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
        $validatedData = $request->validate([
            'name_app'        => 'required|string|max:255',
            'desc'            => 'nullable|string',
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'secondary_logo'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'app_mockup'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'address'         => 'nullable|string',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:20',
            'whatsapp'        => 'nullable|string|max:20',
            'instagram'       => 'nullable|string|max:255',
            'youtube'         => 'nullable|url|max:255',
            'gmap_coordinat'  => 'nullable|string|max:255',
            'price_per_liter'  => 'nullable|string|max:100',
        ]);

        $settings = AppSetting::firstOrNew();

        $fileFields = ['logo', 'secondary_logo', 'app_mockup'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                if ($settings->{$field}) {
                    Storage::disk('public')->delete($settings->{$field});
                }
                $validatedData[$field] = $request->file($field)->store('settings', 'public');
            }
        }

        $settings->fill($validatedData);

        $settings->save();

        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan berhasil diperbarui.');
    }





    public function roles()
    {
        $roles = Role::with(['users.userData', 'permissions'])->get();
        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('-', $item->name)[0];
        });

        return view('admin.settings.roles', compact('roles', 'permissions'));
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::findOrFail($request->role_id);

        $permissionIds = $request->input('permissions', []);

        $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();

        $role->syncPermissions($permissions);

        return response()->json([
            'success' => true,
            'message' => 'Hak akses untuk role "' . $role->name . '" berhasil diperbarui.'
        ]);
    }
}
