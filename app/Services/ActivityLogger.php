<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Catat log aktivitas sistem ke database secara aman dan terisolasi.
     *
     * @param string $module Nama modul sistem (inventaris, monitoring, tiket, akun, konten, auth)
     * @param string $action Aksi yang dilakukan (create, update, delete, upload, login, logout, login_failed)
     * @param string $description Penjelasan ringkas aktivitas
     * @param string|null $subjectLabel Label/nama objek terkait (nama perangkat, nomor tiket, email, dll)
     * @param string|null $subjectId ID referensi objek terkait
     * @param array $properties Data kontekstual tambahan (metadata file, status perubahan, dll)
     * @return ActivityLog|null
     */
    public static function log(
        string $module,
        string $action,
        string $description,
        ?string $subjectLabel = null,
        ?string $subjectId = null,
        array $properties = []
    ): ?ActivityLog {
        // Cegah pencatatan log residu selama pengujian otomatis selain ActivityLogTest
        if (app()->runningUnitTests() && !config('activity_logger.enabled_in_tests', false)) {
            return null;
        }

        try {
            $user = Auth::user();
            $request = request();

            $userName = $user ? $user->name : ($action === 'login_failed' ? ($subjectLabel ?? 'Anonim') : 'Sistem / Tamu');
            $userRole = $user ? ($user->role ?? 'user') : 'guest';

            return ActivityLog::create([
                'user_id' => $user?->id,
                'user_name' => $userName,
                'user_role' => $userRole,
                'module' => $module,
                'action' => $action,
                'subject_label' => $subjectLabel,
                'subject_id' => $subjectId !== null ? (string) $subjectId : null,
                'description' => $description,
                'properties' => !empty($properties) ? $properties : null,
                'ip_address' => $request ? $request->ip() : null,
                'user_agent' => $request ? substr((string) $request->userAgent(), 0, 500) : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat log aktivitas sistem: ' . $e->getMessage(), [
                'module' => $module,
                'action' => $action,
                'description' => $description,
                'exception' => $e,
            ]);

            return null;
        }
    }
}
