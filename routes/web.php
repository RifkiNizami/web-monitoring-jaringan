<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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

// Dashboard User (mengarah ke file user.blade.php)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/user', [DashboardController::class, 'index'])->name('user');