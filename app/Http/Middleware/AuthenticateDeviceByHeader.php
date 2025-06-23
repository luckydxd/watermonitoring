<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Device;
use Illuminate\Support\Facades\Log;

class AuthenticateDeviceByHeader
{
    /**
     * Menangani request yang masuk untuk otentikasi perangkat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil nilai MAC address dari header 'X-Device-Api-Key'
        $macAddress = $request->header('X-Device-Api-Key');

        // 2. Jika header otentikasi tidak ada, langsung tolak request
        if (!$macAddress) {
            Log::warning('Request ditolak: Header otentikasi (X-Device-Api-Key) tidak ditemukan.');
            return response()->json(['message' => 'Unauthorized: Header otentikasi tidak ada.'], 401);
        }

        // 3. Cari perangkat di database berdasarkan MAC address dari header
        // Ini adalah satu-satunya sumber kebenaran (source of truth).
        $device = Device::where('unique_key', $macAddress)->first();

        // 4. Jika perangkat dengan MAC tersebut tidak terdaftar, tolak request
        if (!$device) {
            Log::warning("Request ditolak: MAC address tidak terdaftar: {$macAddress}");
            return response()->json(['message' => 'Unauthorized: Perangkat tidak terdaftar.'], 401);
        }

        // 5. Jika perangkat ditemukan, tambahkan objek 'device' ke dalam request
        // Ini agar controller bisa langsung menggunakannya tanpa perlu query ulang.
        $request->attributes->add(['authenticated_device' => $device]);

        // 6. Jika semua pemeriksaan lolos, lanjutkan request ke controller
        return $next($request);
    }
}
