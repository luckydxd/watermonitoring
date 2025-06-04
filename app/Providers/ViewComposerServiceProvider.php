<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\SidebarService;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Untuk sidebar
        View::composer('layouts.partials.sidebar', function ($view) {
            $sidebarService = new SidebarService();
            $view->with('menuItems', $sidebarService->getMenuItems()['sidebar']);
        });

        // Untuk navbar
        View::composer('layouts.partials.navbar', function ($view) {
            $sidebarService = new SidebarService();
            $view->with([
                'sidebarMenu' => $sidebarService->getMenuItems()['sidebar'],
                'navbarMenu' => $sidebarService->getMenuItems()['navbar']
            ]);
        });
    }

    public function register()
    {
        //
    }
}
