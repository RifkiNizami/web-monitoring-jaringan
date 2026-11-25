<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AdminKonten\KontenAppSettingController;
use App\Http\Controllers\Api\AnnouncementApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for NetFly Customer Mobile App & Integrations
|--------------------------------------------------------------------------
*/

// Endpoint Publik (Tanpa Bearer Token)
Route::middleware('throttle:api')->group(function () {
    Route::post('/login', [AuthController::class, 'apiLogin'])->name('api.login');
    Route::get('/app-info', [KontenAppSettingController::class, 'apiAppInfo'])->name('api.app-info');

    Route::prefix('announcements')->group(function () {
        Route::get('/', [AnnouncementApiController::class, 'index'])->name('api.announcements.index');
        Route::get('/{id}', [AnnouncementApiController::class, 'show'])->name('api.announcements.show');
    });
});

// Endpoint Terproteksi (Wajib Authorization: Bearer <token>)
Route::middleware(['throttle:api', 'api.token'])->group(function () {
    Route::post('/logout', [AuthController::class, 'apiLogout'])->name('api.logout');

    Route::get('/user/profile', [AuthController::class, 'apiProfile'])->name('api.user.profile');
    Route::post('/user/profile', [AuthController::class, 'apiUpdateProfile'])->name('api.user.updateProfile');
    Route::put('/user/profile', [AuthController::class, 'apiUpdateProfile'])->name('api.user.updateProfile.put');
    Route::post('/user/update-profile', [AuthController::class, 'apiUpdateProfile'])->name('api.user.updateProfile.alias');
    Route::post('/user/change-password', [AuthController::class, 'apiChangePassword'])->name('api.user.changePassword');

    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'apiIndex'])->name('api.reports.index');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('api.reports.exportPdf');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('api.reports.exportExcel');
    });

    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketApiController::class, 'index'])->name('api.tickets.index');
        Route::post('/', [TicketApiController::class, 'store'])->name('api.tickets.store');
        Route::get('/{id}', [TicketApiController::class, 'show'])->name('api.tickets.show');
        Route::put('/{id}', [TicketApiController::class, 'update'])->name('api.tickets.update');
        Route::post('/{id}', [TicketApiController::class, 'update'])->name('api.tickets.update.post');
        Route::post('/{id}/reply', [TicketApiController::class, 'update'])->name('api.tickets.reply');
    });
});
