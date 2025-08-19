<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LandingAboutController;


use App\Http\Controllers\API\UserApiController;
use App\Http\Controllers\API\DeviceApiController;
use App\Http\Controllers\API\MonitorApiController;
use App\Http\Controllers\API\ComplaintApiController;
use App\Http\Controllers\API\UserDeviceApiController;
use App\Http\Controllers\API\UserUsageApiController;
use App\Http\Controllers\API\DeviceDataController;
use App\Http\Controllers\API\AboutApiController;
// use App\Http\Controllers\API\MonitoringController;
use App\Http\Controllers\API\AuthApiController;
use App\Http\Controllers\API\ProfileApiController;
use App\Http\Controllers\API\DeviceAssignmentApiController;
use App\Http\Controllers\API\ForgotPasswordApiController;
use App\Http\Controllers\API\ResetPasswordApiController;

use App\Http\Controllers\API\Mobile\MonitoringApiController;
use App\Http\Controllers\API\Mobile\NotificationApiController;

use App\Http\Controllers\Admin\ReportDeviceController;
use App\Http\Controllers\Admin\ReportUsageController;
use App\Http\Controllers\Admin\ReportUserController;
use App\Http\Controllers\Admin\ReportComplaintController;

use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Teknisi\TeknisiDashboardController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthApiController::class, 'register']);
Route::post('login', [AuthApiController::class, 'login']);
Route::post('logout', [AuthApiController::class, 'logout']);

Route::prefix('password')->group(function () {
    Route::post('forgot', [ForgotPasswordApiController::class, 'sendResetLinkEmail'])->name('api.password.email');
    Route::post('reset', [ResetPasswordApiController::class, 'reset'])->name('api.password.reset');
});
// Route::post('/device-auth', [AuthApiController::class, 'deviceLogin']);

// Route::post('/sensor-data', [DeviceDataController::class, 'store'])->middleware('auth:device_api');

Route::post('/devices/register', [DeviceDataController::class, 'registerDevice']);

Route::prefix('sensor')->middleware('auth.device')->group(function () {
    Route::post('/flow-pressure', [DeviceDataController::class, 'storeFlowPressure'])
        ->middleware('throttle:60,1')
        ->name('api.sensor.flow');

    Route::post('/water-quality', [DeviceDataController::class, 'storeWaterQuality'])
        ->middleware('throttle:60,1')
        ->name('api.sensor.quality');

    Route::get('/device/config', [DeviceDataController::class, 'getDeviceConfig'])
        ->middleware('throttle:60,1');
});


Route::middleware(['auth:api'])->group(function () {
    Route::prefix('mobile')->group(function () {
        Route::prefix('device')->group(function () {
            Route::get('/active-status', [MonitoringApiController::class, 'getActiveDevicesInfo'])->name('device.active.status');
            Route::post('/assign-by-qr', [DeviceAssignmentApiController::class, 'assignByQrCode'])->name('device.assign.by.qr');
        });

        Route::prefix('usage')->group(function () {
            Route::get('/', [UserUsageApiController::class, 'getMonthlyUsageWithCost']);
            // Route::get('/today', [UserUsageApiController::class, 'getTodayUsage'])->name('api.user.usage.today');
            Route::get('/monthly', [UserUsageApiController::class, 'usageByMonth']);
        });

        Route::prefix('complaint')->group(function () {
            Route::get('/', [ComplaintApiController::class, 'getComplaint']);
            Route::post('/', [ComplaintApiController::class, 'postComplaint']);
        });
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileApiController::class, 'getProfile']);
            Route::put('/', [ProfileApiController::class, 'updateProfile']);
            Route::post('/change-password', [ProfileApiController::class, 'changePassword'])->name('profile.change-password');
        });
        Route::prefix('monitoring')->name('monitoring.')->controller(MonitoringApiController::class)->group(function () {

            //  konsumsi harian untuk chart
            Route::get('/consumption-summary', 'getConsumptionSummary')->name('consumption.summary');

            //  Data terakhir untuk widget
            Route::get('/latest-readings', 'getLatestReadings')->name('latest.readings');

            // Riwayat data sensor per jam
            Route::get('/history/{metric}', 'getSensorHistory')->name('history');

            // Export laporan bulanan
            Route::get('/export-monthly', 'exportMonthlyReport')->name('export.monthly');

            Route::get('/cost-estimation', 'getCostEstimation')->name('cost.estimation');
        });

        Route::prefix('notifications')->name('notifications.')->controller(NotificationApiController::class)->group(function () {
            Route::get('/', 'getNotifications')->name('index');
            Route::get('/unread-count', 'getUnreadCount')->name('unread_count');
            Route::post('/{notification}/read', 'markAsRead')->name('mark-as-read');
            Route::post('/read-all', 'markAllAsRead')->name('mark-all-as-read');
            Route::delete('/{notification}', 'destroy')->name('destroy');
        });
    });
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/datatables', [NotificationController::class, 'datatables'])->name('api.notifications.datatables');
        Route::get('/', [NotificationController::class, 'getNotifications'])->name('api.notifications.index');
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-as-read');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-as-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('api.notifications.destroy');

        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('api.notifications.unread_count');
        Route::get('/latest', [NotificationController::class, 'getLatestNotifications'])->name('api.notifications.latest');
    });
});



Route::middleware(['auth:web'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Dashboard User
    Route::get('/consumption-summary', [MonitoringApiController::class, 'getConsumptionSummary']);
    Route::get('/sensor-latest', [MonitoringApiController::class, 'getLatestReadings']);
    Route::get('/history/{metric}', [MonitoringApiController::class, 'getSensorHistoryDashboard'])->name('history');
    Route::post('/assign', [DeviceAssignmentApiController::class, 'assignByDashboard'])->name('device.assign');
    Route::get('/assign/{assignment}/edit', [UserDeviceApiController::class, 'edit'])->name('device.edit');
    Route::put('/assign/{assignment}/update', [UserDeviceApiController::class, 'update'])->name('device.update');
    Route::delete('/assign/{assignment}', [UserDeviceApiController::class, 'destroy'])
        ->name('device.assignment.destroy');

    // Dashboard Admin
    Route::post('/track-activity/{type}', [TrackingController::class, 'track']);

    Route::prefix('user')->group(function () {
        Route::get('/devices', [UserDeviceApiController::class, 'getUserDevices'])->name('api.user.devices');
        Route::get('/usage', [UserUsageApiController::class, 'getUserConsumption'])->name('api.user.usage');
        Route::get('/usage-data', [UserUsageApiController::class, 'getUsageData'])->name('api.user.usage-data');
    });

    Route::prefix('monitor')->group(function () {
        Route::get('/assign/datatables', [MonitorApiController::class, 'datatablesAssign'])->name('api.assign.datatables');
        Route::get('/assign/users', [MonitorApiController::class, 'getUsersForSelect'])->name('api.assign.users');
        Route::get('/assign/devices', [MonitorApiController::class, 'getAvailableDevices'])->name('api.assign.devices');
        Route::post('/assign', [MonitorApiController::class, 'storeAssignment'])->name('api.assign.store');
        Route::get('/datatables', [MonitorApiController::class, 'datatables'])->name('api.monitor.datatables');
        Route::get('/', [MonitorApiController::class, 'index'])->name('api.monitor.index');
        Route::post('/', [MonitorApiController::class, 'store']);
        Route::get('/{id}', [MonitorApiController::class, 'show']);
        Route::get('/{id}/edit', [MonitorApiController::class, 'edit']);
        Route::put('/{id}', [MonitorApiController::class, 'update']);
        Route::delete('/{id}', [MonitorApiController::class, 'destroy']);
    });

    //user
    Route::get('/dashboard/today-usage', [UserDashboardController::class, 'getTodayUsage'])->middleware('auth');


    Route::prefix('devices')->group(function () {
        Route::get('/types', [DeviceApiController::class, 'getDeviceTypes']);
        Route::get('/types-datatables', [DeviceApiController::class, 'getDeviceTypeforDatatables']);

        Route::post('/ping', [DeviceApiController::class, 'ping']);
        Route::get('/', [DeviceApiController::class, 'index'])->name('api.devices.index');
        Route::post('/', [DeviceApiController::class, 'store']);
        Route::get('/{id}', [DeviceApiController::class, 'show']);
        Route::put('/{id}', [DeviceApiController::class, 'update']);
        Route::delete('/{id}', [DeviceApiController::class, 'destroy']);
    });


    Route::prefix('complaints')->group(function () {
        Route::get('/', [ComplaintApiController::class, 'index'])->name('api.complaints.index');
        Route::post('/', [ComplaintApiController::class, 'store']);
        Route::get('/{id}', [ComplaintApiController::class, 'show']);
        Route::put('/{id}', [ComplaintApiController::class, 'update']);
        Route::delete('/{id}', [ComplaintApiController::class, 'destroy']);
        Route::post('/{id}/process', [ComplaintApiController::class, 'process']);
        Route::post('/{id}/resolve', [ComplaintApiController::class, 'resolve']);
    });

    Route::prefix('landing')->group(function () {
        Route::prefix('about')->group(function () {
            Route::get('/', [AboutApiController::class, 'index'])->name('api.about.index');
            Route::post('/', [AboutApiController::class, 'store']);
            Route::get('/{id}', [AboutApiController::class, 'show']);
            Route::put('/{id}', [AboutApiController::class, 'update']);
            Route::delete('/{id}', [AboutApiController::class, 'destroy']);
        });

        // Route::prefix('feature')->group(function () {
        //     Route::get('/', [AboutApiController::class, 'index'])->name('api.about.index');
        //     Route::post('/', [AboutApiController::class, 'store']);
        //     Route::get('/{id}', [AboutApiController::class, 'show']);
        //     Route::put('/{id}', [AboutApiController::class, 'update']);
        //     Route::delete('/{id}', [AboutApiController::class, 'destroy']);
        // });
    });



    Route::prefix('users')->group(function () {
        Route::get('/', [UserApiController::class, 'index'])->name('api.users.index');
        Route::get('/{id}', [UserApiController::class, 'show']);
        Route::post('/', [UserApiController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserApiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserApiController::class, 'destroy'])->name('api.users.destroy');
        Route::post('/{id}/toggle-status', [UserApiController::class, 'toggleStatus']);
    });

    Route::prefix('teknisi')->group(function () {
        Route::get('/water-usage', [TeknisiDashboardController::class, 'getWaterUsageData']);
        Route::get('/complaint-bar-data', [TeknisiDashboardController::class, 'getComplaintBarDataApi']);
        Route::post('/assign', [DeviceAssignmentApiController::class, 'assignByTechnician'])->name('device.assignByTechnician');
    });



    Route::prefix('admin')->group(function () {
        Route::get('/water-usage', [DashboardController::class, 'getWaterUsageData'])->name('api.admin.water_usage.data');
    });

    Route::prefix('report')->group(function () {
        Route::prefix('usage')->group(function () {
            Route::get('/datatables', [ReportUsageController::class, 'datatables'])->name('api.report-usage.datatables');
            Route::get('/', [ReportUsageController::class, 'getAdminUsageData'])->name('api.report-usage.admin');
        });
        Route::prefix('device')->group(function () {
            Route::get('/datatables', [ReportDeviceController::class, 'datatables'])->name('api.report-device.datatables');
        });
        Route::prefix('user')->group(function () {
            Route::get('/datatables', [ReportUserController::class, 'datatables'])->name('api.report-user.datatables');
        });
        Route::prefix('complaint')->group(function () {
            Route::get('/datatables', [ReportComplaintController::class, 'datatables'])->name('api.report-complaint.datatables');
        });
    });
});
