<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserApiController;
use App\Http\Controllers\API\DeviceApiController;
use App\Http\Controllers\Api\MonitorApiController;
use App\Http\Controllers\Api\ComplaintApiController;

use App\Http\Controllers\Admin\ReportDeviceController;
use App\Http\Controllers\Admin\ReportUsageController;
use App\Http\Controllers\Admin\ReportUserController;
use App\Http\Controllers\Admin\ReportComplaintController;






use Illuminate\Contracts\Queue\Monitor;

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


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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

Route::get('/device-types', [DeviceApiController::class, 'getDeviceTypes']);

Route::prefix('devices')->group(function () {
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
    Route::post('/{id}/resolve', [ComplaintApiController::class, 'resolve']);
});


Route::prefix('users')->group(function () {
    Route::get('/', [UserApiController::class, 'index'])->name('api.users.index');
    Route::get('/{id}', [UserApiController::class, 'show']);
    Route::post('/', [UserApiController::class, 'store'])->name('store');
    Route::put('/{id}', [UserApiController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserApiController::class, 'destroy'])->name('api.users.destroy');
    Route::post('/{id}/toggle-status', [UserApiController::class, 'toggleStatus']);
});

Route::prefix('report')->group(function () {
    Route::prefix('usage')->group(function () {
        Route::get('/datatables', [ReportUsageController::class, 'datatables'])->name('api.report-usage.datatables');
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
