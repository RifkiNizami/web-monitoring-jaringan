<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerProjectController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\LandingClientController;
use App\Http\Controllers\AnnouncementController;
use Illuminate\Support\Facades\Route;

// Landing page publik
Route::get('/', [LandingPageController::class, 'index'])->name('home');

// Form login & Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

// Rute Terproteksi Autentikasi
Route::middleware('auth')->group(function () {
    // Logout (Aman dari CSRF, wajib method POST + CSRF token)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Bersama (Tampilan disesuaikan otomatis dengan role user di controller)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin');
    Route::redirect('/user', '/admin');

    // ==========================================
    // MENU KHUSUS & UTAMA ADMIN KONTEN
    // (Bisa diakses oleh admin_konten & admin)
    // ==========================================
    Route::middleware('role:admin,admin_konten')->group(function () {
        // Kelola Klien Landing Page (CRUD)
        Route::get('/landing-clients', [LandingClientController::class, 'index'])->name('landing-clients.index');
        Route::post('/landing-clients', [LandingClientController::class, 'store'])->name('landing-clients.store');
        Route::match(['post', 'put'], '/landing-clients/{id}', [LandingClientController::class, 'update'])->name('landing-clients.update');
        Route::delete('/landing-clients/{id}', [LandingClientController::class, 'destroy'])->name('landing-clients.destroy');
        Route::match(['post', 'put'], '/landing-clients/{id}/toggle-status', [LandingClientController::class, 'toggleStatus'])->name('landing-clients.toggle-status');

        // Pemberitahuan / Announcements (Index: Admin Konten kelola, Admin Biasa tinjau)
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::match(['post', 'put'], '/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::match(['post', 'put'], '/announcements/{id}/toggle-status', [AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle-status');
    });

    // ==========================================
    // MENU KHUSUS ADMIN UTAMA / NOC
    // (Hanya untuk role: admin)
    // ==========================================
    Route::middleware('role:admin')->group(function () {
        // Customer & Project Management (Kemitraan & Infrastruktur)
        Route::get('/customers', [CustomerProjectController::class, 'index'])->name('customers.index');
        Route::post('/customers', [CustomerProjectController::class, 'storeCustomer'])->name('customers.store');
        Route::match(['post', 'put'], '/customers/{id}', [CustomerProjectController::class, 'updateCustomer'])->name('customers.update');
        Route::delete('/customers/{id}', [CustomerProjectController::class, 'destroyCustomer'])->name('customers.destroy');

        Route::get('/projects', [CustomerProjectController::class, 'index'])->name('projects.index');
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
        Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
        Route::match(['post', 'put'], '/tickets/{id}', [TicketController::class, 'update'])->name('tickets.update');
        Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');

        // Database & Manajemen Teknisi Lapangan
        Route::get('/teknisi', [TeknisiController::class, 'index'])->name('teknisi.index');
        Route::post('/teknisi', [TeknisiController::class, 'store'])->name('teknisi.store');
        Route::match(['post', 'put'], '/teknisi/{id}', [TeknisiController::class, 'update'])->name('teknisi.update');
        Route::delete('/teknisi/{id}', [TeknisiController::class, 'destroy'])->name('teknisi.destroy');
        Route::get('/teknisi/{id}/whatsapp', [TeknisiController::class, 'redirectToWhatsapp'])->name('teknisi.whatsapp');

        // Inventaris Perangkat Jaringan (Network Devices Management)
        Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');

        // Telemetri Jaringan & Live Alert (NOC Monitoring)
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::post('/monitoring/settings', [MonitoringController::class, 'saveSettings'])->name('monitoring.settings.save');
        Route::get('/monitoring/{id}', [MonitoringController::class, 'show'])->name('monitoring.show');
        Route::get('/api/monitoring/{id}/telemetry', [MonitoringController::class, 'telemetryStream'])->name('monitoring.telemetry');
        Route::post('/monitoring/alerts/{id}/resolve', [MonitoringController::class, 'resolveAlert'])->name('monitoring.alerts.resolve');
        Route::post('/monitoring/devices/{id}/ping', [MonitoringController::class, 'pollDevice'])->name('monitoring.devices.ping');

        // Laporan Kinerja SLA & Audit (Executive Report Generator & PDF Export)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
    });
});
