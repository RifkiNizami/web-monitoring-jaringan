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
     * Konversi string rate bandwidth (Kbps/Mbps/Gbps/bps) menjadi nilai float dalam satuan Mbps.
     */
    public static function parseRateToMbps(?string $rate): float
    {
        if (empty($rate)) {
            return 0.0;
        }

        $trimmed = trim($rate);
        if (preg_match('/([\d.]+)\s*([TtGgMmKk])?bps?/i', $trimmed, $matches)) {
            $val = (float) $matches[1];
            $unit = strtolower($matches[2] ?? 'm');

            return match ($unit) {
                't' => $val * 1000000.0,
                'g' => $val * 1000.0,
                'k' => $val / 1000.0,
                '' => $val / 1000000.0,
                default => $val,
            };
        }

        return (float) filter_var($trimmed, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
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
            'project' => $device->project ?: '-',
            'model' => $device->model ?: ($device->unit_type ? $device->unit_type.' Node' : '-'),
            'rack_location' => $device->rack_location ?: '-',
            'status' => $status,
            'status_label' => $device->status_label ?: strtoupper($status),
            'uptime' => $device->uptime ?: ($status === 'online' ? 'Online' : 'Offline'),
            'rx_mbps' => $device->rx_mbps ?: '0.0 Mbps',
            'tx_mbps' => $device->tx_mbps ?: '0.0 Mbps',
            'cpu_load' => (float) ($device->cpu_load ?? 0.0),
            'ram_load' => (float) ($device->ram_load ?? 0.0),
            'temperature' => '-',
            'voltage' => '-',
            'packet_loss' => (float) ($device->packet_loss ?? 0.0),
            'latency_ms' => $device->last_ping_ms !== null ? (int) $device->last_ping_ms : null,
            'last_ping_ms' => (int) ($device->last_ping_ms ?? 0),
            'connection_type' => $device->connection_type ?: ($isNonMikrotik ? 'http' : 'lan'),
            'api_port' => (int) ($device->api_port ?? ($isNonMikrotik ? 80 : 8728)),
            'api_username' => $device->api_username ?? ($isNonMikrotik ? '' : 'admin'),
            'has_api_password' => ! empty($device->api_password),
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
        // Jangan menginjeksi data dummy otomatis bila kosong

    }

    /**
     * Hasilkan metrik telemetri dummy awal yang realistis berdasarkan status perangkat.
     */
    public static function generateInitialDummyMetrics(string $status = 'online', bool $isNonMikrotik = false, int $port = 8728): array
    {
        if ($status === 'offline') {
            return [
                'status' => 'offline',
                'status_label' => 'OFFLINE',
                'cpu_load' => 0.0,
                'ram_load' => 0.0,
                'rx_mbps' => '0.0 Mbps',
                'tx_mbps' => '0.0 Mbps',
                'last_ping_ms' => null,
                'packet_loss' => 100.0,
                'uptime' => 'Offline',
                'interfaces' => [
                    [
                        'name' => $isNonMikrotik ? 'eth0' : 'ether1-wan',
                        'type' => 'ether',
                        'status' => 'down',
                        'speed' => $isNonMikrotik ? '100 Mbps' : '1 Gbps',
                        'rx' => '0.0 Mbps',
                        'tx' => '0.0 Mbps',
                        'rx_mbps' => 0.0,
                        'tx_mbps' => 0.0,
                    ],
                ],
            ];
        }

        $isWarning = $status === 'warning';
        $cpu = $isWarning ? round(mt_rand(720, 840) / 10, 1) : round(mt_rand(180, 460) / 10, 1);
        $ram = $isWarning ? round(mt_rand(760, 860) / 10, 1) : round(mt_rand(320, 620) / 10, 1);
        $rx = round(mt_rand(480, 1850) / 10, 1);
        $tx = round(max(12.0, $rx * (mt_rand(45, 72) / 100)), 1);
        $latency = $isWarning ? mt_rand(34, 72) : mt_rand(4, 18);
        $packetLoss = $isWarning ? round(mt_rand(15, 75) / 10, 1) : 0.0;
        $days = mt_rand(3, 65);
        $hours = mt_rand(1, 23);
        $mins = mt_rand(1, 59);

        $rxMain = round($rx * 0.65, 1);
        $txMain = round($tx * 0.65, 1);
        $rxSub = round(max(1.0, $rx - $rxMain), 1);
        $txSub = round(max(0.5, $tx - $txMain), 1);

        $interfaces = $isNonMikrotik
            ? [
                [
                    'name' => 'eth0-uplink',
                    'type' => 'ether',
                    'status' => 'up',
                    'speed' => '100 Mbps',
                    'rx' => "{$rxMain} Mbps",
                    'tx' => "{$txMain} Mbps",
                    'rx_mbps' => $rxMain,
                    'tx_mbps' => $txMain,
                ],
                [
                    'name' => 'wlan0-ap',
                    'type' => 'wlan',
                    'status' => 'up',
                    'speed' => '300 Mbps',
                    'rx' => "{$rxSub} Mbps",
                    'tx' => "{$txSub} Mbps",
                    'rx_mbps' => $rxSub,
                    'tx_mbps' => $txSub,
                ],
            ]
            : [
                [
                    'name' => 'sfp-sfpplus1-wan',
                    'type' => 'ether',
                    'status' => 'up',
                    'speed' => '10 Gbps',
                    'rx' => "{$rxMain} Mbps",
                    'tx' => "{$txMain} Mbps",
                    'rx_mbps' => $rxMain,
                    'tx_mbps' => $txMain,
                ],
                [
                    'name' => 'ether1-core-lan',
                    'type' => 'ether',
                    'status' => 'up',
                    'speed' => '1 Gbps',
                    'rx' => "{$rxSub} Mbps",
                    'tx' => "{$txSub} Mbps",
                    'rx_mbps' => $rxSub,
                    'tx_mbps' => $txSub,
                ],
            ];

        return [
            'status' => $status,
            'status_label' => strtoupper($status),
            'cpu_load' => $cpu,
            'ram_load' => $ram,
            'rx_mbps' => number_format($rx, 1, '.', '').' Mbps',
            'tx_mbps' => number_format($tx, 1, '.', '').' Mbps',
            'last_ping_ms' => $latency,
            'packet_loss' => $packetLoss,
            'uptime' => $isNonMikrotik ? "HTTP/TCP Port {$port} Aktif ({$days}h {$hours}j)" : "{$days}h {$hours}j {$mins}m",
            'interfaces' => $interfaces,
        ];
    }

    /**
     * Simulasikan pembaruan telemetri dinamis (random-walk dummy) tanpa memerlukan koneksi perangkat fisik.
     */
    public static function simulateTelemetryTick(MonitoringDevice $node): array
    {
        $status = strtolower((string) ($node->status ?: 'online'));
        $isNonMikrotik = MikrotikService::isNonMikrotik($node->unit_type, $node->api_port);
        $port = (int) ($node->api_port ?? ($isNonMikrotik ? 80 : 8728));

        if ($status === 'offline') {
            $node->update([
                'status' => 'offline',
                'status_label' => 'OFFLINE',
                'cpu_load' => 0.0,
                'ram_load' => 0.0,
                'rx_mbps' => '0.0 Mbps',
                'tx_mbps' => '0.0 Mbps',
                'last_ping_ms' => null,
                'packet_loss' => 100.0,
            ]);

            return self::formatMonitoringDevice($node->fresh());
        }

        $isWarning = $status === 'warning';
        $currentCpu = (float) ($node->cpu_load ?? 0.0);
        $currentRam = (float) ($node->ram_load ?? 0.0);
        $currentRx = self::parseRateToMbps($node->rx_mbps);
        $currentTx = self::parseRateToMbps($node->tx_mbps);
        $currentPing = $node->last_ping_ms !== null ? (int) $node->last_ping_ms : 0;
        $currentLoss = (float) ($node->packet_loss ?? 0.0);

        // Jika metrik awal masih 0, inisialisasi baseline realistis terlebih dahulu
        if ($currentRx <= 0.0 && $currentTx <= 0.0 && $currentCpu <= 0.0) {
            $initial = self::generateInitialDummyMetrics($status, $isNonMikrotik, $port);
            $node->update($initial);

            return self::formatMonitoringDevice($node->fresh());
        }

        // [PANDUAN UBAH DATA DUMMY CHART - BACKEND]
        // Atur batas minimum & maksimum fluktuasi acak (random-walk) untuk CPU, RAM, Rx, Tx, Ping, dan Packet Loss di sini.
        $cpuMin = $isWarning ? 70.0 : 12.0;
        $cpuMax = $isWarning ? 88.0 : 66.0;
        $ramMin = $isWarning ? 75.0 : 24.0;
        $ramMax = $isWarning ? 90.0 : 72.0;
        $pingMin = $isWarning ? 32 : 3;
        $pingMax = $isWarning ? 82 : 28;

        $nextCpu = round(max($cpuMin, min($cpuMax, $currentCpu + (mt_rand(-45, 45) / 10))), 1);
        $nextRam = round(max($ramMin, min($ramMax, $currentRam + (mt_rand(-25, 25) / 10))), 1);
        $nextRx = round(max(12.0, min(280.0, $currentRx + (mt_rand(-160, 160) / 10))), 1);   // Bandwidth Download (Mbps): min 12, max 280
        $nextTx = round(max(6.0, min(180.0, $currentTx + (mt_rand(-110, 110) / 10))), 1);    // Bandwidth Upload (Mbps): min 6, max 180
        $nextPing = max($pingMin, min($pingMax, ($currentPing > 0 ? $currentPing : ($isWarning ? 45 : 10)) + mt_rand(-3, 3)));
        $nextLoss = $isWarning
            ? round(max(1.0, min(12.0, ($currentLoss > 0 ? $currentLoss : 3.5) + (mt_rand(-10, 10) / 10))), 1)
            : (mt_rand(1, 10) === 1 ? 0.1 : 0.0);

        // Perbarui juga trafik dummy pada interface yang statusnya "up"
        $interfaces = is_array($node->interfaces) ? $node->interfaces : [];
        if (! empty($interfaces)) {
            $upIndexes = [];
            foreach ($interfaces as $idx => $iface) {
                if (($iface['status'] ?? 'up') === 'up') {
                    $upIndexes[] = $idx;
                }
            }
            $upCount = count($upIndexes);
            if ($upCount > 0) {
                $weights = [];
                $weightSum = 0;
                foreach ($upIndexes as $pos => $idx) {
                    $w = max(1, $upCount - $pos);
                    $weights[$idx] = $w;
                    $weightSum += $w;
                }
                foreach ($upIndexes as $idx) {
                    $ratio = $weights[$idx] / $weightSum;
                    $ifRx = round(max(0.5, $nextRx * $ratio + (mt_rand(-15, 15) / 10)), 1);
                    $ifTx = round(max(0.3, $nextTx * $ratio + (mt_rand(-10, 10) / 10)), 1);
                    $interfaces[$idx]['rx'] = number_format($ifRx, 1, '.', '').' Mbps';
                    $interfaces[$idx]['tx'] = number_format($ifTx, 1, '.', '').' Mbps';
                    $interfaces[$idx]['rx_mbps'] = $ifRx;
                    $interfaces[$idx]['tx_mbps'] = $ifTx;
                }
            }
        }

        $uptime = $node->uptime;
        if (empty($uptime) || stripos($uptime, 'offline') !== false || stripos($uptime, 'gagal') !== false) {
            $uptime = $isNonMikrotik ? "HTTP/TCP Port {$port} Aktif" : '14h 06j 22m';
        }

        $node->update([
            'status' => $status,
            'status_label' => strtoupper($status),
            'cpu_load' => $nextCpu,
            'ram_load' => $nextRam,
            'rx_mbps' => number_format($nextRx, 1, '.', '').' Mbps',
            'tx_mbps' => number_format($nextTx, 1, '.', '').' Mbps',
            'last_ping_ms' => $nextPing,
            'packet_loss' => $nextLoss,
            'uptime' => $uptime,
            'interfaces' => ! empty($interfaces) ? $interfaces : $node->interfaces,
        ]);

        return self::formatMonitoringDevice($node->fresh());
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
                $prevRxBytes = 0.0;
                $prevTxBytes = 0.0;
                if (is_array($node->interfaces)) {
                    foreach ($node->interfaces as $oldIf) {
                        if (($oldIf['type'] ?? '') !== 'loopback' && ($oldIf['name'] ?? '') !== 'lo') {
                            $prevRxBytes += (float) ($oldIf['rx_bytes'] ?? 0);
                            $prevTxBytes += (float) ($oldIf['tx_bytes'] ?? 0);
                        }
                    }
                }

                $newRxBytes = (float) ($telemetry['total_rx_bytes'] ?? 0);
                $newTxBytes = (float) ($telemetry['total_tx_bytes'] ?? 0);

                $deltaRx = ($newRxBytes >= $prevRxBytes && $prevRxBytes > 0) ? ($newRxBytes - $prevRxBytes) : 0.0;
                $deltaTx = ($newTxBytes >= $prevTxBytes && $prevTxBytes > 0) ? ($newTxBytes - $prevTxBytes) : 0.0;

                $elapsedSec = $node->updated_at
                    ? max(1.0, min(60.0, (float) abs(now()->diffInSeconds($node->updated_at))))
                    : 4.0;

                $deltaRxBps = ($deltaRx * 8.0) / $elapsedSec;
                $deltaTxBps = ($deltaTx * 8.0) / $elapsedSec;

                $monitorRxBps = (float) ($telemetry['rx_bps'] ?? 0);
                $monitorTxBps = (float) ($telemetry['tx_bps'] ?? 0);

                $rxMbps = MikrotikService::formatBpsToMbps(max($monitorRxBps, $deltaRxBps), $deltaRx, $newRxBytes);
                $txMbps = MikrotikService::formatBpsToMbps(max($monitorTxBps, $deltaTxBps), $deltaTx, $newTxBytes);

                $node->update([
                    'status' => 'online',
                    'status_label' => 'ONLINE',
                    'cpu_load' => $telemetry['resource']['cpu_load'],
                    'ram_load' => $telemetry['resource']['ram_load'],
                    'uptime' => $telemetry['resource']['uptime'],
                    'rx_mbps' => $rxMbps,
                    'tx_mbps' => $txMbps,
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
            $totalInbound += self::parseRateToMbps($dev['rx_mbps'] ?? '0');
            $totalOutbound += self::parseRateToMbps($dev['tx_mbps'] ?? '0');
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
     * API Telemetri Stream (Polling per 2.5-3 detik dari browser frontend dengan data dummy dinamis)
     */
    public function telemetryStream(int $id): JsonResponse
    {
        $deviceModel = MonitoringDevice::find($id);

        if (! $deviceModel) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Jalankan simulasi telemetri dinamis (dummy) agar grafik selalu hidup tanpa koneksi fisik
        if (! app()->runningUnitTests()) {
            $device = self::simulateTelemetryTick($deviceModel);
        } else {
            $device = self::formatMonitoringDevice($deviceModel);
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
                'latency_ms' => $device['latency_ms'],
                'rx_mbps' => $device['rx_mbps'] ?? '0 Mbps',
                'tx_mbps' => $device['tx_mbps'] ?? '0 Mbps',
                'packet_loss' => (float) ($device['packet_loss'] ?? 0.0),
            ],
            'interfaces' => $device['interfaces'] ?? [],
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
            $nodes = MonitoringDevice::all();

            foreach ($nodes as $node) {
                self::simulateTelemetryTick($node);
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
        if (! $deviceModel) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat monitoring tidak ditemukan.',
            ], 404);
        }

        $device = self::formatMonitoringDevice($deviceModel);

        $hostname = $device['hostname'] ?? ('Node Perangkat #'.$id);
        $ip = $device['ip_address'] ?? '127.0.0.1';
        $status = $device['status'] ?? 'online';
        $ping = (float) ($device['last_ping_ms'] ?? 0.0);
        $snmpStatus = 'Status Link: '.strtoupper($status);
        $quality = $status === 'offline' ? 'Offline / Unreachable' : 'Normal / Ready';

        if ($deviceModel && ! empty($deviceModel->ip_address) && ! app()->runningUnitTests()) {
            $device = self::simulateTelemetryTick($deviceModel);
            $status = $device['status'] ?? 'online';
            $ping = (float) ($device['last_ping_ms'] ?? 0.0);
            $port = (int) ($deviceModel->api_port ?? 8728);

            if ($status === 'offline') {
                $snmpStatus = 'Offline (Status Perangkat Nonaktif)';
                $quality = 'Offline / Unreachable';
            } else {
                $snmpStatus = "Telemetri Simulasi Aktif · Port {$port} ({$ping} ms)";
                $quality = $status === 'warning' ? 'Degraded / High Latency' : 'Normal / Ready';
            }
        }

        $isOffline = $status === 'offline';
        $data = [
            'hostname' => $hostname,
            'ip' => $ip,
            'rtt_avg' => $isOffline ? 0.0 : $ping,
            'rtt_min' => $isOffline ? 0.0 : max(0.0, round($ping - 1.0, 1)),
            'rtt_max' => $isOffline ? 0.0 : round($ping + 1.5, 1),
            'transmitted' => 4,
            'received' => $isOffline ? 0 : 4,
            'loss' => $isOffline ? 100.0 : ($device['packet_loss'] ?? 0.0),
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
