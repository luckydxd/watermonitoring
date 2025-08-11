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

        $user->update(['email' => $request->email]);

        $userData = [
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number
        ];

        if ($request->hasFile('image')) {
            if ($user->userData && $user->userData->image) {
                Storage::disk('public')->delete($user->userData->image);
            }

            $newImagePath = $request->file('image')->store('profile_images', 'public');
            $userData['image'] = $newImagePath;
        }

        $user->userData()->updateOrCreate(
            ['user_id' => $user->id],
            $userData
        );

        return back()->with('success', 'Profile updated successfully');
    }

    public function deleteProfileImage(Request $request)
    {
        $user = Auth::user();

        try {
            $userData = $user->userData;

            if ($userData && $userData->image) {
                Storage::disk('public')->delete($userData->image);
                $userData->update(['image' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada foto profil untuk dihapus.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto profil.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
