<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\API\Mobile\MonitoringApiController;
use App\Http\Controllers\Admin\LandingBuilderPageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DeviceManagementController;
use App\Http\Controllers\Admin\MonitorManagementController;
use App\Http\Controllers\Admin\DetailMonitorController;
use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\LandingHeroController;
use App\Http\Controllers\Admin\LandingAboutController;
use App\Http\Controllers\Admin\LandingFeatureController;
use App\Http\Controllers\Admin\LandingContactController;
use App\Http\Controllers\Admin\LandingFooterController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\ReportComplaintController;
use App\Http\Controllers\Admin\ReportDeviceController;
use App\Http\Controllers\Admin\ReportUsageController;
use App\Http\Controllers\Admin\ReportUserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserDeviceController;
use App\Http\Controllers\User\UserUsageController;
use App\Http\Controllers\Teknisi\TeknisiDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public Routes ---
Route::get('/', [LandingPageController::class, 'index'])->name('landing.index');
Route::get('/user/login', [AuthController::class, 'index'])->name('login.user');
Route::get('/user/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/user/register', [RegisterController::class, 'register'])->name('register.submit');
Route::get('/admin/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/admin/login', [AuthController::class, 'login'])->middleware('guest');

// --- Password Reset Routes ---
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// --- Logout Route ---
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('/track-activity/{type}', [TrackingController::class, 'track'])->name('track.activity');

// --- Authenticated General Routes ---
Route::middleware(['auth', 'verified'])->group(function () {
    // Profil bisa diakses semua role yang login, jadi tidak perlu permission spesifik
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/image', [ProfileController::class, 'deleteProfileImage'])->name('profile.image.delete');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});

// ==========================================================
// USER ROUTES (Role: user)
// ==========================================================
Route::middleware(['auth', 'verified', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard')->middleware('permission:access-user-dashboard');
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile'); // Profil tidak perlu permission khusus
    Route::get('/device', [UserDeviceController::class, 'index'])->name('device')->middleware('permission:view-own-devices');
    Route::get('/usage', [UserUsageController::class, 'index'])->name('usage')->middleware('permission:view-own-usage');
});

// ==========================================================
// ADMIN ROUTES (Role: admin)
// ==========================================================
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/chart-data', [DashboardController::class, 'getChartData']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:access-admin-dashboard');

    // Route individu dengan permission
    Route::get('/user', [UserManagementController::class, 'index'])->name('user')->middleware('permission:view-users');
    Route::get('/complaint', [ComplaintController::class, 'index'])->name('complaint')->middleware('permission:view-complaints');
    Route::get('/device', [DeviceManagementController::class, 'index'])->name('device')->middleware('permission:view-devices');
    Route::get('/monitor', [MonitorManagementController::class, 'index'])->name('monitor')->middleware('permission:view-monitoring');
    Route::get('/detail-monitor/{id}', [DetailMonitorController::class, 'index'])->name('detail-monitor')->middleware('permission:view-monitoring');

    // Grup route dengan permission
    Route::prefix('report')->name('report-')->middleware('permission:view-reports')->group(function () {
        Route::get('/complaint', [ReportComplaintController::class, 'index'])->name('complaint');
        Route::get('/user', [ReportUserController::class, 'index'])->name('user');
        Route::get('/device', [ReportDeviceController::class, 'index'])->name('device');
        Route::get('/usage', [ReportUsageController::class, 'index'])->name('usage');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AppSettingController::class, 'edit'])->name('edit')->middleware('permission:manage-app-settings');
        Route::put('/', [AppSettingController::class, 'update'])->name('update')->middleware('permission:manage-app-settings');
        Route::get('/roles', [AppSettingController::class, 'roles'])->name('roles')->middleware('permission:manage-roles');
        Route::post('/update-role', [AppSettingController::class, 'updateRole'])->name('update-role')->middleware('permission:manage-permissions');
    });

    Route::prefix('landing')->name('landing.')->middleware('permission:manage-landing-page')->group(function () {

        // 1. SATU PINTU MASUK UTAMA
        Route::get('/', [LandingBuilderPageController::class, 'editor'])->name('editor');

        // 2. SEMUA ROUTE UNTUK AKSI PADA BLOK KONTEN

        //======================================================================
        // TAMBAHKAN ROUTE INI DI SINI
        // Route untuk MENAMPILKAN form TAMBAH sebuah blok (via AJAX)
        Route::get('/pages/{page}/blocks/create', [LandingBuilderPageController::class, 'createBlock'])->name('blocks.create');
        //======================================================================

        // Route untuk MENYIMPAN blok BARU ke halaman
        Route::post('/blocks', [LandingBuilderPageController::class, 'storeBlock'])->name('blocks.store');

        // Route untuk MENAMPILKAN form EDIT sebuah blok
        Route::get('/blocks/{content_block}/edit', [LandingBuilderPageController::class, 'editBlock'])->name('blocks.edit');

        // Route untuk MENGUPDATE data blok yang sudah diedit
        Route::put('/blocks/{content_block}', [LandingBuilderPageController::class, 'updateBlock'])->name('blocks.update');

        // Route untuk MENGHAPUS sebuah blok
        Route::delete('/blocks/{content_block}', [LandingBuilderPageController::class, 'destroyBlock'])->name('blocks.destroy');

        // Route untuk MENGUBAH URUTAN blok
        Route::post('/blocks/reorder', [LandingBuilderPageController::class, 'reorder'])->name('blocks.reorder');
    });

    // Route::prefix('landing')->name('landing.')->middleware('permission:manage-landing-page')->group(function () {
    //     Route::get('/hero', [LandingHeroController::class, 'index'])->name('hero');
    //     Route::prefix('about')->name('about.')->controller(LandingAboutController::class)->group(function () {
    //         Route::get('/', 'index')->name('index');
    //     });

    //     Route::get('/features', [LandingFeatureController::class, 'index'])->name('features');
    //     Route::get('/contact', [LandingContactController::class, 'index'])->name('contact');
    //     Route::get('/footer', [LandingFooterController::class, 'index'])->name('footer');
    // });
});

// ==========================================================
// TEKNISI ROUTES (Role: teknisi)
// ==========================================================
Route::middleware(['auth', 'verified', 'role:teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::get('/dashboard', [TeknisiDashboardController::class, 'index'])->name('dashboard')->middleware('permission:access-teknisi-dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/device', [DeviceManagementController::class, 'index'])->name('device')->middleware('permission:view-devices');
    Route::get('/user', [UserManagementController::class, 'index'])->name('user')->middleware('permission:view-users');
    Route::get('/complaint', [ComplaintController::class, 'index'])->name('complaint')->middleware('permission:view-complaints');
    Route::get('/report-device', [ReportDeviceController::class, 'index'])->name('report-device')->middleware('permission:view-reports');
    Route::get('/report-complaint', [ReportComplaintController::class, 'index'])->name('report-complaint')->middleware('permission:view-reports');
    Route::get('/monitor', [MonitorManagementController::class, 'index'])->name('monitor')->middleware('permission:view-monitoring');
    Route::get('/detail-monitor/{id}', [DetailMonitorController::class, 'index'])->name('detail-monitor')->middleware('permission:view-monitoring');
});
