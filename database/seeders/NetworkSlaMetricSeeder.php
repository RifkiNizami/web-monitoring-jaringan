<?php

namespace Database\Seeders;

use App\Models\NetworkSlaMetric;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NetworkSlaMetricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();

        // 30 hari data historis telemetri SLA
        // Pola realistis: mayoritas 99.90% - 100.00%, dengan insiden minor di pertengahan bulan
        $uptimePattern = [
            -29 => ['uptime' => 99.95, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -28 => ['uptime' => 99.92, 'downtime' => 2, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -27 => ['uptime' => 100.00, 'downtime' => 0, 'incidents' => 0, 'notes' => 'Ketersediaan Penuh'],
            -26 => ['uptime' => 99.98, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -25 => ['uptime' => 99.90, 'downtime' => 2, 'incidents' => 0, 'notes' => 'Fluktuasi Latensi Ringan'],
            -24 => ['uptime' => 99.94, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -23 => ['uptime' => 99.88, 'downtime' => 3, 'incidents' => 0, 'notes' => 'Beban Trafik Tinggi'],
            -22 => ['uptime' => 100.00, 'downtime' => 0, 'incidents' => 0, 'notes' => 'Ketersediaan Penuh'],
            -21 => ['uptime' => 99.96, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -20 => ['uptime' => 99.98, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -19 => ['uptime' => 100.00, 'downtime' => 0, 'incidents' => 0, 'notes' => 'Ketersediaan Penuh'],
            -18 => ['uptime' => 99.95, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -17 => ['uptime' => 99.82, 'downtime' => 4, 'incidents' => 1, 'notes' => 'Maintenance Switch POP Bks'],
            -16 => ['uptime' => 99.70, 'downtime' => 6, 'incidents' => 1, 'notes' => 'Reroute Redundan Jawa Barat'],
            -15 => ['uptime' => 99.50, 'downtime' => 7, 'incidents' => 2, 'notes' => 'FO Cut Jalur Pantura (Mitigasi Cepat)'],
            -14 => ['uptime' => 99.65, 'downtime' => 5, 'incidents' => 1, 'notes' => 'Restorasi Transmisi Backbone'],
            -13 => ['uptime' => 99.80, 'downtime' => 3, 'incidents' => 0, 'notes' => 'Normalisasi Trafik'],
            -12 => ['uptime' => 99.90, 'downtime' => 2, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -11 => ['uptime' => 99.94, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -10 => ['uptime' => 99.98, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Ketersediaan Penuh'],
            -9  => ['uptime' => 100.00, 'downtime' => 0, 'incidents' => 0, 'notes' => 'Ketersediaan Penuh'],
            -8  => ['uptime' => 99.92, 'downtime' => 2, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -7  => ['uptime' => 99.95, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -6  => ['uptime' => 99.97, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -5  => ['uptime' => 100.00, 'downtime' => 0, 'incidents' => 0, 'notes' => 'Ketersediaan Penuh'],
            -4  => ['uptime' => 99.95, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -3  => ['uptime' => 99.98, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            -2  => ['uptime' => 99.90, 'downtime' => 2, 'incidents' => 0, 'notes' => 'Pemeliharaan Rutin Firmware'],
            -1  => ['uptime' => 99.96, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal'],
            0   => ['uptime' => 99.92, 'downtime' => 1, 'incidents' => 0, 'notes' => 'Operasi Normal (Hari Ini)'],
        ];

        foreach ($uptimePattern as $daysAgo => $data) {
            $date = $today->copy()->addDays($daysAgo)->toDateString();

            NetworkSlaMetric::updateOrCreate(
                ['date' => $date],
                [
                    'uptime_percentage' => $data['uptime'],
                    'downtime_minutes' => $data['downtime'],
                    'incidents_count' => $data['incidents'],
                    'status' => $data['uptime'] >= 99.9 ? 'Normal' : ($data['uptime'] >= 99.5 ? 'Warning' : 'Incident'),
                    'notes' => $data['notes'],
                ]
            );
        }
    }
}
