<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerProjectController extends Controller
{
    /**
     * Default list of customers based on Figma designs
     */
    protected function getDefaultCustomers(): array
    {
        return [
            [
                'id' => 1,
                'code' => 'CUST-0004',
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
     * Default list of projects based on Figma designs
     */
    protected function getDefaultProjects(): array
    {
        return [
            [
                'id' => 1,
                'customer_id' => 1,
                'customer_name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'pic_name' => 'Drs. H. Mulyadi',
                'name' => 'Smart City Backbone Sekolah Tahap II',
                'subtitle' => 'Uji coba jaringan fiber optik program smart classroom',
                'branch_location' => 'Disdik Jakarta Pusat Network Hub',
                'gps_lat' => '-6.175118',
                'gps_long' => '106.865939',
                'service_type' => 'Dedicated Fiber 500 Mbps',
                'status' => 'Trial / PoC',
                'status_color' => 'amber',
                'description' => 'Uji coba jaringan fiber optik program smart classroom',
            ],
            [
                'id' => 2,
                'customer_id' => 2,
                'customer_name' => 'PT. Shopee International Indonesia',
                'pic_name' => 'Indra Gunawan',
                'name' => 'Dedicated Internet SCBD Hub',
                'subtitle' => 'Koneksi premium video streaming lelang & operasional',
                'branch_location' => 'Pacific Century Place SCBD',
                'gps_lat' => '-6.225484',
                'gps_long' => '106.804862',
                'service_type' => 'Dedicated Fiber 1 Gbps',
                'status' => 'Aktif',
                'status_color' => 'emerald',
                'description' => 'Koneksi premium video streaming lelang & operasional kantor pusat',
            ],
            [
                'id' => 3,
                'customer_id' => 2,
                'customer_name' => 'PT. Shopee International Indonesia',
                'pic_name' => 'Indra Gunawan',
                'name' => 'Fulfillment Center Warehouse Cikarang',
                'subtitle' => 'Backup nirkabel otomatis untuk gudang logistik',
                'branch_location' => 'Kawasan Industri GIIC Cikarang',
                'gps_lat' => '-6.345683',
                'gps_long' => '107.155362',
                'service_type' => 'Wireless Broadband Backup 200M',
                'status' => 'Aktif',
                'status_color' => 'emerald',
                'description' => 'Backup nirkabel otomatis untuk gudang logistik',
            ],
            [
                'id' => 4,
                'customer_id' => 3,
                'customer_name' => 'PT. XYZ Teknologi Solusi',
                'pic_name' => 'Rian Hendrawan',
                'name' => 'Metro-Ethernet Cyber 2 Kuningan',
                'subtitle' => 'Interkoneksi kantar form ke gateway NetFly',
                'branch_location' => 'Cyber 2 Tower Kuningan',
                'gps_lat' => '-6.223580',
                'gps_long' => '106.832180',
                'service_type' => 'Metro - E 500 Mbps',
                'status' => 'Aktif',
                'status_color' => 'emerald',
                'description' => 'Interkoneksi kantar form ke gateway NetFly',
            ],
            [
                'id' => 5,
                'customer_id' => 4,
                'customer_name' => 'PT. Bank Mandiri (Persero) Tbk',
                'pic_name' => 'Bambang Pratama',
                'name' => 'MPLS Core Interconnect Plaza Mandiri',
                'subtitle' => 'Backbone sekunder fiber link 1Gbps terhubung ke POP Sudirman',
                'branch_location' => 'Kantor Pusat Plaza Mandiri Jakarta',
                'gps_lat' => '-6.229748',
                'gps_long' => '106.816358',
                'service_type' => 'MPLS VPN 1 Gbps',
                'status' => 'Aktif',
                'status_color' => 'emerald',
                'description' => 'Backbone sekunder fiber link 1Gbps terhubung ke POP Sudirman',
            ],
            [
                'id' => 6,
                'customer_id' => 4,
                'customer_name' => 'PT. Bank Mandiri (Persero) Tbk',
                'pic_name' => 'Bambang Pratama',
                'name' => 'DRC Link Mandiri Rungkut Surabaya',
                'subtitle' => 'Disaster Recovery Center link lintas pulau Jawa',
                'branch_location' => 'Data Center Rungkut, Surabaya',
                'gps_lat' => '-7.334281',
                'gps_long' => '112.764520',
                'service_type' => 'Dark Fiber & Backup Link',
                'status' => 'Aktif',
                'status_color' => 'emerald',
                'description' => 'Disaster Recovery Center link lintas pulau Jawa',
            ],
        ];
    }

    protected function getActiveCustomers(): array
    {
        $customers = session('nms_customers', null);
        if ($customers === null) {
            $customers = $this->getDefaultCustomers();
            session(['nms_customers' => $customers]);
        }
        return $customers;
    }

    protected function getActiveProjects(): array
    {
        $projects = session('nms_projects', null);
        if ($projects === null) {
            $projects = $this->getDefaultProjects();
            session(['nms_projects' => $projects]);
        }
        return $projects;
    }

    /**
     * Tampilkan halaman Customer & Project Management
     */
    public function index(Request $request): View
    {
        $customers = $this->getActiveCustomers();
        $projects = $this->getActiveProjects();
        $activeTab = $request->query('tab', 'customer');

        // Recalculate project count per customer
        $projectCountMap = [];
        foreach ($projects as $p) {
            $cId = $p['customer_id'] ?? null;
            if ($cId) {
                $projectCountMap[$cId] = ($projectCountMap[$cId] ?? 0) + 1;
            }
        }
        foreach ($customers as $k => $c) {
            $customers[$k]['active_projects'] = $projectCountMap[$c['id']] ?? 0;
        }

        return view('admin.customers', compact('customers', 'projects', 'activeTab'));
    }

    /**
     * Simpan Mitra Customer Baru
     */
    public function storeCustomer(Request $request)
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

        $customers = $this->getActiveCustomers();
        $newId = count($customers) > 0 ? max(array_column($customers, 'id')) + 1 : 1;
        $code = sprintf('CUST-%04d', $newId);

        $newCustomer = [
            'id' => $newId,
            'code' => $code,
            'name' => $validated['name'],
            'pic_name' => $validated['pic_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'note' => $validated['note'] ?? 'Kemitraan Jaringan Korporat NetFly',
            'status' => $validated['status'],
            'status_color' => $validated['status'] === 'Aktif' ? 'emerald' : ($validated['status'] === 'Negosiasi' ? 'amber' : 'slate'),
            'active_projects' => 0,
        ];

        array_unshift($customers, $newCustomer);
        session(['nms_customers' => $customers]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Mitra Customer '{$validated['name']}' berhasil ditambahkan!",
                'customer' => $newCustomer,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'customer'])
            ->with('success', "Mitra Customer '{$validated['name']}' berhasil ditambahkan!");
    }

    /**
     * Update Data Mitra Customer
     */
    public function updateCustomer(Request $request, $id)
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

        $customers = $this->getActiveCustomers();
        $updatedCustomer = null;

        foreach ($customers as $k => $c) {
            if ((string) $c['id'] === (string) $id) {
                $customers[$k]['name'] = $validated['name'];
                $customers[$k]['pic_name'] = $validated['pic_name'];
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

        session(['nms_customers' => $customers]);

        // Also update customer name & pic in related projects
        $projects = $this->getActiveProjects();
        foreach ($projects as $k => $p) {
            if ((string) ($p['customer_id'] ?? '') === (string) $id) {
                $projects[$k]['customer_name'] = $validated['name'];
                $projects[$k]['pic_name'] = $validated['pic_name'];
            }
        }
        session(['nms_projects' => $projects]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data Mitra Customer '{$validated['name']}' berhasil diperbarui!",
                'customer' => $updatedCustomer,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'customer'])
            ->with('success', "Data Mitra Customer '{$validated['name']}' berhasil diperbarui!");
    }

    /**
     * Hapus Mitra Customer
     */
    public function destroyCustomer(Request $request, $id)
    {
        $customers = $this->getActiveCustomers();
        $customerName = 'Customer';
        $filtered = [];

        foreach ($customers as $c) {
            if ((string) $c['id'] === (string) $id) {
                $customerName = $c['name'];
            } else {
                $filtered[] = $c;
            }
        }

        session(['nms_customers' => $filtered]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Mitra Customer '{$customerName}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'customer'])
            ->with('success', "Mitra Customer '{$customerName}' berhasil dihapus.");
    }

    /**
     * Simpan Project Baru
     */
    public function storeProject(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'name' => 'required|string|max:200',
            'branch_location' => 'required|string|max:200',
            'service_type' => 'required|string|max:150',
            'gps_lat' => 'nullable|string|max:50',
            'gps_long' => 'nullable|string|max:50',
            'status' => 'required|string',
            'description' => 'nullable|string|max:300',
        ]);

        $customers = collect($this->getActiveCustomers())->keyBy('id');
        $matchedCust = $customers->get($validated['customer_id']);
        $custName = $matchedCust['name'] ?? 'Mitra Customer';
        $picName = $matchedCust['pic_name'] ?? 'PIC Customer';

        $projects = $this->getActiveProjects();
        $newId = count($projects) > 0 ? max(array_column($projects, 'id')) + 1 : 1;

        $statusColor = $validated['status'] === 'Aktif' ? 'emerald' : ($validated['status'] === 'Trial / PoC' ? 'amber' : 'slate');

        $newProject = [
            'id' => $newId,
            'customer_id' => (int) $validated['customer_id'],
            'customer_name' => $custName,
            'pic_name' => $picName,
            'name' => $validated['name'],
            'subtitle' => $validated['description'] ?? 'Layanan konektivitas jaringan terkelola NetFly',
            'branch_location' => $validated['branch_location'],
            'gps_lat' => $validated['gps_lat'] ?? '-6.2088',
            'gps_long' => $validated['gps_long'] ?? '106.8456',
            'service_type' => $validated['service_type'],
            'status' => $validated['status'],
            'status_color' => $statusColor,
            'description' => $validated['description'] ?? '',
        ];

        array_unshift($projects, $newProject);
        session(['nms_projects' => $projects]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Project '{$validated['name']}' berhasil ditambahkan!",
                'project' => $newProject,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'project'])
            ->with('success', "Project '{$validated['name']}' berhasil ditambahkan!");
    }

    /**
     * Update Data Project
     */
    public function updateProject(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'name' => 'required|string|max:200',
            'branch_location' => 'required|string|max:200',
            'service_type' => 'required|string|max:150',
            'gps_lat' => 'nullable|string|max:50',
            'gps_long' => 'nullable|string|max:50',
            'status' => 'required|string',
            'description' => 'nullable|string|max:300',
        ]);

        $customers = collect($this->getActiveCustomers())->keyBy('id');
        $matchedCust = $customers->get($validated['customer_id']);
        $custName = $matchedCust['name'] ?? 'Mitra Customer';
        $picName = $matchedCust['pic_name'] ?? 'PIC Customer';

        $projects = $this->getActiveProjects();
        $updatedProject = null;
        $statusColor = $validated['status'] === 'Aktif' ? 'emerald' : ($validated['status'] === 'Trial / PoC' ? 'amber' : 'slate');

        foreach ($projects as $k => $p) {
            if ((string) $p['id'] === (string) $id) {
                $projects[$k]['customer_id'] = (int) $validated['customer_id'];
                $projects[$k]['customer_name'] = $custName;
                $projects[$k]['pic_name'] = $picName;
                $projects[$k]['name'] = $validated['name'];
                $projects[$k]['subtitle'] = $validated['description'] ?? $projects[$k]['subtitle'];
                $projects[$k]['branch_location'] = $validated['branch_location'];
                $projects[$k]['gps_lat'] = $validated['gps_lat'] ?? $projects[$k]['gps_lat'];
                $projects[$k]['gps_long'] = $validated['gps_long'] ?? $projects[$k]['gps_long'];
                $projects[$k]['service_type'] = $validated['service_type'];
                $projects[$k]['status'] = $validated['status'];
                $projects[$k]['status_color'] = $statusColor;
                $projects[$k]['description'] = $validated['description'] ?? $projects[$k]['description'];
                $updatedProject = $projects[$k];
                break;
            }
        }

        session(['nms_projects' => $projects]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data Project '{$validated['name']}' berhasil diperbarui!",
                'project' => $updatedProject,
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'project'])
            ->with('success', "Data Project '{$validated['name']}' berhasil diperbarui!");
    }

    /**
     * Hapus Project
     */
    public function destroyProject(Request $request, $id)
    {
        $projects = $this->getActiveProjects();
        $projectName = 'Project';
        $filtered = [];

        foreach ($projects as $p) {
            if ((string) $p['id'] === (string) $id) {
                $projectName = $p['name'];
            } else {
                $filtered[] = $p;
            }
        }

        session(['nms_projects' => $filtered]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Project '{$projectName}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('customers.index', ['tab' => 'project'])
            ->with('success', "Project '{$projectName}' berhasil dihapus.");
    }
}
