<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SidebarService
{
    public function getMenuItems()
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $isAdmin = $user->hasRole('admin');
        $prefix = $isAdmin ? 'admin' : 'teknisi';

        return [
            'dashboard' => $this->createMenuItem(
                "{$prefix}.dashboard",
                'ti-tent',
                'Dashboard',
                ['admin', 'teknisi']
            ),

            'device' => $this->createMenuItem(
                "{$prefix}.device",
                'ti-cpu',
                'Manajemen Alat',
                ['admin', 'teknisi']
            ),

            'user' => $this->createMenuItem(
                "{$prefix}.user",
                'ti-users',
                'Manajemen Pengguna',
                ['admin', 'teknisi']
            ),


            'complaint' => $this->createMenuItem(
                "{$prefix}.complaint",
                'ti-bubble-text',
                'Keluhan Pengguna',
                ['admin', 'teknisi']
            ),

            'report' => [
                'type' => 'submenu',
                'icon' => 'ti-report-analytics',
                'title' => 'Manajemen Laporan',
                'roles' => ['admin', 'teknisi'],
                'is_active' => Request::routeIs('admin.report-*') ||
                    Request::routeIs('teknisi.report-*'),
                'submenu' => $isAdmin ? [
                    $this->createSubMenuItem('admin.report-usage', 'Laporan Penggunaan'),
                    $this->createSubMenuItem('admin.report-device', 'Laporan Alat'),
                    $this->createSubMenuItem('admin.report-user', 'Laporan Pengguna'),
                ] : [
                    $this->createSubMenuItem('teknisi.report-device', 'Laporan Alat'),
                    $this->createSubMenuItem('teknisi.report-complaint', 'Laporan Keluhan')
                ]
            ],

            'landingpage' => [
                'type' => 'submenu',
                'icon' => 'ti-layout-dashboard',
                'title' => 'Landingpage',
                'roles' => ['admin'], // Admin only
                'is_active' => Request::routeIs([
                    'admin.landing.hero',
                    'admin.landing.about',
                    'admin.landing.features',
                    'admin.landing.contact',
                    'admin.landing.footer'
                ]),
                'submenu' => [
                    $this->createSubMenuItem('admin.landing.hero', 'Hero Section'),
                    $this->createSubMenuItem('admin.landing.about', 'Tentang Kami'),
                    $this->createSubMenuItem('admin.landing.features', 'Fitur'),
                    $this->createSubMenuItem('admin.landing.contact', 'Kontak'),
                    $this->createSubMenuItem('admin.landing.footer', 'Footer & Sosial Media')
                ]
            ],

            'settings' => $this->createMenuItem(
                "{$prefix}.settings",
                'ti-world-cog',
                'Pengaturan Web',
                ['admin']

            ),

            'monitor' => $this->createMenuItem(
                "{$prefix}.monitor",
                'ti-device-desktop-analytics',
                'Manajemen Monitor',
                ['admin', 'teknisi']
            )

        ];
    }

    protected function createMenuItem($route, $icon, $title, $roles, $isDynamic = false)
    {
        return [
            'type' => 'single',
            'route' => $route,
            'icon' => $icon,
            'title' => $title,
            'roles' => $roles,
            'is_active' => $isDynamic
                ? Request::routeIs("{$route}.*")
                : Request::routeIs($route)
        ];
    }

    protected function createSubMenuItem($route, $title)
    {
        return [
            'route' => $route,
            'title' => $title,
            'is_active' => Request::routeIs($route)
        ];
    }
}
