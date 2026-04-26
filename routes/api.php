<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CatalogApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// ===== API =====
Route::prefix('api')->group(function () {
    //danh sách dịch vụ
    Route::get('/services', [CatalogApiController::class, 'services'])->name('api.services');
    //danh sách thợ
    Route::get('/stylists', [CatalogApiController::class, 'stylists'])->name('api.stylists');
    //khung giờ đã có ngươì đặt
    Route::get('/booked-slots', [BookingController::class, 'bookedSlots'])->name('api.bookings.bookedSlots');
    //tạo lịch đặt mới
    Route::post('/bookings', [BookingController::class, 'store'])->name('api.bookings.store');
    //tra cứu lich
    Route::get('/lookup', [BookingController::class, 'lookup'])->name('api.lookup');
    //tự ẩn slot bị chiếm
    Route::get('/available-slots', [BookingController::class, 'availableSlots'])->name('api.availableSlots');
});
