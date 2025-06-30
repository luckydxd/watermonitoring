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
                    'ti-smart-home',
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
                    // 1. BERUBAH: Tipe menu bukan lagi 'submenu', tapi 'item' tunggal.
                    'type' => 'item',
                    'icon' => 'ti-layout-dashboard', // Ikon tetap sama
                    'title' => 'Editor Landingpage', // 2. Diubah agar lebih deskriptif
                    'roles' => ['admin'],
                    'permission' => 'manage-landing-page', // Hak akses tetap sama

                    // 3. BARU: Menambahkan route tujuan untuk link ini
                    'route' => 'admin.landing.editor',

                    // 4. BERUBAH: Kondisi aktif sekarang jauh lebih simpel
                    // Menu akan aktif jika route saat ini diawali dengan 'admin.landing.'
                    'is_active' => Request::routeIs('admin.landing.*'),

                    // 5. DIHAPUS: Key 'submenu' tidak diperlukan lagi sama sekali.
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
                    'ti-settings',
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
