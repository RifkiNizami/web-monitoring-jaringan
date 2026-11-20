<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AuthenticateSingleSession;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'email' => ['required', 'string'],
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

        // Dukung Hybrid Login: Cocokkan ke kolom email atau username secara fleksibel
        $fieldType = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $authCredentials = [
            $fieldType => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($authCredentials, $remember)) {
            $user = Auth::user();

            // Proteksi Role Web Portal: Akun Pelanggan (customer_representative) hanya untuk Mobile App
            // Izinkan jika request berasal dari aplikasi mobile NetFly (via User-Agent NetFly-Mobile)
            $isMobileApp = str_contains($request->userAgent() ?? '', 'NetFly-Mobile')
                || $request->header('X-App-Platform') === 'NetFly-Mobile';

            if (! $isMobileApp && ! in_array($user->role, ['admin', 'admin_konten'], true)) {
                ActivityLogger::log(
                    'auth',
                    'login_failed',
                    "Percobaan login portal web oleh akun pelanggan '{$user->name}' ditolak (Khusus Aplikasi Mobile)",
                    $user->name,
                    (string) $user->id,
                    [
                        'email' => $user->email,
                        'role' => $user->role,
                        'reason' => 'mobile_only_role',
                    ]
                );

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $mobileMsg = 'Akses Ditolak: Akun pelanggan hanya dapat digunakan melalui aplikasi mobile NetFly.';

                return redirect()->route('login')
                    ->with('error', $mobileMsg)
                    ->withErrors(['email' => $mobileMsg])
                    ->onlyInput('email');
            }

            // Proteksi Status Akun Aktif
            if (isset($user->is_active) && ! $user->is_active) {
                ActivityLogger::log(
                    'auth',
                    'login_failed',
                    "Percobaan login oleh akun nonaktif '{$user->name}' ditolak",
                    $user->name,
                    (string) $user->id,
                    [
                        'email' => $user->email,
                        'role' => $user->role,
                        'reason' => 'inactive_account',
                    ]
                );

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $inactiveMsg = 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator NOC.';

                return redirect()->route('login')
                    ->with('error', $inactiveMsg)
                    ->withErrors(['email' => $inactiveMsg])
                    ->onlyInput('email');
            }

            // Berhasil login: reset hitungan percobaan
            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();

            // Single Device Login: putus sesi akun ini di perangkat/browser lain
            Auth::logoutOtherDevices($credentials['password']);
            AuthenticateSingleSession::refreshCurrentSessionHash($request);
            $terminatedSessions = AuthenticateSingleSession::terminateOtherSessions($request, Auth::id());

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

            if ($request->wantsJson() || $isMobileApp) {
                $user->load('customer');
                $customer = $user->customer;

                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'username' => $user->username,
                        'role' => $user->role,
                        'role_label' => $user->role === 'customer_residential' ? 'Customer Rumahan' : 'PIC Perwakilan Customer',
                        'phone' => $user->phone ?? ($customer?->phone ?? '-'),
                        'customer_id' => $user->customer_id,
                        'customer' => $customer ? [
                            'id' => $customer->id,
                            'code' => $customer->code ?? 'CUST-0001',
                            'customer_type' => $customer->customer_type ?? 'perusahaan',
                            'name' => $customer->name,
                            'category' => $customer->category ?? ($customer->customer_type === 'rumahan' ? 'Customer Rumahan' : 'Mitra Korporat'),
                            'pic_name' => ($customer->pic_name && $customer->pic_name !== '-') ? $customer->pic_name : $user->name,
                            'email' => $customer->email ?? $user->email,
                            'phone' => $customer->phone ?? $user->phone,
                            'address' => $customer->address ?? '-',
                            'package' => $customer->package ?? ($customer->note ?? 'Layanan Internet NetFly'),
                            'status' => $customer->status ?? 'Aktif',
                        ] : null,
                    ],
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        // Gagal login: tambah hitungan percobaan (decay 60 detik)
        RateLimiter::hit($throttleKey, 60);

        ActivityLogger::log(
            'auth',
            'login_failed',
            "Percobaan login gagal dengan identitas '{$credentials['email']}'",
            $credentials['email'],
            null,
            ['attempted_email' => $credentials['email']]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Email atau kata sandi yang Anda masukkan salah.',
            ], 422);
        }

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

    /**
     * API Profile Pengguna & Customer untuk Mobile App NetFly.
     */
    public function apiProfile(Request $request): JsonResponse
    {
        $identifier = $request->query('email') ?? $request->query('username');
        if (! $identifier) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter email atau username wajib disertakan.',
            ], 400);
        }

        $user = User::with(['customer.devices', 'customer.monitoringDevices'])
            ->where('email', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan dalam database.',
            ], 404);
        }

        $customer = $user->customer;

        $allCustomerDevices = [];
        if ($customer) {
            $monitoringList = $customer->monitoringDevices->map(function ($m) {
                $st = strtolower($m->status ?: 'online');
                $isOnline = in_array($st, ['online', 'tersedia', 'terpakai']);

                return [
                    'id' => (string) $m->id,
                    'hostname' => $m->hostname,
                    'type' => $m->unit_type ?: 'Router',
                    'model' => $m->model ?: ($m->unit_type ?: 'Router Node'),
                    'ip_address' => $m->ip_address ?: '192.168.1.1',
                    'status' => ucfirst($m->status ?: 'online'),
                    'status_state' => $st,
                    'is_online' => $isOnline,
                    'uptime' => $m->uptime ?: '10 hari 05 jam',
                    'rx_rate' => $m->rx_mbps ?: '50.0 Mbps',
                    'tx_rate' => $m->tx_mbps ?: '35.0 Mbps',
                    'ping_ms' => $m->last_ping_ms !== null ? "{$m->last_ping_ms} ms" : '8 ms',
                    'last_ping_ms' => $m->last_ping_ms ?? 8,
                    'packet_loss' => (string) ($m->packet_loss ?? 0).'%',
                    'cpu_load' => (float) ($m->cpu_load ?? 0.0),
                    'ram_load' => (float) ($m->ram_load ?? 0.0),
                    'rack_location' => $m->rack_location ?: 'CPE Pelanggan',
                ];
            });

            $deviceList = $customer->devices->map(function ($d) {
                $st = strtolower($d->status_state ?: 'online');
                $isOnline = in_array($st, ['online', 'available', 'in_use', 'tersedia', 'terpakai']);

                return [
                    'id' => (string) $d->id,
                    'hostname' => $d->hostname,
                    'type' => $d->type ?: 'Router',
                    'model' => $d->brand_model ?: ($d->type ?: 'Hardware Node'),
                    'ip_address' => $d->ip_address ?: '10.200.1.1',
                    'status' => ucfirst($d->status ?: 'Tersedia'),
                    'status_state' => $st,
                    'is_online' => $isOnline,
                    'uptime' => $d->uptime ?: '14 hari 02 jam',
                    'rx_rate' => $d->rx_rate ?: '50.0 Mbps',
                    'tx_rate' => $d->tx_rate ?: '35.0 Mbps',
                    'ping_ms' => $d->last_ping_ms !== null ? "{$d->last_ping_ms} ms" : '10 ms',
                    'last_ping_ms' => $d->last_ping_ms ?? 10,
                    'packet_loss' => (string) ($d->packet_loss ?? '0.00').'%',
                    'cpu_load' => (float) ($d->cpu_load ?? 0.0),
                    'ram_load' => (float) ($d->ram_load ?? 0.0),
                    'rack_location' => $d->rack_location ?: 'Slot POP NetFly',
                ];
            });

            $allCustomerDevices = $monitoringList->concat($deviceList)->unique('hostname')->values()->all();
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role,
                'role_label' => $user->role === 'customer_residential' ? 'Customer Rumahan' : 'PIC Perwakilan Customer',
                'phone' => $user->phone ?? ($customer?->phone ?? '-'),
                'customer_id' => $user->customer_id,
                'customer' => $customer ? [
                    'id' => $customer->id,
                    'code' => $customer->code ?? 'CUST-0001',
                    'customer_type' => $customer->customer_type ?? 'perusahaan',
                    'name' => $customer->name,
                    'category' => $customer->category ?? ($customer->customer_type === 'rumahan' ? 'Customer Rumahan' : 'Mitra Korporat'),
                    'pic_name' => ($customer->pic_name && $customer->pic_name !== '-') ? $customer->pic_name : $user->name,
                    'email' => $customer->email ?? $user->email,
                    'phone' => $customer->phone ?? $user->phone,
                    'address' => $customer->address ?? '-',
                    'package' => $customer->package ?? ($customer->note ?? 'Layanan Internet NetFly'),
                    'status' => $customer->status ?? 'Aktif',
                    'devices' => $allCustomerDevices,
                ] : null,
            ],
        ]);
    }
}
