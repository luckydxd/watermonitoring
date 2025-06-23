<?php

namespace App\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use App\Models\Device; // Pastikan ini mengarah ke model Device Anda

class DeviceApiKeyGuard implements Guard
{
    use GuardHelpers;

    protected $provider;
    protected $request;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (! is_null($this->user)) {
            return $this->user;
        }

        // Ambil API Key dari header 'X-API-Key'
        $apiKey = $this->request->header('X-API-Key');

        // Ambil unique_id perangkat dari header 'X-Device-Unique-Id'
        $deviceUniqueId = $this->request->header('X-Device-Unique-Id');

        if (! $apiKey || ! $deviceUniqueId) {
            return null;
        }

        // Cari perangkat berdasarkan unique_id dan api_key
        $device = Device::where('unique_id', $deviceUniqueId)
            ->where('api_key', $apiKey)
            ->first();

        // Jika perangkat ditemukan, set sebagai user yang terotentikasi
        if ($device) {
            // Opsional: Perbarui last_seen_at setiap kali perangkat mengirim data
            $device->update(['last_seen_at' => now()]);
            return $this->user = $device;
        }

        return null;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        // Tidak digunakan secara langsung untuk API Key, user() sudah melakukan validasi.
        // Namun, jika Anda ingin mendukung kredensial lain, Anda bisa mengimplementasikannya di sini.
        return (bool) $this->user();
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
    {
        $this->user = $user;
    }
}
