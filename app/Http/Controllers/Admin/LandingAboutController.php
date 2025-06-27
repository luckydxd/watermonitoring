<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutSetting;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;


class LandingAboutController extends Controller
{
    public function index()
    {
        return view('admin.landingpage.about.index');
    }
}
