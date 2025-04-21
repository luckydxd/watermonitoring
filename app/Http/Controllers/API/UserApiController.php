<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;


class UserApiController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::with('userData')->get());
    }

    public function show($id)
    {
        $user = User::with('userData')->findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'name' => 'required|string|max:255',
            'role' => 'required|string',
        ]);

        // Simpan ke tabel users
        $user = User::create([
            'id' => (string) Str::uuid(),
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Beri role menggunakan Spatie
        $user->assignRole($request->role);

        // Simpan ke tabel user_datas
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
        $user = User::findOrFail($id);
        $userData = $user->userData;

        $user->update([
            'username' => $request->username ?? $user->username,
            'email'    => $request->email ?? $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        $userData->update([
            'name'         => $request->name ?? $userData->name,
            'address'      => $request->address ?? $userData->address,
            'phone_number' => $request->phone_number ?? $userData->phone_number,
            'image'        => $request->image ?? $userData->image,
        ]);

        return response()->json(['message' => 'User updated successfully.']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->userData()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
