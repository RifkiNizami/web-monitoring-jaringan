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
                'customer_id' => null,
                'project_id' => null,
                'hostname' => 'RTR-CORE-01',
                'type' => 'Router',
                'brand_model' => 'MikroTik CCR2004-16G-2S+',
                'serial_number' => 'SN-9A82B1002',
                'quantity' => 4,
                'ip_address' => '10.200.45.1',
                'mac_address' => '00:1A:2B:3C:4D:5E',
                'status' => 'Tersedia',
                'status_state' => 'available',
                'photo' => null,
                'rack_location' => 'Rack Core-A, U-42',
            ],
            [
                'id' => 2,
                'customer_id' => null,
                'project_id' => null,
                'hostname' => 'SW-AGG-BKS-04',
                'type' => 'Switch',
                'brand_model' => 'MikroTik CRS328-24P-4S+RM',
                'serial_number' => 'SN-7C11D4098',
                'quantity' => 8,
                'ip_address' => '10.200.46.12',
                'mac_address' => 'A4:93:4C:11:22:33',
                'status' => 'Terpakai',
                'status_state' => 'in_use',
                'photo' => null,
                'rack_location' => 'Rack BKS-02, Slot 12U',
            ],
            [
                'id' => 3,
                'customer_id' => null,
                'project_id' => null,
                'hostname' => 'AP-OUTDOOR-DISDIK-01',
                'type' => 'Access Point',
                'brand_model' => 'Ubiquiti UniFi U6-Mesh Pro',
                'serial_number' => 'SN-4F55E9210',
                'quantity' => 12,
                'ip_address' => '10.200.120.15',
                'mac_address' => 'CC:2D:E0:44:55:66',
                'status' => 'Tersedia',
                'status_state' => 'available',
                'photo' => null,
                'rack_location' => 'Pole A-Disdik, Outdoor',
            ],
            [
                'id' => 4,
                'customer_id' => null,
                'project_id' => null,
                'hostname' => 'FW-EDGE-01',
                'type' => 'Firewall',
                'brand_model' => 'FortiGate 60F Next-Gen',
                'serial_number' => 'SN-1B33C8841',
                'quantity' => 0,
                'ip_address' => '10.200.40.1',
                'mac_address' => 'F0:9F:C2:77:88:99',
                'status' => 'Habis',
                'status_state' => 'out_of_stock',
                'photo' => null,
                'rack_location' => 'Rack Cyber-01, U-10',
            ],
            [
                'id' => 5,
                'customer_id' => null,
                'project_id' => null,
                'hostname' => 'RTR-CORE-MANDIRI-01',
                'type' => 'Router',
                'brand_model' => 'MikroTik CCR2116-12G-4S+',
                'serial_number' => 'SN-MND00192',
                'quantity' => 2,
                'ip_address' => '10.200.45.2',
                'mac_address' => '00:1A:2B:3C:4D:5F',
                'status' => 'Tersedia',
                'status_state' => 'available',
                'photo' => null,
                'rack_location' => 'Rack Core-A, U-40',
            ],
        ];

        foreach ($devices as $data) {
            Device::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
