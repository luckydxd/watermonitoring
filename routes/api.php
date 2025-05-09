<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserApiController;
use App\Http\Controllers\API\DeviceApiController;



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

Route::get('/device-types', [DeviceApiController::class, 'getDeviceTypes']);

Route::prefix('devices')->group(function () {
    Route::get('/', [DeviceApiController::class, 'index'])->name('api.devices.index');
    Route::post('/', [DeviceApiController::class, 'store']);
    Route::get('/{id}', [DeviceApiController::class, 'show']);
    Route::put('/{id}', [DeviceApiController::class, 'update']);
    Route::delete('/{id}', [DeviceApiController::class, 'destroy']);
});

Route::prefix('users')->group(function () {
    Route::get('/', [UserApiController::class, 'index'])->name('api.users.index');
    Route::get('/{id}', [UserApiController::class, 'show']);

    Route::post('/', [UserApiController::class, 'store'])->name('store');
    Route::put('/{id}', [UserApiController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserApiController::class, 'destroy'])->name('api.users.destroy');
});
