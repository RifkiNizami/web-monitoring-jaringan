<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    /**
     * Tampilkan halaman inventaris perangkat jaringan & modal manajemen.
     */
    public function index(Request $request): View
    {
        // 4 Summary Metrics
        $summary = [
            'total' => 4,
            'online' => 3,
            'offline' => 1,
            'routers' => 2,
            'switches' => 1,
            'others' => 1,
        ];

        // Opsi jenis perangkat
        $deviceTypes = [
            'Router',
            'Switch',
            'Firewall',
            'Access Point',
            'Server',
        ];

        // Opsi status fisik
        $statusOptions = [
            'Online (Beroperasi Normal)',
            'Offline (Padam / Gangguan)',
            'Maintenance (Pemeliharaan)',
        ];

        // Opsi mitra customer
        $customers = [
            'PT. Bank Mandiri (Persero) Tbk',
            'PT. Shopee International Indonesia',
            'Dinas Pendidikan Provinsi DKI Jakarta',
            'PT. XYZ Teknologi',
            'RS Medika Center',
        ];

        // Opsi project terhubung
        $projects = [
            '-- Tanpa Project / Umum --',
            'MPLS Core Interconnect Plaza Mandiri',
            'Fulfillment Center Warehouse Cikarang',
            'Dedicated Internet SCBD Hub',
            'Smart City Backbone Sekolah Tahap II',
            'Dedicated Fiber 1Gbps',
        ];

        // Data inventaris perangkat (termasuk RTR-CORE-MANDIRI-01 sesuai mockup modal edit & hapus)
        $devices = [
            [
                'id' => 1,
                'hostname' => 'RTR-CORE-MANDIRI-01',
                'type' => 'Router',
                'ip_address' => '10.200.45.1',
                'status' => 'Online (Beroperasi Normal)',
                'status_state' => 'online',
                'customer' => 'PT. Bank Mandiri (Persero) Tbk',
                'project' => 'MPLS Core Interconnect Plaza Mandiri',
                'rack_location' => 'Rack Core-A, U-42',
                'snmp_community' => 'mandiri-ro-noc',
                'uptime' => '45 hari 12 jam',
            ],
            [
                'id' => 2,
                'hostname' => 'SW-AGG-BKS-02',
                'type' => 'Switch',
                'ip_address' => '10.200.46.12',
                'status' => 'Offline (Padam / Gangguan)',
                'status_state' => 'offline',
                'customer' => 'PT. Shopee International Indonesia',
                'project' => 'Fulfillment Center Warehouse Cikarang',
                'rack_location' => 'Rack BKS-02, Slot 12U',
                'snmp_community' => 'netfly-ro-noc',
                'uptime' => '0 menit (Down)',
            ],
            [
                'id' => 3,
                'hostname' => 'AP-OUTDOOR-DISDIK-01',
                'type' => 'Access Point',
                'ip_address' => '10.200.50.88',
                'status' => 'Online (Beroperasi Normal)',
                'status_state' => 'online',
                'customer' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'project' => 'Smart City Backbone Sekolah Tahap II',
                'rack_location' => 'Pole A-Disdik, Outdoor',
                'snmp_community' => 'netfly-ro-noc',
                'uptime' => '12 hari 08 jam',
            ],
            [
                'id' => 4,
                'hostname' => 'FW-EDGE-XYZ-01',
                'type' => 'Firewall',
                'ip_address' => '10.200.40.1',
                'status' => 'Online (Beroperasi Normal)',
                'status_state' => 'online',
                'customer' => 'PT. XYZ Teknologi',
                'project' => 'Dedicated Fiber 1Gbps',
                'rack_location' => 'Rack Edge-01, Slot 24U',
                'snmp_community' => 'xyz-ro-sec',
                'uptime' => '89 hari 04 jam',
            ],
        ];

        return view('devices', compact('summary', 'deviceTypes', 'statusOptions', 'customers', 'projects', 'devices'));
    }
}
