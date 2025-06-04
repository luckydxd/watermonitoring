<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        // Get authenticated user with userData relationship
        $user = Auth::user()->load('userData');

        return view('admin.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Update user
        $user->update([
            'email' => $request->email
        ]);

        // Handle image upload
        $userData = [
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number
        ];

        // Di ProfileController.php
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->userData && $user->userData->image) {
                Storage::delete('public/profile_images/' . basename($user->userData->image));
            }

            $path = $request->file('image')->store('public/profile_images');
            $userData['image'] = str_replace('public/', '', $path); // Simpan path tanpa 'public/'
        }

        // Update or create user data
        $user->userData()->updateOrCreate(
            ['user_id' => $user->id],
            $userData
        );

        return back()->with('success', 'Profile updated successfully');
    }
}
