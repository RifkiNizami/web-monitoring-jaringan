<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CustomerProjectController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\MonitoringDeviceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TeknisiController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\AdminKonten\KontenAppSettingController;
use App\Http\Controllers\AdminKonten\KontenDashboardController;
use App\Http\Controllers\AdminKonten\KontenKlienController;
use App\Http\Controllers\AdminKonten\KontenPemberitahuanController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LandingPageController;
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
    Route::middleware('role:admin,admin_konten')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/admin', [DashboardController::class, 'index'])->name('admin');
        Route::redirect('/user', '/admin');
    });

    // ==========================================
    // MENU KHUSUS & UTAMA ADMIN KONTEN
    // (Bisa diakses oleh admin_konten & admin)
    // ==========================================
    Route::middleware('role:admin,admin_konten')->group(function () {
        // Dashboard Admin Konten
        Route::get('/admin-konten', [KontenDashboardController::class, 'index'])->name('admin-konten.dashboard');

        // Kelola Klien Landing Page (CRUD)
        Route::get('/klien', [KontenKlienController::class, 'index'])->name('klien.index');
        Route::get('/klien/create', [KontenKlienController::class, 'create'])->name('klien.create');
        Route::post('/klien', [KontenKlienController::class, 'store'])->name('klien.store');
        Route::get('/klien/{id}/edit', [KontenKlienController::class, 'edit'])->name('klien.edit');
        Route::match(['post', 'put'], '/klien/{id}', [KontenKlienController::class, 'update'])->name('klien.update');
        Route::delete('/klien/{id}', [KontenKlienController::class, 'destroy'])->name('klien.destroy');
        Route::match(['post', 'put'], '/klien/{id}/toggle-status', [KontenKlienController::class, 'toggleStatus'])->name('klien.toggle-status');

        // Alias URL /landing-clients agar tautan & test kompatibel
        Route::get('/landing-clients', [KontenKlienController::class, 'index'])->name('landing-clients.index');
        Route::get('/landing-clients/create', [KontenKlienController::class, 'create'])->name('landing-clients.create');
        Route::post('/landing-clients', [KontenKlienController::class, 'store'])->name('landing-clients.store');
        Route::get('/landing-clients/{id}/edit', [KontenKlienController::class, 'edit'])->name('landing-clients.edit');
        Route::match(['post', 'put'], '/landing-clients/{id}', [KontenKlienController::class, 'update'])->name('landing-clients.update');
        Route::delete('/landing-clients/{id}', [KontenKlienController::class, 'destroy'])->name('landing-clients.destroy');
        Route::match(['post', 'put'], '/landing-clients/{id}/toggle-status', [KontenKlienController::class, 'toggleStatus'])->name('landing-clients.toggle-status');

        // Pemberitahuan / Announcements (Index: Admin Konten kelola, Admin Biasa tinjau)
        Route::get('/pemberitahuan', [KontenPemberitahuanController::class, 'index'])->name('pemberitahuan.index');
        Route::get('/pemberitahuan/create', [KontenPemberitahuanController::class, 'create'])->name('pemberitahuan.create');
        Route::post('/pemberitahuan', [KontenPemberitahuanController::class, 'store'])->name('pemberitahuan.store');
        Route::get('/pemberitahuan/{id}/edit', [KontenPemberitahuanController::class, 'edit'])->name('pemberitahuan.edit');
        Route::match(['post', 'put'], '/pemberitahuan/{id}', [KontenPemberitahuanController::class, 'update'])->name('pemberitahuan.update');
        Route::delete('/pemberitahuan/{id}', [KontenPemberitahuanController::class, 'destroy'])->name('pemberitahuan.destroy');
        Route::match(['post', 'put'], '/pemberitahuan/{id}/toggle-status', [KontenPemberitahuanController::class, 'toggleStatus'])->name('pemberitahuan.toggle-status');

        // Alias URL /announcements agar tautan & test kompatibel
        Route::get('/announcements', [KontenPemberitahuanController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/create', [KontenPemberitahuanController::class, 'create'])->name('announcements.create');
        Route::post('/announcements', [KontenPemberitahuanController::class, 'store'])->name('announcements.store');
        Route::get('/announcements/{id}/edit', [KontenPemberitahuanController::class, 'edit'])->name('announcements.edit');
        Route::match(['post', 'put'], '/announcements/{id}', [KontenPemberitahuanController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{id}', [KontenPemberitahuanController::class, 'destroy'])->name('announcements.destroy');
        Route::match(['post', 'put'], '/announcements/{id}/toggle-status', [KontenPemberitahuanController::class, 'toggleStatus'])->name('announcements.toggle-status');

        // Pengaturan Tautan Aplikasi Mobile (Barcode Landing Page)
        Route::get('/pengaturan-aplikasi', [KontenAppSettingController::class, 'index'])->name('app-setting.index');
        Route::match(['post', 'put'], '/pengaturan-aplikasi', [KontenAppSettingController::class, 'update'])->name('app-setting.update');
    });

    // ==========================================
    // MENU KHUSUS ADMIN UTAMA / NOC
    // (Hanya untuk role: admin)
    // ==========================================
    Route::middleware('role:admin')->group(function () {
        // Customer & Project Management (Kemitraan & Infrastruktur)
        Route::get('/customers', [CustomerProjectController::class, 'index'])->name('customers.index');
        Route::get('/customers/rumahan/create', [CustomerProjectController::class, 'createRumahan'])->name('customers.rumahan.create');
        Route::get('/customers/rumahan/{id}/edit', [CustomerProjectController::class, 'editRumahan'])->name('customers.rumahan.edit');
        Route::get('/customers/perusahaan/create', [CustomerProjectController::class, 'createPerusahaan'])->name('customers.perusahaan.create');
        Route::get('/customers/perusahaan/{id}/edit', [CustomerProjectController::class, 'editPerusahaan'])->name('customers.perusahaan.edit');
        Route::post('/customers', [CustomerProjectController::class, 'storeCustomer'])->name('customers.store');
        Route::match(['post', 'put'], '/customers/{id}', [CustomerProjectController::class, 'updateCustomer'])->name('customers.update');
        Route::delete('/customers/{id}', [CustomerProjectController::class, 'destroyCustomer'])->name('customers.destroy');

        Route::get('/projects', [CustomerProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [CustomerProjectController::class, 'createPerusahaan'])->name('projects.create');
        Route::get('/projects/{id}/edit', [CustomerProjectController::class, 'editPerusahaan'])->name('projects.edit');
        Route::post('/projects', [CustomerProjectController::class, 'storeProject'])->name('projects.store');
        Route::match(['post', 'put'], '/projects/{id}', [CustomerProjectController::class, 'updateProject'])->name('projects.update');
        Route::delete('/projects/{id}', [CustomerProjectController::class, 'destroyProject'])->name('projects.destroy');

        // Pusat Kredensial & Manajemen Akun Customer B2B
        Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
        Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
        Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
        Route::get('/accounts/{id}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
        Route::match(['post', 'put'], '/accounts/{id}', [AccountController::class, 'update'])->name('accounts.update');
        Route::match(['post', 'put'], '/accounts/{id}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
        Route::match(['post', 'put'], '/accounts/{id}/reset-password', [AccountController::class, 'resetPassword'])->name('accounts.reset-password');
        Route::delete('/accounts/{id}', [AccountController::class, 'destroy'])->name('accounts.destroy');

        // Database & Manajemen Teknisi Lapangan
        Route::get('/teknisi', [TeknisiController::class, 'index'])->name('teknisi.index');
        Route::get('/teknisi/create', [TeknisiController::class, 'create'])->name('teknisi.create');
        Route::post('/teknisi', [TeknisiController::class, 'store'])->name('teknisi.store');
        Route::get('/teknisi/{id}/edit', [TeknisiController::class, 'edit'])->name('teknisi.edit');
        Route::match(['post', 'put'], '/teknisi/{id}', [TeknisiController::class, 'update'])->name('teknisi.update');
        Route::delete('/teknisi/{id}', [TeknisiController::class, 'destroy'])->name('teknisi.destroy');
        Route::get('/teknisi/{id}/whatsapp', [TeknisiController::class, 'redirectToWhatsapp'])->name('teknisi.whatsapp');

        // Layanan Pelanggan & Penanganan Insiden (Antrean Tiket Komplain)
        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
        Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
        Route::get('/tickets/{id}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
        Route::match(['post', 'put'], '/tickets/{id}', [TicketController::class, 'update'])->name('tickets.update');
        Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');

        // Inventaris Perangkat Jaringan (Network Devices Management)
        Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
        Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
        Route::get('/devices/export-excel', [DeviceController::class, 'exportExcel'])->name('devices.exportExcel');
        Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
        Route::get('/devices/{id}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
        Route::match(['put', 'post'], '/devices/{id}', [DeviceController::class, 'update'])->name('devices.update');
        Route::delete('/devices/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');

        // Telemetri Jaringan & Live Alert (NOC Monitoring)
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/devices/create', [MonitoringDeviceController::class, 'create'])->name('monitoring.devices.create');
        Route::post('/monitoring/devices', [MonitoringDeviceController::class, 'store'])->name('monitoring.devices.store');
        Route::get('/monitoring/devices/{id}/edit', [MonitoringDeviceController::class, 'edit'])->name('monitoring.devices.edit');
        Route::match(['put', 'post'], '/monitoring/devices/{id}', [MonitoringDeviceController::class, 'update'])->name('monitoring.devices.update');
        Route::delete('/monitoring/devices/{id}', [MonitoringDeviceController::class, 'destroy'])->name('monitoring.devices.destroy');
        Route::get('/monitoring/devices/{id}', [MonitoringController::class, 'show'])->name('monitoring.show');
        Route::get('/monitoring/devices/{id}/telemetry', [MonitoringController::class, 'telemetryStream'])->name('monitoring.telemetry');
        Route::post('/monitoring/devices/{id}/ping', [MonitoringController::class, 'pollDevice'])->name('monitoring.devices.ping');
        Route::post('/monitoring/poll-all', [MonitoringController::class, 'pollAllNodes'])->name('monitoring.poll-all');
        Route::post('/monitoring/alerts/{id}/resolve', [MonitoringController::class, 'resolveAlert'])->name('monitoring.alerts.resolve');
        Route::post('/monitoring/alerts/resolve-all', [MonitoringController::class, 'resolveAllAlerts'])->name('monitoring.alerts.resolve-all');
        Route::post('/monitoring/settings', [MonitoringDeviceController::class, 'saveSettings'])->name('monitoring.settings.save');

        // Laporan Kinerja SLA & Audit (Executive Report Generator & PDF/Excel Export)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.exportExcel');

        // Log Aktivitas Sistem (Activity Log Audit Trail)
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });
});
