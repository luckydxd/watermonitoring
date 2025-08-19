<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserDetailResource;
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
        // 1. Otorisasi: Apakah user ini boleh melihat halaman daftar user?
        // Jika tidak, Laravel akan otomatis melempar error 403 Forbidden.
        $this->authorize('viewAny', User::class);

        $currentUser = Auth::user();

        $query = User::with(['userData', 'branch']);

        if ($currentUser->hasRole('teknisi')) {
            $query->role('user')->where('branch_id', $currentUser->branch_id);
        }

        $users = $query->get();

        return UserResource::collection($users);
    }

    public function show($id)
    {
        try {
            $user = User::with(['roles', 'userData', 'deviceAssignments.device.deviceType'])->findOrFail($id);

            $data = [
                'id' => $user->id,
                'role' => $user->getRoleNames()->first() ?? 'User',
                'email' => $user->email,
                'name' => optional($user->userData)->name ?? $user->name,
                'address' => optional($user->userData)->address ?? '-',
                'phone_number' => optional($user->userData)->phone_number ?? '-',

                'image' => $user->userData && $user->userData->image ? asset('storage/' . $user->userData->image) : null,

                'isActive' => $user->is_active,
                'created_at' => $user->created_at,

                'devices' => $user->deviceAssignments->map(function ($assignment) {
                    return [
                        'device_unique_id' => optional($assignment->device)->unique_id ?? 'N/A',
                        'device_type' => optional(optional($assignment->device)->deviceType)->name ?? 'N/A',
                        'created_at' => optional($assignment->created_at)->format('d M Y') ?? '-',
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }
    }
    public function store(Request $request)
    {

        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'phone_number' => [
                'nullable',
                'string',
                'regex:/^(\+62|0)8[1-9][0-9]{8,12}$/'
            ],
            'image' => 'nullable|string',
        ]);

        $roleToAssign = $request->has('role') ? $request->role : 'user';

        if ($request->has('role')) {
            $validAdminRoles = Role::all()->pluck('name')->toArray();
            if (!in_array($roleToAssign, $validAdminRoles)) {
                $roleToAssign = 'user';
            }
        }


        $user = User::create([
            'id' => (string) Str::uuid(),
            'email' => $request->email,
            'password' => Hash::make($request->password), // Default to active
            'branch_id' => $request->branch_id,
        ]);

        $user->assignRole($roleToAssign);

        $token = JWTAuth::fromUser($user);

        UserData::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => $request->name,
            'address' => $request->address ?? null,
            'phone_number' => $request->phone_number ?? null,
            'image' => $request->image ?? null,

        ]);

        return response()->json([
            'message' => 'User berhasil ditambahkan!',
            'user' => $user->load('roles'),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function edit($id)
    {
        $user = User::with('roles', 'userData', 'branch')->findOrFail($id);
        return response()->json($user);
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
            'branch_id' => 'sometimes|exists:branches,id',
            'phone_number' => [
                'nullable',
                'string',
                'regex:/^(\+62|0)8[1-9][0-9]{8,12}$/'
            ],
            'isActive' => 'sometimes|boolean'
        ]);

        try {
            $user = User::findOrFail($id);

            $user->email = $request->email ?? $user->email;
            $user->is_active = $request->has('isActive') ? $request->isActive : $user->is_active;
            $user->branch_id = $request->branch_id ?? $user->branch_id;

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

            return response()->json($user->load('roles', 'userData'));
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
        $userData = $user->userData()->firstOrNew([]);

        if ($request->has('name')) {
            $userData->name = $request->name;
        }
        if ($request->has('address')) {
            $userData->address = $request->address;
        }
        if ($request->has('phone_number')) {
            $userData->phone_number = $request->phone_number;
        }

        if ($request->hasFile('image')) {
            if ($userData->image) {
                Storage::delete('public/' . $userData->image);
            }

            $path = $request->file('image')->store('profile_images', 'public');
            $userData->image = $path;
        }

        $userData->user_id = $user->id;
        $userData->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'image_url' => asset('storage/' . $userData->image)
        ]);
    }
}
