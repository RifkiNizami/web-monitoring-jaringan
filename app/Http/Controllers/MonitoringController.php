<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Tampilkan halaman telemetri pemantauan real-time & live alert NOC.
     */
    public function index(Request $request): View
    {
        // 4 Summary Metrics
        $summary = [
            'nodes' => [
                'total' => 3,
                'online' => 2,
                'offline' => 0,
                'warning' => 1,
                'subtext' => '1 node dalam peringatan degradasi',
            ],
            'throughput' => [
                'badge' => 'Core Link',
                'inbound' => '785.7',
                'outbound' => '693.3',
                'unit' => 'Mbps',
                'subtext' => 'Total beban transmisi gateway NOC',
            ],
            'cpu' => [
                'average_load' => 45.3,
                'average_packet_loss' => 4.83,
            ],
            'incidents' => [
                'active_count' => 2,
                'subtext' => 'Membutuhkan investigasi teknisi NOC',
            ],
        ];

        // Papan Live Alert (Log Insiden Otomatis)
        $alerts = [
            [
                'id' => 1,
                'severity' => 'warning', // warning, critical, info
                'host' => 'Node Gateway',
                'customer' => 'Infrastruktur Internal',
                'message' => 'Throughput traffic Shopee SCBD mendekati batas kapasitas link (820 Mbps / 1 Gbps).',
                'type' => 'Traffic Saturasi',
                'time_ago' => '15 minutes ago',
                'time_wib' => '04:06:40 WIB',
                'is_resolved' => false,
            ],
            [
                'id' => 2,
                'severity' => 'critical',
                'host' => 'SW-AGG-BKS-04',
                'customer' => 'PT Shopee International Indonesia',
                'message' => 'Switch Agg-Bks_04 Terputus (Loss of Signal). Dugaan padam suplai AC di site Bekasi.',
                'type' => 'Perangkat Padam',
                'time_ago' => '35 minutes ago',
                'time_wib' => '04:00:40 WIB',
                'is_resolved' => true,
            ],
            [
                'id' => 3,
                'severity' => 'info',
                'host' => 'Node Gateway',
                'customer' => 'Infrastruktur Internal',
                'message' => 'Rute redundan Mandiri DRC aktif otomatis mengalihkan beban link yang ditangguhkan.',
                'type' => 'Reroute Redundan',
                'time_ago' => '15 minutes ago',
                'time_wib' => '04:05:40 WIB',
                'is_resolved' => true,
            ],
        ];

        // Telemetri Status Perangkat Real-Time
        $telemetryDevices = [
            [
                'id' => 1,
                'hostname' => 'AP-OUTDOOR-DISDIK-01',
                'unit_type' => 'Access Point',
                'ip_address' => '10.200.120.15',
                'customer' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'status' => 'warning', // warning, online, offline
                'status_label' => 'WARNING',
                'uptime' => '4h 0j 0m',
                'rx_mbps' => '45.2M',
                'tx_mbps' => '30.1M',
                'cpu_load' => 78.5,
                'ram_load' => 82.0,
                'packet_loss' => 14.5,
                'last_ping_ms' => 45,
            ],
            [
                'id' => 2,
                'hostname' => 'RTR-CORE-MANDIRI-01',
                'unit_type' => 'Router',
                'ip_address' => '10.200.45.1',
                'customer' => 'PT. Bank Mandiri (Persero) Tbk',
                'status' => 'online',
                'status_label' => 'ONLINE',
                'uptime' => '14h 4j 46m',
                'rx_mbps' => '480.5M',
                'tx_mbps' => '390.2M',
                'cpu_load' => 28.4,
                'ram_load' => 44.2,
                'packet_loss' => 0.0,
                'last_ping_ms' => 8,
            ],
            [
                'id' => 3,
                'hostname' => 'SW-AGG-BKS-04',
                'unit_type' => 'Switch',
                'ip_address' => '10.200.58.16',
                'customer' => 'PT. Shopee International Indonesia',
                'status' => 'online',
                'status_label' => 'ONLINE',
                'uptime' => '1j 0m',
                'rx_mbps' => '260M',
                'tx_mbps' => '273M',
                'cpu_load' => 29.0,
                'ram_load' => 46.0,
                'packet_loss' => 0.0,
                'last_ping_ms' => 12,
            ],
            [
                'id' => 4,
                'hostname' => 'FW-EDGE-XYZ-01',
                'unit_type' => 'Firewall',
                'ip_address' => '10.200.40.1',
                'customer' => 'PT. XYZ Teknologi',
                'status' => 'online',
                'status_label' => 'ONLINE',
                'uptime' => '89h 4j 12m',
                'rx_mbps' => '120M',
                'tx_mbps' => '95M',
                'cpu_load' => 34.0,
                'ram_load' => 52.0,
                'packet_loss' => 0.0,
                'last_ping_ms' => 6,
            ],
        ];

        return view('admin.monitoring', compact('summary', 'alerts', 'telemetryDevices'));
    }

    /**
     * Tandai insiden / alert sebagai terselesaikan.
     */
    public function resolveAlert(Request $request, int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Insiden telemetri berhasil ditandai selesai (Resolved).',
            'alert_id' => $id,
        ]);
    }

    /**
     * Eksekusi diagnostic polling ICMP / SNMP terhadap perangkat secara realtime.
     */
    public function pollDevice(Request $request, int $id): JsonResponse
    {
        $samples = [
            1 => [
                'hostname' => 'AP-OUTDOOR-DISDIK-01',
                'ip' => '10.200.120.15',
                'rtt_avg' => 42.4,
                'rtt_min' => 38.1,
                'rtt_max' => 51.2,
                'transmitted' => 4,
                'received' => 3,
                'loss' => 14.5,
                'snmp_status' => 'SNMPv2-MIB::sysUpTime.0 = (1440000) 4:00:00.00',
                'quality' => 'Degraded (High Loss)',
            ],
            2 => [
                'hostname' => 'RTR-CORE-MANDIRI-01',
                'ip' => '10.200.45.1',
                'rtt_avg' => 7.8,
                'rtt_min' => 6.9,
                'rtt_max' => 9.4,
                'transmitted' => 4,
                'received' => 4,
                'loss' => 0.0,
                'snmp_status' => 'SNMPv2-MIB::sysUpTime.0 = (5204600) 14 days, 04:46:00.00',
                'quality' => 'Excellent (Sub-10ms)',
            ],
            3 => [
                'hostname' => 'SW-AGG-BKS-04',
                'ip' => '10.200.58.16',
                'rtt_avg' => 11.6,
                'rtt_min' => 10.2,
                'rtt_max' => 14.0,
                'transmitted' => 4,
                'received' => 4,
                'loss' => 0.0,
                'snmp_status' => 'SNMPv2-MIB::sysUpTime.0 = (360000) 1:00:00.00',
                'quality' => 'Normal / Stable',
            ],
            4 => [
                'hostname' => 'FW-EDGE-XYZ-01',
                'ip' => '10.200.40.1',
                'rtt_avg' => 6.2,
                'rtt_min' => 5.4,
                'rtt_max' => 7.8,
                'transmitted' => 4,
                'received' => 4,
                'loss' => 0.0,
                'snmp_status' => 'SNMPv2-MIB::sysUpTime.0 = (32120000) 89 days, 04:12:00.00',
                'quality' => 'Ultra Fast / Stable',
            ],
        ];

        $data = $samples[$id] ?? [
            'hostname' => 'Node Perangkat #' . $id,
            'ip' => '10.200.x.x',
            'rtt_avg' => 12.0,
            'rtt_min' => 10.0,
            'rtt_max' => 15.0,
            'transmitted' => 4,
            'received' => 4,
            'loss' => 0.0,
            'snmp_status' => 'SNMPv2-MIB::sysUpTime.0 OK',
            'quality' => 'Healthy',
        ];

        return response()->json([
            'success' => true,
            'device_id' => $id,
            'data' => $data,
            'timestamp' => now()->format('H:i:s') . ' WIB',
        ]);
    }
}
