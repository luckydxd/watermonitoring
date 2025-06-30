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
        // Ambil halaman utama (yang memiliki ID = 1)
        $page = Page::find(1);

        // Jika halaman tidak ditemukan, tampilkan halaman 404
        if (!$page) {
            abort(404);
        }

        // Ambil semua blok konten yang AKTIF dan terhubung dengan halaman ini,
        // urutkan berdasarkan posisinya.
        // Eager loading ('blockable') sangat penting untuk performa.
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
