<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class ProfileApiController extends Controller
{
    /**
     * Get user profile
     * @authenticated
     */
    public function getProfile()
    {
        try {
            $user = Auth::user()->load('userData');

            return response()->json([
                'success' => true,
                'data' => $this->formatProfileData($user)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     * @authenticated
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'sometimes|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update user basic info
            if ($request->has('email')) {
                $user->update(['email' => $request->email]);
            }

            // Prepare user data
            $userData = [
                'name' => $request->name ?? $user->userData->name,
                'address' => $request->address ?? $user->userData->address,
                'phone_number' => $request->phone_number ?? $user->userData->phone_number
            ];

            // Handle image upload (base64 or file)
            if ($request->image) {
                $imagePath = $this->handleImageUpload($request->image, $user);
                $userData['image'] = $imagePath;
            }

            // Update or create user data
            $user->userData()->updateOrCreate(
                ['user_id' => $user->id],
                $userData
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $this->formatProfileData($user->fresh()->load('userData')),
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle image upload from mobile (supports base64 and file upload)
     */
    private function handleImageUpload($image, $user)
    {
        // Delete old image if exists
        if ($user->userData && $user->userData->image) {
            Storage::delete('public/profile_images/' . basename($user->userData->image));
        }

        // Handle base64 image
        if (preg_match('/^data:image\/(\w+);base64,/', $image)) {
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            $fileName = 'profile_' . $user->id . '_' . time() . '.png';
            Storage::put('public/profile_images/' . $fileName, $imageData);

            return 'profile_images/' . $fileName;
        }

        // Handle regular file upload
        $path = $image->store('public/profile_images');
        return str_replace('public/', '', $path);
    }

    /**
     * Format profile data for mobile response
     */
    private function formatProfileData($user)
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->userData->name ?? null,
            'address' => $user->userData->address ?? null,
            'phone_number' => $user->userData->phone_number ?? null,
            'image_url' => $user->userData->image ?
                url('storage/' . $user->userData->image) :
                null,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ];
    }
    public function changePassword(Request $request)
    {
        $user = $request->user();

        // 1. Validasi input dari aplikasi Flutter
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'new_password'     => ['required', 'string', 'confirmed', 'min:8', 'different:current_password'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Verifikasi apakah password lama yang dimasukkan sudah benar
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama tidak sesuai.',
                'errors' => ['current_password' => ['Password lama yang Anda masukkan salah.']]
            ], 422);
        }

        // 3. Jika semua validasi lolos, update password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        // 4. (SANGAT DIREKOMENDASIKAN) Logout pengguna dari sesi saat ini.
        // Ini memaksa mereka untuk login kembali dengan password baru,
        // dan membuat token lama yang mungkin tersimpan di tempat lain menjadi tidak valid.
        // Untuk JWT, gunakan Auth::logout().
        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah. Silakan login kembali dengan password baru Anda.'
        ], 200);
    }
    public function deleteProfileImage(Request $request)
    {
        $user = Auth::user();

        try {
            // Cari data user
            $userData = $user->userData;

            // Jika ada data dan ada gambar, lakukan penghapusan
            if ($userData && $userData->image) {
                // Hapus file fisik dari storage
                Storage::delete('public/' . $userData->image);

                // Kosongkan kolom 'image' di database
                $userData->update(['image' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil dihapus.'
                ]);
            }

            // Jika tidak ada gambar untuk dihapus
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
