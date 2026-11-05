<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan halaman audit trail / log aktivitas sistem.
     */
    public function index(Request $request): View
    {
        // Konversi rentang hari ini dari timezone Asia/Jakarta (WIB) ke UTC untuk database
        $startOfTodayUtc = Carbon::now('Asia/Jakarta')->startOfDay()->setTimezone('UTC');
        $endOfTodayUtc = Carbon::now('Asia/Jakarta')->endOfDay()->setTimezone('UTC');

        // Kartu ringkasan aktivitas hari ini
        $summary = [
            'today_total' => ActivityLog::whereBetween('created_at', [$startOfTodayUtc, $endOfTodayUtc])->count(),
            'today_failed_login' => ActivityLog::whereBetween('created_at', [$startOfTodayUtc, $endOfTodayUtc])
                ->where('action', 'login_failed')
                ->count(),
            'today_deletions' => ActivityLog::whereBetween('created_at', [$startOfTodayUtc, $endOfTodayUtc])
                ->where('action', 'delete')
                ->count(),
        ];

        // Parameter filter
        $search = $request->query('search');
        $module = $request->query('module');
        $action = $request->query('action');
        $userId = $request->query('user_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = ActivityLog::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('subject_label', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if (!empty($module) && $module !== 'all') {
            $query->where('module', $module);
        }

        if (!empty($action) && $action !== 'all') {
            $query->where('action', $action);
        }

        if (!empty($userId) && $userId !== 'all') {
            $query->where('user_id', $userId);
        }

        // Filter Rentang Tanggal (diurai berdasarkan zona waktu Indonesia WIB / Asia/Jakarta)
        if (!empty($dateFrom)) {
            try {
                $startUtc = Carbon::parse($dateFrom, 'Asia/Jakarta')->startOfDay()->setTimezone('UTC');
                $query->where('created_at', '>=', $startUtc);
            } catch (\Throwable $e) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
        }

        if (!empty($dateTo)) {
            try {
                $endUtc = Carbon::parse($dateTo, 'Asia/Jakarta')->endOfDay()->setTimezone('UTC');
                $query->where('created_at', '<=', $endUtc);
            } catch (\Throwable $e) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
        }

        $logs = $query->latest('id')->paginate(25)->withQueryString();

        // Opsi modul dan aksi untuk filter
        $modules = [
            'inventaris' => 'Inventaris Perangkat',
            'monitoring' => 'Monitoring Telemetri',
            'tiket' => 'Tiket Insiden',
            'akun' => 'Manajemen Akun',
            'konten' => 'Konten & Publikasi',
            'auth' => 'Autentikasi & Keamanan',
        ];

        $actions = [
            'create' => 'Create (Tambah)',
            'update' => 'Update (Ubah)',
            'delete' => 'Delete (Hapus)',
            'upload' => 'Upload (Unggah)',
            'login' => 'Login (Masuk)',
            'logout' => 'Logout (Keluar)',
            'login_failed' => 'Login Gagal',
        ];

        $users = User::select('id', 'name', 'role')->orderBy('name')->get();

        return view('admin.activity-logs.index', compact(
            'logs',
            'summary',
            'modules',
            'actions',
            'users',
            'search',
            'module',
            'action',
            'userId',
            'dateFrom',
            'dateTo'
        ));
    }
}
