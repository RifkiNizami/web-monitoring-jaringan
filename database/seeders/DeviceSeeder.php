<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devices = [
            [
                'id' => 1,
                'customer_id' => 1,
                'project_id' => 5,
                'hostname' => 'RTR-CORE-MANDIRI-01',
                'type' => 'Router',
                'ip_address' => '10.200.45.1',
                'mac_address' => '00:1A:2B:3C:4D:5E',
                'status' => 'Online (Beroperasi Normal)',
                'status_state' => 'online',
                'rack_location' => 'Rack Core-A, U-42',
                'snmp_community' => 'mandiri-ro-noc',
                'snmp_port' => 161,
                'uptime' => '45 hari 12 jam',
                'last_ping_ms' => 8,
                'packet_loss' => 0.00,
                'cpu_load' => 28.40,
                'ram_load' => 44.20,
                'rx_rate' => '480.5M',
                'tx_rate' => '390.2M',
            ],
            [
                'id' => 2,
                'customer_id' => 2,
                'project_id' => 3,
                'hostname' => 'SW-AGG-BKS-02',
                'type' => 'Switch',
                'ip_address' => '10.200.46.12',
                'mac_address' => 'A4:93:4C:11:22:33',
                'status' => 'Offline (Padam / Gangguan)',
                'status_state' => 'offline',
                'rack_location' => 'Rack BKS-02, Slot 12U',
                'snmp_community' => 'netfly-ro-noc',
                'snmp_port' => 161,
                'uptime' => '0 menit (Down)',
                'last_ping_ms' => null,
                'packet_loss' => 100.00,
                'cpu_load' => 0.00,
                'ram_load' => 0.00,
                'rx_rate' => '0M',
                'tx_rate' => '0M',
            ],
            [
                'id' => 3,
                'customer_id' => 3,
                'project_id' => 1,
                'hostname' => 'AP-OUTDOOR-DISDIK-01',
                'type' => 'Access Point',
                'ip_address' => '10.200.120.15',
                'mac_address' => 'CC:2D:E0:44:55:66',
                'status' => 'Online (Beroperasi Normal)',
                'status_state' => 'warning',
                'rack_location' => 'Pole A-Disdik, Outdoor',
                'snmp_community' => 'netfly-ro-noc',
                'snmp_port' => 161,
                'uptime' => '12 hari 08 jam',
                'last_ping_ms' => 45,
                'packet_loss' => 14.50,
                'cpu_load' => 78.50,
                'ram_load' => 82.00,
                'rx_rate' => '45.2M',
                'tx_rate' => '30.1M',
            ],
            [
                'id' => 4,
                'customer_id' => 4,
                'project_id' => 4,
                'hostname' => 'FW-EDGE-XYZ-01',
                'type' => 'Firewall',
                'ip_address' => '10.200.40.1',
                'mac_address' => 'F0:9F:C2:77:88:99',
                'status' => 'Online (Beroperasi Normal)',
                'status_state' => 'online',
                'rack_location' => 'Rack Cyber-01, U-10',
                'snmp_community' => 'netfly-ro-noc',
                'snmp_port' => 161,
                'uptime' => '89 hari 04 jam',
                'last_ping_ms' => 6,
                'packet_loss' => 0.00,
                'cpu_load' => 34.00,
                'ram_load' => 52.00,
                'rx_rate' => '120M',
                'tx_rate' => '95M',
            ],
        ];

        foreach ($devices as $data) {
            Device::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
