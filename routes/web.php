<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerProjectController;
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

// Customer & Project Management (Kemitraan & Infrastruktur)
Route::get('/customers', [CustomerProjectController::class, 'index'])->name('customers.index');
Route::post('/customers', [CustomerProjectController::class, 'storeCustomer'])->name('customers.store');
Route::match(['post', 'put'], '/customers/{id}', [CustomerProjectController::class, 'updateCustomer'])->name('customers.update');
Route::delete('/customers/{id}', [CustomerProjectController::class, 'destroyCustomer'])->name('customers.destroy');

Route::post('/projects', [CustomerProjectController::class, 'storeProject'])->name('projects.store');
Route::match(['post', 'put'], '/projects/{id}', [CustomerProjectController::class, 'updateProject'])->name('projects.update');
Route::delete('/projects/{id}', [CustomerProjectController::class, 'destroyProject'])->name('projects.destroy');

// Pusat Kredensial & Manajemen Akun Customer B2B
Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
Route::match(['post', 'put'], '/accounts/{id}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
Route::match(['post', 'put'], '/accounts/{id}/reset-password', [AccountController::class, 'resetPassword'])->name('accounts.reset-password');
Route::delete('/accounts/{id}', [AccountController::class, 'destroy'])->name('accounts.destroy');

// Layanan Pelanggan & Penanganan Insiden (Antrean Tiket Komplain)
Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

// Inventaris Perangkat Jaringan (Network Devices Management)
Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');