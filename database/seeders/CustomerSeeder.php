<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'id' => 1,
                'code' => 'CUST-0001',
                'name' => 'PT. Bank Mandiri (Persero) Tbk',
                'pic_name' => 'Bambang Pratama',
                'phone' => '021-5263000',
                'email' => 'bambang.p@bankmandiri.co.id',
                'address' => 'Gedung Plaza Mandiri, Jl. Jend. Gatot Subroto Kav. 36-38, Jakarta Selatan 12190',
                'category' => 'Enterprise Banking',
                'package' => 'MPLS Core Interconnect 10Gbps',
                'note' => 'Pelanggan Enterprise Prioritas SLA 99.95% 24/7',
                'status' => 'Aktif',
            ],
            [
                'id' => 2,
                'code' => 'CUST-0002',
                'name' => 'PT. Shopee International Indonesia',
                'pic_name' => 'Indra Gunawan',
                'phone' => '021-80647300',
                'email' => 'indra.g@shopee.co.id',
                'address' => 'Pacific Century Place Lt. 26, SCBD Lot 10, Jl. Jend. Sudirman Kav 52-53, Jakarta Selatan',
                'category' => 'E-Commerce Tech Hub',
                'package' => 'Dedicated Fiber 2Gbps + SCBD Direct Peering',
                'note' => 'High-Traffic e-Commerce Hub, Penanganan Khusus SLA 99.9%',
                'status' => 'Aktif',
            ],
            [
                'id' => 3,
                'code' => 'CUST-0003',
                'name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'pic_name' => 'Drs. H. Mulyadi',
                'phone' => '021-3844567',
                'email' => 'tik@disdik.jakarta.go.id',
                'address' => 'Jl. Gatot Subroto No. 40, RT.7/RW.1, Bendungan Hilir, Tanah Abang, Jakarta Pusat',
                'category' => 'Pemerintahan & Pendidikan',
                'package' => 'Smart City Backbone Fiber 1Gbps',
                'note' => 'Proyek Smart School dan Integrasi Jaringan AMDK antar wilayah sekolah negeri',
                'status' => 'Negosiasi',
            ],
            [
                'id' => 4,
                'code' => 'CUST-0004',
                'name' => 'PT. XYZ Teknologi Solusi',
                'pic_name' => 'Rian Hendrawan',
                'phone' => '0812-3456-7890',
                'email' => 'rian@xyzteknologi.id',
                'address' => 'Cyber 2 Tower Lt. 18, Jl. HR Rasuna Said Blok X-5 No. 13, Kuningan, Jakarta Selatan',
                'category' => 'Software & Datacenter',
                'package' => 'Dedicated Fiber 1Gbps',
                'note' => 'Dedicated IP Transit dan Cloud Connect langsung gateway NetFly',
                'status' => 'Aktif',
            ],
            [
                'id' => 5,
                'code' => 'CUST-0005',
                'name' => 'RS Medika Center',
                'pic_name' => 'dr. Anton Pratama, Sp.A',
                'phone' => '0818-7766-5544',
                'email' => 'simrs@medikacenter.org',
                'address' => 'Jl. RS Fatmawati No. 15, Cilandak, Jakarta Selatan',
                'category' => 'Layanan Kesehatan & RS',
                'package' => 'Dedicated Fiber 500Mbps High SLA',
                'note' => 'Interkoneksi Sistem SIMRS dan Telemedis Rawat Inap',
                'status' => 'Aktif',
            ],
        ];

        foreach ($customers as $data) {
            Customer::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
