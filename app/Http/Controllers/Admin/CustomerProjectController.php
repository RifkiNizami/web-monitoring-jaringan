<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerProjectController extends Controller
{
    /**
     * Default daftar Customer Rumahan (Residensial / Home Fiber)
     */
    protected function getDefaultResidentialCustomers(): array
    {
        return [
            [
                'id' => 101,
                'code' => 'HOME-0001',
                'customer_type' => 'rumahan',
                'name' => 'Budi Santoso',
                'pic_name' => '-',
                'phone' => '0812-8450-1122',
                'email' => 'budi.santoso@gmail.com',
                'address' => 'Perumahan Griya Asri Blok C2 No. 14, Kel. Sukamaju, Kec. Cilodong, Depok',
                'note' => 'Paket Home Fiber 50 Mbps Unlimited',
                'status' => 'Aktif',
                'status_color' => 'emerald',
                'active_projects' => 1,
            ],
            [
                'id' => 102,
                'code' => 'HOME-0002',
                'customer_type' => 'rumahan',
                'name' => 'Siti Aminah',
                'pic_name' => '-',
                'phone' => '0813-9088-3344',
                'email' => 'sitiaminah.home@yahoo.com',
                'address' => 'Jl. Melati Indah IV No. 8, RT.04/RW.09, Bintaro, Tangerang Selatan',
                'note' => 'Paket Home Fiber Gamer 100 Mbps + Static IP',
                'status' => 'Aktif',
                'status_color' => 'emerald',
                'active_projects' => 1,
            ],
            [
                'id' => 103,
                'code' => 'HOME-0003',
                'customer_type' => 'rumahan',
                'name' => 'Hendra Kusuma',
                'pic_name' => '-',
                'phone' => '0857-2211-9090',
                'email' => 'hendra.kusuma88@gmail.com',
                'address' => 'Cluster Harmoni Residence No. B12, Bekasi Barat',
                'note' => 'Instalasi baru FTTH Home Broadband 30 Mbps',
                'status' => 'Negosiasi',
                'status_color' => 'amber',
                'active_projects' => 0,
            ],
        ];
    }

    /**
     * Default daftar Customer Perusahaan (Korporat / Mitra B2B)
     */
    protected function getDefaultCorporateCustomers(): array
    {
        return [
            [
                'id' => 1,
                'code' => 'CUST-0004',
                'customer_type' => 'perusahaan',
                'name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'pic_name' => 'Drs. H. Mulyadi',
                'phone' => '021-3844567',
                'email' => 'tik@disdik.jakarta.go.id',
                'address' => 'Jl. Gatot Subroto No. 40, RT.7/RW.1, Bendungan Hilir, Tanah Abang, Jakarta Pusat',
                'note' => 'Proyek Smart School dan Integrasi Jaringan AMDK...',
                'status' => 'Negosiasi',
                'status_color' => 'amber',
                'active_projects' => 1,
            ],
            [
                'id' => 2,
                'code' => 'CUST-0002',
                'customer_type' => 'perusahaan',
                'name' => 'PT. Shopee International Indonesia',
                'pic_name' => 'Indra Gunawan',
                'phone' => '021-80647300',
                'email' => 'indra.g@shopee.co.id',
                'address' => 'Pacific Century Place Lt. 26, SCBD Lot 10, Jl. Jend. Sudirman Kav 52-53, Jakarta Selatan',
                'note' => 'High-Traffic e-Commerce Hub, Penanganan Khusus...',
                'status' => 'Aktif',
                'status_color' => 'emerald',
                'active_projects' => 2,
            ],
            [
                'id' => 3,
                'code' => 'CUST-0003',
                'customer_type' => 'perusahaan',
                'name' => 'PT. XYZ Teknologi Solusi',
                'pic_name' => 'Rian Hendrawan',
                'phone' => '0812-3456-7890',
                'email' => 'rian@xyzteknologi.id',
                'address' => 'Cyber 2 Tower Lt. 18, Jl. HR Rasuna Said Blok X-5 No. 13, Kuningan, Jakarta Selatan',
                'note' => 'Dedicated IP Transit dan Cloud Connect',
                'status' => 'Aktif',
                'status_color' => 'emerald',
                'active_projects' => 1,
            ],
            [
                'id' => 4,
                'code' => 'CUST-0001',
                'customer_type' => 'perusahaan',
                'name' => 'PT. Bank Mandiri (Persero) Tbk',
                'pic_name' => 'Bambang Pratama',
                'phone' => '021-5263000',
                'email' => 'bambang.p@bankmandiri.co.id',
                'address' => 'Gedung Plaza Mandiri, Jl. Jend. Gatot Subroto Kav. 36-38, Jakarta Selatan 12190',
                'note' => 'Pelanggan Enterprise Prioritas SLA 99.95% 24/7',
                'status' => 'Aktif',
                'status_color' => 'emerald',
                'active_projects' => 2,
            ],
        ];
    }

    /**
     * Ambil daftar Customer Rumahan aktif dari session
     */
    public function getActiveResidentialCustomers(): array
    {
        $customers = session('nms_customers_rumahan', null);
        if ($customers === null) {
            $customers = $this->getDefaultResidentialCustomers();
            session(['nms_customers_rumahan' => $customers]);
        }
        return $customers;
    }

    /**
     * Ambil daftar Customer Perusahaan aktif dari session
     */
    public function getActiveCorporateCustomers(): array
    {
        $corporate = session('nms_customers_perusahaan', null);
        if ($corporate === null) {
            $corporate = $this->getDefaultCorporateCustomers();
            session(['nms_customers_perusahaan' => $corporate]);
        }
        return $corporate;
    }

    /**
     * Tampilkan halaman Customer & Project Management (Customer Rumahan & Customer Perusahaan)
     */
    public function index(Request $request): View
    {
        $customers = $this->getActiveResidentialCustomers();
        $projects = $this->getActiveCorporateCustomers();
        $activeTab = $request->query('tab', 'customer');

        return view('admin.customers', compact('customers', 'projects', 'activeTab'));
    }

    /**
     * Simpan Customer Rumahan Baru
     */
    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'status' => 'required|string',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:300',
            'note' => 'nullable|string|max:300',
        ]);

        $customers = $this->getActiveResidentialCustomers();
        $newId = count($customers) > 0 ? max(array_column($customers, 'id')) + 1 : 101;
        $seq = count($customers) + 1;
        $code = sprintf('HOME-%04d', $seq);

        $newCustomer = [
            'id' => $newId,
            'code' => $code,
            'customer_type' => 'rumahan',
            'name' => $validated['name'],
            'pic_name' => '-',
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'note' => $validated['note'] ?? 'Layanan Internet Rumahan NetFly',
            'status' => $validated['status'],
            'status_color' => $validated['status'] === 'Aktif' ? 'emerald' : ($validated['status'] === 'Negosiasi' ? 'amber' : 'slate'),
            'active_projects' => 1,
        ];

        array_unshift($customers, $newCustomer);
        session(['nms_customers_rumahan' => $customers]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Customer Rumahan '{$validated['name']}' berhasil ditambahkan!",
                'customer' => $newCustomer,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'customer'])
            ->with('success', "Customer Rumahan '{$validated['name']}' berhasil ditambahkan!");
    }

    /**
     * Update Data Customer Rumahan
     */
    public function updateCustomer(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'status' => 'required|string',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:300',
            'note' => 'nullable|string|max:300',
        ]);

        $customers = $this->getActiveResidentialCustomers();
        $updatedCustomer = null;

        foreach ($customers as $k => $c) {
            if ((string) $c['id'] === (string) $id) {
                $customers[$k]['name'] = $validated['name'];
                $customers[$k]['status'] = $validated['status'];
                $customers[$k]['status_color'] = $validated['status'] === 'Aktif' ? 'emerald' : ($validated['status'] === 'Negosiasi' ? 'amber' : 'slate');
                $customers[$k]['email'] = $validated['email'];
                $customers[$k]['phone'] = $validated['phone'];
                $customers[$k]['address'] = $validated['address'];
                $customers[$k]['note'] = $validated['note'] ?? $customers[$k]['note'];
                $updatedCustomer = $customers[$k];
                break;
            }
        }

        session(['nms_customers_rumahan' => $customers]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data Customer Rumahan '{$validated['name']}' berhasil diperbarui!",
                'customer' => $updatedCustomer,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'customer'])
            ->with('success', "Data Customer Rumahan '{$validated['name']}' berhasil diperbarui!");
    }

    /**
     * Hapus Customer Rumahan
     */
    public function destroyCustomer(Request $request, $id)
    {
        $customers = $this->getActiveResidentialCustomers();
        $customerName = 'Customer Rumahan';
        $filtered = [];

        foreach ($customers as $c) {
            if ((string) $c['id'] === (string) $id) {
                $customerName = $c['name'];
            } else {
                $filtered[] = $c;
            }
        }

        session(['nms_customers_rumahan' => $filtered]);

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
     * Simpan Customer Perusahaan Baru
     */
    public function storeProject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'pic_name' => 'required|string|max:150',
            'status' => 'required|string',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:300',
            'note' => 'nullable|string|max:300',
        ]);

        $corporate = $this->getActiveCorporateCustomers();
        $newId = count($corporate) > 0 ? max(array_column($corporate, 'id')) + 1 : 1;
        $code = sprintf('CUST-%04d', $newId);

        $statusColor = $validated['status'] === 'Aktif' ? 'emerald' : ($validated['status'] === 'Negosiasi' ? 'amber' : 'slate');

        $newCorporate = [
            'id' => $newId,
            'code' => $code,
            'customer_type' => 'perusahaan',
            'name' => $validated['name'],
            'pic_name' => $validated['pic_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'note' => $validated['note'] ?? 'Kemitraan Jaringan Korporat NetFly',
            'status' => $validated['status'],
            'status_color' => $statusColor,
            'active_projects' => 1,
        ];

        array_unshift($corporate, $newCorporate);
        session(['nms_customers_perusahaan' => $corporate]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Customer Perusahaan '{$validated['name']}' berhasil ditambahkan!",
                'project' => $newCorporate,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'project'])
            ->with('success', "Customer Perusahaan '{$validated['name']}' berhasil ditambahkan!");
    }

    /**
     * Update Data Customer Perusahaan
     */
    public function updateProject(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'pic_name' => 'required|string|max:150',
            'status' => 'required|string',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:300',
            'note' => 'nullable|string|max:300',
        ]);

        $corporate = $this->getActiveCorporateCustomers();
        $updatedCorporate = null;
        $statusColor = $validated['status'] === 'Aktif' ? 'emerald' : ($validated['status'] === 'Negosiasi' ? 'amber' : 'slate');

        foreach ($corporate as $k => $p) {
            if ((string) $p['id'] === (string) $id) {
                $corporate[$k]['name'] = $validated['name'];
                $corporate[$k]['pic_name'] = $validated['pic_name'];
                $corporate[$k]['status'] = $validated['status'];
                $corporate[$k]['status_color'] = $statusColor;
                $corporate[$k]['email'] = $validated['email'];
                $corporate[$k]['phone'] = $validated['phone'];
                $corporate[$k]['address'] = $validated['address'];
                $corporate[$k]['note'] = $validated['note'] ?? $corporate[$k]['note'];
                $updatedCorporate = $corporate[$k];
                break;
            }
        }

        session(['nms_customers_perusahaan' => $corporate]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data Customer Perusahaan '{$validated['name']}' berhasil diperbarui!",
                'project' => $updatedCorporate,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'project'])
            ->with('success', "Data Customer Perusahaan '{$validated['name']}' berhasil diperbarui!");
    }

    /**
     * Hapus Customer Perusahaan
     */
    public function destroyProject(Request $request, $id)
    {
        $corporate = $this->getActiveCorporateCustomers();
        $companyName = 'Customer Perusahaan';
        $filtered = [];

        foreach ($corporate as $p) {
            if ((string) $p['id'] === (string) $id) {
                $companyName = $p['name'];
            } else {
                $filtered[] = $p;
            }
        }

        session(['nms_customers_perusahaan' => $filtered]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Customer Perusahaan '{$companyName}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'project'])
            ->with('success', "Customer Perusahaan '{$companyName}' berhasil dihapus.");
    }
}
