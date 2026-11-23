<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Master Data Customer Perusahaan (Korporat / B2B) NetFly dari Database.
     */
    protected function getCorporateCustomerList(): array
    {
        return Customer::where('customer_type', 'perusahaan')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($c) {
                $shortCode = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', str_replace(['PT.', 'PT', 'CV.'], '', $c->name))[0] ?? 'CORP'));
                $shortCode = substr($shortCode ?: 'CORP', 0, 8);

                return [
                    'id' => $c->id,
                    'customer_type' => 'perusahaan',
                    'name' => $c->name,
                    'code' => $c->code ?: $shortCode,
                    'category' => $c->category ?: 'Mitra Korporat',
                    'package' => $c->package ?: ($c->note ?: 'Dedicated Corporate Fiber'),
                    'address' => $c->address ?: '-',
                    'latitude' => $c->latitude,
                    'longitude' => $c->longitude,
                    'default_pic' => ($c->pic_name && $c->pic_name !== '-') ? $c->pic_name : $c->name,
                    'default_email' => $c->email ?: '',
                    'default_phone' => $c->phone ?: '',
                    'username_prefix' => strtolower($shortCode).'_',
                ];
            })->toArray();
    }

    /**
     * Master Data Customer Rumahan (Residensial / Home Fiber) NetFly dari Database.
     */
    protected function getResidentialCustomerList(): array
    {
        return Customer::where('customer_type', 'rumahan')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'customer_type' => 'rumahan',
                    'name' => $c->name,
                    'code' => $c->code ?: 'HOME',
                    'category' => $c->category ?: 'Customer Rumahan',
                    'package' => $c->package ?: ($c->note ?: 'Paket Home Fiber Unlimited'),
                    'address' => $c->address ?: '-',
                    'latitude' => $c->latitude,
                    'longitude' => $c->longitude,
                    'default_pic' => ($c->pic_name && $c->pic_name !== '-') ? $c->pic_name : $c->name,
                    'default_email' => $c->email ?: '',
                    'default_phone' => $c->phone ?: '',
                    'username_prefix' => 'home_',
                ];
            })->toArray();
    }

    /**
     * Gabungan seluruh customer (Perusahaan & Rumahan) dari Database.
     */
    public function getCustomerList(): array
    {
        return array_merge($this->getCorporateCustomerList(), $this->getResidentialCustomerList());
    }

    /**
     * Mengambil daftar akun pengguna perwakilan customer langsung dari tabel users database.
     */
    public function getActiveAccounts(): array
    {
        $users = User::with('customer')
            ->whereIn('role', ['customer_representative', 'customer_residential'])
            ->orderBy('id', 'desc')
            ->get();

        $userIds = $users->pluck('id')->all();
        $userIdsStr = array_map('strval', $userIds);

        $loginLogs = collect();
        if (! empty($userIds)) {
            $loginLogs = ActivityLog::where('module', 'auth')
                ->where('action', 'login')
                ->where(function ($q) use ($userIds, $userIdsStr) {
                    $q->whereIn('user_id', $userIds)
                        ->orWhereIn('subject_id', $userIdsStr);
                })
                ->orderBy('id', 'desc')
                ->get();
        }

        return $users->map(function ($user) use ($loginLogs) {
            $customer = $user->customer;
            $custType = $user->role === 'customer_residential'
                ? 'rumahan'
                : ($customer?->customer_type ?? 'perusahaan');

            $latestLogin = $loginLogs->first(function ($log) use ($user) {
                return (int) $log->user_id === (int) $user->id
                    || (string) $log->subject_id === (string) $user->id;
            });

            $lastLoginText = 'Belum pernah login';
            $lastLoginIp = '-';

            if ($latestLogin) {
                if ($latestLogin->created_at) {
                    $wibTime = $latestLogin->created_at->copy()->setTimezone('Asia/Jakarta');
                    $lastLoginText = $wibTime->format('d M Y, H:i').' WIB';
                } else {
                    $lastLoginText = 'Tercatat login';
                }
                $props = is_array($latestLogin->properties) ? $latestLogin->properties : [];
                $lastLoginIp = $latestLogin->ip_address ?: ($props['ip'] ?? '-');
            }

            return [
                'id' => $user->id,
                'customer_id' => $user->customer_id,
                'customer_type' => $custType,
                'customer_name' => $customer?->name ?? 'Customer Terdaftar',
                'customer_code' => $custType === 'rumahan' ? 'RUMAHAN' : ($customer?->code ?? 'CUST'),
                'package' => $customer?->package ?: ($customer?->note ?: 'Dedicated Connection'),
                'address' => $customer?->address ?: '-',
                'latitude' => $customer?->latitude,
                'longitude' => $customer?->longitude,
                'name' => $user->name,
                'username' => $user->username ?: ('user_'.$user->id),
                'email' => $user->email,
                'phone' => $user->phone ?: ($customer?->phone ?: '-'),
                'role' => $user->role,
                'role_label' => $custType === 'rumahan' ? 'Customer Rumahan' : 'PIC Perwakilan Customer',
                'is_active' => (bool) $user->is_active,
                'last_login' => $lastLoginText,
                'last_login_ip' => $lastLoginIp,
                'created_at' => $user->created_at ? $user->created_at->format('d M Y') : date('d M Y'),
                'must_change_password' => false,
            ];
        })->toArray();
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
        $activeAccounts = count(array_filter($accounts, fn ($a) => ! empty($a['is_active'])));
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
     * Tampilkan formulir pembuatan akun customer baru di halaman penuh (dedicated page).
     */
    public function create(): View
    {
        $corporateCustomers = $this->getCorporateCustomerList();
        $residentialCustomers = $this->getResidentialCustomerList();
        $customers = array_merge($corporateCustomers, $residentialCustomers);

        return view('admin.accounts.create', compact('customers', 'corporateCustomers', 'residentialCustomers'));
    }

    /**
     * 1. Form Buat Akun User Baru untuk Perwakilan Customer (Perusahaan & Rumahan) ke Database.
     */
    public function store(Request $request)
    {
        $customerType = $request->input('customer_type', 'perusahaan');

        $validated = $request->validate([
            'customer_type' => 'nullable|string|in:perusahaan,rumahan',
            'customer_id' => 'required|exists:customers,id',
            'name' => $customerType === 'rumahan' ? 'nullable|string|max:150' : 'required|string|max:150',
            'username' => 'nullable|string|min:3|max:50|regex:/^[a-zA-Z0-9_.]+$/|unique:users,username',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'nullable|boolean',
            'must_change_password' => 'nullable|boolean',
        ], [
            'customer_id.required' => 'Wajib memilih Customer.',
            'customer_id.exists' => 'Customer yang dipilih tidak ditemukan.',
            'name.required' => 'Nama lengkap perwakilan (PIC) wajib diisi.',
            'email.required' => 'Alamat email resmi wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Password awal akun wajib ditetapkan.',
            'password.min' => 'Password awal minimal 6 karakter.',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $resolvedType = $customer->customer_type ?: $customerType;
        $role = $resolvedType === 'rumahan' ? 'customer_residential' : 'customer_representative';

        // Auto-generate username dari email jika tidak diisi
        $rawUsername = ! empty($validated['username'])
            ? $validated['username']
            : strtolower(preg_replace('/[^a-zA-Z0-9_.]/', '', explode('@', $validated['email'])[0]));
        $baseUser = substr($rawUsername ?: 'customer', 0, 30);
        $candidateUser = $baseUser;
        $counter = 1;
        while (User::where('username', $candidateUser)->exists()) {
            $candidateUser = substr($baseUser, 0, 25).'_'.($counter++);
        }
        $resolvedUsername = $candidateUser;

        // Update alamat customer jika diisi pada formulir akun
        $customerUpdates = [];
        if (! empty($validated['address']) && empty($customer->address)) {
            $customerUpdates['address'] = $validated['address'];
        }
        if (array_key_exists('latitude', $validated) && $validated['latitude'] !== null) {
            $customerUpdates['latitude'] = $validated['latitude'];
        }
        if (array_key_exists('longitude', $validated) && $validated['longitude'] !== null) {
            $customerUpdates['longitude'] = $validated['longitude'];
        }
        if (! empty($customerUpdates)) {
            $customer->update($customerUpdates);
            $customer->refresh();
        }

        $accountHolderName = ! empty($validated['name']) ? $validated['name'] : $customer->name;
        // Akun customer baru langsung aktif otomatis setelah dibuat
        $isActive = true;

        $user = User::create([
            'name' => $accountHolderName,
            'customer_id' => $customer->id,
            'username' => $resolvedUsername,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'is_active' => $isActive,
            'phone' => $validated['phone'] ?? null,
        ]);

        ActivityLogger::log(
            'akun',
            'create',
            "Menambahkan akun customer '{$accountHolderName}' ({$validated['email']})",
            $validated['email'],
            (string) $user->id,
            [
                'name' => $accountHolderName,
                'username' => $resolvedUsername,
                'email' => $validated['email'],
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'role' => $role,
                'is_active' => $isActive,
            ]
        );

        $newAccount = [
            'id' => $user->id,
            'customer_id' => $customer->id,
            'customer_type' => $resolvedType,
            'customer_name' => $customer->name,
            'customer_code' => $resolvedType === 'rumahan' ? 'RUMAHAN' : ($customer->code ?? 'CUST'),
            'package' => $customer->package ?: ($customer->note ?: 'Paket Layanan'),
            'address' => $customer->address ?: '-',
            'latitude' => $customer->latitude,
            'longitude' => $customer->longitude,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone ?? '-',
            'role' => $user->role,
            'role_label' => $resolvedType === 'rumahan' ? 'Customer Rumahan' : 'PIC Perwakilan Customer',
            'is_active' => (bool) $user->is_active,
            'last_login' => 'Belum pernah login',
            'last_login_ip' => '-',
            'created_at' => $user->created_at->format('d M Y'),
            'must_change_password' => $request->has('must_change_password') ? $request->boolean('must_change_password') : true,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Akun customer '{$customer->name}' ({$user->email}) berhasil dibuat!",
                'account' => $newAccount,
                'initial_password' => $validated['password'],
            ]);
        }

        return redirect()->route('accounts.index')->with('success', "Akun customer '{$customer->name}' ({$user->email}) berhasil dibuat!");
    }

    /**
     * Tampilkan halaman formulir ubah data akun customer.
     */
    public function edit($id): View
    {
        $user = User::with('customer')
            ->whereIn('role', ['customer_representative', 'customer_residential'])
            ->findOrFail($id);

        $customer = $user->customer;
        $custType = $user->role === 'customer_residential'
            ? 'rumahan'
            : ($customer?->customer_type ?? 'perusahaan');

        $account = [
            'id' => $user->id,
            'customer_id' => $user->customer_id,
            'customer_type' => $custType,
            'customer_name' => $customer?->name ?? 'Customer Terdaftar',
            'customer_code' => $custType === 'rumahan' ? 'RUMAHAN' : ($customer?->code ?? 'CUST'),
            'package' => $customer?->package ?: ($customer?->note ?: 'Paket Layanan'),
            'address' => $customer?->address ?: '-',
            'latitude' => $customer?->latitude,
            'longitude' => $customer?->longitude,
            'name' => $user->name,
            'username' => $user->username ?: ('user_'.$user->id),
            'email' => $user->email,
            'phone' => $user->phone ?: ($customer?->phone ?: '-'),
            'role' => $user->role,
            'role_label' => $custType === 'rumahan' ? 'Customer Rumahan' : 'PIC Perwakilan Customer',
            'is_active' => (bool) $user->is_active,
            'must_change_password' => false,
        ];

        $corporateCustomers = $this->getCorporateCustomerList();
        $residentialCustomers = $this->getResidentialCustomerList();
        $customers = array_merge($corporateCustomers, $residentialCustomers);

        return view('admin.accounts.edit', compact('account', 'customers', 'corporateCustomers', 'residentialCustomers'));
    }

    /**
     * Perbarui data akun customer di database.
     */
    public function update(Request $request, $id)
    {
        $user = User::whereIn('role', ['customer_representative', 'customer_residential'])
            ->findOrFail($id);

        $customerType = $request->input('customer_type', 'perusahaan');

        $validated = $request->validate([
            'customer_type' => 'nullable|string|in:perusahaan,rumahan',
            'customer_id' => 'required|exists:customers,id',
            'name' => $customerType === 'rumahan' ? 'nullable|string|max:150' : 'required|string|max:150',
            'username' => 'nullable|string|min:3|max:50|regex:/^[a-zA-Z0-9_.]+$/|unique:users,username,'.$user->id,
            'email' => 'required|email|max:150|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'nullable|boolean',
            'must_change_password' => 'nullable|boolean',
        ], [
            'customer_id.required' => 'Wajib memilih Customer.',
            'customer_id.exists' => 'Customer yang dipilih tidak ditemukan.',
            'name.required' => 'Nama lengkap perwakilan (PIC) wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'username.regex' => 'Username hanya boleh memuat huruf, angka, titik, atau garis bawah (_).',
            'email.required' => 'Alamat email resmi wajib diisi.',
            'email.unique' => 'Alamat email ini sudah digunakan.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.min' => 'Password minimal 6 karakter jika ingin diubah.',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);
        $resolvedType = $customer->customer_type ?: $customerType;
        $role = $resolvedType === 'rumahan' ? 'customer_residential' : 'customer_representative';

        // Update data customer terkait jika ada perubahan alamat atau koordinat lokasi
        $custUpdates = [];
        if (! empty($validated['address'])) {
            $custUpdates['address'] = $validated['address'];
        }
        if (array_key_exists('latitude', $validated) && $validated['latitude'] !== null) {
            $custUpdates['latitude'] = $validated['latitude'];
        }
        if (array_key_exists('longitude', $validated) && $validated['longitude'] !== null) {
            $custUpdates['longitude'] = $validated['longitude'];
        }
        if (! empty($custUpdates)) {
            $customer->update($custUpdates);
            $customer->refresh();
        }

        $accountHolderName = ! empty($validated['name']) ? $validated['name'] : $customer->name;
        $isActive = $request->boolean('is_active');

        $userUpdates = [
            'name' => $accountHolderName,
            'customer_id' => $customer->id,
            'email' => $validated['email'],
            'role' => $role,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $isActive,
        ];

        if (! empty($validated['username'])) {
            $userUpdates['username'] = $validated['username'];
        }

        if (! empty($validated['password'])) {
            $userUpdates['password'] = Hash::make($validated['password']);
        }

        $user->update($userUpdates);

        ActivityLogger::log(
            'akun',
            'update',
            "Memperbarui akun customer '{$accountHolderName}' ({$validated['email']})",
            $validated['email'],
            (string) $user->id,
            [
                'name' => $accountHolderName,
                'email' => $validated['email'],
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'is_active' => $isActive,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data akun customer '{$validated['email']}' berhasil diperbarui!",
            ]);
        }

        return redirect()->route('accounts.index')->with('success', "Data akun customer '{$validated['email']}' berhasil diperbarui!");
    }

    /**
     * 2. Kontrol status akun: Toggle tombol Aktif / Nonaktifkan akun di Database.
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::whereIn('role', ['customer_representative', 'customer_residential'])
            ->findOrFail($id);

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLogger::log(
            'akun',
            'update',
            "Mengubah status akun '{$user->username}' menjadi ".($user->is_active ? 'Aktif' : 'Nonaktif'),
            $user->username,
            (string) $user->id,
            [
                'username' => $user->username,
                'is_active' => (bool) $user->is_active,
            ]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => (bool) $user->is_active,
                'message' => "Status akun '{$user->username}' berhasil {$statusText}!",
            ]);
        }

        return redirect()->route('accounts.index')->with('success', "Status akun '{$user->username}' berhasil {$statusText}!");
    }

    /**
     * 3. Fitur Reset Password akun customer di Database.
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::whereIn('role', ['customer_representative', 'customer_residential'])
            ->findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|string|min:6',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        ActivityLogger::log(
            'akun',
            'update',
            "Mereset kata sandi akun '{$user->username}'",
            $user->username,
            (string) $user->id,
            [
                'username' => $user->username,
                'action_type' => 'reset_password',
            ]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Password akun customer '{$user->username}' berhasil di-reset!",
                'new_password' => $validated['password'],
            ]);
        }

        return redirect()->route('accounts.index')->with('success', "Password akun '{$user->username}' berhasil di-reset!");
    }

    /**
     * Hapus akun perwakilan customer dari Database.
     */
    public function destroy(Request $request, $id)
    {
        $user = User::whereIn('role', ['customer_representative', 'customer_residential'])
            ->findOrFail($id);

        $username = $user->username;
        $user->delete();

        ActivityLogger::log(
            'akun',
            'delete',
            "Menghapus akun pengguna '{$username}'",
            $username,
            (string) $id,
            ['username' => $username]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Akun customer '{$username}' telah berhasil dihapus dari sistem.",
            ]);
        }

        return redirect()->route('accounts.index')->with('success', "Akun '{$username}' telah dihapus.");
    }
}
