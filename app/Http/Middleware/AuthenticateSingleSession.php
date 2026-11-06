<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Single Device Login.
 *
 * Turunan AuthenticateSession bawaan Laravel yang:
 * - Memutus sesi lama ketika hash password di sesi tidak cocok lagi
 *   (terjadi setelah Auth::logoutOtherDevices() dipanggil saat login di perangkat lain).
 * - Menampilkan flash message ke perangkat yang tertendang, termasuk ketika
 *   baris sesinya sudah dihapus dari tabel `sessions`.
 */
class AuthenticateSingleSession extends AuthenticateSession
{
    /**
     * Pesan yang ditampilkan ke perangkat yang sesinya diputus.
     */
    public const KICKED_MESSAGE = 'Sesi Anda telah berakhir karena akun ini telah login di perangkat lain.';

    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if ($request->hasSession() && Cache::pull(self::kickedCacheKey($request->session()->getId()))) {
            // Baris sesi perangkat ini sudah dihapus karena akun login di perangkat lain
            $request->session()->flash('error', self::KICKED_MESSAGE);
        }

        return parent::handle($request, $next);
    }

    /**
     * Log the user out of the application (dengan flash message).
     *
     * @throws AuthenticationException
     */
    protected function logout($request)
    {
        $guard = $this->guard();
        if ($guard instanceof SessionGuard) {
            $guard->logoutCurrentDevice();
        } elseif (method_exists($guard, 'logoutCurrentDevice')) {
            $guard->logoutCurrentDevice();
        }

        $request->session()->flush();
        $request->session()->flash('error', self::KICKED_MESSAGE);

        $defaultDriver = method_exists($this->auth, 'getDefaultDriver')
            ? $this->auth->getDefaultDriver()
            : config('auth.defaults.guard', 'web');

        throw new AuthenticationException(
            'Unauthenticated.', [$defaultDriver], $this->redirectTo($request)
        );
    }

    /**
     * Perbarui hash password di sesi saat ini.
     *
     * Wajib dipanggil setelah Auth::logoutOtherDevices(): SessionGuard::login() menyimpan
     * hash password LAMA ke sesi, sedangkan logoutOtherDevices() me-rehash password di database.
     * Tanpa pembaruan ini, perangkat yang baru login justru ikut ter-logout di request berikutnya.
     */
    public static function refreshCurrentSessionHash(Request $request): void
    {
        $guard = auth()->guard();
        $user = $guard->user();

        if (! $user || ! ($guard instanceof SessionGuard)) {
            return;
        }

        $authManager = auth();
        $defaultDriver = $authManager instanceof AuthManager 
            ? $authManager->getDefaultDriver() 
            : config('auth.defaults.guard', 'web');

        $request->session()->put(
            'password_hash_'.$defaultDriver,
            $guard->hashPasswordForCookie($user->getAuthPassword())
        );
    }

    /**
     * Hapus seluruh baris sesi lain milik user dari tabel `sessions`
     * dan tandai ID-nya agar perangkat terkait mendapat flash message.
     *
     * @return int Jumlah sesi lain yang dihapus
     */
    public static function terminateOtherSessions(Request $request, int|string $userId): int
    {
        if (config('session.driver') !== 'database') {
            return 0;
        }

        $table = config('session.table', 'sessions');
        $connection = DB::connection(config('session.connection'));
        $currentId = $request->session()->getId();

        $otherIds = $connection->table($table)
            ->where('user_id', $userId)
            ->where('id', '!=', $currentId)
            ->pluck('id');

        if ($otherIds->isEmpty()) {
            return 0;
        }

        $ttl = now()->addMinutes((int) config('session.lifetime', 120));
        foreach ($otherIds as $id) {
            Cache::put(self::kickedCacheKey($id), true, $ttl);
        }

        return $connection->table($table)->whereIn('id', $otherIds)->delete();
    }

    /**
     * Kunci cache penanda sesi yang diputus.
     */
    protected static function kickedCacheKey(string $sessionId): string
    {
        return 'single_session_kicked:'.hash('sha256', $sessionId);
    }
}
