<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Master Data Customer B2B NetFly
     */
    protected function getCustomerList(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'PT. Bank Mandiri (Persero) Tbk',
                'code' => 'MANDIRI',
                'category' => 'Enterprise Banking',
                'package' => 'MPLS Core Interconnect 10Gbps',
                'address' => 'Plaza Mandiri, Jl. Jend. Gatot Subroto Kav. 36-38, Jakarta Selatan',
                'default_pic' => 'Bambang Kurniawan, S.Kom',
                'default_email' => 'pic.noc@bankmandiri.co.id',
                'default_phone' => '0812-8877-6655',
                'username_prefix' => 'mandiri_',
            ],
            [
                'id' => 2,
                'name' => 'PT. Shopee International Indonesia',
                'code' => 'SHOPEE',
                'category' => 'E-Commerce Tech Hub',
                'package' => 'Dedicated Fiber 2Gbps + SCBD Direct Peering',
                'address' => 'Pacific Century Place Tower Lt. 26, SCBD, Jakarta Selatan',
                'default_pic' => 'Jessica Tanuwijaya',
                'default_email' => 'noc-corp@shopee.co.id',
                'default_phone' => '0813-1122-3344',
                'username_prefix' => 'shopee_',
            ],
            [
                'id' => 3,
                'name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'code' => 'DISDIK',
                'category' => 'Pemerintahan & Pendidikan',
                'package' => 'Smart City Backbone Fiber 1Gbps',
                'address' => 'Jl. Jend. Gatot Subroto No. 40-41, Kuningan Barat, Jakarta Selatan',
                'default_pic' => 'Ir. Hendra Wijaya, M.T.',
                'default_email' => 'admin.jaringan@disdik.jakarta.go.id',
                'default_phone' => '0815-9988-1234',
                'username_prefix' => 'disdik_',
            ],
            [
                'id' => 4,
                'name' => 'PT. XYZ Teknologi',
                'code' => 'XYZ',
                'category' => 'Software & Datacenter',
                'package' => 'Dedicated Fiber 1Gbps',
                'address' => 'Cyber 2 Tower Lt. 18, Jl. HR Rasuna Said, Jakarta Selatan',
                'default_pic' => 'Rian Saputra',
                'default_email' => 'infra@xyzteknologi.id',
                'default_phone' => '0821-4455-6677',
                'username_prefix' => 'xyz_',
            ],
            [
                'id' => 5,
                'name' => 'RS Medika Center',
                'code' => 'MEDIKA',
                'category' => 'Healthcare & Rumah Sakit',
                'package' => 'Dedicated Internet Broadband 500Mbps',
                'address' => 'Jl. Kesehatan Raya No. 12, Jakarta Barat',
                'default_pic' => 'dr. Anton Pratama, Sp.A',
                'default_email' => 'simrs@medikacenter.org',
                'default_phone' => '0818-7766-5544',
                'username_prefix' => 'medika_',
            ],
            [
                'id' => 6,
                'name' => 'PT. Astra Agro Lestari Tbk',
                'code' => 'ASTRA',
                'category' => 'Manufaktur & Agribisnis',
                'package' => 'Microwave Wireless Remote Backbone 200Mbps',
                'address' => 'Kawasan Industri Pulo Gadung, Jakarta Timur',
                'default_pic' => 'Ferry Gunawan',
                'default_email' => 'it-noc@astra-agro.co.id',
                'default_phone' => '0811-2233-4455',
                'username_prefix' => 'astra_',
            ],
        ];
    }

    /**
     * Default daftar akun perwakilan customer
     */
    protected function getDefaultAccounts(): array
    {
        return [
            [
                'id' => 101,
                'customer_id' => 1,
                'customer_name' => 'PT. Bank Mandiri (Persero) Tbk',
                'customer_code' => 'MANDIRI',
                'package' => 'MPLS Core Interconnect 10Gbps',
                'name' => 'Bambang Kurniawan, S.Kom',
                'username' => 'pic_mandiri',
                'email' => 'pic.noc@bankmandiri.co.id',
                'phone' => '0812-8877-6655',
                'role' => 'customer_representative',
                'role_label' => 'PIC Perwakilan Customer',
                'is_active' => true,
                'last_login' => 'Hari ini, 14:22 WIB',
                'last_login_ip' => '103.28.12.45',
                'created_at' => '12 Jan 2026',
                'must_change_password' => false,
            ],
            [
                'id' => 102,
                'customer_id' => 2,
                'customer_name' => 'PT. Shopee International Indonesia',
                'customer_code' => 'SHOPEE',
                'package' => 'Dedicated Fiber 2Gbps + SCBD Peering',
                'name' => 'Jessica Tanuwijaya',
                'username' => 'noc_shopee',
                'email' => 'noc-corp@shopee.co.id',
                'phone' => '0813-1122-3344',
                'role' => 'customer_representative',
                'role_label' => 'PIC Perwakilan Customer',
                'is_active' => true,
                'last_login' => 'Kemarin, 19:40 WIB',
                'last_login_ip' => '182.253.98.11',
                'created_at' => '15 Jan 2026',
                'must_change_password' => false,
            ],
            [
                'id' => 103,
                'customer_id' => 3,
                'customer_name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'customer_code' => 'DISDIK',
                'package' => 'Smart City Backbone Fiber 1Gbps',
                'name' => 'Ir. Hendra Wijaya, M.T.',
                'username' => 'disdik_jkt',
                'email' => 'admin.jaringan@disdik.jakarta.go.id',
                'phone' => '0815-9988-1234',
                'role' => 'customer_representative',
                'role_label' => 'PIC Perwakilan Customer',
                'is_active' => false,
                'last_login' => '10 Feb 2026, 09:15 WIB',
                'last_login_ip' => '36.72.15.89',
                'created_at' => '20 Jan 2026',
                'must_change_password' => true,
            ],
            [
                'id' => 104,
                'customer_id' => 4,
                'customer_name' => 'PT. XYZ Teknologi',
                'customer_code' => 'XYZ',
                'package' => 'Dedicated Fiber 1Gbps',
                'name' => 'Rian Saputra',
                'username' => 'it_xyz_tech',
                'email' => 'infra@xyzteknologi.id',
                'phone' => '0821-4455-6677',
                'role' => 'customer_representative',
                'role_label' => 'PIC Perwakilan Customer',
                'is_active' => true,
                'last_login' => 'Hari ini, 08:30 WIB',
                'last_login_ip' => '114.122.45.10',
                'created_at' => '01 Feb 2026',
                'must_change_password' => false,
            ],
            [
                'id' => 105,
                'customer_id' => 5,
                'customer_name' => 'RS Medika Center',
                'customer_code' => 'MEDIKA',
                'package' => 'Dedicated Internet Broadband 500Mbps',
                'name' => 'dr. Anton Pratama, Sp.A',
                'username' => 'medika_simrs',
                'email' => 'simrs@medikacenter.org',
                'phone' => '0818-7766-5544',
                'role' => 'customer_representative',
                'role_label' => 'PIC Perwakilan Customer',
                'is_active' => true,
                'last_login' => '3 hari lalu',
                'last_login_ip' => '180.252.61.3',
                'created_at' => '05 Feb 2026',
                'must_change_password' => false,
            ],
        ];
    }

    /**
     * Helper to load dynamic accounts from session or database
     */
    protected function getActiveAccounts(): array
    {
        $sessionAccounts = session('customer_accounts', null);

        if ($sessionAccounts !== null) {
            return $sessionAccounts;
        }

        $accounts = $this->getDefaultAccounts();

        // Check if database has users with customer_id or customer_representative role
        try {
            $dbUsers = User::whereNotNull('customer_id')
                ->orWhere('role', 'customer_representative')
                ->get();

            if ($dbUsers->isNotEmpty()) {
                $customersMap = collect($this->getCustomerList())->keyBy('id');

                foreach ($dbUsers as $dbUser) {
                    $matchedCustomer = $customersMap->get($dbUser->customer_id);
                    $alreadyExists = false;

                    foreach ($accounts as $k => $acc) {
                        if ($acc['id'] === $dbUser->id || $acc['username'] === $dbUser->username) {
                            $alreadyExists = true;
                            $accounts[$k]['is_active'] = (bool) $dbUser->is_active;
                            break;
                        }
                    }

                    if (!$alreadyExists) {
                        $accounts[] = [
                            'id' => $dbUser->id,
                            'customer_id' => $dbUser->customer_id ?? 1,
                            'customer_name' => $matchedCustomer['name'] ?? 'Mitra Customer Terdaftar',
                            'customer_code' => $matchedCustomer['code'] ?? 'CUST',
                            'package' => $matchedCustomer['package'] ?? 'Dedicated Connection',
                            'name' => $dbUser->name,
                            'username' => $dbUser->username ?? ('user_' . $dbUser->id),
                            'email' => $dbUser->email,
                            'phone' => $dbUser->phone ?? '-',
                            'role' => $dbUser->role ?? 'customer_representative',
                            'role_label' => 'PIC Perwakilan Customer',
                            'is_active' => (bool) $dbUser->is_active,
                            'last_login' => 'Belum pernah login',
                            'last_login_ip' => '-',
                            'created_at' => $dbUser->created_at ? $dbUser->created_at->format('d M Y') : date('d M Y'),
                            'must_change_password' => true,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Log fallback without failing
            Log::info('Database query fallback in AccountController: ' . $e->getMessage());
        }

        session(['customer_accounts' => $accounts]);
        return $accounts;
    }

    /**
     * Tampilkan halaman Account Management.
     */
    public function index(Request $request): View
    {
        $customers = $this->getCustomerList();
        $accounts = $this->getActiveAccounts();

        // Hitung Summary Metrics
        $totalAccounts = count($accounts);
        $activeAccounts = count(array_filter($accounts, fn($a) => !empty($a['is_active'])));
        $inactiveAccounts = $totalAccounts - $activeAccounts;
        $totalCustomers = count($customers);

        $summary = [
            'total' => $totalAccounts,
            'active' => $activeAccounts,
            'inactive' => $inactiveAccounts,
            'customers_count' => $totalCustomers,
        ];

        return view('accounts', compact('summary', 'customers', 'accounts'));
    }

    /**
     * 1. Form Buat Akun User Baru untuk Perwakilan Customer
     * (Pilih Customer -> Buat Username -> Tetapkan Password awal)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'name' => 'required|string|max:150',
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_.]+$/',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'nullable|boolean',
            'must_change_password' => 'nullable|boolean',
        ], [
            'customer_id.required' => 'Wajib memilih mitra Customer.',
            'name.required' => 'Nama lengkap perwakilan (PIC) wajib diisi.',
            'username.required' => 'Username akun wajib ditentukan.',
            'username.regex' => 'Username hanya boleh memuat huruf, angka, titik, atau garis bawah (_).',
            'email.required' => 'Alamat email resmi PIC wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Password awal akun wajib ditetapkan.',
            'password.min' => 'Password awal minimal 6 karakter.',
        ]);

        $customers = collect($this->getCustomerList())->keyBy('id');
        $selectedCustomer = $customers->get($validated['customer_id']);
        $customerName = $selectedCustomer['name'] ?? 'Mitra Korporat';
        $customerCode = $selectedCustomer['code'] ?? 'CUST';
        $customerPackage = $selectedCustomer['package'] ?? 'Dedicated Link';

        $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;
        $mustChange = $request->has('must_change_password') ? $request->boolean('must_change_password') : true;

        $newId = (int) (round(microtime(true) * 10) % 900000 + 100000);

        // Try persisting in Database if User model and table ready
        try {
            $user = User::create([
                'name' => $validated['name'],
                'customer_id' => $validated['customer_id'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'customer_representative',
                'is_active' => $isActive,
                'phone' => $validated['phone'] ?? null,
            ]);
            $newId = $user->id;
        } catch (\Throwable $e) {
            Log::info('Store User DB Fallback: ' . $e->getMessage());
        }

        $newAccount = [
            'id' => $newId,
            'customer_id' => (int) $validated['customer_id'],
            'customer_name' => $customerName,
            'customer_code' => $customerCode,
            'package' => $customerPackage,
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? '-',
            'role' => 'customer_representative',
            'role_label' => 'PIC Perwakilan Customer',
            'is_active' => $isActive,
            'last_login' => 'Belum pernah login',
            'last_login_ip' => '-',
            'created_at' => date('d M Y'),
            'must_change_password' => $mustChange,
        ];

        $currentAccounts = $this->getActiveAccounts();
        array_unshift($currentAccounts, $newAccount);
        session(['customer_accounts' => $currentAccounts]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Akun perwakilan customer '{$customerName}' dengan username '{$validated['username']}' berhasil dibuat!",
                'account' => $newAccount,
                'initial_password' => $validated['password'],
            ]);
        }

        return redirect()->route('accounts.index')->with('success', "Akun perwakilan customer '{$customerName}' dengan username '{$validated['username']}' berhasil dibuat!");
    }

    /**
     * 2. Kontrol status akun: Toggle tombol Aktif / Nonaktifkan akun.
     */
    public function toggleStatus(Request $request, $id)
    {
        $accounts = $this->getActiveAccounts();
        $targetAccount = null;
        $newStatus = false;

        foreach ($accounts as $k => $account) {
            if ((string) $account['id'] === (string) $id) {
                $newStatus = !$account['is_active'];
                $accounts[$k]['is_active'] = $newStatus;
                $targetAccount = $accounts[$k];
                break;
            }
        }

        session(['customer_accounts' => $accounts]);

        // Attempt DB update
        try {
            $user = User::find($id);
            if ($user) {
                $user->is_active = $newStatus;
                $user->save();
            }
        } catch (\Throwable $e) {
            Log::info('Toggle Status DB Fallback: ' . $e->getMessage());
        }

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        $username = $targetAccount['username'] ?? 'User';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $newStatus,
                'message' => "Status akun '{$username}' berhasil {$statusText}!",
            ]);
        }

        return redirect()->route('accounts.index')->with('success', "Status akun '{$username}' berhasil {$statusText}!");
    }

    /**
     * 3. Fitur Reset Password akun customer.
     */
    public function resetPassword(Request $request, $id)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $accounts = $this->getActiveAccounts();
        $targetAccount = null;

        foreach ($accounts as $k => $account) {
            if ((string) $account['id'] === (string) $id) {
                $accounts[$k]['must_change_password'] = true;
                $targetAccount = $accounts[$k];
                break;
            }
        }

        session(['customer_accounts' => $accounts]);

        // Attempt DB update
        try {
            $user = User::find($id);
            if ($user) {
                $user->password = Hash::make($validated['password']);
                $user->save();
            }
        } catch (\Throwable $e) {
            Log::info('Reset Password DB Fallback: ' . $e->getMessage());
        }

        $username = $targetAccount['username'] ?? 'User';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Password akun customer '{$username}' berhasil di-reset!",
                'new_password' => $validated['password'],
            ]);
        }

        return redirect()->route('accounts.index')->with('success', "Password akun '{$username}' berhasil di-reset!");
    }

    /**
     * Hapus akun perwakilan customer.
     */
    public function destroy(Request $request, $id)
    {
        $accounts = $this->getActiveAccounts();
        $username = 'User';

        $filtered = [];
        foreach ($accounts as $account) {
            if ((string) $account['id'] === (string) $id) {
                $username = $account['username'];
            } else {
                $filtered[] = $account;
            }
        }

        session(['customer_accounts' => $filtered]);

        try {
            $user = User::find($id);
            if ($user) {
                $user->delete();
            }
        } catch (\Throwable $e) {
            Log::info('Destroy User DB Fallback: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Akun customer '{$username}' telah berhasil dihapus dari sistem.",
            ]);
        }

        return redirect()->route('accounts.index')->with('success', "Akun '{$username}' telah dihapus.");
    }
}
