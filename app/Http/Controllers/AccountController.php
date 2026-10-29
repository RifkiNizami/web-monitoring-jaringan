<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Master Data Customer Perusahaan (Korporat / B2B) NetFly
     */
    protected function getCorporateCustomerList(): array
    {
        $defaults = [
            [
                'id' => 1,
                'customer_type' => 'perusahaan',
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
                'customer_type' => 'perusahaan',
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
                'customer_type' => 'perusahaan',
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
                'customer_type' => 'perusahaan',
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
                'customer_type' => 'perusahaan',
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
                'customer_type' => 'perusahaan',
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

        // Sinkronisasi dengan Customer Perusahaan yang ditambahkan dari menu Customer & Project
        $sessionCorporate = session('nms_customers_perusahaan', []);
        $existingNames = array_map('strtolower', array_column($defaults, 'name'));

        foreach ($sessionCorporate as $sc) {
            if (!in_array(strtolower($sc['name']), $existingNames, true)) {
                $shortCode = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', str_replace(['PT.', 'PT', 'CV.'], '', $sc['name']))[0] ?? 'CORP'));
                $shortCode = substr($shortCode ?: 'CORP', 0, 8);
                $defaults[] = [
                    'id' => (int) $sc['id'] + 50,
                    'customer_type' => 'perusahaan',
                    'name' => $sc['name'],
                    'code' => $shortCode,
                    'category' => 'Mitra Korporat',
                    'package' => $sc['note'] ?: 'Dedicated Corporate Fiber',
                    'address' => $sc['address'] ?? '-',
                    'default_pic' => $sc['pic_name'] ?? 'PIC Perusahaan',
                    'default_email' => $sc['email'] ?? '',
                    'default_phone' => $sc['phone'] ?? '',
                    'username_prefix' => strtolower($shortCode) . '_',
                ];
            }
        }

        return $defaults;
    }

    /**
     * Master Data Customer Rumahan (Residensial / Home Fiber) NetFly
     */
    protected function getResidentialCustomerList(): array
    {
        $defaults = [
            [
                'id' => 101,
                'customer_type' => 'rumahan',
                'name' => 'Budi Santoso',
                'code' => 'HOME-0001',
                'category' => 'Customer Rumahan',
                'package' => 'Paket Home Fiber 50 Mbps Unlimited',
                'address' => 'Perumahan Griya Asri Blok C2 No. 14, Kel. Sukamaju, Kec. Cilodong, Depok',
                'default_pic' => 'Budi Santoso',
                'default_email' => 'budi.santoso@gmail.com',
                'default_phone' => '0812-8450-1122',
                'username_prefix' => 'home_',
            ],
            [
                'id' => 102,
                'customer_type' => 'rumahan',
                'name' => 'Siti Aminah',
                'code' => 'HOME-0002',
                'category' => 'Customer Rumahan',
                'package' => 'Paket Home Fiber Gamer 100 Mbps + Static IP',
                'address' => 'Jl. Melati Indah IV No. 8, RT.04/RW.09, Bintaro, Tangerang Selatan',
                'default_pic' => 'Siti Aminah',
                'default_email' => 'sitiaminah.home@yahoo.com',
                'default_phone' => '0813-9088-3344',
                'username_prefix' => 'home_',
            ],
            [
                'id' => 103,
                'customer_type' => 'rumahan',
                'name' => 'Hendra Kusuma',
                'code' => 'HOME-0003',
                'category' => 'Customer Rumahan',
                'package' => 'FTTH Home Broadband 30 Mbps',
                'address' => 'Cluster Harmoni Residence No. B12, Bekasi Barat',
                'default_pic' => 'Hendra Kusuma',
                'default_email' => 'hendra.kusuma88@gmail.com',
                'default_phone' => '0857-2211-9090',
                'username_prefix' => 'home_',
            ],
        ];

        // Sinkronisasi dengan Customer Rumahan yang ditambahkan dari menu Customer & Project
        $sessionResidential = session('nms_customers_rumahan', []);
        $existingNames = array_map('strtolower', array_column($defaults, 'name'));

        foreach ($sessionResidential as $sr) {
            if (!in_array(strtolower($sr['name']), $existingNames, true)) {
                $defaults[] = [
                    'id' => (int) $sr['id'],
                    'customer_type' => 'rumahan',
                    'name' => $sr['name'],
                    'code' => $sr['code'] ?? 'HOME',
                    'category' => 'Customer Rumahan',
                    'package' => $sr['note'] ?: 'Layanan Internet Rumahan NetFly',
                    'address' => $sr['address'] ?? '-',
                    'default_pic' => $sr['name'],
                    'default_email' => $sr['email'] ?? '',
                    'default_phone' => $sr['phone'] ?? '',
                    'username_prefix' => 'home_',
                ];
            }
        }

        return $defaults;
    }

    /**
     * Gabungan seluruh customer (Perusahaan & Rumahan)
     */
    protected function getCustomerList(): array
    {
        return array_merge($this->getCorporateCustomerList(), $this->getResidentialCustomerList());
    }

    /**
     * Default daftar akun customer (Perusahaan & Rumahan)
     */
    protected function getDefaultAccounts(): array
    {
        return [
            [
                'id' => 101,
                'customer_id' => 1,
                'customer_type' => 'perusahaan',
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
                'customer_type' => 'perusahaan',
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
                'id' => 106,
                'customer_id' => 101,
                'customer_type' => 'rumahan',
                'customer_name' => 'Budi Santoso',
                'customer_code' => 'RUMAHAN',
                'package' => 'Paket Home Fiber 50 Mbps Unlimited',
                'name' => 'Budi Santoso',
                'username' => 'home_budi',
                'email' => 'budi.santoso@gmail.com',
                'phone' => '0812-8450-1122',
                'role' => 'customer_residential',
                'role_label' => 'Customer Rumahan',
                'is_active' => true,
                'last_login' => 'Hari ini, 11:10 WIB',
                'last_login_ip' => '36.80.112.9',
                'created_at' => '18 Jan 2026',
                'must_change_password' => false,
            ],
            [
                'id' => 103,
                'customer_id' => 3,
                'customer_type' => 'perusahaan',
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
                'customer_type' => 'perusahaan',
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
                'customer_type' => 'perusahaan',
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
                        $custType = $matchedCustomer['customer_type'] ?? 'perusahaan';
                        $accounts[] = [
                            'id' => $dbUser->id,
                            'customer_id' => $dbUser->customer_id ?? 1,
                            'customer_type' => $custType,
                            'customer_name' => $matchedCustomer['name'] ?? 'Mitra Customer Terdaftar',
                            'customer_code' => $custType === 'rumahan' ? 'RUMAHAN' : ($matchedCustomer['code'] ?? 'CUST'),
                            'package' => $matchedCustomer['package'] ?? 'Dedicated Connection',
                            'name' => $dbUser->name,
                            'username' => $dbUser->username ?? ('user_' . $dbUser->id),
                            'email' => $dbUser->email,
                            'phone' => $dbUser->phone ?? '-',
                            'role' => $dbUser->role ?? 'customer_representative',
                            'role_label' => $custType === 'rumahan' ? 'Customer Rumahan' : 'PIC Perwakilan Customer',
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
        $corporateCustomers = $this->getCorporateCustomerList();
        $residentialCustomers = $this->getResidentialCustomerList();
        $customers = array_merge($corporateCustomers, $residentialCustomers);
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
            'corporate_count' => count($corporateCustomers),
            'residential_count' => count($residentialCustomers),
        ];

        return view('admin.accounts', compact(
            'summary',
            'customers',
            'corporateCustomers',
            'residentialCustomers',
            'accounts'
        ));
    }

    /**
     * 1. Form Buat Akun User Baru untuk Perwakilan Customer (Perusahaan & Rumahan)
     */
    public function store(Request $request)
    {
        $customerType = $request->input('customer_type', 'perusahaan');

        $validated = $request->validate([
            'customer_type' => 'nullable|string|in:perusahaan,rumahan',
            'customer_id' => 'required|integer',
            'name' => $customerType === 'rumahan' ? 'nullable|string|max:150' : 'required|string|max:150',
            'username' => 'required|string|min:3|max:50|regex:/^[a-zA-Z0-9_.]+$/',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:30',
            'is_active' => 'nullable|boolean',
            'must_change_password' => 'nullable|boolean',
        ], [
            'customer_id.required' => 'Wajib memilih Customer.',
            'name.required' => 'Nama lengkap perwakilan (PIC) wajib diisi.',
            'username.required' => 'Username akun wajib ditentukan.',
            'username.regex' => 'Username hanya boleh memuat huruf, angka, titik, atau garis bawah (_).',
            'email.required' => 'Alamat email resmi wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Password awal akun wajib ditetapkan.',
            'password.min' => 'Password awal minimal 6 karakter.',
        ]);

        $customers = collect($this->getCustomerList())->keyBy('id');
        $selectedCustomer = $customers->get((int) $validated['customer_id']);

        $resolvedType = $selectedCustomer['customer_type'] ?? $customerType;
        $customerName = $selectedCustomer['name'] ?? ($resolvedType === 'rumahan' ? 'Customer Rumahan' : 'Mitra Korporat');
        $customerCode = $resolvedType === 'rumahan' ? 'RUMAHAN' : ($selectedCustomer['code'] ?? 'CUST');
        $customerPackage = $selectedCustomer['package'] ?? ($resolvedType === 'rumahan' ? 'Home Fiber Broadband' : 'Dedicated Link');

        // Untuk customer rumahan tanpa PIC terpisah, gunakan nama customer langsung
        $accountHolderName = !empty($validated['name']) ? $validated['name'] : $customerName;

        $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;
        $mustChange = $request->has('must_change_password') ? $request->boolean('must_change_password') : true;

        $newId = (int) (round(microtime(true) * 10) % 900000 + 100000);

        // Try persisting in Database if User model and table ready
        try {
            $user = User::create([
                'name' => $accountHolderName,
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
            'customer_type' => $resolvedType,
            'customer_name' => $customerName,
            'customer_code' => $customerCode,
            'package' => $customerPackage,
            'name' => $accountHolderName,
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? '-',
            'role' => $resolvedType === 'rumahan' ? 'customer_residential' : 'customer_representative',
            'role_label' => $resolvedType === 'rumahan' ? 'Customer Rumahan' : 'PIC Perwakilan Customer',
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
                'message' => "Akun customer '{$customerName}' dengan username '{$validated['username']}' berhasil dibuat!",
                'account' => $newAccount,
                'initial_password' => $validated['password'],
            ]);
        }

        return redirect()->route('accounts.index')->with('success', "Akun customer '{$customerName}' dengan username '{$validated['username']}' berhasil dibuat!");
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
