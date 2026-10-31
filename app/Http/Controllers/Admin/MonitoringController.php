<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Tampilkan halaman telemetri pemantauan real-time & live alert NOC.
     */
    /**
     * Helper data telemetri devices
     */
    protected function getTelemetryDevicesList(): array
    {
        return [
            1 => [
                'id' => 1,
                'hostname' => 'AP-OUTDOOR-DISDIK-01',
                'unit_type' => 'Access Point',
                'ip_address' => '10.200.120.15',
                'customer' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'project' => 'Smart City Backbone Sekolah Tahap II',
                'model' => 'MikroTik NetMetal 5 (RouterOS v7.14)',
                'rack_location' => 'Pole A-Disdik, Outdoor',
                'status' => 'warning', // warning, online, offline
                'status_label' => 'WARNING',
                'uptime' => '4h 0j 0m',
                'rx_mbps' => '45.2M',
                'tx_mbps' => '30.1M',
                'cpu_load' => 78.5,
                'ram_load' => 82.0,
                'temperature' => '54°C',
                'voltage' => '24.1 V',
                'packet_loss' => 14.5,
                'last_ping_ms' => 45,
                'interfaces' => [
                    ['name' => 'ether1 (PoE IN)', 'status' => 'up', 'speed' => '1 Gbps', 'rx' => '45.2 Mbps', 'tx' => '30.1 Mbps'],
                    ['name' => 'sfp1 (Fiber Uplink)', 'status' => 'down', 'speed' => '10 Gbps', 'rx' => '0 Mbps', 'tx' => '0 Mbps'],
                    ['name' => 'wlan1 (5GHz Outdoor)', 'status' => 'up', 'speed' => '866 Mbps', 'rx' => '32.4 Mbps', 'tx' => '24.1 Mbps'],
                ]
            ],
            2 => [
                'id' => 2,
                'hostname' => 'RTR-CORE-MANDIRI-01',
                'unit_type' => 'Router',
                'ip_address' => '10.200.45.1',
                'customer' => 'PT. Bank Mandiri (Persero) Tbk',
                'project' => 'MPLS Core Interconnect Plaza Mandiri',
                'model' => 'MikroTik CCR2004-16G-2S+ (RouterOS v7.15)',
                'rack_location' => 'Rack Core-A, U-42',
                'status' => 'online',
                'status_label' => 'ONLINE',
                'uptime' => '14h 4j 46m',
                'rx_mbps' => '480.5M',
                'tx_mbps' => '390.2M',
                'cpu_load' => 28.4,
                'ram_load' => 44.2,
                'temperature' => '41°C',
                'voltage' => '12.2 V',
                'packet_loss' => 0.0,
                'last_ping_ms' => 8,
                'interfaces' => [
                    ['name' => 'sfp-sfpplus1 (Backbone DRC)', 'status' => 'up', 'speed' => '10 Gbps', 'rx' => '340.2 Mbps', 'tx' => '280.1 Mbps'],
                    ['name' => 'sfp-sfpplus2 (LAN Core)', 'status' => 'up', 'speed' => '10 Gbps', 'rx' => '140.3 Mbps', 'tx' => '110.1 Mbps'],
                    ['name' => 'ether1 (Management)', 'status' => 'up', 'speed' => '1 Gbps', 'rx' => '1.2 Mbps', 'tx' => '0.8 Mbps'],
                    ['name' => 'ether2 (Backup Link)', 'status' => 'up', 'speed' => '1 Gbps', 'rx' => '0.5 Mbps', 'tx' => '0.2 Mbps'],
                ]
            ],
            3 => [
                'id' => 3,
                'hostname' => 'SW-AGG-BKS-04',
                'unit_type' => 'Switch',
                'ip_address' => '10.200.58.16',
                'customer' => 'PT. Shopee International Indonesia',
                'project' => 'Fulfillment Center Warehouse Cikarang',
                'model' => 'MikroTik CRS328-24P-4S+RM',
                'rack_location' => 'Rack BKS-02, Slot 12U',
                'status' => 'online',
                'status_label' => 'ONLINE',
                'uptime' => '1j 0m',
                'rx_mbps' => '260M',
                'tx_mbps' => '273M',
                'cpu_load' => 29.0,
                'ram_load' => 46.0,
                'temperature' => '47°C',
                'voltage' => '48.0 V (PoE)',
                'packet_loss' => 0.0,
                'last_ping_ms' => 12,
                'interfaces' => [
                    ['name' => 'sfp-sfpplus1 (Uplink Aggregation)', 'status' => 'up', 'speed' => '10 Gbps', 'rx' => '260.0 Mbps', 'tx' => '273.0 Mbps'],
                    ['name' => 'ether1 - ether24 (PoE AP/CCTV)', 'status' => 'up', 'speed' => '1 Gbps', 'rx' => '240.0 Mbps', 'tx' => '250.0 Mbps'],
                ]
            ],
            4 => [
                'id' => 4,
                'hostname' => 'FW-EDGE-XYZ-01',
                'unit_type' => 'Firewall',
                'ip_address' => '10.200.40.1',
                'customer' => 'PT. XYZ Teknologi',
                'project' => 'Dedicated IP Transit dan Cloud Connect',
                'model' => 'MikroTik CCR1036-8G-2S+',
                'rack_location' => 'Rack Cyber-01, Slot 08U',
                'status' => 'online',
                'status_label' => 'ONLINE',
                'uptime' => '89h 4j 12m',
                'rx_mbps' => '120M',
                'tx_mbps' => '95M',
                'cpu_load' => 34.0,
                'ram_load' => 52.0,
                'temperature' => '43°C',
                'voltage' => '12.0 V',
                'packet_loss' => 0.0,
                'last_ping_ms' => 6,
                'interfaces' => [
                    ['name' => 'ether1 (Public WAN Transit)', 'status' => 'up', 'speed' => '1 Gbps', 'rx' => '120.0 Mbps', 'tx' => '95.0 Mbps'],
                    ['name' => 'ether2 (DMZ / Server Farm)', 'status' => 'up', 'speed' => '1 Gbps', 'rx' => '80.0 Mbps', 'tx' => '65.0 Mbps'],
                ]
            ],
        ];
    }

    /**
     * Tampilkan halaman telemetri pemantauan real-time & live alert NOC.
     */
    public function index(Request $request): View
    {
        // 4 Summary Metrics
        $summary = [
            'nodes' => [
                'total' => 4,
                'online' => 3,
                'offline' => 0,
                'warning' => 1,
                'subtext' => '1 node dalam peringatan degradasi',
            ],
            'throughput' => [
                'badge' => 'Core Link',
                'inbound' => '905.7',
                'outbound' => '788.3',
                'unit' => 'Mbps',
                'subtext' => 'Total beban transmisi gateway NOC',
            ],
            'cpu' => [
                'average_load' => 42.4,
                'average_packet_loss' => 3.6,
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
                'host' => 'AP-OUTDOOR-DISDIK-01',
                'customer' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'message' => 'Throughput packet loss meningkat hingga 14.5% di pole outdoor.',
                'type' => 'Degradasi Link',
                'time_ago' => '15 menit lalu',
                'time_wib' => '04:06:40 WIB',
                'is_resolved' => false,
            ],
            [
                'id' => 2,
                'severity' => 'critical',
                'host' => 'SW-AGG-BKS-04',
                'customer' => 'PT Shopee International Indonesia',
                'message' => 'Switch Agg-Bks_04 Terputus (Loss of Signal). Catu daya AC sempat padam.',
                'type' => 'Perangkat Padam',
                'time_ago' => '35 menit lalu',
                'time_wib' => '04:00:40 WIB',
                'is_resolved' => true,
            ],
            [
                'id' => 3,
                'severity' => 'info',
                'host' => 'RTR-CORE-MANDIRI-01',
                'customer' => 'PT. Bank Mandiri (Persero) Tbk',
                'message' => 'Rute redundan Mandiri DRC aktif otomatis mengalihkan beban link primer.',
                'type' => 'Reroute Redundan',
                'time_ago' => '15 menit lalu',
                'time_wib' => '04:05:40 WIB',
                'is_resolved' => true,
            ],
        ];

        $telemetryDevices = array_values($this->getTelemetryDevicesList());

        return view('admin.monitoring.index', compact('summary', 'alerts', 'telemetryDevices'));
    }

    /**
     * Simpan / Perbarui Konfigurasi IP & Kredensial API MikroTik
     */
    public function saveSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required',
            'ip_address' => 'required|string',
            'api_port' => 'required|numeric',
            'api_username' => 'required|string',
            'api_password' => 'nullable|string',
            'connection_type' => 'required|string',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi IP & Kredensial API MikroTik berhasil diperbarui.',
            'data' => $validated,
        ]);
    }

    /**
     * Tampilkan halaman khusus detail telemetri perangkat tertentu.
     */
    public function show(int $id): View
    {
        $devices = $this->getTelemetryDevicesList();
        
        if (!isset($devices[$id])) {
            abort(404, 'Perangkat jaringan tidak ditemukan.');
        }

        $device = $devices[$id];

        // Filter alert khusus device ini
        $alerts = [
            [
                'severity' => $device['status'] === 'warning' ? 'warning' : 'info',
                'time' => '10:45:00 WIB',
                'title' => 'Telemetri Realtime Terpantau Stabil',
                'desc' => 'Sensor CPU, RAM, dan link port beroperasi dalam batas toleransi aman.',
            ],
            [
                'severity' => 'info',
                'time' => '08:00:00 WIB',
                'title' => 'Diagnostic Poller Otomatis Selesai',
                'desc' => 'Hasil ping latency rata-rata ' . $device['last_ping_ms'] . ' ms dengan packet loss ' . $device['packet_loss'] . '%.',
            ]
        ];

        return view('admin.monitoring.detail', compact('device', 'alerts'));
    }

    /**
     * API Telemetri Stream (Polling per 3-5 detik dari browser frontend)
     */
    public function telemetryStream(int $id): JsonResponse
    {
        $devices = $this->getTelemetryDevicesList();
        
        if (!isset($devices[$id])) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $device = $devices[$id];

        // Simulasi osilasi metrik realtime
        $jitter = rand(-3, 3);
        $cpu = max(5, min(99, $device['cpu_load'] + $jitter));
        $ram = max(10, min(95, $device['ram_load'] + rand(-1, 1)));
        $ping = max(2, $device['last_ping_ms'] + rand(-2, 4));
        $rxFloat = (float) filter_var($device['rx_mbps'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $txFloat = (float) filter_var($device['tx_mbps'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        $currentRx = round(max(1.0, $rxFloat + (rand(-15, 15) / 10)), 1);
        $currentTx = round(max(1.0, $txFloat + (rand(-15, 15) / 10)), 1);

        return response()->json([
            'success' => true,
            'timestamp' => now()->format('H:i:s'),
            'device_id' => $id,
            'status' => $device['status'],
            'metrics' => [
                'cpu_load' => $cpu,
                'ram_load' => $ram,
                'ping_ms' => $ping,
                'rx_mbps' => $currentRx,
                'tx_mbps' => $currentTx,
                'packet_loss' => $device['packet_loss'],
            ]
        ]);
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
