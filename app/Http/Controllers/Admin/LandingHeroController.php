<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LandingHeroController extends Controller
{
    public function index()
    {
        return view('admin.landingpage.hero.index');
    }
}
