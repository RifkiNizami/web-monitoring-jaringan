<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page publik (tanpa tombol login sesuai instruksi)
Route::get('/', [LandingPageController::class, 'index'])->name('home');

// Form login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::any('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Admin (mengarah ke file admin.blade.php)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/admin', [DashboardController::class, 'index'])->name('admin');
Route::redirect('/user', '/admin');

// Layanan Pelanggan & Penanganan Insiden (Antrean Tiket Komplain)
Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

// Inventaris Perangkat Jaringan (Network Devices Management)
Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');