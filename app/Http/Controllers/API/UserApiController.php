<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;





class UserApiController extends Controller
{
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        if (!$user->hasRole('user')) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya bisa mengubah status dengan role adalah user'
            ], 403);
        }
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pengguna berhasil diubah.',
            'is_active' => $user->is_active,
        ]);
    }

    public function getRoles()
    {
        $roles = Role::all()->pluck('name');
        return response()->json($roles);
    }
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->hasRole('admin')) {
            $users = User::with('userData')->get();
        } elseif ($user && $user->hasRole('teknisi')) {
            $users = User::with('userData')->role('user')->get();
        } else {
            $users = collect();
        }

        return UserResource::collection($users);
    }



    public function show($id)
    {
        $user = User::with('userData')->findOrFail($id);
        return response()->json($user);
    }
    public function store(Request $request)
    {
        $validRoles = Role::all()->pluck('name')->toArray();
        $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:' . implode(',', $validRoles),
        ]);

        $user = User::create([
            'id' => (string) Str::uuid(),
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        UserData::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        return response()->json([
            'message' => 'User berhasil ditambahkan!',
            'user' => new UserResource($user)
        ]);
    }

    public function update(Request $request, $id)
    {
        $validRoles = Role::all()->pluck('name')->toArray();
        $request->validate([
            'username' => 'sometimes|string|unique:users,username,' . $id,
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|string|in:' . implode(',', $validRoles),
            'address' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:20',
            'isActive' => 'sometimes|boolean'
        ]);

        try {
            $user = User::findOrFail($id);

            $user->username = $request->username ?? $user->username;
            $user->email = $request->email ?? $user->email;
            $user->is_active = $request->has('isActive') ? $request->isActive : $user->is_active;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            if ($request->role) {
                $user->syncRoles([$request->role]);
            }

            $userData = $user->userData()->firstOrNew();
            $userData->name = $request->name ?? $userData->name;
            $userData->address = $request->address ?? $userData->address;
            $userData->phone_number = $request->phone_number ?? $userData->phone_number;
            $userData->save();

            return response()->json([
                'message' => 'User berhasil diupdate!',
                'user' => new UserResource($user->load('roles', 'userData'))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengupdate user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->userData()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
