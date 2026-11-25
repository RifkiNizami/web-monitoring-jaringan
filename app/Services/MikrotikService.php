<?php

namespace App\Services;

/**
 * Class MikrotikService
 *
 * Service komunikasi langsung dengan router MikroTik RouterOS (v6 & v7)
 * melalui RouterOS API binary protocol (TCP Port 8728 / SSL 8729).
 */
class MikrotikService
{
    /**
     * Resource socket connection.
     *
     * @var resource|null
     */
    protected $socket = null;

    /**
     * Status koneksi saat ini.
     */
    protected bool $connected = false;

    /**
     * Pesan error terakhir.
     */
    protected ?string $lastError = null;

    /**
     * Latensi handshake saat koneksi (dalam milidetik).
     */
    protected float $latencyMs = 0.0;

    /**
     * Buka koneksi socket dan login ke RouterOS.
     */
    public function connect(string $ip, string $username, string $password = '', int $port = 8728, int $timeout = 2): bool
    {
        $this->disconnect();
        $this->lastError = null;

        $startTime = microtime(true);
        $errno = 0;
        $errstr = '';

        // Hubungi TCP port 8728 dengan batas timeout ketat
        $context = stream_context_create([
            'socket' => [
                'connect_timeout' => $timeout,
            ],
        ]);

        set_error_handler(static fn () => true);
        try {
            $socket = stream_socket_client(
                "tcp://{$ip}:{$port}",
                $errno,
                $errstr,
                (float) $timeout,
                STREAM_CLIENT_CONNECT,
                $context
            );
        } finally {
            restore_error_handler();
        }

        if (! $socket) {
            $this->lastError = "Gagal terhubung ke {$ip}:{$port}. ".($errstr ? "({$errstr})" : 'Koneksi timeout / port tertutup.');

            return false;
        }

        $this->socket = $socket;
        stream_set_timeout($this->socket, $timeout);
        $this->latencyMs = round((microtime(true) - $startTime) * 1000, 1);

        // Lakukan autentikasi /login
        if (! $this->login($username, $password)) {
            $this->disconnect();

            return false;
        }

        $this->connected = true;

        return true;
    }

    /**
     * Tutup koneksi socket.
     */
    public function disconnect(): void
    {
        if (is_resource($this->socket)) {
            @fclose($this->socket);
        }
        $this->socket = null;
        $this->connected = false;
    }

    /**
     * Dapatkan pesan error terakhir.
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Dapatkan latensi koneksi (ms).
     */
    public function getLatencyMs(): float
    {
        return $this->latencyMs;
    }

    /**
     * Proses autentikasi RouterOS (Mendukung RouterOS v6.43+ / v7 modern dan legacy MD5).
     */
    protected function login(string $username, string $password): bool
    {
        // 1. Coba login metode RouterOS modern (v6.43+ & v7.x)
        $this->writeSentence([
            '/login',
            '=name='.$username,
            '=password='.$password,
        ]);

        $response = $this->readSentence();

        if (empty($response)) {
            $this->lastError = 'Tidak ada respon dari router saat proses autentikasi.';

            return false;
        }

        // Jika berhasil login langsung (!done)
        if (isset($response['!done']) && ! isset($response['!trap'])) {
            // Cek apakah router meminta challenge legacy pre-v6.43 (=ret=xxx)
            if (isset($response['=ret'])) {
                $challenge = $response['=ret'];
                $hashed = '00'.md5(chr(0).$password.hex2bin($challenge));

                $this->writeSentence([
                    '/login',
                    '=name='.$username,
                    '=response='.$hashed,
                ]);

                $response2 = $this->readSentence();
                if (isset($response2['!done']) && ! isset($response2['!trap'])) {
                    return true;
                }

                $this->lastError = $response2['=message'] ?? 'Login legacy gagal ditolak.';

                return false;
            }

            return true;
        }

        // Tangkap pesan kesalahan autentikasi dari RouterOS
        if (isset($response['!trap'])) {
            $msg = $response['=message'] ?? 'Username atau password API salah.';
            $this->lastError = "Autentikasi gagal: {$msg}";

            return false;
        }

        $this->lastError = 'Respon autentikasi router tidak dikenali.';

        return false;
    }

    /**
     * Kirim perintah ke RouterOS API dan ambil seluruh responnya.
     */
    public function comm(string $command, array $params = []): array
    {
        if (! $this->connected || ! is_resource($this->socket)) {
            $this->lastError = 'Socket belum terkoneksi.';

            return [];
        }

        $sentence = [$command];
        foreach ($params as $key => $value) {
            $sentence[] = '='.$key.'='.$value;
        }

        $this->writeSentence($sentence);

        $results = [];
        while (true) {
            $response = $this->readSentence();
            if (empty($response)) {
                break;
            }

            if (isset($response['!re'])) {
                $item = [];
                foreach ($response as $k => $v) {
                    if (str_starts_with($k, '=')) {
                        $item[substr($k, 1)] = $v;
                    }
                }
                $results[] = $item;
            }

            if (isset($response['!done'])) {
                if (isset($response['=ret'])) {
                    $results['ret'] = $response['=ret'];
                }
                break;
            }

            if (isset($response['!trap']) || isset($response['!fatal'])) {
                $this->lastError = $response['=message'] ?? 'Eksekusi perintah API gagal.';
                break;
            }
        }

        return $results;
    }

    /**
     * Tulis sebuah kata (word) dengan encoding panjang ala MikroTik.
     */
    protected function writeWord(string $word): void
    {
        $length = strlen($word);

        if ($length < 0x80) {
            fwrite($this->socket, chr($length));
        } elseif ($length < 0x4000) {
            $length |= 0x8000;
            fwrite($this->socket, chr(($length >> 8) & 0xFF).chr($length & 0xFF));
        } elseif ($length < 0x200000) {
            $length |= 0xC00000;
            fwrite($this->socket, chr(($length >> 16) & 0xFF).chr(($length >> 8) & 0xFF).chr($length & 0xFF));
        } elseif ($length < 0x10000000) {
            $length |= 0xE0000000;
            fwrite($this->socket, chr(($length >> 24) & 0xFF).chr(($length >> 16) & 0xFF).chr(($length >> 8) & 0xFF).chr($length & 0xFF));
        } else {
            fwrite($this->socket, chr(0xF0).chr(($length >> 24) & 0xFF).chr(($length >> 16) & 0xFF).chr(($length >> 8) & 0xFF).chr($length & 0xFF));
        }

        fwrite($this->socket, $word);
    }

    /**
     * Tulis kalimat (sentence) diakhiri byte 0x00.
     */
    protected function writeSentence(array $words): void
    {
        foreach ($words as $word) {
            $this->writeWord($word);
        }
        fwrite($this->socket, chr(0));
    }

    /**
     * Baca satu kata (word) dari stream socket MikroTik.
     */
    protected function readWord(): string
    {
        if (! $this->socket || feof($this->socket)) {
            return '';
        }

        $byte = ord(fread($this->socket, 1));
        $length = 0;

        if ($byte & 0x80) {
            if (($byte & 0xC0) === 0x80) {
                $length = (($byte & 0x3F) << 8) + ord(fread($this->socket, 1));
            } elseif (($byte & 0xE0) === 0xC0) {
                $length = (($byte & 0x1F) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
            } elseif (($byte & 0xF0) === 0xE0) {
                $length = (($byte & 0x0F) << 24) + (ord(fread($this->socket, 1)) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
            } elseif (($byte & 0xF8) === 0xF0) {
                $length = (ord(fread($this->socket, 1)) << 24) + (ord(fread($this->socket, 1)) << 16) + (ord(fread($this->socket, 1)) << 8) + ord(fread($this->socket, 1));
            }
        } else {
            $length = $byte;
        }

        if ($length === 0) {
            return '';
        }

        $word = '';
        while (strlen($word) < $length) {
            $toRead = $length - strlen($word);
            $chunk = fread($this->socket, $toRead);
            if ($chunk === false || strlen($chunk) === 0) {
                break;
            }
            $word .= $chunk;
        }

        return $word;
    }

    /**
     * Baca satu kalimat (sentence) dari router.
     */
    protected function readSentence(): array
    {
        $sentence = [];
        while (true) {
            $word = $this->readWord();
            if ($word === '') {
                break;
            }

            if (str_starts_with($word, '!')) {
                $sentence[$word] = true;
            } elseif (str_starts_with($word, '=')) {
                $parts = explode('=', substr($word, 1), 2);
                $key = '='.$parts[0];
                $val = $parts[1] ?? '';
                $sentence[$key] = $val;
            } else {
                $sentence[$word] = true;
            }
        }

        return $sentence;
    }

    /**
     * Periksa apakah perangkat termasuk kategori Non-MikroTik (Access Point / Generic Router / HTTP Port selain 8728 & 8729).
     */
    public static function isNonMikrotik(?string $unitType = null, ?int $port = null, ?string $connectionType = null): bool
    {
        if ($port !== null && $port > 0 && ! in_array($port, [8728, 8729], true)) {
            return true;
        }

        if ($connectionType !== null && strtolower(trim($connectionType)) === 'http') {
            return true;
        }

        if ($unitType !== null) {
            $normalized = strtolower(trim($unitType));
            if (in_array($normalized, ['access point', 'generic router', 'generic router (http/ping)', 'router non-mikrotik'], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Pastikan port untuk perangkat Non-MikroTik menggunakan port HTTP/TCP yang valid (default 80 jika masih 8728/8729).
     */
    public static function resolveNonMikrotikPort(?int $port = null): int
    {
        if ($port === null || $port <= 0 || in_array($port, [8728, 8729], true)) {
            return 80;
        }

        return $port;
    }

    /**
     * Kondisi B: Pengecekan koneksi socket TCP HTTP/Ping untuk perangkat Non-MikroTik (misal TOTOLINK N300RT / Access Point).
     */
    public function checkHttpSocket(string $ip, int $port = 80, int $timeout = 2): array
    {
        $this->disconnect();
        $this->lastError = null;
        $port = self::resolveNonMikrotikPort($port);

        $startTime = microtime(true);
        $errno = 0;
        $errstr = '';

        $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);

        if (! $socket) {
            $this->lastError = "Gagal terhubung ke {$ip}:{$port}. ".($errstr ? "({$errstr})" : 'Koneksi timeout / port tertutup.');
            $this->latencyMs = 0.0;

            return [
                'online' => false,
                'status' => 'offline',
                'latency_ms' => null,
                'cpu_load' => 0.0,
                'ram_load' => 0.0,
                'rx_mbps' => '0.0 Mbps',
                'tx_mbps' => '0.0 Mbps',
                'uptime' => 'Offline (Timeout)',
                'resource' => null,
                'interfaces' => [],
            ];
        }

        $elapsedMs = round((microtime(true) - $startTime) * 1000, 1);
        @fclose($socket);
        $this->latencyMs = max(1.0, $elapsedMs);
        $latencyInt = (int) round($this->latencyMs);

        return [
            'online' => true,
            'status' => 'online',
            'latency_ms' => $latencyInt,
            'cpu_load' => 0.0,
            'ram_load' => 0.0,
            'rx_mbps' => '0.0 Mbps',
            'tx_mbps' => '0.0 Mbps',
            'uptime' => "HTTP/TCP Port {$port} Aktif",
            'resource' => [
                'cpu_load' => 0.0,
                'ram_load' => 0.0,
                'free_memory' => 0,
                'total_memory' => 0,
                'free_memory_human' => '0 B',
                'total_memory_human' => '0 B',
                'uptime' => "HTTP/TCP Port {$port} Aktif",
                'version' => 'HTTP/Ping',
                'board_name' => 'Generic / Access Point',
                'cpu_count' => 1,
                'cpu_frequency' => '-',
            ],
            'interfaces' => [
                [
                    'name' => 'eth0',
                    'type' => 'ether',
                    'status' => 'up',
                    'speed' => '100 Mbps',
                    'rx' => '0 B total',
                    'tx' => '0 B total',
                    'rx_bytes' => 0,
                    'tx_bytes' => 0,
                ],
            ],
        ];
    }

    /**
     * Tes koneksi ke perangkat (Kondisi A: MikroTik RouterOS API, Kondisi B: Non-MikroTik HTTP/TCP Socket).
     */
    public function testConnection(string $ip, ?string $username = '', string $password = '', int $port = 8728, int $timeout = 2, ?string $unitType = null): array
    {
        // Kondisi B: Non-MikroTik / TOTOLINK HTTP & Ping (Port 80/8080 atau Access Point / Generic Router)
        if (self::isNonMikrotik($unitType, $port)) {
            $targetPort = $port ?: 80;
            $check = $this->checkHttpSocket($ip, $targetPort, $timeout);

            if (! $check['online']) {
                return [
                    'success' => false,
                    'message' => $this->getLastError() ?: "Gagal terhubung ke perangkat pada port {$targetPort}.",
                    'latency_ms' => null,
                    'resource' => null,
                ];
            }

            return [
                'success' => true,
                'message' => "Terhubung via HTTP/TCP Port {$targetPort} ({$check['latency_ms']} ms)",
                'latency_ms' => $check['latency_ms'],
                'resource' => $check['resource'],
            ];
        }

        // Kondisi A: MikroTik RouterOS API (Port 8728 / 8729)
        $connected = $this->connect($ip, (string) $username, $password, $port, $timeout);

        if (! $connected) {
            return [
                'success' => false,
                'message' => $this->getLastError() ?: 'Gagal terhubung ke router MikroTik.',
                'latency_ms' => null,
                'resource' => null,
            ];
        }

        $resource = $this->fetchSystemResource();
        $this->disconnect();

        return [
            'success' => true,
            'message' => 'Terhubung ke '.($resource['board_name'] ?? 'MikroTik RouterOS')." (RouterOS v{$resource['version']})",
            'latency_ms' => $this->latencyMs,
            'resource' => $resource,
        ];
    }

    /**
     * Query data CPU, RAM, Uptime, dan Model hardware (/system/resource/print).
     */
    public function fetchSystemResource(): array
    {
        $raw = $this->comm('/system/resource/print');
        $item = $raw[0] ?? [];

        $totalMem = (int) ($item['total-memory'] ?? 0);
        $freeMem = (int) ($item['free-memory'] ?? 0);
        $usedMem = max(0, $totalMem - $freeMem);
        $ramPercent = $totalMem > 0 ? round(($usedMem / $totalMem) * 100, 1) : 0.0;

        return [
            'cpu_load' => (float) ($item['cpu-load'] ?? 0),
            'ram_load' => $ramPercent,
            'free_memory' => $freeMem,
            'total_memory' => $totalMem,
            'free_memory_human' => $this->formatBytes($freeMem),
            'total_memory_human' => $this->formatBytes($totalMem),
            'uptime' => $item['uptime'] ?? '0j 0m',
            'version' => $item['version'] ?? 'RouterOS',
            'board_name' => $item['board-name'] ?? ($item['platform'] ?? 'MikroTik'),
            'cpu_count' => (int) ($item['cpu-count'] ?? 1),
            'cpu_frequency' => ($item['cpu-frequency'] ?? '').' MHz',
        ];
    }

    /**
     * Query daftar antarmuka ethernet, status link (/interface/print), dan trafik aktual (/interface/monitor-traffic).
     */
    public function fetchInterfaces(): array
    {
        $raw = $this->comm('/interface/print');
        $interfaces = [];

        foreach ($raw as $row) {
            if (! is_array($row) || empty($row['name'])) {
                continue;
            }

            $ifName = $row['name'];
            $ifType = $row['type'] ?? 'ether';
            $isRunning = ($row['running'] ?? 'false') === 'true';
            $rxBytes = (float) ($row['rx-byte'] ?? 0);
            $txBytes = (float) ($row['tx-byte'] ?? 0);
            $rxPackets = (float) ($row['rx-packet'] ?? 0);
            $txPackets = (float) ($row['tx-packet'] ?? 0);

            $rxBps = 0.0;
            $txBps = 0.0;

            if ($isRunning && $ifType !== 'loopback' && $ifName !== 'lo') {
                $mon = $this->comm('/interface/monitor-traffic', [
                    'interface' => $ifName,
                    'once' => '',
                ]);
                if (! empty($mon[0]) && is_array($mon[0])) {
                    $rxBps = (float) ($mon[0]['rx-bits-per-second'] ?? 0);
                    $txBps = (float) ($mon[0]['tx-bits-per-second'] ?? 0);
                }
            }

            $interfaces[] = [
                'name' => $ifName,
                'type' => $ifType,
                'status' => $isRunning ? 'up' : 'down',
                'speed' => $row['actual-mtu'] ?? '1500',
                'mac_address' => $row['mac-address'] ?? '-',
                'rx' => $this->formatBytes($rxBytes).' total',
                'tx' => $this->formatBytes($txBytes).' total',
                'rx_bytes' => $rxBytes,
                'tx_bytes' => $txBytes,
                'rx_packets' => $rxPackets,
                'tx_packets' => $txPackets,
                'rx_bps' => $rxBps,
                'tx_bps' => $txBps,
            ];
        }

        return $interfaces;
    }

    /**
     * Konversi bits-per-second menjadi string throughput akurat (Mbps / Kbps) tanpa inflasi.
     */
    public static function formatBpsToMbps(float $bps, float $deltaBytes = 0.0, float $totalBytes = 0.0): string
    {
        if ($bps <= 0) {
            return '0.0 Mbps';
        }

        $mbps = $bps / 1000000.0;

        if ($mbps >= 0.01) {
            return number_format($mbps, 2, '.', '').' Mbps';
        }

        $kbps = $bps / 1000.0;

        return number_format($kbps, 1, '.', '').' Kbps';
    }

    /**
     * Ambil data lengkap perangkat (Kondisi A: MikroTik RouterOS API, Kondisi B: Non-MikroTik HTTP/TCP Socket).
     */
    public function getDeviceTelemetry(string $ip, ?string $username = '', string $password = '', int $port = 8728, int $timeout = 2, ?string $unitType = null): ?array
    {
        // Kondisi B: Non-MikroTik / TOTOLINK HTTP & Ping (Port 80/8080 atau Access Point / Generic Router)
        if (self::isNonMikrotik($unitType, $port)) {
            $targetPort = $port ?: 80;
            $check = $this->checkHttpSocket($ip, $targetPort, $timeout);

            if (! $check['online']) {
                return null;
            }

            return [
                'online' => true,
                'status' => 'online',
                'resource' => $check['resource'],
                'interfaces' => $check['interfaces'],
                'latency_ms' => $check['latency_ms'],
                'cpu_load' => 0.0,
                'ram_load' => 0.0,
                'rx_mbps' => '0.0 Mbps',
                'tx_mbps' => '0.0 Mbps',
                'uptime' => $check['uptime'],
            ];
        }

        // Kondisi A: MikroTik RouterOS API (Port 8728 / 8729)
        if (empty($username) || ! $this->connect($ip, $username, $password, $port, $timeout)) {
            return null;
        }

        $resource = $this->fetchSystemResource();
        $interfaces = $this->fetchInterfaces();
        $latency = $this->latencyMs;

        $this->disconnect();

        $totalRxBps = 0.0;
        $totalTxBps = 0.0;
        $totalRxBytes = 0.0;
        $totalTxBytes = 0.0;
        foreach ($interfaces as $iface) {
            if (($iface['type'] ?? '') !== 'loopback' && ($iface['name'] ?? '') !== 'lo') {
                $totalRxBps += (float) ($iface['rx_bps'] ?? 0);
                $totalTxBps += (float) ($iface['tx_bps'] ?? 0);
                $totalRxBytes += (float) ($iface['rx_bytes'] ?? 0);
                $totalTxBytes += (float) ($iface['tx_bytes'] ?? 0);
            }
        }

        return [
            'online' => true,
            'status' => 'online',
            'resource' => $resource,
            'interfaces' => $interfaces,
            'latency_ms' => $latency,
            'cpu_load' => $resource['cpu_load'],
            'ram_load' => $resource['ram_load'],
            'rx_bps' => $totalRxBps,
            'tx_bps' => $totalTxBps,
            'total_rx_bytes' => $totalRxBytes,
            'total_tx_bytes' => $totalTxBytes,
            'rx_mbps' => self::formatBpsToMbps($totalRxBps, 0.0, $totalRxBytes),
            'tx_mbps' => self::formatBpsToMbps($totalTxBps, 0.0, $totalTxBytes),
            'uptime' => $resource['uptime'],
        ];
    }

    /**
     * Format byte ke string human-readable (KB, MB, GB).
     */
    protected function formatBytes(float|int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 1).' GB';
        }
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1).' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
