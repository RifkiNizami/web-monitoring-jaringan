<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    /**
     * Verifikasi Bearer Token pada endpoint API aplikasi mobile NetFly.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rawToken = $request->bearerToken();

        // Izinkan parameter ?token=... hanya untuk unduhan dokumen PDF/Excel dari WebView/DownloadManager mobile
        if (empty($rawToken) && $request->routeIs('api.reports.exportPdf', 'api.reports.exportExcel')) {
            $queryToken = trim((string) $request->query('token', ''));
            if ($queryToken !== '') {
                $rawToken = $queryToken;
            }
        }

        if (empty($rawToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Autentikasi diperlukan. Sertakan header Authorization: Bearer <token>.',
            ], 401);
        }

        $apiToken = ApiToken::findValidToken($rawToken);
        if (! $apiToken || ! $apiToken->user) {
            return response()->json([
                'success' => false,
                'message' => 'Token autentikasi tidak valid atau telah kedaluwarsa. Silakan login kembali.',
            ], 401);
        }

        $user = $apiToken->user;

        if (in_array($user->role, ['admin', 'admin_konten'], true)) {
            $apiToken->delete();

            return response()->json([
                'success' => false,
                'message' => 'Akses API mobile ditolak untuk akun administrator.',
            ], 403);
        }

        if (isset($user->is_active) && ! $user->is_active) {
            $apiToken->delete();

            return response()->json([
                'success' => false,
                'message' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator NOC.',
            ], 403);
        }

        // Perbarui cap waktu penggunaan terakhir (maksimal 1x per 5 menit agar hemat query)
        if (! $apiToken->last_used_at || $apiToken->last_used_at->lt(now()->subMinutes(5))) {
            $apiToken->forceFill(['last_used_at' => now()])->saveQuietly();
        }

        $request->setUserResolver(static fn () => $user);
        $request->attributes->set('current_api_token', $apiToken);

        return $next($request);
    }
}
