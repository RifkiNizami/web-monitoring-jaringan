<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AuthenticateSingleSession;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('admin.login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = $this->throttleKey($request);

        // Periksa apakah user telah melebihi batas percobaan login (5 kali per menit)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Berhasil login: reset hitungan percobaan
            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();

            // Single Device Login: putus sesi akun ini di perangkat/browser lain
            Auth::logoutOtherDevices($credentials['password']);
            AuthenticateSingleSession::refreshCurrentSessionHash($request);
            $terminatedSessions = AuthenticateSingleSession::terminateOtherSessions($request, Auth::id());

            $user = Auth::user();
            ActivityLogger::log(
                'auth',
                'login',
                "Pengguna '{$user->name}' berhasil login ke sistem (Sesi perangkat lain otomatis ditutup)",
                $user->name,
                (string) $user->id,
                [
                    'email' => $user->email,
                    'role' => $user->role,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'terminated_sessions' => $terminatedSessions,
                ]
            );

            return redirect()->intended(route('dashboard'));
        }

        // Gagal login: tambah hitungan percobaan (decay 60 detik)
        RateLimiter::hit($throttleKey, 60);

        ActivityLogger::log(
            'auth',
            'login_failed',
            "Percobaan login gagal dengan email '{$credentials['email']}'",
            $credentials['email'],
            null,
            ['attempted_email' => $credentials['email']]
        );

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Format throttle key berdasarkan IP dan email input.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    /**
     * Proses logout user (Hanya via method POST untuk mencegah CSRF vulnerability).
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogger::log(
                'auth',
                'logout',
                "Pengguna '{$user->name}' keluar dari sistem",
                $user->name,
                (string) $user->id,
                ['email' => $user->email, 'role' => $user->role]
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
