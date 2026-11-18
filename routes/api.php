<?php

use App\Http\Controllers\Api\AnnouncementApiController;
use App\Http\Controllers\Api\TicketApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for NetFly Customer Mobile App & Integrations
|--------------------------------------------------------------------------
*/

Route::prefix('tickets')->group(function () {
    Route::get('/', [TicketApiController::class, 'index'])->name('api.tickets.index');
    Route::post('/', [TicketApiController::class, 'store'])->name('api.tickets.store');
    Route::get('/{id}', [TicketApiController::class, 'show'])->name('api.tickets.show');
    Route::put('/{id}', [TicketApiController::class, 'update'])->name('api.tickets.update');
    Route::post('/{id}', [TicketApiController::class, 'update'])->name('api.tickets.update.post');
    Route::post('/{id}/reply', [TicketApiController::class, 'update'])->name('api.tickets.reply');
});

Route::prefix('announcements')->group(function () {
    Route::get('/', [AnnouncementApiController::class, 'index'])->name('api.announcements.index');
    Route::get('/{id}', [AnnouncementApiController::class, 'show'])->name('api.announcements.show');
});
