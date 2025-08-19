<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\DeviceApiKeyGuard;
use App\Models\Device;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

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
