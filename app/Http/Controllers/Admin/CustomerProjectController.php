<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerProjectController extends Controller
{
    /**
     * Tampilkan halaman Customer Management (Customer Rumahan & Customer Perusahaan).
     */
    public function index(Request $request): View
    {
        $customers = Customer::where('customer_type', 'rumahan')
            ->orderBy('id', 'desc')
            ->get();

        $projects = Customer::where('customer_type', 'perusahaan')
            ->withCount('projects')
            ->orderBy('id', 'desc')
            ->get();

        $activeTab = $request->query('tab', 'customer');

        return view('admin.customers', compact('customers', 'projects', 'activeTab'));
    }

    /**
     * Tampilkan halaman formulir tambah customer rumahan baru.
     */
    public function createRumahan(): View
    {
        $statusOptions = ['Aktif', 'Negosiasi', 'Nonaktif'];

        return view('admin.customers.rumahan_create', compact('statusOptions'));
    }

    /**
     * Tampilkan halaman formulir edit customer rumahan.
     */
    public function editRumahan(int|string $id): View
    {
        $customer = Customer::where('id', $id)
            ->where('customer_type', 'rumahan')
            ->firstOrFail();

        $statusOptions = ['Aktif', 'Negosiasi', 'Nonaktif'];

        return view('admin.customers.rumahan_edit', compact('customer', 'statusOptions'));
    }

    /**
     * Tampilkan halaman formulir tambah customer perusahaan baru.
     */
    public function createPerusahaan(): View
    {
        $statusOptions = ['Aktif', 'Negosiasi', 'Nonaktif'];

        return view('admin.customers.perusahaan_create', compact('statusOptions'));
    }

    /**
     * Tampilkan halaman formulir edit customer perusahaan.
     */
    public function editPerusahaan(int|string $id): View
    {
        $project = Customer::where('id', $id)
            ->where('customer_type', 'perusahaan')
            ->firstOrFail();

        $statusOptions = ['Aktif', 'Negosiasi', 'Nonaktif'];

        return view('admin.customers.perusahaan_edit', compact('project', 'statusOptions'));
    }

    /**
     * Simpan Customer Rumahan Baru ke database.
     */
    public function storeCustomer(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'status' => 'required|string|max:50',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:300',
            'note' => 'nullable|string|max:300',
        ], [
            'name.required' => 'Nama customer rumahan wajib diisi.',
            'email.required' => 'Email customer wajib diisi.',
            'phone.required' => 'Nomor kontak telepon/WA wajib diisi.',
            'address.required' => 'Alamat customer wajib diisi.',
        ]);

        $code = $this->generateNextCode('rumahan');

        $customer = Customer::create([
            'customer_type' => 'rumahan',
            'code' => $code,
            'name' => trim($validated['name']),
            'pic_name' => '-',
            'phone' => trim($validated['phone']),
            'email' => trim($validated['email']),
            'address' => trim($validated['address']),
            'note' => !empty($validated['note']) ? trim($validated['note']) : 'Layanan Internet Rumahan NetFly',
            'status' => $validated['status'],
            'category' => 'Customer Rumahan',
        ]);

        ActivityLogger::log(
            'kemitraan',
            'create',
            "Menambahkan customer rumahan '{$customer->name}' (#{$customer->code})",
            $customer->name,
            (string) $customer->id,
            [
                'code' => $customer->code,
                'status' => $customer->status,
                'type' => 'rumahan',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Customer Rumahan '{$customer->name}' berhasil ditambahkan!",
                'customer' => $customer,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'customer'])
            ->with('success', "Customer Rumahan '{$customer->name}' berhasil ditambahkan!");
    }

    /**
     * Update Data Customer Rumahan di database.
     */
    public function updateCustomer(Request $request, int|string $id): JsonResponse|RedirectResponse
    {
        $customer = Customer::where('id', $id)
            ->where('customer_type', 'rumahan')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'status' => 'required|string|max:50',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:300',
            'note' => 'nullable|string|max:300',
        ], [
            'name.required' => 'Nama customer rumahan wajib diisi.',
            'email.required' => 'Email customer wajib diisi.',
            'phone.required' => 'Nomor kontak telepon/WA wajib diisi.',
            'address.required' => 'Alamat customer wajib diisi.',
        ]);

        $customer->update([
            'name' => trim($validated['name']),
            'status' => $validated['status'],
            'email' => trim($validated['email']),
            'phone' => trim($validated['phone']),
            'address' => trim($validated['address']),
            'note' => !empty($validated['note']) ? trim($validated['note']) : $customer->note,
        ]);

        ActivityLogger::log(
            'kemitraan',
            'update',
            "Memperbarui data customer rumahan '{$customer->name}' (#{$customer->code})",
            $customer->name,
            (string) $customer->id,
            [
                'code' => $customer->code,
                'status' => $customer->status,
                'type' => 'rumahan',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data Customer Rumahan '{$customer->name}' berhasil diperbarui!",
                'customer' => $customer,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'customer'])
            ->with('success', "Data Customer Rumahan '{$customer->name}' berhasil diperbarui!");
    }

    /**
     * Hapus Customer Rumahan dari database.
     */
    public function destroyCustomer(Request $request, int|string $id): JsonResponse|RedirectResponse
    {
        $customer = Customer::where('id', $id)
            ->where('customer_type', 'rumahan')
            ->firstOrFail();

        $customerName = $customer->name;
        $customerCode = $customer->code;

        $customer->delete();

        ActivityLogger::log(
            'kemitraan',
            'delete',
            "Menghapus customer rumahan '{$customerName}' (#{$customerCode})",
            $customerName,
            (string) $id,
            [
                'code' => $customerCode,
                'type' => 'rumahan',
            ]
        );

        // Sinkronisasi: Hapus juga akun terkait dari session 'customer_accounts' jika ada
        $sessionAccounts = session('customer_accounts', null);
        if (is_array($sessionAccounts)) {
            $targetNameLower = strtolower(trim($customerName));
            $sessionAccounts = array_values(array_filter($sessionAccounts, function ($acc) use ($id, $targetNameLower) {
                $isSameRumahanId = (($acc['customer_type'] ?? '') === 'rumahan') && ((string) ($acc['customer_id'] ?? '') === (string) $id);
                $isSameName = strtolower(trim($acc['customer_name'] ?? '')) === $targetNameLower;
                return !($isSameRumahanId || $isSameName);
            }));
            session(['customer_accounts' => $sessionAccounts]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Customer Rumahan '{$customerName}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'customer'])
            ->with('success', "Customer Rumahan '{$customerName}' berhasil dihapus.");
    }

    /**
     * Simpan Customer Perusahaan Baru ke database.
     */
    public function storeProject(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'pic_name' => 'required|string|max:150',
            'status' => 'required|string|max:50',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:300',
            'note' => 'nullable|string|max:300',
        ], [
            'name.required' => 'Nama perusahaan / PT wajib diisi.',
            'pic_name.required' => 'Nama PIC penanggung jawab wajib diisi.',
            'email.required' => 'Email PIC wajib diisi.',
            'phone.required' => 'Nomor kontak telepon/WA wajib diisi.',
            'address.required' => 'Alamat kantor wajib diisi.',
        ]);

        $code = $this->generateNextCode('perusahaan');

        $corporate = Customer::create([
            'customer_type' => 'perusahaan',
            'code' => $code,
            'name' => trim($validated['name']),
            'pic_name' => trim($validated['pic_name']),
            'phone' => trim($validated['phone']),
            'email' => trim($validated['email']),
            'address' => trim($validated['address']),
            'note' => !empty($validated['note']) ? trim($validated['note']) : 'Kemitraan Jaringan Korporat NetFly',
            'status' => $validated['status'],
            'category' => 'Mitra Korporat',
        ]);

        ActivityLogger::log(
            'kemitraan',
            'create',
            "Menambahkan customer perusahaan '{$corporate->name}' (#{$corporate->code})",
            $corporate->name,
            (string) $corporate->id,
            [
                'code' => $corporate->code,
                'status' => $corporate->status,
                'pic' => $corporate->pic_name,
                'type' => 'perusahaan',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Customer Perusahaan '{$corporate->name}' berhasil ditambahkan!",
                'project' => $corporate,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'project'])
            ->with('success', "Customer Perusahaan '{$corporate->name}' berhasil ditambahkan!");
    }

    /**
     * Update Data Customer Perusahaan di database.
     */
    public function updateProject(Request $request, int|string $id): JsonResponse|RedirectResponse
    {
        $corporate = Customer::where('id', $id)
            ->where('customer_type', 'perusahaan')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'pic_name' => 'required|string|max:150',
            'status' => 'required|string|max:50',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:300',
            'note' => 'nullable|string|max:300',
        ], [
            'name.required' => 'Nama perusahaan / PT wajib diisi.',
            'pic_name.required' => 'Nama PIC penanggung jawab wajib diisi.',
            'email.required' => 'Email PIC wajib diisi.',
            'phone.required' => 'Nomor kontak telepon/WA wajib diisi.',
            'address.required' => 'Alamat kantor wajib diisi.',
        ]);

        $corporate->update([
            'name' => trim($validated['name']),
            'pic_name' => trim($validated['pic_name']),
            'status' => $validated['status'],
            'email' => trim($validated['email']),
            'phone' => trim($validated['phone']),
            'address' => trim($validated['address']),
            'note' => !empty($validated['note']) ? trim($validated['note']) : $corporate->note,
        ]);

        ActivityLogger::log(
            'kemitraan',
            'update',
            "Memperbarui data customer perusahaan '{$corporate->name}' (#{$corporate->code})",
            $corporate->name,
            (string) $corporate->id,
            [
                'code' => $corporate->code,
                'status' => $corporate->status,
                'pic' => $corporate->pic_name,
                'type' => 'perusahaan',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data Customer Perusahaan '{$corporate->name}' berhasil diperbarui!",
                'project' => $corporate,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'project'])
            ->with('success', "Data Customer Perusahaan '{$corporate->name}' berhasil diperbarui!");
    }

    /**
     * Hapus Customer Perusahaan dari database.
     */
    public function destroyProject(Request $request, int|string $id): JsonResponse|RedirectResponse
    {
        $corporate = Customer::where('id', $id)
            ->where('customer_type', 'perusahaan')
            ->firstOrFail();

        $companyName = $corporate->name;
        $companyCode = $corporate->code;

        $corporate->delete();

        ActivityLogger::log(
            'kemitraan',
            'delete',
            "Menghapus customer perusahaan '{$companyName}' (#{$companyCode})",
            $companyName,
            (string) $id,
            [
                'code' => $companyCode,
                'type' => 'perusahaan',
            ]
        );

        // Sinkronisasi: Hapus juga akun terkait dari session 'customer_accounts' jika ada
        $sessionAccounts = session('customer_accounts', null);
        if (is_array($sessionAccounts)) {
            $targetNameLower = strtolower(trim($companyName));
            $sessionAccounts = array_values(array_filter($sessionAccounts, function ($acc) use ($id, $targetNameLower) {
                $isSameCorpId = (($acc['customer_type'] ?? 'perusahaan') === 'perusahaan') && ((string) ($acc['customer_id'] ?? '') === (string) $id);
                $isSameName = strtolower(trim($acc['customer_name'] ?? '')) === $targetNameLower;
                return !($isSameCorpId || $isSameName);
            }));
            session(['customer_accounts' => $sessionAccounts]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Customer Perusahaan '{$companyName}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'project'])
            ->with('success', "Customer Perusahaan '{$companyName}' berhasil dihapus.");
    }

    /**
     * Generate kode unik berikutnya untuk customer rumahan (HOME-xxxx) atau perusahaan (CUST-xxxx).
     */
    private function generateNextCode(string $type): string
    {
        $prefix = $type === 'rumahan' ? 'HOME' : 'CUST';
        $codes = Customer::where('code', 'like', "{$prefix}-%")->pluck('code');

        $maxSeq = 0;
        foreach ($codes as $c) {
            if (preg_match("/^{$prefix}-(\d+)$/", $c, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxSeq) {
                    $maxSeq = $num;
                }
            }
        }

        return sprintf('%s-%04d', $prefix, $maxSeq + 1);
    }
}
