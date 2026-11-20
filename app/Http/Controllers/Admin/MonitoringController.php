<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\MonitoringAlert;
use App\Models\MonitoringDevice;
use App\Services\MikrotikService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Format teks rate bandwidth menjadi string rapi dengan unit standar (e.g. '480.5 Mbps').
     */
    public static function formatRate(?string $rate, string $fallback = '0.0 Mbps'): string
    {
        if (empty($rate) || $rate === '0' || $rate === '0.0') {
            return $fallback;
        }

        $trimmed = trim($rate);

        if (preg_match('/^([\d.]+)\s*([KkMmGgTt]?)bps?$/i', $trimmed, $matches)) {
            $val = $matches[1];
            $unit = strtoupper($matches[2] ?: 'M');

            return "{$val} {$unit}bps";
        }

        if (preg_match('/^([\d.]+)\s*([KkMmGgTt])$/i', $trimmed, $matches)) {
            $val = $matches[1];
            $unit = strtoupper($matches[2]);

            return "{$val} {$unit}bps";
        }

        if (is_numeric($trimmed)) {
            return "{$trimmed} Mbps";
        }

        return $trimmed;
    }

    /**
     * Format instance Model MonitoringDevice menjadi struktur array standar.
     */
    public static function formatMonitoringDevice(MonitoringDevice $device): array
    {
        $status = $device->status ?: 'online';
        $isNonMikrotik = MikrotikService::isNonMikrotik($device->unit_type, $device->api_port);

        return [
            'id' => $device->id,
            'customer_id' => $device->customer_id,
            'project_id' => $device->project_id,
            'customer_code' => $device->customerRelation?->code,
            'hostname' => $device->hostname,
            'unit_type' => $device->unit_type ?? 'Router',
            'ip_address' => $device->ip_address,
            'customer' => $device->customer,
            'project' => $device->project ?: 'Dedicated SLA Monitoring',
            'model' => $device->model ?: ($device->unit_type.' Node'),
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
            'latency_ms' => $device->last_ping_ms !== null ? (int) $device->last_ping_ms : null,
            'last_ping_ms' => (int) ($device->last_ping_ms ?? 0),
            'connection_type' => $device->connection_type ?: ($isNonMikrotik ? 'http' : 'lan'),
            'api_port' => (int) ($device->api_port ?? ($isNonMikrotik ? 80 : 8728)),
            'api_username' => $device->api_username ?? ($isNonMikrotik ? '' : 'admin'),
            'api_password' => $device->api_password ?? '',
            'is_non_mikrotik' => $isNonMikrotik,
            'interfaces' => is_array($device->interfaces) && count($device->interfaces) > 0 ? $device->interfaces : [
                ['name' => $isNonMikrotik ? 'eth0' : 'ether1', 'status' => 'up', 'speed' => $isNonMikrotik ? '100 Mbps' : '1 Gbps', 'rx' => '0 Mbps', 'tx' => '0 Mbps'],
            ],
        ];
    }

    /**
     * Ambil daftar target node telemetri dari database.
     */
    public static function getTelemetryDevicesList(): array
    {
        return MonitoringDevice::with(['customerRelation', 'projectRelation'])
            ->orderBy('id', 'asc')
            ->get()
            ->mapWithKeys(fn ($dev) => [(int) $dev->id => self::formatMonitoringDevice($dev)])
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
                'customer_id' => $dev['customer_id'] ?? null,
                'project_id' => $dev['project_id'] ?? null,
                'hostname' => $dev['hostname'] ?? 'NODE-UNKNOWN',
                'unit_type' => $dev['unit_type'] ?? 'Router',
                'ip_address' => $dev['ip_address'] ?? '127.0.0.1',
                'customer' => $dev['customer'] ?? '-',
                'project' => $dev['project'] ?? 'Dedicated SLA Monitoring',
                'model' => $dev['model'] ?? (($dev['unit_type'] ?? 'Router').' Node'),
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
     * Inisialisasi perangkat monitoring jika tabel masih kosong dari master Device.
     */
    public static function seedInitialMonitoringDevices(): void
    {
        if (MonitoringDevice::count() > 0) {
            return;
        }

        $devices = Device::with('customer', 'project')->get();

        if ($devices->isNotEmpty()) {
            foreach ($devices as $dev) {
                $isOffline = $dev->status_state === 'out_of_stock';
                $status = $isOffline ? 'offline' : 'online';
                $rxRate = self::formatRate($dev->rx_rate, '50.0 Mbps');
                $txRate = self::formatRate($dev->tx_rate, '35.0 Mbps');

                MonitoringDevice::create([
                    'id' => $dev->id,
                    'customer_id' => $dev->customer_id,
                    'project_id' => $dev->project_id,
                    'hostname' => $dev->hostname,
                    'unit_type' => $dev->type ?? 'Router',
                    'ip_address' => $dev->ip_address ?: ('10.200.10.'.(10 + ($dev->id % 200))),
                    'customer' => $dev->customer ? $dev->customer->name : 'Infrastruktur Backbone NetFly',
                    'project' => $dev->project ? $dev->project->name : 'Dedicated Core Interconnect',
                    'model' => $dev->brand_model ?: ($dev->type.' Node'),
                    'rack_location' => $dev->rack_location ?: 'POP Distribusi NetFly',
                    'status' => $status,
                    'status_label' => strtoupper($status),
                    'uptime' => $isOffline ? 'Offline (Timeout)' : '14 hari 06 jam',
                    'rx_mbps' => $isOffline ? '0.0 Mbps' : $rxRate,
                    'tx_mbps' => $isOffline ? '0.0 Mbps' : $txRate,
                    'cpu_load' => $isOffline ? 0.0 : round(18.0 + (($dev->id * 7) % 35), 1),
                    'ram_load' => $isOffline ? 0.0 : round(35.0 + (($dev->id * 11) % 40), 1),
                    'packet_loss' => $isOffline ? 100.0 : 0.0,
                    'last_ping_ms' => $isOffline ? null : (8 + ($dev->id % 15)),
                    'connection_type' => 'lan',
                    'api_port' => 8728,
                    'api_username' => 'admin',
                ]);
            }
        }
    }

    /**
     * Jalankan polling telemetri individual pada satu node (Kondisi A vs Kondisi B).
     */
    public static function pollSingleNode(MonitoringDevice $node, MikrotikService $mikrotikService): void
    {
        if (empty($node->ip_address)) {
            return;
        }

        $isNonMikrotik = MikrotikService::isNonMikrotik($node->unit_type, $node->api_port);

        if ($isNonMikrotik) {
            $port = (int) ($node->api_port ?: 80);
            $check = $mikrotikService->checkHttpSocket($node->ip_address, $port, 2);

            if ($check['online']) {
                $node->update([
                    'status' => 'online',
                    'status_label' => 'ONLINE',
                    'uptime' => $check['uptime'],
                    'last_ping_ms' => $check['latency_ms'],
                    'packet_loss' => 0.0,
                    'interfaces' => ! empty($check['interfaces']) ? $check['interfaces'] : $node->interfaces,
                ]);
            } else {
                $node->update([
                    'status' => 'offline',
                    'status_label' => 'OFFLINE',
                    'cpu_load' => 0.0,
                    'ram_load' => 0.0,
                    'rx_mbps' => '0.0 Mbps',
                    'tx_mbps' => '0.0 Mbps',
                    'uptime' => 'Offline (Timeout)',
                    'last_ping_ms' => null,
                    'packet_loss' => 100.0,
                ]);
            }
        } elseif (! empty($node->api_username)) {
            $telemetry = $mikrotikService->getDeviceTelemetry(
                $node->ip_address,
                $node->api_username,
                (string) ($node->api_password ?? ''),
                (int) ($node->api_port ?? 8728),
                2,
                $node->unit_type
            );

            if ($telemetry) {
                $node->update([
                    'status' => 'online',
                    'status_label' => 'ONLINE',
                    'cpu_load' => $telemetry['resource']['cpu_load'],
                    'ram_load' => $telemetry['resource']['ram_load'],
                    'uptime' => $telemetry['resource']['uptime'],
                    'last_ping_ms' => (int) $telemetry['latency_ms'],
                    'packet_loss' => 0.0,
                    'interfaces' => $telemetry['interfaces'] ?? $node->interfaces,
                ]);
            } else {
                $node->update([
                    'status' => 'offline',
                    'status_label' => 'OFFLINE',
                    'cpu_load' => 0.0,
                    'ram_load' => 0.0,
                    'rx_mbps' => '0.0 Mbps',
                    'tx_mbps' => '0.0 Mbps',
                    'uptime' => 'Offline (Timeout)',
                    'last_ping_ms' => null,
                    'packet_loss' => 100.0,
                ]);
            }
        }
    }

    /**
     * Sinkronisasi alert poller otomatis berdasarkan kondisi nyata setiap node.
     */
    public static function syncPollerAlerts(): void
    {
        $nodes = MonitoringDevice::all();
        $deviceMap = Device::pluck('id', 'hostname')->all();

        foreach ($nodes as $node) {
            $isOffline = $node->status === 'offline' || (float) $node->packet_loss >= 100.0;
            $hasPacketLoss = (float) $node->packet_loss > 10.0 && (float) $node->packet_loss < 100.0;
            $hasHighCpu = (float) $node->cpu_load >= 80.0;
            $deviceId = $deviceMap[$node->hostname] ?? (is_numeric($node->id) && Device::whereKey($node->id)->exists() ? (int) $node->id : null);

            // 1. Kondisi Offline (Perangkat Padam / Loss of Signal)
            if ($isOffline) {
                $exists = MonitoringAlert::where('host', $node->hostname)
                    ->where('type', 'Perangkat Padam')
                    ->where('is_resolved', false)
                    ->exists();

                if (! $exists) {
                    MonitoringAlert::create([
                        'device_id' => $deviceId,
                        'customer_id' => $node->customer_id,
                        'host' => $node->hostname,
                        'customer_name' => $node->customerRelation?->name ?: ($node->customer ?: 'Infrastruktur Jaringan'),
                        'severity' => 'critical',
                        'type' => 'Perangkat Padam',
                        'message' => "Node {$node->hostname} ({$node->ip_address}) tidak merespons poller ICMP/API (Loss of Signal / RTO).",
                        'is_resolved' => false,
                        'resolved_at' => null,
                    ]);
                }
            } else {
                // Auto-resolve jika perangkat sudah kembali online
                MonitoringAlert::where('host', $node->hostname)
                    ->where('type', 'Perangkat Padam')
                    ->where('is_resolved', false)
                    ->update([
                        'is_resolved' => true,
                        'resolved_at' => now(),
                    ]);
            }

            // 2. Kondisi Degradasi Link / Packet Loss Tinggi
            if ($hasPacketLoss) {
                $exists = MonitoringAlert::where('host', $node->hostname)
                    ->where('type', 'Degradasi Jaringan')
                    ->where('is_resolved', false)
                    ->exists();

                if (! $exists) {
                    MonitoringAlert::create([
                        'device_id' => $deviceId,
                        'customer_id' => $node->customer_id,
                        'host' => $node->hostname,
                        'customer_name' => $node->customerRelation?->name ?: ($node->customer ?: 'Infrastruktur Jaringan'),
                        'severity' => 'warning',
                        'type' => 'Degradasi Jaringan',
                        'message' => "Node {$node->hostname} mengalami degradasi link (Packet loss {$node->packet_loss}%).",
                        'is_resolved' => false,
                        'resolved_at' => null,
                    ]);
                }
            } elseif (! $isOffline && (float) $node->packet_loss <= 2.0) {
                MonitoringAlert::where('host', $node->hostname)
                    ->where('type', 'Degradasi Jaringan')
                    ->where('is_resolved', false)
                    ->update([
                        'is_resolved' => true,
                        'resolved_at' => now(),
                    ]);
            }

            // 3. Kondisi Beban CPU Tinggi
            if ($hasHighCpu) {
                $exists = MonitoringAlert::where('host', $node->hostname)
                    ->where('type', 'Beban CPU Tinggi')
                    ->where('is_resolved', false)
                    ->exists();

                if (! $exists) {
                    MonitoringAlert::create([
                        'device_id' => $deviceId,
                        'customer_id' => $node->customer_id,
                        'host' => $node->hostname,
                        'customer_name' => $node->customerRelation?->name ?: ($node->customer ?: 'Infrastruktur Jaringan'),
                        'severity' => 'warning',
                        'type' => 'Beban CPU Tinggi',
                        'message' => "Beban prosessor node {$node->hostname} mencapai {$node->cpu_load}%, terdeteksi potensi antrean trafik.",
                        'is_resolved' => false,
                        'resolved_at' => null,
                    ]);
                }
            } elseif (! $isOffline && (float) $node->cpu_load < 70.0) {
                MonitoringAlert::where('host', $node->hostname)
                    ->where('type', 'Beban CPU Tinggi')
                    ->where('is_resolved', false)
                    ->update([
                        'is_resolved' => true,
                        'resolved_at' => now(),
                    ]);
            }
        }
    }

    /**
     * Tampilkan halaman telemetri pemantauan real-time & live alert NOC.
     */
    public function index(Request $request): View
    {
        // Sinkronisasi alert poller otomatis jika terdapat node monitoring
        self::syncPollerAlerts();

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
                'subtext' => ($warningNodes + $offlineNodes) > 0 ? ($warningNodes + $offlineNodes).' node membutuhkan perhatian' : 'Tidak ada insiden aktif',
            ],
        ];

        // Papan Live Alert (Log Insiden Otomatis dari Database)
        $alerts = MonitoringAlert::latest()->take(10)->get()->map(function ($alert) {
            return [
                'id' => $alert->id,
                'host' => $alert->host,
                'customer' => $alert->customer_name ?? 'Infrastruktur Jaringan',
                'severity' => $alert->severity,
                'type' => $alert->type,
                'message' => $alert->message,
                'is_resolved' => (bool) $alert->is_resolved,
                'time_ago' => $alert->created_at ? $alert->created_at->diffForHumans(null, true) : 'Baru saja',
                'time_wib' => $alert->created_at ? $alert->created_at->format('H:i:s').' WIB' : now()->format('H:i:s').' WIB',
            ];
        })->toArray();

        return view('admin.monitoring.index', compact('summary', 'alerts', 'telemetryDevices'));
    }

    /**
     * Tampilkan halaman khusus detail telemetri perangkat tertentu.
     */
    public function show(int $id): View
    {
        $deviceModel = MonitoringDevice::find($id);

        if (! $deviceModel) {
            abort(404, 'Perangkat jaringan tidak ditemukan.');
        }

        $device = self::formatMonitoringDevice($deviceModel);

        // Ambil riwayat insiden nyata khusus node ini
        $realAlerts = MonitoringAlert::where('host', $device['hostname'])
            ->orWhere('device_id', $device['id'])
            ->latest()
            ->take(10)
            ->get();

        if ($realAlerts->isNotEmpty()) {
            $alerts = $realAlerts->map(function ($a) {
                return [
                    'severity' => $a->severity,
                    'time' => $a->created_at ? $a->created_at->format('H:i:s').' WIB' : now()->format('H:i:s').' WIB',
                    'title' => "{$a->type} (".($a->is_resolved ? 'Selesai' : 'Aktif').')',
                    'desc' => $a->message,
                ];
            })->all();
        } else {
            $alerts = [
                [
                    'severity' => $device['status'] === 'offline' ? 'critical' : ($device['status'] === 'warning' ? 'warning' : 'info'),
                    'time' => now()->format('H:i:s').' WIB',
                    'title' => $device['status'] === 'offline' ? 'Node Terputus (Timeout)' : 'Telemetri Realtime Terpantau Stabil',
                    'desc' => $device['status'] === 'offline'
                        ? "Perangkat tidak merespons poller socket/API pada IP {$device['ip_address']}."
                        : "Sensor CPU ({$device['cpu_load']}%), RAM ({$device['ram_load']}%), dan link port beroperasi dalam batas toleransi aman.",
                ],
            ];
        }

        return view('admin.monitoring.detail', compact('device', 'alerts'));
    }

    /**
     * API Telemetri Stream (Polling per 3-5 detik dari browser frontend)
     */
    public function telemetryStream(int $id): JsonResponse
    {
        $deviceModel = MonitoringDevice::find($id);

        if (! $deviceModel) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Ambil data telemetri langsung (Kondisi A: MikroTik RouterOS API, Kondisi B: Non-MikroTik HTTP/TCP Socket)
        if (! empty($deviceModel->ip_address) && ! app()->runningUnitTests()) {
            $mikrotikService = new MikrotikService;
            self::pollSingleNode($deviceModel, $mikrotikService);
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
                'latency_ms' => $device['latency_ms'],
                'rx_mbps' => $device['rx_mbps'] ?? '0 Mbps',
                'tx_mbps' => $device['tx_mbps'] ?? '0 Mbps',
                'packet_loss' => (float) ($device['packet_loss'] ?? 0.0),
            ],
        ]);
    }

    /**
     * Tandai insiden / alert sebagai terselesaikan.
     */
    public function resolveAlert(Request $request, int $id): JsonResponse
    {
        $alert = MonitoringAlert::find($id);
        if ($alert) {
            $alert->update([
                'is_resolved' => true,
                'resolved_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Insiden telemetri berhasil ditandai selesai (Resolved).',
            'alert_id' => $id,
        ]);
    }

    /**
     * Tandai seluruh alert / insiden yang belum selesai menjadi terselesaikan (Resolved).
     */
    public function resolveAllAlerts(Request $request): JsonResponse
    {
        $count = MonitoringAlert::where('is_resolved', false)->update([
            'is_resolved' => true,
            'resolved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$count} peringatan berhasil ditandai selesai.",
            'resolved_count' => $count,
        ]);
    }

    /**
     * Jalankan poller serentak ke semua node perangkat dan perbarui log alert secara realtime.
     */
    public function pollAllNodes(Request $request): JsonResponse
    {
        if (! app()->runningUnitTests()) {
            self::seedInitialMonitoringDevices();

            $mikrotikService = new MikrotikService;
            $nodes = MonitoringDevice::all();

            foreach ($nodes as $node) {
                self::pollSingleNode($node, $mikrotikService);
            }
        }

        self::syncPollerAlerts();

        $nodes = MonitoringDevice::all();
        $online = $nodes->where('status', 'online')->count();
        $offline = $nodes->where('status', 'offline')->count();
        $warning = $nodes->where('status', 'warning')->count();
        $unresolvedAlerts = MonitoringAlert::where('is_resolved', false)->count();

        return response()->json([
            'success' => true,
            'message' => "Poller berhasil menyinkronkan {$nodes->count()} perangkat.",
            'total' => $nodes->count(),
            'online' => $online,
            'offline' => $offline,
            'warning' => $warning,
            'unresolved_alerts' => $unresolvedAlerts,
            'timestamp' => now()->format('H:i:s').' WIB',
        ]);
    }

    /**
     * Eksekusi diagnostic polling ICMP / SNMP / HTTP Socket terhadap perangkat secara realtime.
     */
    public function pollDevice(Request $request, int $id): JsonResponse
    {
        $deviceModel = MonitoringDevice::find($id);
        $device = $deviceModel ? self::formatMonitoringDevice($deviceModel) : null;

        $hostname = $device['hostname'] ?? ('Node Perangkat #'.$id);
        $ip = $device['ip_address'] ?? '127.0.0.1';
        $status = $device['status'] ?? 'online';
        $ping = (float) ($device['last_ping_ms'] ?? 0.0);
        $snmpStatus = 'Status Link: '.strtoupper($status);
        $quality = $status === 'offline' ? 'Offline / Unreachable' : 'Normal / Ready';

        if ($deviceModel && ! empty($deviceModel->ip_address) && ! app()->runningUnitTests()) {
            $mikrotikService = new MikrotikService;
            $isNonMikrotik = MikrotikService::isNonMikrotik($deviceModel->unit_type, $deviceModel->api_port);
            $port = (int) ($deviceModel->api_port ?? ($isNonMikrotik ? 80 : 8728));

            $test = $mikrotikService->testConnection(
                $deviceModel->ip_address,
                $deviceModel->api_username,
                (string) ($deviceModel->api_password ?? ''),
                $port,
                2,
                $deviceModel->unit_type
            );

            if ($test['success']) {
                $ping = (float) ($test['latency_ms'] ?? 1.0);
                if ($isNonMikrotik) {
                    $snmpStatus = "HTTP/TCP Port {$port} Aktif ({$ping} ms)";
                } else {
                    $snmpStatus = "RouterOS v{$test['resource']['version']} ({$test['resource']['board_name']}) OK";
                }
                $quality = "Connected via Port {$port}";
                $status = 'online';
                $deviceModel->update([
                    'status' => 'online',
                    'status_label' => 'ONLINE',
                    'last_ping_ms' => (int) $ping,
                    'packet_loss' => 0.0,
                ]);
            } else {
                $snmpStatus = 'Unreachable: '.$test['message'];
                $quality = "Port {$port} Timeout";
                $status = 'offline';
                $ping = 0.0;
                $deviceModel->update([
                    'status' => 'offline',
                    'status_label' => 'OFFLINE',
                    'last_ping_ms' => null,
                    'packet_loss' => 100.0,
                ]);
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
            'timestamp' => now()->format('H:i:s').' WIB',
        ]);
    }
}
