<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Auth;

class UserUsageController extends Controller
{

    public function index()
    {
        // Ambil baris pertama dari pengaturan aplikasi
        $appSettings = AppSetting::first();

        // Ambil data pengguna yang sedang login
        $currentUser = Auth::user();

        return view('user.usage.index', compact('appSettings', 'currentUser'));
    }
}
