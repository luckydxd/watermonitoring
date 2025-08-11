<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use App\Models\Page;
use App\Models\AboutSetting;

class LandingPageController extends Controller
{
    public function index()
    {
        $page = Page::find(1);

        if (!$page) {
            abort(404);
        }

        $page->load(['content_blocks' => function ($query) {
            $query->where('is_active', true)->orderBy('position', 'asc');
        }, 'content_blocks.blockable']);

        $aboutSettings = AboutSetting::first();
        $appSettings = AppSetting::firstOrFail();
        return view('landing-page.index', [
            'appSettings' => $appSettings,
            'aboutSettings' => $aboutSettings,
            'page' => $page

        ]);
    }
}
