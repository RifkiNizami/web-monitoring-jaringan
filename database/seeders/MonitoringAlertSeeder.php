<?php

namespace Database\Seeders;

use App\Models\MonitoringAlert;
use Illuminate\Database\Seeder;

class MonitoringAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alerts = [
            [
                'id' => 1,
                'device_id' => null,
                'customer_id' => null,
                'host' => 'Node Gateway',
                'customer_name' => 'Infrastruktur Internal',
                'severity' => 'warning',
                'type' => 'Traffic Saturasi',
                'message' => 'Throughput traffic Shopee SCBD mendekati batas kapasitas link (820 Mbps / 1 Gbps).',
                'is_resolved' => false,
                'resolved_at' => null,
            ],
            [
                'id' => 2,
                'device_id' => 2,
                'customer_id' => 2,
                'host' => 'SW-AGG-BKS-04',
                'customer_name' => 'PT Shopee International Indonesia',
                'severity' => 'critical',
                'type' => 'Perangkat Padam',
                'message' => 'Switch Agg-Bks_04 Terputus (Loss of Signal). Dugaan padam suplai AC di site Bekasi.',
                'is_resolved' => true,
                'resolved_at' => '2026-10-10 04:00:40',
            ],
            [
                'id' => 3,
                'device_id' => null,
                'customer_id' => null,
                'host' => 'Node Gateway',
                'customer_name' => 'Infrastruktur Internal',
                'severity' => 'info',
                'type' => 'Reroute Redundan',
                'message' => 'Rute redundan Mandiri DRC aktif otomatis mengalihkan beban link yang ditangguhkan.',
                'is_resolved' => true,
                'resolved_at' => '2026-10-10 04:05:40',
            ],
        ];

        foreach ($alerts as $data) {
            MonitoringAlert::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
