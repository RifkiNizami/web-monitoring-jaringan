<?php

namespace App\Http\Controllers\AdminKonten;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KontenAppSettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan link aplikasi mobile.
     */
    public function index(): View
    {
        $setting = AppSetting::where('key', 'mobile_app_download_url')->first();
        $downloadUrl = $setting?->value ?? 'https://netmediafiberoptik.com/download/netfly-mobile.apk';
        $lastUpdated = $setting?->updated_at;

        return view('admin-konten.app_setting', compact('downloadUrl', 'lastUpdated', 'setting'));
    }

    /**
     * Perbarui link unduh aplikasi mobile.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'download_url' => ['required', 'url', 'max:2048'],
        ], [
            'download_url.required' => 'Tautan unduh aplikasi mobile wajib diisi.',
            'download_url.url' => 'Format tautan unduh harus berupa URL yang valid (contoh: https://netmediafiberoptik.com/download/netfly-mobile.apk).',
            'download_url.max' => 'Panjang tautan maksimal 2048 karakter.',
        ]);

        $setting = AppSetting::setValue(
            'mobile_app_download_url',
            $validated['download_url'],
            'Tautan Unduh Aplikasi Mobile',
            'mobile_app'
        );

        // Catat aktivitas ke ActivityLog jika user terautentikasi
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'user_role' => Auth::user()->role,
                'module' => 'app_settings',
                'action' => 'update',
                'subject_label' => 'Tautan Aplikasi Mobile',
                'subject_id' => (string) $setting->id,
                'description' => 'Memperbarui tautan unduh aplikasi mobile menjadi: '.$validated['download_url'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Tautan unduh aplikasi mobile berhasil diperbarui! Barcode di landing page otomatis tersinkronisasi.');
    }
}
