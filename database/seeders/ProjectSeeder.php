<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'id' => 1,
                'customer_id' => 3,
                'name' => 'Smart City Backbone Sekolah Tahap II',
                'subtitle' => 'Uji coba jaringan fiber optik program smart classroom',
                'branch_location' => 'Disdik Jakarta Pusat Network Hub',
                'gps_lat' => '-6.175118',
                'gps_long' => '106.865939',
                'service_type' => 'Dedicated Fiber 500 Mbps',
                'status' => 'Trial / PoC',
                'description' => 'Uji coba jaringan fiber optik program smart classroom',
            ],
            [
                'id' => 2,
                'customer_id' => 2,
                'name' => 'Dedicated Internet SCBD Hub',
                'subtitle' => 'Koneksi premium video streaming lelang & operasional',
                'branch_location' => 'Pacific Century Place SCBD',
                'gps_lat' => '-6.225484',
                'gps_long' => '106.804862',
                'service_type' => 'Dedicated Fiber 1 Gbps',
                'status' => 'Aktif',
                'description' => 'Koneksi premium video streaming lelang & operasional kantor pusat',
            ],
            [
                'id' => 3,
                'customer_id' => 2,
                'name' => 'Fulfillment Center Warehouse Cikarang',
                'subtitle' => 'Backup nirkabel otomatis untuk gudang logistik',
                'branch_location' => 'Kawasan Industri GIIC Cikarang',
                'gps_lat' => '-6.345683',
                'gps_long' => '107.155362',
                'service_type' => 'Wireless Broadband Backup 200M',
                'status' => 'Aktif',
                'description' => 'Backup nirkabel otomatis untuk gudang logistik',
            ],
            [
                'id' => 4,
                'customer_id' => 4,
                'name' => 'Metro-Ethernet Cyber 2 Kuningan',
                'subtitle' => 'Interkoneksi kantar form ke gateway NetFly',
                'branch_location' => 'Cyber 2 Tower Kuningan',
                'gps_lat' => '-6.223580',
                'gps_long' => '106.832180',
                'service_type' => 'Metro - E 500 Mbps',
                'status' => 'Aktif',
                'description' => 'Interkoneksi kantar form ke gateway NetFly',
            ],
            [
                'id' => 5,
                'customer_id' => 1,
                'name' => 'MPLS Core Interconnect Plaza Mandiri',
                'subtitle' => 'Jaringan perbankan primer SLA 99.95%',
                'branch_location' => 'Plaza Mandiri Gatot Subroto',
                'gps_lat' => '-6.230873',
                'gps_long' => '106.818320',
                'service_type' => 'MPLS VPN 1 Gbps',
                'status' => 'Aktif',
                'description' => 'Backbone sekunder fiber link 1Gbps terhubung ke POP Sudirman',
            ],
            [
                'id' => 6,
                'customer_id' => 1,
                'name' => 'DRC Link Mandiri Rungkut Surabaya',
                'subtitle' => 'Disaster Recovery Center link lintas pulau Jawa',
                'branch_location' => 'Data Center Rungkut, Surabaya',
                'gps_lat' => '-7.334281',
                'gps_long' => '112.764520',
                'service_type' => 'Dark Fiber & Backup Link',
                'status' => 'Aktif',
                'description' => 'Disaster Recovery Center link lintas pulau Jawa',
            ],
        ];

        foreach ($projects as $data) {
            Project::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
