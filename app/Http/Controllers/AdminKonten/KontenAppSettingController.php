<?php

namespace App\Http\Controllers\AdminKonten;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class KontenAppSettingController extends Controller
{
    /**
     * Endpoint API publik untuk informasi tautan unduh APK aplikasi mobile NetFly.
     */
    public function apiAppInfo(): JsonResponse
    {
        $setting = AppSetting::where('key', 'mobile_app_download_url')->first();
        $downloadUrl = $setting?->value ?? 'https://netmediafiberoptik.com/download/netfly-mobile.apk';
        $localApkPath = public_path('download/netfly-mobile.apk');
        $hasLocalApk = File::exists($localApkPath);

        return response()->json([
            'success' => true,
            'app' => [
                'name' => 'NetFly Mobile',
                'download_url' => $downloadUrl,
                'has_hosted_apk' => $hasLocalApk,
                'apk_size_bytes' => $hasLocalApk ? File::size($localApkPath) : null,
                'updated_at' => $setting?->updated_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Tampilkan halaman pengaturan link aplikasi mobile.
     */
    public function index(): View
    {
        $setting = AppSetting::where('key', 'mobile_app_download_url')->first();
        $downloadUrl = $setting?->value ?? 'https://netmediafiberoptik.com/download/netfly-mobile.apk';
        $lastUpdated = $setting?->updated_at;
        $localApkPath = public_path('download/netfly-mobile.apk');
        $hostedApkExists = File::exists($localApkPath);
        $hostedApkSizeMb = $hostedApkExists ? round(File::size($localApkPath) / 1048576, 2) : null;

        return view('admin-konten.app_setting', compact(
            'downloadUrl',
            'lastUpdated',
            'setting',
            'hostedApkExists',
            'hostedApkSizeMb'
        ));
    }

    /**
     * Perbarui link unduh aplikasi mobile (via URL langsung atau upload file .apk).
     *
     * [PANDUAN UBAH BARCODE / APK ADMIN KONTEN]
     * - Ubah hak akses role pada pengecekan Auth::user()?->role !== 'admin_konten'
     * - Ubah batas ukuran upload APK pada aturan validasi 'max:153600' (150 MB dalam KB)
     * - Ubah lokasi/nama file APK tersimpan pada public_path('download') & 'netfly-mobile.apk'
     * - URL final disimpan ke tabel app_settings dengan key 'mobile_app_download_url'
     */
    public function update(Request $request): RedirectResponse
    {
        if (Auth::user()?->role !== 'admin_konten') {
            return redirect()->route('app-setting.index')
                ->with('error', 'Akses dibatasi hanya untuk Admin Konten.');
        }

        $validated = $request->validate([
            'download_url' => ['required_without:apk_file', 'nullable', 'url', 'max:2048'],
            'apk_file' => ['nullable', 'file', 'max:153600'],
        ], [
            'download_url.required_without' => 'Isi tautan unduh aplikasi mobile atau unggah file .apk.',
            'download_url.url' => 'Format tautan unduh harus berupa URL yang valid (contoh: https://netmediafiberoptik.com/download/netfly-mobile.apk).',
            'download_url.max' => 'Panjang tautan maksimal 2048 karakter.',
            'apk_file.max' => 'Ukuran file APK maksimal 150 MB.',
        ]);

        $finalUrl = trim((string) ($validated['download_url'] ?? ''));

        if ($request->hasFile('apk_file')) {
            $apk = $request->file('apk_file');
            $ext = strtolower($apk->getClientOriginalExtension());
            if ($ext !== 'apk') {
                return redirect()->back()
                    ->withErrors(['apk_file' => 'File yang diunggah wajib berekstensi .apk.'])
                    ->withInput();
            }

            // Verifikasi magic bytes ZIP/APK (PK\x03\x04) untuk mencegah upload skrip palsu
            $handle = @fopen($apk->getRealPath(), 'rb');
            $magic = $handle ? fread($handle, 4) : '';
            if ($handle) {
                fclose($handle);
            }
            if ($magic !== "PK\x03\x04") {
                return redirect()->back()
                    ->withErrors(['apk_file' => 'File APK tidak valid (signature arsip Android tidak cocok).'])
                    ->withInput();
            }

            $destination = public_path('download');
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $apk->move($destination, 'netfly-mobile.apk');
            $finalUrl = rtrim(config('app.url') ?: url('/'), '/').'/download/netfly-mobile.apk';
        }

        $setting = AppSetting::setValue(
            'mobile_app_download_url',
            $finalUrl,
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
                'description' => ($request->hasFile('apk_file') ? 'Mengunggah file APK baru & memperbarui tautan menjadi: ' : 'Memperbarui tautan unduh aplikasi mobile menjadi: ').$finalUrl,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Tautan unduh aplikasi mobile berhasil diperbarui! Barcode di landing page otomatis tersinkronisasi.');
    }
}
