<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use App\Models\AboutSetting;

class LandingPageController extends Controller
{
    public function index()
    {
        $aboutSettings = AboutSetting::first();
        $appSettings = AppSetting::firstOrFail();
        return view('landing-page.index', [
            'appSettings' => $appSettings,
            'aboutSettings' => $aboutSettings

        ]);
    }
}
