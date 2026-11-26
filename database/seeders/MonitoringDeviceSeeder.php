<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\MonitoringAlert;
use App\Models\MonitoringDevice;
use App\Models\Project;
use Illuminate\Database\Seeder;

class MonitoringDeviceSeeder extends Seeder
{
    /**
     * Seed data perangkat monitoring dengan status ONLINE beserta metrik telemetri awal.
     */
    public function run(): void
    {
        $customersByName = Customer::all()->keyBy(fn (Customer $c) => strtolower(trim($c->name)));
        $projectsByName = Project::all()->keyBy(fn (Project $p) => strtolower(trim($p->name)));

        $devices = [
            [
                'hostname' => 'RTR-CORE-NFM-01',
                'unit_type' => 'Router',
                'ip_address' => '10.200.10.1',
                'customer' => 'PT. Nusantara FinTech Mandiri',
                'project' => 'Core Interconnect FinTech Surabaya',
                'model' => 'MikroTik CCR2004-16G-2S+',
                'rack_location' => 'POP Surabaya Barat — Rack A1 U-12',
                'status' => 'online',
                'status_label' => 'ONLINE',
                'uptime' => '42h 14j 28m',
                'rx_mbps' => '184.6 Mbps',
                'tx_mbps' => '112.4 Mbps',
                'cpu_load' => 28.5,
                'ram_load' => 46.2,
                'packet_loss' => 0.0,
                'last_ping_ms' => 7,
                'connection_type' => 'lan',
                'api_port' => 8728,
                'api_username' => 'noc_monitor',
                'api_password' => '',
                'interfaces' => [
                    [
                        'name' => 'sfp-sfpplus1-wan',
                        'type' => 'ether',
                        'status' => 'up',
                        'speed' => '10 Gbps',
                        'rx' => '118.2 Mbps',
                        'tx' => '72.1 Mbps',
                        'rx_mbps' => 118.2,
                        'tx_mbps' => 72.1,
                    ],
                    [
                        'name' => 'ether1-core-lan',
                        'type' => 'ether',
                        'status' => 'up',
                        'speed' => '1 Gbps',
                        'rx' => '44.8 Mbps',
                        'tx' => '26.5 Mbps',
                        'rx_mbps' => 44.8,
                        'tx_mbps' => 26.5,
                    ],
                    [
                        'name' => 'ether2-backup-bgp',
                        'type' => 'ether',
                        'status' => 'up',
                        'speed' => '1 Gbps',
                        'rx' => '21.6 Mbps',
                        'tx' => '13.8 Mbps',
                        'rx_mbps' => 21.6,
                        'tx_mbps' => 13.8,
                    ],
                    [
                        'name' => 'ether3-mgmt',
                        'type' => 'ether',
                        'status' => 'up',
                        'speed' => '100 Mbps',
                        'rx' => '2.4 Mbps',
                        'tx' => '1.1 Mbps',
                        'rx_mbps' => 2.4,
                        'tx_mbps' => 1.1,
                    ],
                ],
            ],
            [
                'hostname' => 'SW-DIST-GHS-01',
                'unit_type' => 'Switch',
                'ip_address' => '10.200.20.2',
                'customer' => 'RS. Graha Husada Surabaya',
                'project' => 'Backbone SIMRS Graha Husada',
                'model' => 'MikroTik CRS326-24G-2S+RM',
                'rack_location' => 'POP Darmo — Rack B2 U-06',
                'status' => 'online',
                'status_label' => 'ONLINE',
                'uptime' => '31h 09j 45m',
                'rx_mbps' => '146.8 Mbps',
                'tx_mbps' => '91.5 Mbps',
                'cpu_load' => 22.4,
                'ram_load' => 38.7,
                'packet_loss' => 0.0,
                'last_ping_ms' => 11,
                'connection_type' => 'lan',
                'api_port' => 8728,
                'api_username' => 'noc_monitor',
                'api_password' => '',
                'interfaces' => [
                    [
                        'name' => 'sfp-uplink-darmo',
                        'type' => 'ether',
                        'status' => 'up',
                        'speed' => '10 Gbps',
                        'rx' => '94.2 Mbps',
                        'tx' => '58.4 Mbps',
                        'rx_mbps' => 94.2,
                        'tx_mbps' => 58.4,
                    ],
                    [
                        'name' => 'ether1-simrs-igd',
                        'type' => 'ether',
                        'status' => 'up',
                        'speed' => '1 Gbps',
                        'rx' => '34.1 Mbps',
                        'tx' => '21.7 Mbps',
                        'rx_mbps' => 34.1,
                        'tx_mbps' => 21.7,
                    ],
                    [
                        'name' => 'ether2-poliklinik',
                        'type' => 'ether',
                        'status' => 'up',
                        'speed' => '1 Gbps',
                        'rx' => '18.5 Mbps',
                        'tx' => '11.4 Mbps',
                        'rx_mbps' => 18.5,
                        'tx_mbps' => 11.4,
                    ],
                ],
            ],
            [
                'hostname' => 'FW-EDGE-SPG-01',
                'unit_type' => 'Firewall',
                'ip_address' => '10.200.30.1',
                'customer' => 'Hotel Santika Premiere Gubeng',
                'project' => 'Managed Wi-Fi Hospitality Gubeng',
                'model' => 'MikroTik RB5009UG+S+IN',
                'rack_location' => 'Ruang Server Gubeng — Rack C1 U-04',
                'status' => 'online',
                'status_label' => 'ONLINE',
                'uptime' => '64h 02j 19m',
                'rx_mbps' => '98.4 Mbps',
                'tx_mbps' => '56.2 Mbps',
                'cpu_load' => 35.0,
                'ram_load' => 51.8,
                'packet_loss' => 0.0,
                'last_ping_ms' => 14,
                'connection_type' => 'lan',
                'api_port' => 8728,
                'api_username' => 'noc_monitor',
                'api_password' => '',
                'interfaces' => [
                    [
                        'name' => 'ether1-isp-main',
                        'type' => 'ether',
                        'status' => 'up',
                        'speed' => '2.5 Gbps',
                        'rx' => '62.4 Mbps',
                        'tx' => '35.8 Mbps',
                        'rx_mbps' => 62.4,
                        'tx_mbps' => 35.8,
                    ],
                    [
                        'name' => 'ether2-guest-wifi',
                        'type' => 'ether',
                        'status' => 'up',
                        'speed' => '1 Gbps',
                        'rx' => '24.6 Mbps',
                        'tx' => '13.9 Mbps',
                        'rx_mbps' => 24.6,
                        'tx_mbps' => 13.9,
                    ],
                    [
                        'name' => 'ether3-ballroom',
                        'type' => 'ether',
                        'status' => 'up',
                        'speed' => '1 Gbps',
                        'rx' => '11.4 Mbps',
                        'tx' => '6.5 Mbps',
                        'rx_mbps' => 11.4,
                        'tx_mbps' => 6.5,
                    ],
                ],
            ],
        ];

        foreach ($devices as $data) {
            $customer = $customersByName->get(strtolower(trim($data['customer'])));
            $project = $projectsByName->get(strtolower(trim($data['project'])));

            $payload = array_merge($data, [
                'customer_id' => $customer?->id,
                'project_id' => $project?->id,
            ]);

            MonitoringDevice::updateOrCreate(
                ['hostname' => $data['hostname']],
                $payload
            );

            // Tandai alert "Perangkat Padam" lama sebagai selesai karena node sudah online
            MonitoringAlert::where('host', $data['hostname'])
                ->where('type', 'Perangkat Padam')
                ->where('is_resolved', false)
                ->update([
                    'is_resolved' => true,
                    'resolved_at' => now(),
                ]);
        }
    }
}
