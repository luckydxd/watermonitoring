<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ForgotPasswordApiController extends Controller
{
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

        if (!User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Email yang Anda masukkan tidak terdaftar.',
            ], 404);
        }

        $response = Password::sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link reset kata sandi telah dikirim ke email Anda. Silakan periksa kotak masuk Anda. Pastikan untuk memeriksa folder spam jika tidak ada di kotak masuk.',
            ], 200);
        }

        return response()->json([
            'message' => 'Gagal mengirim tautan reset kata sandi. Silakan coba lagi nanti.',
            'error' => trans($response)
        ], 500);
    }
}
