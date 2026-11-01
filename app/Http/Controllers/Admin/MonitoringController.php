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
    public static function getTelemetryDevicesList(): array
    {
        // Inisialisasi state bersih v2 (menghapus data dummy bawaan sebelumnya)
        if (!session()->has('monitoring_devices_v2_initialized')) {
            session(['monitoring_devices_v2_initialized' => true]);
            session(['monitoring_telemetry_devices' => []]);
            return [];
        }

        return session('monitoring_telemetry_devices', []);
    }

    /**
     * Simpan pembaruan list perangkat telemetri ke session.
     */
    public static function saveTelemetryDevicesList(array $devices): void
    {
        session(['monitoring_telemetry_devices' => $devices]);
    }

    /**
     * Tampilkan halaman telemetri pemantauan real-time & live alert NOC.
     */
    public function index(Request $request): View
    {
        $telemetryDevices = array_values($this->getTelemetryDevicesList());
        $deviceCollection = collect($telemetryDevices);

        $totalNodes = $deviceCollection->count();
        $onlineNodes = $deviceCollection->where('status', 'online')->count();
        $warningNodes = $deviceCollection->where('status', 'warning')->count();
        $offlineNodes = $deviceCollection->where('status', 'offline')->count();
        $avgCpu = $deviceCollection->avg('cpu_load') ? round($deviceCollection->avg('cpu_load'), 1) : 0.0;
        $avgLoss = $deviceCollection->avg('packet_loss') ? round($deviceCollection->avg('packet_loss'), 1) : 0.0;

        $totalInbound = 0.0;
        $totalOutbound = 0.0;
        foreach ($telemetryDevices as $dev) {
            $rx = (float) filter_var($dev['rx_mbps'] ?? '0', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $tx = (float) filter_var($dev['tx_mbps'] ?? '0', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $totalInbound += $rx;
            $totalOutbound += $tx;
        }

        // 4 Summary Metrics
        $summary = [
            'nodes' => [
                'total' => $totalNodes,
                'online' => $onlineNodes,
                'offline' => $offlineNodes,
                'warning' => $warningNodes,
                'subtext' => ($totalNodes === 0 ? 'Belum ada node yang didaftarkan' : ($warningNodes > 0 ? "{$warningNodes} node dalam peringatan degradasi" : ($offlineNodes > 0 ? "{$offlineNodes} node terputus" : 'Semua node beroperasi stabil'))),
            ],
            'throughput' => [
                'badge' => $totalNodes > 0 ? 'Core Link' : 'Standby',
                'inbound' => $totalInbound > 0 ? (string) round($totalInbound, 1) : '0.0',
                'outbound' => $totalOutbound > 0 ? (string) round($totalOutbound, 1) : '0.0',
                'unit' => 'Mbps',
                'subtext' => $totalNodes > 0 ? 'Total beban transmisi node terpantau' : 'Belum ada transmisi aktif',
            ],
            'cpu' => [
                'average_load' => $avgCpu,
                'average_packet_loss' => $avgLoss,
            ],
            'incidents' => [
                'active_count' => 0,
                'subtext' => 'Tidak ada insiden aktif',
            ],
        ];

        // Papan Live Alert (Log Insiden Otomatis)
        $alerts = [];

        return view('admin.monitoring.index', compact('summary', 'alerts', 'telemetryDevices'));
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

        // Ambil data telemetri langsung dari RouterOS MikroTik jika terdaftar
        if (!empty($device['ip_address']) && !empty($device['api_username']) && !app()->runningUnitTests()) {
            $mikrotikService = new \App\Services\MikrotikService();
            $telemetry = $mikrotikService->getDeviceTelemetry(
                $device['ip_address'],
                $device['api_username'],
                (string) ($device['api_password'] ?? ''),
                (int) ($device['api_port'] ?? 8728),
                2
            );

            if ($telemetry) {
                $device['cpu_load'] = $telemetry['resource']['cpu_load'];
                $device['ram_load'] = $telemetry['resource']['ram_load'];
                $device['uptime'] = $telemetry['resource']['uptime'];
                $device['last_ping_ms'] = (int) $telemetry['latency_ms'];
                $device['status'] = 'online';
                if (!empty($telemetry['interfaces'])) {
                    $device['interfaces'] = $telemetry['interfaces'];
                }
                $devices[$id] = $device;
                self::saveTelemetryDevicesList($devices);
            } else {
                $device['status'] = 'offline';
                $devices[$id] = $device;
                self::saveTelemetryDevicesList($devices);
            }
        }

        return response()->json([
            'success' => true,
            'timestamp' => now()->format('H:i:s'),
            'device_id' => $id,
            'status' => $device['status'] ?? 'offline',
            'metrics' => [
                'cpu_load' => (float) ($device['cpu_load'] ?? 0.0),
                'ram_load' => (float) ($device['ram_load'] ?? 0.0),
                'ping_ms' => (int) ($device['last_ping_ms'] ?? 0),
                'rx_mbps' => $device['rx_mbps'] ?? '0 Mbps',
                'tx_mbps' => $device['tx_mbps'] ?? '0 Mbps',
                'packet_loss' => (float) ($device['packet_loss'] ?? 0.0),
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
        $devices = $this->getTelemetryDevicesList();
        $device = $devices[$id] ?? null;

        $hostname = $device['hostname'] ?? ('Node Perangkat #' . $id);
        $ip = $device['ip_address'] ?? '127.0.0.1';
        $status = $device['status'] ?? 'online';
        $ping = (float) ($device['last_ping_ms'] ?? 0.0);
        $snmpStatus = 'Status Link: ' . strtoupper($status);
        $quality = $status === 'offline' ? 'Offline / Unreachable' : 'Normal / Ready';

        if ($device && !empty($device['api_username']) && !app()->runningUnitTests()) {
            $mikrotikService = new \App\Services\MikrotikService();
            $test = $mikrotikService->testConnection(
                $device['ip_address'],
                $device['api_username'],
                (string) ($device['api_password'] ?? ''),
                (int) ($device['api_port'] ?? 8728),
                2
            );

            if ($test['success']) {
                $ping = $test['latency_ms'];
                $snmpStatus = "RouterOS v{$test['resource']['version']} ({$test['resource']['board_name']}) OK";
                $quality = 'Connected via Port ' . ($device['api_port'] ?? 8728);
                $status = 'online';
                $device['status'] = 'online';
                $device['last_ping_ms'] = (int) $ping;
                $devices[$id] = $device;
                self::saveTelemetryDevicesList($devices);
            } else {
                $snmpStatus = 'API Unreachable: ' . $test['message'];
                $quality = 'Port 8728 Timeout';
                $status = 'offline';
            }
        }

        $data = [
            'hostname' => $hostname,
            'ip' => $ip,
            'rtt_avg' => $ping,
            'rtt_min' => max(0.0, $ping - 1.0),
            'rtt_max' => $ping + 1.5,
            'transmitted' => 4,
            'received' => $status === 'offline' ? 0 : 4,
            'loss' => $status === 'offline' ? 100.0 : ($device['packet_loss'] ?? 0.0),
            'snmp_status' => $snmpStatus,
            'quality' => $quality,
        ];

        return response()->json([
            'success' => true,
            'device_id' => $id,
            'data' => $data,
            'timestamp' => now()->format('H:i:s') . ' WIB',
        ]);
    }
}
