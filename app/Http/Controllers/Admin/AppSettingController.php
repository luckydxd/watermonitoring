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
        // 1. Validasi semua input sesuai dengan form dan database baru
        $validatedData = $request->validate([
            'name_app'        => 'required|string|max:255',
            'desc'            => 'nullable|string',
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'secondary_logo'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'app_mockup'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Mockup biasanya tidak svg
            'address'         => 'nullable|string',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:20',
            'whatsapp'        => 'nullable|string|max:20',
            'instagram'       => 'nullable|string|max:255',
            'youtube'         => 'nullable|url|max:255',
            'gmap_coordinat'  => 'nullable|string|max:255',
            'price_per_liter'  => 'nullable|string|max:100',
        ]);

        // 2. Ambil data setting yang ada, atau buat instance baru jika belum ada
        $settings = AppSetting::firstOrNew();

        // 3. Handle semua file upload secara efisien
        $fileFields = ['logo', 'secondary_logo', 'app_mockup'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Hapus file lama jika ada
                if ($settings->{$field}) {
                    Storage::disk('public')->delete($settings->{$field});
                }
                // Simpan file baru dan tambahkan path-nya ke data yang akan disimpan
                $validatedData[$field] = $request->file($field)->store('settings', 'public');
            }
        }

        // 4. Isi model dengan semua data yang sudah divalidasi dan di-handle
        $settings->fill($validatedData);

        // 5. Simpan perubahan ke database
        $settings->save();

        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan berhasil diperbarui.');
    }


    // Role Management

    // In RoleManagementController


    public function roles()
    {
        $roles = Role::with(['users.userData', 'permissions'])->get();
        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('-', $item->name)[0]; // Group by permission prefix
        });

        return view('admin.settings.roles', compact('roles', 'permissions'));
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id' // Validasi ini tetap penting
        ]);

        $role = Role::findOrFail($request->role_id);

        // Ambil array ID permission dari request, default ke array kosong jika tidak ada
        $permissionIds = $request->input('permissions', []);

        // --- BAGIAN PERBAIKAN UTAMA ---
        // 1. Cari NAMA permission berdasarkan ID yang diterima dari form.
        $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();

        // 2. Lakukan sinkronisasi menggunakan NAMA permission, bukan ID.
        //    Ini adalah cara yang paling andal dan bebas ambiguitas untuk Spatie.
        $role->syncPermissions($permissions);
        // --- AKHIR PERBAIKAN ---

        return response()->json([
            'success' => true,
            'message' => 'Hak akses untuk role "' . $role->name . '" berhasil diperbarui.'
        ]);
    }
}
