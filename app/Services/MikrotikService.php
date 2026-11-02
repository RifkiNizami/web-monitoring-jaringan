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

        set_error_handler(static fn() => true);
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

        if (!$socket) {
            $this->lastError = "Gagal terhubung ke {$ip}:{$port}. " . ($errstr ? "({$errstr})" : 'Koneksi timeout / port tertutup.');
            return false;
        }

        $this->socket = $socket;
        stream_set_timeout($this->socket, $timeout);
        $this->latencyMs = round((microtime(true) - $startTime) * 1000, 1);

        // Lakukan autentikasi /login
        if (!$this->login($username, $password)) {
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
            '=name=' . $username,
            '=password=' . $password,
        ]);

        $response = $this->readSentence();

        if (empty($response)) {
            $this->lastError = 'Tidak ada respon dari router saat proses autentikasi.';
            return false;
        }

        // Jika berhasil login langsung (!done)
        if (isset($response['!done']) && !isset($response['!trap'])) {
            // Cek apakah router meminta challenge legacy pre-v6.43 (=ret=xxx)
            if (isset($response['=ret'])) {
                $challenge = $response['=ret'];
                $hashed = '00' . md5(chr(0) . $password . hex2bin($challenge));

                $this->writeSentence([
                    '/login',
                    '=name=' . $username,
                    '=response=' . $hashed,
                ]);

                $response2 = $this->readSentence();
                if (isset($response2['!done']) && !isset($response2['!trap'])) {
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
        if (!$this->connected || !is_resource($this->socket)) {
            $this->lastError = 'Socket belum terkoneksi.';
            return [];
        }

        $sentence = [$command];
        foreach ($params as $key => $value) {
            $sentence[] = '=' . $key . '=' . $value;
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
            fwrite($this->socket, chr(($length >> 8) & 0xFF) . chr($length & 0xFF));
        } elseif ($length < 0x200000) {
            $length |= 0xC00000;
            fwrite($this->socket, chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF));
        } elseif ($length < 0x10000000) {
            $length |= 0xE0000000;
            fwrite($this->socket, chr(($length >> 24) & 0xFF) . chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF));
        } else {
            fwrite($this->socket, chr(0xF0) . chr(($length >> 24) & 0xFF) . chr(($length >> 16) & 0xFF) . chr(($length >> 8) & 0xFF) . chr($length & 0xFF));
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
        if (!$this->socket || feof($this->socket)) {
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
                $key = '=' . $parts[0];
                $val = $parts[1] ?? '';
                $sentence[$key] = $val;
            } else {
                $sentence[$word] = true;
            }
        }

        return $sentence;
    }

    /**
     * Tes koneksi ke router dan kembalikan metrik hardware jika berhasil.
     */
    public function testConnection(string $ip, string $username, string $password = '', int $port = 8728, int $timeout = 2): array
    {
        $connected = $this->connect($ip, $username, $password, $port, $timeout);

        if (!$connected) {
            return [
                'success' => false,
                'message' => $this->getLastError() ?: 'Gagal terhubung ke router MikroTik.',
                'latency_ms' => $this->latencyMs,
                'resource' => null,
            ];
        }

        $resource = $this->fetchSystemResource();
        $this->disconnect();

        return [
            'success' => true,
            'message' => 'Terhubung ke ' . ($resource['board_name'] ?? 'MikroTik RouterOS') . " (RouterOS v{$resource['version']})",
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
            'cpu_frequency' => ($item['cpu-frequency'] ?? '') . ' MHz',
        ];
    }

    /**
     * Query daftar antarmuka ethernet dan status link (/interface/print).
     */
    public function fetchInterfaces(): array
    {
        $raw = $this->comm('/interface/print');
        $interfaces = [];

        foreach ($raw as $row) {
            if (!is_array($row) || empty($row['name'])) {
                continue;
            }

            $isRunning = ($row['running'] ?? 'false') === 'true';
            $rxBytes = (float) ($row['rx-byte'] ?? 0);
            $txBytes = (float) ($row['tx-byte'] ?? 0);

            $interfaces[] = [
                'name' => $row['name'],
                'type' => $row['type'] ?? 'ether',
                'status' => $isRunning ? 'up' : 'down',
                'speed' => $row['actual-mtu'] ?? '1500',
                'rx' => $this->formatBytes($rxBytes) . ' total',
                'tx' => $this->formatBytes($txBytes) . ' total',
                'rx_bytes' => $rxBytes,
                'tx_bytes' => $txBytes,
            ];
        }

        return $interfaces;
    }

    /**
     * Ambil data lengkap perangkat (CPU, RAM, Uptime, Interfaces) dalam 1 koneksi.
     */
    public function getDeviceTelemetry(string $ip, string $username, string $password = '', int $port = 8728, int $timeout = 2): ?array
    {
        if (!$this->connect($ip, $username, $password, $port, $timeout)) {
            return null;
        }

        $resource = $this->fetchSystemResource();
        $interfaces = $this->fetchInterfaces();
        $latency = $this->latencyMs;

        $this->disconnect();

        return [
            'resource' => $resource,
            'interfaces' => $interfaces,
            'latency_ms' => $latency,
        ];
    }

    /**
     * Format byte ke string human-readable (KB, MB, GB).
     */
    protected function formatBytes(float|int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 1) . ' GB';
        }
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
