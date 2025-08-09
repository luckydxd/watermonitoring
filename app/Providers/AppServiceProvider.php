<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('*', 'App\Http\View\Composers\UserDataComposer');
        Carbon::setLocale('id');

        try {
            // Cek apakah tabel 'app_settings' sudah ada
            if (Schema::hasTable('app_settings')) {
                // Ambil baris pertama dari pengaturan aplikasi (diasumsikan hanya ada satu baris)
                // Kita gunakan cache agar tidak query ke database setiap kali halaman dimuat
                $appSetting = cache()->rememberForever('app_setting', function () {
                    return AppSetting::first();
                });

                // Bagikan variabel $appSetting ke semua view
                View::share('appSetting', $appSetting);
            }
        } catch (\Exception $e) {
            // Tangani error jika terjadi (misalnya, saat koneksi database gagal)
            // Anda bisa log error di sini jika perlu
        }
    }
}
