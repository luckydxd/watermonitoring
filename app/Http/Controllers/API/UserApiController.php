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
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;





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
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:' . implode(',', $validRoles),
        ]);

        $user = User::create([
            'id' => (string) Str::uuid(),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);
        $token = JWTAuth::fromUser($user);

        UserData::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        return response()->json([

            'message' => 'User berhasil ditambahkan!',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $validRoles = Role::all()->pluck('name')->toArray();
        $request->validate([
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


    // ----------------- MOBILE --------------
    public function getProfile($userId)
    {
        try {
            $user = User::with('userData')->findOrFail($userId);

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $user->userData->name,
                    'email' => $user->email,
                    'address' => $user->userData->address,
                    'phone_number' => $user->userData->phone_number,
                    'image_url' => $user->userData->image ? asset('storage/' . $user->userData->image) : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found'
            ], 404);
        }
    }

    public function updateProfile(Request $request, $userId)
    {
        $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'address' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::findOrFail($userId);
        $userData = $user->userData;

        if ($request->has('name')) {
            $userData->name = $request->name;
        }

        // Update other fields similarly...

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($userData->image) {
                Storage::delete('public/' . $userData->image);
            }

            $path = $request->file('image')->store('profile_images', 'public');
            $userData->image = $path;
        }

        $userData->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'image_url' => asset('storage/' . $userData->image)
        ]);
    }
}
