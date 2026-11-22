<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Controller;
use App\Http\Middleware\AuthenticateSingleSession;
use App\Models\Customer;
use App\Models\MonitoringDevice;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\MikrotikService;
use Illuminate\Http\JsonResponse;
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
     * API Login Cepat 1-Request (Single Round-Trip) untuk Mobile App NetFly.
     */
    public function apiLogin(Request $request): JsonResponse
    {
        $identifier = trim((string) ($request->input('email') ?? $request->input('username') ?? ''));
        $password = (string) $request->input('password', '');

        if ($identifier === '' || $password === '') {
            return response()->json([
                'success' => false,
                'message' => 'Email/username dan kata sandi wajib diisi.',
            ], 422);
        }

        $user = User::with(['customer.devices', 'customer.monitoringDevices'])
            ->where('email', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            ActivityLogger::log(
                'auth',
                'login_failed',
                "Percobaan login mobile gagal dengan identitas '{$identifier}'",
                $identifier,
                null,
                ['attempted_email' => $identifier]
            );

            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi yang Anda masukkan salah.',
            ], 422);
        }

        if (isset($user->is_active) && ! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator NOC.',
            ], 403);
        }

        ActivityLogger::log(
            'auth',
            'login',
            "Pengguna '{$user->name}' berhasil login ke Aplikasi Mobile NetFly",
            $user->name,
            (string) $user->id,
            [
                'email' => $user->email,
                'role' => $user->role,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return $this->buildMobileUserPayloadResponse($user);
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

        return $this->buildMobileUserPayloadResponse($user);
    }

    /**
     * Helper penyusun payload profil + daftar perangkat monitoring yang terikat dengan user/customer.
     */
    protected function buildMobileUserPayloadResponse(User $user): JsonResponse
    {
        $customer = $user->customer;

        // Auto-link jika user belum memiliki customer_id namun cocok berdasarkan email atau nama di tabel customers
        if (! $customer) {
            $customer = Customer::where('email', $user->email)
                ->orWhereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($user->name))])
                ->first();

            if ($customer && empty($user->customer_id)) {
                $user->update(['customer_id' => $customer->id]);
            }
        }

        $allCustomerDevices = [];
        $matchedMonitoringDevices = collect();

        if ($customer || $user) {
            $projectIds = $customer
                ? $customer->projects()->pluck('id')->all()
                : [];

            $matchedMonitoringDevices = MonitoringDevice::where(function ($q) use ($customer, $user, $projectIds) {
                if ($customer) {
                    $q->where('customer_id', $customer->id)
                        ->orWhereRaw('LOWER(TRIM(customer)) = ?', [strtolower(trim($customer->name))]);
                }
                if (! empty($user->name)) {
                    $q->orWhereRaw('LOWER(TRIM(customer)) = ?', [strtolower(trim($user->name))]);
                }
                if (! empty($projectIds)) {
                    $q->orWhereIn('project_id', $projectIds);
                }
            })->orderBy('id', 'asc')->get();

            foreach ($matchedMonitoringDevices as $mNode) {
                // Sinkronkan foreign key customer_id secara permanen jika sebelumnya hanya terhubung lewat nama
                if ($customer && (int) $mNode->customer_id !== (int) $customer->id) {
                    $mNode->update(['customer_id' => $customer->id]);
                }
            }

            if (! app()->runningUnitTests() && $matchedMonitoringDevices->isNotEmpty()) {
                $mikrotikService = new MikrotikService;
                foreach ($matchedMonitoringDevices as $mNode) {
                    MonitoringController::pollSingleNode($mNode, $mikrotikService);
                }
            }

            $monitoringList = $matchedMonitoringDevices->map(function ($m) {
                $st = strtolower($m->status ?: 'online');
                $isOnline = in_array($st, ['online', 'tersedia', 'terpakai']);

                $ifaces = is_array($m->interfaces) ? $m->interfaces : [];
                $primaryIface = collect($ifaces)->first(fn ($i) => ($i['type'] ?? '') !== 'loopback' && ($i['name'] ?? '') !== 'lo') ?? ($ifaces[0] ?? []);

                return [
                    'id' => (string) $m->id,
                    'hostname' => $m->hostname,
                    'project' => $m->project ?: '-',
                    'type' => $m->unit_type ?: 'Router',
                    'model' => $m->model ?: ($m->unit_type ?: '-'),
                    'ip_address' => $m->ip_address ?: '-',
                    'mac_address' => $primaryIface['mac_address'] ?? '-',
                    'interface_name' => $primaryIface['name'] ?? 'ether1',
                    'interface_speed' => (string) ($primaryIface['speed'] ?? '1500'),
                    'rx_total' => $primaryIface['rx'] ?? '0 B total',
                    'tx_total' => $primaryIface['tx'] ?? '0 B total',
                    'status' => ucfirst($m->status ?: 'online'),
                    'status_state' => $st,
                    'is_online' => $isOnline,
                    'uptime' => $m->uptime ?: ($isOnline ? 'Online' : 'Offline'),
                    'rx_rate' => $m->rx_mbps ?: '0.0 Mbps',
                    'tx_rate' => $m->tx_mbps ?: '0.0 Mbps',
                    'ping_ms' => $m->last_ping_ms !== null ? "{$m->last_ping_ms} ms" : '-',
                    'last_ping_ms' => $m->last_ping_ms ?? null,
                    'packet_loss' => (string) ($m->packet_loss ?? 0).'%',
                    'cpu_load' => (float) ($m->cpu_load ?? 0.0),
                    'ram_load' => (float) ($m->ram_load ?? 0.0),
                    'rack_location' => $m->rack_location ?: '-',
                ];
            });

            $assignedDevices = $customer ? $customer->devices : collect();
            $deviceList = $assignedDevices->map(function ($d) {
                $st = strtolower($d->status_state ?: 'online');
                $isOnline = in_array($st, ['online', 'available', 'in_use', 'tersedia', 'terpakai']);

                return [
                    'id' => (string) $d->id,
                    'hostname' => $d->hostname,
                    'project' => '-',
                    'type' => $d->type ?: 'Router',
                    'model' => $d->brand_model ?: ($d->type ?: '-'),
                    'ip_address' => $d->ip_address ?: '-',
                    'mac_address' => $d->mac_address ?: '-',
                    'interface_name' => 'ether1',
                    'interface_speed' => '1 Gbps',
                    'rx_total' => '0 B total',
                    'tx_total' => '0 B total',
                    'status' => ucfirst($d->status ?: 'Tersedia'),
                    'status_state' => $st,
                    'is_online' => $isOnline,
                    'uptime' => $d->uptime ?: ($isOnline ? 'Online' : 'Offline'),
                    'rx_rate' => $d->rx_rate ?: '0.0 Mbps',
                    'tx_rate' => $d->tx_rate ?: '0.0 Mbps',
                    'ping_ms' => $d->last_ping_ms !== null ? "{$d->last_ping_ms} ms" : '-',
                    'last_ping_ms' => $d->last_ping_ms ?? null,
                    'packet_loss' => (string) ($d->packet_loss ?? '0.00').'%',
                    'cpu_load' => (float) ($d->cpu_load ?? 0.0),
                    'ram_load' => (float) ($d->ram_load ?? 0.0),
                    'rack_location' => $d->rack_location ?: '-',
                ];
            });

            $allCustomerDevices = $monitoringList->concat($deviceList)->unique('hostname')->values()->all();
        }

        $activeMonitoringProject = $matchedMonitoringDevices->first()?->project;
        $resolvedPackage = $customer?->package
            ?: ($activeMonitoringProject ?: ($customer?->note ?: 'Layanan Internet NetFly'));

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
                    'package' => $resolvedPackage,
                    'status' => $customer->status ?? 'Aktif',
                    'devices' => $allCustomerDevices,
                ] : null,
            ],
        ]);
    }

    /**
     * API Ubah Kata Sandi Akun untuk Aplikasi Mobile NetFly.
     */
    public function apiChangePassword(Request $request): JsonResponse
    {
        $identifier = $request->input('email') ?? $request->input('username');
        $currentPassword = (string) $request->input('current_password', '');
        $newPassword = (string) $request->input('new_password', '');

        if (! $identifier || $currentPassword === '' || $newPassword === '') {
            return response()->json([
                'success' => false,
                'message' => 'Identitas akun, kata sandi lama, dan kata sandi baru wajib diisi.',
            ], 400);
        }

        if (strlen($newPassword) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi baru minimal harus terdiri dari 8 karakter.',
            ], 422);
        }

        $user = User::where('email', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Akun pengguna tidak ditemukan.',
            ], 404);
        }

        if (! Hash::check($currentPassword, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi lama yang Anda masukkan salah.',
            ], 422);
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        ActivityLogger::log(
            'auth',
            'password_change',
            "Pengguna '{$user->name}' berhasil memperbarui kata sandi akun melalui Aplikasi Mobile NetFly",
            $user->name,
            (string) $user->id,
            ['email' => $user->email, 'role' => $user->role]
        );

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi akun berhasil diperbarui.',
        ]);
    }

    /**
     * API Update Profil Pengguna (Nama & No. HP) untuk Mobile App NetFly.
     */
    public function apiUpdateProfile(Request $request): JsonResponse
    {
        $identifier = $request->input('email') ?? $request->input('username') ?? $request->query('email') ?? $request->query('username');
        if (! $identifier) {
            return response()->json([
                'success' => false,
                'message' => 'Identitas akun (email atau username) wajib disertakan.',
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

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'phone' => 'nullable|string|max:30',
        ], [
            'name.required' => 'Nama PIC / Pengguna wajib diisi.',
            'name.min' => 'Nama minimal harus terdiri dari 2 karakter.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'phone.max' => 'Nomor telepon maksimal 30 karakter.',
        ]);

        $oldName = $user->name;
        $oldPhone = $user->phone;

        $user->name = trim($validated['name']);
        if (array_key_exists('phone', $validated)) {
            $user->phone = $validated['phone'] !== null ? trim($validated['phone']) : null;
        }
        $user->save();

        if ($user->customer) {
            $customer = $user->customer;
            if (array_key_exists('phone', $validated)) {
                $customer->phone = $user->phone;
            }
            if ($customer->customer_type === 'rumahan') {
                $customer->name = $user->name;
            } else {
                $customer->pic_name = $user->name;
            }
            $customer->save();
        }

        ActivityLogger::log(
            'akun',
            'update',
            "Pengguna '{$user->name}' memperbarui data profil via Aplikasi Mobile NetFly",
            $user->name,
            (string) $user->id,
            [
                'email' => $user->email,
                'old_name' => $oldName,
                'new_name' => $user->name,
                'old_phone' => $oldPhone,
                'new_phone' => $user->phone,
            ]
        );

        $response = $this->buildMobileUserPayloadResponse($user->fresh(['customer.devices', 'customer.monitoringDevices']));
        $data = $response->getData(true);
        $data['message'] = 'Profil berhasil diperbarui.';

        return response()->json($data);
    }
}
