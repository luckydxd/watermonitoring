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
        $prefix = auth()->user()->hasRole('admin') ? 'admin' : (auth()->user()->hasRole('teknisi') ? 'teknisi' : 'user');

        return [
            'sidebar' => [
                'dashboard' => $this->createMenuItem(
                    "{$prefix}.dashboard",
                    'ti-tent',
                    'Dashboard',
                    ['admin', 'teknisi', 'user'],
                    "access-{$prefix}-dashboard"
                ),
                'device' => $this->createMenuItem(
                    "{$prefix}.device",
                    'ti-cpu',
                    'Manajemen Alat',
                    ['admin', 'teknisi', 'user'],
                    ($prefix === 'user' ? 'view-own-devices' : 'view-devices')
                ),
                'user' => $this->createMenuItem(
                    "{$prefix}.user",
                    'ti-users',
                    'Manajemen Pengguna',
                    ['admin', 'teknisi'],
                    'view-users'
                ),
                'complaint' => $this->createMenuItem(
                    "{$prefix}.complaint",
                    'ti-bubble-text',
                    'Keluhan Pengguna',
                    ['admin', 'teknisi'],
                    'view-complaints'
                ),
                'report' => [
                    'type' => 'submenu',
                    'icon' => 'ti-report-analytics',
                    'title' => 'Manajemen Laporan',
                    'roles' => ['admin', 'teknisi'],
                    'permission' => 'view-reports',
                    'is_active' => Request::is("{$prefix}/report*"),
                    'submenu' => $isAdmin ? [
                        $this->createSubMenuItem('admin.report-usage', 'Laporan Penggunaan'),
                        $this->createSubMenuItem('admin.report-device', 'Laporan Alat'),
                        $this->createSubMenuItem('admin.report-user', 'Laporan Pengguna'),
                    ] : [
                        $this->createSubMenuItem('teknisi.report-device', 'Laporan Alat'),
                        $this->createSubMenuItem('teknisi.report-complaint', 'Laporan Keluhan')
                    ]
                ],
                'Builder' => [
                    'type' => 'item',
                    'icon' => 'ti-layout-dashboard',
                    'title' => 'Editor Landingpage',
                    'roles' => ['admin'],
                    'permission' => 'manage-landing-page',

                    'route' => 'admin.landing.editor',

                    'is_active' => Request::routeIs('admin.landing.*'),

                ],
                // 'landingpage' => [
                //     'type' => 'submenu',
                //     'icon' => 'ti-layout-dashboard',
                //     'title' => 'Landingpage',
                //     'roles' => ['admin'],
                //     'permission' => 'manage-landing-page',
                //     'is_active' => Request::routeIs([
                //         'admin.landing.hero',
                //         'admin.landing.about.index',
                //         'admin.landing.features',
                //         'admin.landing.contact',
                //         'admin.landing.footer'
                //     ]),
                //     'submenu' => [
                //         $this->createSubMenuItem('admin.landing.hero', 'Hero Section'),
                //         $this->createSubMenuItem('admin.landing.about.index', 'Tentang Kami'),
                //         $this->createSubMenuItem('admin.landing.features', 'Fitur'),
                //         $this->createSubMenuItem('admin.landing.contact', 'Kontak'),
                //         $this->createSubMenuItem('admin.landing.footer', 'Footer & Sosial Media')
                //     ]
                // ],
                'settings' => $this->createMenuItem(
                    "admin.settings.edit",
                    'ti-settings-2',
                    'Pengaturan Aplikasi',
                    ['admin'],
                    'manage-app-settings'
                ),
                'usage' => $this->createMenuItem(
                    "user.usage",
                    'ti-device-desktop-analytics',
                    'Monitoring Pemakaian',
                    ['user'],
                    'view-own-usage'
                ),
                // 'monitor' => $this->createMenuItem(
                //     "{$prefix}.monitor",
                //     'ti-device-desktop-analytics',
                //     'Manajemen Monitor',
                //     ['admin', 'teknisi']
                // ),
            ],
            'navbar' => [
                'profile' => $this->createMenuItem(
                    "{$prefix}.profile",
                    'ti-user',
                    'Pengaturan Akun',
                    ['admin', 'teknisi', 'user']
                )
            ],

        ];
    }

    protected function createMenuItem($route, $icon, $title, $roles, $permission = null)
    {
        return [
            'type' => 'single',
            'route' => $route,
            'icon' => $icon,
            'title' => $title,
            'roles' => $roles,
            'permission' => $permission,
            'is_active' => Request::routeIs($route)
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
