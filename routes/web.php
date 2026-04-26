<?php

use App\Http\Controllers\Admin\BookingActionController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\HairstyleController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===== SITE PAGES =====
//trang chủ
Route::get('/', [SiteController::class, 'home'])->name('site.home');
// đặt lịch
Route::get('/booking', [SiteController::class, 'booking'])->name('site.booking');
//tra cứu lịch
Route::get('/lookup', [SiteController::class, 'lookup'])->name('site.lookup');

// ===== VOYAGER ADMIN =====
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Route::group(['prefix' => 'admin', 'middleware' => ['admin.user']], function () {
    Route::match(['GET', 'POST'], 'bookings/{id}/confirm', [BookingActionController::class, 'confirm'])
        ->name('admin.bookings.action.confirm');

    Route::match(['GET', 'POST'], 'bookings/{id}/cancel', [BookingActionController::class, 'cancel'])
        ->name('admin.bookings.action.cancel');

    Route::match(['GET', 'POST'], 'bookings/{id}/complete', [BookingActionController::class, 'complete'])
        ->name('admin.bookings.action.complete');
});

Route::get('/hairstyle/suggest', [HairstyleController::class, 'index'])->name('hairstyle.index');
Route::post('/hairstyle/analyze', [HairstyleController::class, 'analyze'])->name('hairstyle.analyze');

Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send');
