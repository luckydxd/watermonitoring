<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User; // Pastikan model User diimpor

class ForgotPasswordApiController extends Controller
{
    /**
     * Send a password reset link to the given user's email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }

        // Check if the user exists
        if (!User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email yang Anda masukkan tidak terdaftar.',
            ], 404);
        }

        // We will send the password reset link to this user.
        $response = Password::sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link reset kata sandi telah dikirim ke email Anda. Silakan periksa kotak masuk Anda. Pastikan untuk memeriksa folder spam jika tidak ada di kotak masuk.',
            ], 200);
        }

        // If the password reset link failed to send, we will return an error response
        return response()->json([
            'message' => 'Gagal mengirim tautan reset kata sandi. Silakan coba lagi nanti.',
            'error' => trans($response) // This might give more detailed error if available
        ], 500); // 500 Internal Server Error for generic failure
    }
}
