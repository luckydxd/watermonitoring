<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\DeviceApiKeyGuard;
use App\Models\Device;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {

        Auth::extend('api_key', function ($app, $name, array $config) {
            return new DeviceApiKeyGuard(
                Auth::createUserProvider($config['provider'] ?? 'devices'),
                $app['request']
            );
        });
    }
}
