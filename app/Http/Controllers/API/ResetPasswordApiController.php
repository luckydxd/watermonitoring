<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User; // Pastikan model User diimpor
use Illuminate\Support\Facades\Auth; // Hanya jika Anda ingin login otomatis setelah reset, tapi tidak umum untuk API reset

class ResetPasswordApiController extends Controller
{
    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        // Validate if the new password is the same as the old one (optional but good practice for security)
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The new password cannot be the same as the old password.',
                'errors' => ['password' => ['Password baru tidak boleh sama dengan password lama.']],
            ], 422);
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on their record and fire the PasswordReset
        // event, otherwise we'll send back an error response.
        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Optional: Revoke all existing tokens (e.g., Passport/Sanctum tokens)
                // if (method_exists($user, 'tokens')) {
                //     $user->tokens->each->delete();
                // }

                // For API, we typically do NOT automatically log the user in after reset
                // Auth::login($user); // Hapus baris ini untuk API, mobile app akan login ulang secara eksplisit
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password has been reset successfully. Please login with your new password.',
            ], 200);
        }

        // If the password reset was unsuccessful, we will respond with the error message.
        return response()->json([
            'message' => 'Failed to reset password.',
            'error' => trans($response) // This will typically be 'passwords.token' or 'passwords.user'
        ], 400); // 400 Bad Request for generic failure
    }
}
