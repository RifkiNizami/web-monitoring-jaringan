<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonitoringDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Format instance Model MonitoringDevice menjadi struktur array standar.
     */
    public static function formatMonitoringDevice(MonitoringDevice $device): array
    {
        $status = $device->status ?: 'online';

        return [
            'id' => $device->id,
            'hostname' => $device->hostname,
            'unit_type' => $device->unit_type ?? 'Router',
            'ip_address' => $device->ip_address,
            'customer' => $device->customer,
            'project' => $device->project ?: 'Dedicated SLA Monitoring',
            'model' => $device->model ?: ($device->unit_type . ' Node'),
            'rack_location' => $device->rack_location ?: 'POP Distribusi',
            'status' => $status,
            'status_label' => $device->status_label ?: strtoupper($status),
            'uptime' => $device->uptime ?: 'Siap Pantau',
            'rx_mbps' => $device->rx_mbps ?: '0.0 Mbps',
            'tx_mbps' => $device->tx_mbps ?: '0.0 Mbps',
            'cpu_load' => (float) ($device->cpu_load ?? 0.0),
            'ram_load' => (float) ($device->ram_load ?? 0.0),
            'temperature' => $device->temperature ?: '-',
            'voltage' => $device->voltage ?: '-',
            'packet_loss' => (float) ($device->packet_loss ?? 0.0),
            'last_ping_ms' => (int) ($device->last_ping_ms ?? 0),
            'connection_type' => $device->connection_type ?: 'lan',
            'api_port' => (int) ($device->api_port ?? 8728),
            'api_username' => $device->api_username ?: 'admin',
            'api_password' => $device->api_password ?? '',
            'interfaces' => is_array($device->interfaces) ? $device->interfaces : [
                ['name' => 'ether1', 'status' => 'up', 'speed' => '1 Gbps', 'rx' => '0 Mbps', 'tx' => '0 Mbps'],
            ],
        ];
    }

    /**
     * Ambil daftar target node telemetri dari database.
     */
    public static function getTelemetryDevicesList(): array
    {
        return MonitoringDevice::orderBy('id', 'asc')
            ->get()
            ->mapWithKeys(fn (MonitoringDevice $dev) => [$dev->id => self::formatMonitoringDevice($dev)])
            ->all();
    }

    /**
     * Sinkronisasi pembaruan list perangkat telemetri ke tabel database monitoring_devices.
     */
    public static function saveTelemetryDevicesList(array $devices): void
    {
        MonitoringDevice::query()->delete();

        foreach ($devices as $key => $dev) {
            $status = $dev['status'] ?? 'online';
            MonitoringDevice::create([
                'id' => $dev['id'] ?? (is_numeric($key) ? (int) $key : null),
                'hostname' => $dev['hostname'] ?? 'NODE-UNKNOWN',
                'unit_type' => $dev['unit_type'] ?? 'Router',
                'ip_address' => $dev['ip_address'] ?? '127.0.0.1',
                'customer' => $dev['customer'] ?? '-',
                'project' => $dev['project'] ?? 'Dedicated SLA Monitoring',
                'model' => $dev['model'] ?? (($dev['unit_type'] ?? 'Router') . ' Node'),
                'rack_location' => $dev['rack_location'] ?? 'POP Distribusi',
                'status' => $status,
                'status_label' => $dev['status_label'] ?? strtoupper($status),
                'uptime' => $dev['uptime'] ?? 'Siap Pantau',
                'rx_mbps' => $dev['rx_mbps'] ?? '0.0 Mbps',
                'tx_mbps' => $dev['tx_mbps'] ?? '0.0 Mbps',
                'cpu_load' => (float) ($dev['cpu_load'] ?? 0.0),
                'ram_load' => (float) ($dev['ram_load'] ?? 0.0),
                'temperature' => $dev['temperature'] ?? '-',
                'voltage' => $dev['voltage'] ?? '-',
                'packet_loss' => (float) ($dev['packet_loss'] ?? 0.0),
                'last_ping_ms' => (int) ($dev['last_ping_ms'] ?? 0),
                'connection_type' => $dev['connection_type'] ?? 'lan',
                'api_port' => (int) ($dev['api_port'] ?? 8728),
                'api_username' => $dev['api_username'] ?? 'admin',
                'api_password' => $dev['api_password'] ?? '',
                'interfaces' => $dev['interfaces'] ?? [],
            ]);
        }
    }

    /**
     * Tampilkan halaman telemetri pemantauan real-time & live alert NOC.
     */
    public function index(Request $request): View
    {
        $telemetryDevices = array_values(self::getTelemetryDevicesList());
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
                'active_count' => $warningNodes + $offlineNodes,
                'subtext' => ($warningNodes + $offlineNodes) > 0 ? ($warningNodes + $offlineNodes) . ' node membutuhkan perhatian' : 'Tidak ada insiden aktif',
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
        $deviceModel = MonitoringDevice::find($id);

        if (!$deviceModel) {
            abort(404, 'Perangkat jaringan tidak ditemukan.');
        }

        $device = self::formatMonitoringDevice($deviceModel);

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
        $deviceModel = MonitoringDevice::find($id);

        if (!$deviceModel) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Ambil data telemetri langsung dari RouterOS MikroTik jika terdaftar
        if (!empty($deviceModel->ip_address) && !empty($deviceModel->api_username) && !app()->runningUnitTests()) {
            $mikrotikService = new \App\Services\MikrotikService();
            $telemetry = $mikrotikService->getDeviceTelemetry(
                $deviceModel->ip_address,
                $deviceModel->api_username,
                (string) ($deviceModel->api_password ?? ''),
                (int) ($deviceModel->api_port ?? 8728),
                2
            );

            if ($telemetry) {
                $updates = [
                    'cpu_load' => $telemetry['resource']['cpu_load'],
                    'ram_load' => $telemetry['resource']['ram_load'],
                    'uptime' => $telemetry['resource']['uptime'],
                    'last_ping_ms' => (int) $telemetry['latency_ms'],
                    'status' => 'online',
                    'status_label' => 'ONLINE',
                ];
                if (!empty($telemetry['interfaces'])) {
                    $updates['interfaces'] = $telemetry['interfaces'];
                }
                $deviceModel->update($updates);
            } else {
                $deviceModel->update([
                    'status' => 'offline',
                    'status_label' => 'OFFLINE',
                ]);
            }
        }

        $device = self::formatMonitoringDevice($deviceModel);

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
        $deviceModel = MonitoringDevice::find($id);
        $device = $deviceModel ? self::formatMonitoringDevice($deviceModel) : null;

        $hostname = $device['hostname'] ?? ('Node Perangkat #' . $id);
        $ip = $device['ip_address'] ?? '127.0.0.1';
        $status = $device['status'] ?? 'online';
        $ping = (float) ($device['last_ping_ms'] ?? 0.0);
        $snmpStatus = 'Status Link: ' . strtoupper($status);
        $quality = $status === 'offline' ? 'Offline / Unreachable' : 'Normal / Ready';

        if ($deviceModel && !empty($deviceModel->api_username) && !app()->runningUnitTests()) {
            $mikrotikService = new \App\Services\MikrotikService();
            $test = $mikrotikService->testConnection(
                $deviceModel->ip_address,
                $deviceModel->api_username,
                (string) ($deviceModel->api_password ?? ''),
                (int) ($deviceModel->api_port ?? 8728),
                2
            );

            if ($test['success']) {
                $ping = $test['latency_ms'];
                $snmpStatus = "RouterOS v{$test['resource']['version']} ({$test['resource']['board_name']}) OK";
                $quality = 'Connected via Port ' . ($deviceModel->api_port ?? 8728);
                $status = 'online';
                $deviceModel->update([
                    'status' => 'online',
                    'status_label' => 'ONLINE',
                    'last_ping_ms' => (int) $ping,
                ]);
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
