<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\API\UserApiController;
use App\Http\Controllers\Admin\DeviceManagementController;
use App\Http\Controllers\Admin\MonitorManagementController;
use App\Http\Controllers\Admin\DetailMonitorController;
use App\Http\Controllers\LandingPageController;
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
use App\Http\Controllers\Admin\WebSettingsController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginUserController;





use App\Http\Controllers\User\UserDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LandingPageController::class, 'index'])->name('landing.index');
Route::get('/user/login', [LoginUserController::class, 'showLoginForm'])->name('login-user');
Route::get('/user/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/user/register', [RegisterController::class, 'register'])->name('register.submit');
// Route::get('/', function () {
//     return view('auth.login');
// });
Route::get('/admin/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/admin/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth', 'verified');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth', 'verified');

Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitor', [MonitorManagementController::class, 'index'])->name('monitor');
    Route::get('/detail-monitor/{id}', [DetailMonitorController::class, 'index'])->name('detail-monitor');
    Route::get('/user', [UserManagementController::class, 'index'])->name('user');
    Route::get('/complaint', [ComplaintController::class, 'index'])->name('complaint');
    Route::get('/device', [DeviceManagementController::class, 'index'])->name('device');
    Route::prefix('report')->group(function () {
        Route::get('/complaint', [ReportComplaintController::class, 'index'])->name('report-complaint');
        Route::get('/user', [ReportUserController::class, 'index'])->name('report-user');
        Route::get('/device', [ReportDeviceController::class, 'index'])->name('report-device');
        Route::get('/usage', [ReportUsageController::class, 'index'])->name('report-usage');
    });
    Route::get('/settings', [WebSettingsController::class, 'index'])->name('settings');
    Route::prefix('landing')->name('landing.')->group(function () {
        Route::get('/hero', [LandingHeroController::class, 'index'])->name('hero');
        Route::get('/about', [LandingAboutController::class, 'index'])->name('about');
        Route::get('/features', [LandingFeatureController::class, 'index'])->name('features');
        Route::get('/contact', [LandingContactController::class, 'index'])->name('contact');
        Route::get('/footer', [LandingFooterController::class, 'index'])->name('footer');
    });
});


Route::middleware(['auth', 'verified', 'role:teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/device', [DeviceManagementController::class, 'index'])->name('device');
    Route::get('/user', [UserManagementController::class, 'index'])->name('user');
    Route::get('/complaint', [ComplaintController::class, 'index'])->name('complaint');
    Route::get('/report-device', [ReportDeviceController::class, 'index'])->name('report-device');
    Route::get('/report-complaint', [ReportComplaintController::class, 'index'])->name('report-complaint');
    Route::get('/monitor', [MonitorManagementController::class, 'index'])->name('monitor');
    Route::get('/detail-monitor/{id}', [DetailMonitorController::class, 'index'])->name('detail-monitor');
});
