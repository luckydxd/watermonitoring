<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\SidebarService;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('layouts.partials.sidebar', function ($view) {
            $sidebarService = new SidebarService();
            $view->with('menuItems', $sidebarService->getMenuItems());
        });
    }

    public function register()
    {
        //
    }
}
