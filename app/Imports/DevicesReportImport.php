<?php

namespace App\Imports;

use App\Http\Controllers\Admin\MonitoringController;
use App\Models\Customer;
use App\Models\Device;
use App\Models\MonitoringDevice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DevicesReportImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;

    public int $updatedCount = 0;

    public array $errors = [];

    protected ?int $defaultCustomerId = null;

    public function __construct(?int $defaultCustomerId = null)
    {
        $this->defaultCustomerId = $defaultCustomerId;
    }

    // [PANDUAN UBAH IMPORT EXCEL - PERANGKAT]
    // - Nama key $row['...'] otomatis hasil snake_case dari judul header baris 1 Excel
    // - Urutan pencocokan data eksisting: 1) ID -> 2) Hostname -> 3) IP Address (jika cocok = update + sinkron MonitoringDevice, jika tidak = create baru)
    public function collection(Collection $rows): void
    {
        $allCustomers = Customer::all();

        foreach ($rows as $index => $row) {
            // Deteksi ID (jika ada di kolom ID / No / nomor yang diexport)
            $idVal = trim((string) ($row['id'] ?? ''));
            $rowNo = trim((string) ($row['no'] ?? ''));

            // Normalisasi key heading (lowercase & snake_case)
            $hostname = trim((string) ($row['hostname_perangkat'] ?? $row['hostname'] ?? $row['perangkat'] ?? ''));
            if (empty($hostname) || $hostname === '-') {
                continue;
            }

            $type = trim((string) ($row['tipe_perangkat'] ?? $row['tipe'] ?? 'Router'));
            $ipAddress = trim((string) ($row['ip_address'] ?? $row['ip'] ?? ''));
            $customerName = trim((string) ($row['mitra_customer'] ?? $row['pelanggan'] ?? $row['customer'] ?? ''));
            $location = trim((string) ($row['lokasi_pemasangan'] ?? $row['lokasi_rak'] ?? $row['lokasi'] ?? ''));
            $rackPosition = trim((string) ($row['posisi_rak_pole'] ?? $row['posisi_rak'] ?? ''));
            $statusRaw = trim((string) ($row['status_operasional'] ?? $row['status'] ?? 'Online'));
            $uptimePercent = trim((string) ($row['uptime_sla'] ?? $row['uptime_sla_percent'] ?? $row['uptime_percent'] ?? ''));
            $uptimeText = trim((string) ($row['durasi_uptime'] ?? $row['uptime'] ?? ''));
            $latencyRaw = trim((string) ($row['latensi_rtt_ms'] ?? $row['latensi'] ?? $row['rtt'] ?? ''));

            // Bersihkan string persen / ms
            $uptimePercentVal = is_numeric($uptimePercent) ? (float) $uptimePercent : (float) str_replace(['%', ' '], '', $uptimePercent);
            $latencyVal = is_numeric($latencyRaw) ? (int) $latencyRaw : (int) preg_replace('/[^0-9]/', '', $latencyRaw);

            // Mapping status & status_state
            $statusLower = strtolower($statusRaw);
            $statusState = match (true) {
                str_contains($statusLower, 'online') || str_contains($statusLower, 'tersedia') || str_contains($statusLower, 'normal') => 'online',
                str_contains($statusLower, 'warning') || str_contains($statusLower, 'perhatian') => 'warning',
                default => 'offline',
            };

            // Simpan status label yang sesuai dengan enum sistem
            $normalizedStatus = match ($statusState) {
                'online' => 'Online',
                'warning' => 'Warning',
                default => 'Offline',
            };

            // Cocokkan customer ID jika nama ada
            $customerId = null;
            if (! empty($customerName) && $customerName !== '-' && $customerName !== 'Semua Mitra') {
                $cleanTarget = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $customerName));
                $foundCust = $allCustomers->first(function ($c) use ($cleanTarget) {
                    $cName = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $c->name ?? ''));
                    $cCode = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $c->code ?? $c->customer_code ?? ''));
                    return (! empty($cName) && (str_contains($cleanTarget, $cName) || str_contains($cName, $cleanTarget)))
                        || (! empty($cCode) && str_contains($cleanTarget, $cCode));
                });
                if ($foundCust) {
                    $customerId = $foundCust->id;
                }
            }

            // Fallback ke filter customer aktif di halaman laporan jika kolom Mitra Customer kosong / tidak cocok
            if (! $customerId && $this->defaultCustomerId) {
                $customerId = $this->defaultCustomerId;
            }
            $resolvedCustomerLabel = ($customerId ? $allCustomers->firstWhere('id', $customerId)?->name : null)
                ?: ((! empty($customerName) && $customerName !== '-' && $customerName !== 'Semua Mitra') ? $customerName : 'Infrastruktur Backbone NetFly');

            // Lokasi rak gabungan jika ada posisi
            $finalLocation = $location !== '-' && ! empty($location) ? $location : null;
            if (! empty($rackPosition) && $rackPosition !== '-' && $rackPosition !== $finalLocation) {
                $finalLocation = $finalLocation ? "{$finalLocation} ({$rackPosition})" : $rackPosition;
            }

            // PENCARIAN DATA EKSISTING:
            // 1. Cari di tabel devices (inventaris/perangkat) berdasarkan Hostname, lalu ID jika cocok, lalu IP
            $device = Device::where('hostname', $hostname)->first();
            if (! $device && is_numeric($idVal) && (int) $idVal > 0) {
                $candidate = Device::find((int) $idVal);
                // Pastikan tidak menimpa record Device lain yang sebenarnya milik MonitoringDevice
                if ($candidate && ! MonitoringDevice::where('hostname', $hostname)->exists()) {
                    $device = $candidate;
                }
            }
            if (! $device && ! empty($ipAddress) && $ipAddress !== '-') {
                $device = Device::where('ip_address', $ipAddress)->first();
            }

            if ($device) {
                $oldHostname = $device->hostname;

                // UPDATE HOSTNAME jika diubah di Excel
                if (! empty($hostname) && $hostname !== '-') {
                    $device->hostname = $hostname;
                }
                if ($type && $type !== '-') {
                    $device->type = $type;
                }
                if ($ipAddress && $ipAddress !== '-') {
                    $device->ip_address = $ipAddress;
                }
                if ($customerId) {
                    $device->customer_id = $customerId;
                }
                if ($finalLocation) {
                    $device->rack_location = $finalLocation;
                }
                $device->status = $normalizedStatus;
                $device->status_state = $statusState;
                // Catatan: kolom telemetri (uptime, last_ping_ms, packet_loss) sudah dipindah ke monitoring_devices
                $device->save();
                $this->updatedCount++;

                // Sinkronkan juga ke MonitoringDevice agar selalu tampil di Laporan & Monitoring
                $monDevice = MonitoringDevice::where('hostname', $oldHostname)
                    ->orWhere('hostname', $hostname)
                    ->first();
                if ($monDevice) {
                    $monDevice->hostname = $hostname;
                    if ($type && $type !== '-') {
                        $monDevice->unit_type = $type;
                    }
                    if ($ipAddress && $ipAddress !== '-') {
                        $monDevice->ip_address = $ipAddress;
                    }
                    if ($customerId) {
                        $monDevice->customer_id = $customerId;
                        $monDevice->customer = $resolvedCustomerLabel;
                    }
                    if ($finalLocation) {
                        $monDevice->rack_location = $finalLocation;
                    }
                    $monDevice->status = $statusState;
                    $monDevice->status_label = strtoupper($statusState);
                    if ($uptimeText && $uptimeText !== '-') {
                        $monDevice->uptime = $uptimeText;
                    }
                    if ($latencyVal > 0) {
                        $monDevice->last_ping_ms = $latencyVal;
                    }
                    if ($uptimePercentVal > 0) {
                        $monDevice->packet_loss = max(0.0, 100.0 - $uptimePercentVal);
                    }
                    $monDevice->save();
                } elseif (! $device->customer_id) {
                    // Jika Device tidak punya customer_id, daftarkan juga ke monitoring_devices agar tampil di Laporan
                    $dummy = MonitoringController::generateInitialDummyMetrics($statusState);
                    MonitoringDevice::create([
                        'hostname' => $device->hostname,
                        'unit_type' => $device->type ?: 'Router',
                        'ip_address' => $device->ip_address ?: '10.0.0.1',
                        'customer_id' => $customerId,
                        'customer' => $resolvedCustomerLabel,
                        'project' => 'Dedicated SLA Monitoring',
                        'model' => ($device->type ?: 'Router').' Node',
                        'rack_location' => $device->rack_location ?: 'POP Distribusi',
                        'status' => $statusState,
                        'status_label' => strtoupper($statusState),
                        'uptime' => ($uptimeText && $uptimeText !== '-') ? $uptimeText : $dummy['uptime'],
                        'rx_mbps' => $dummy['rx_mbps'],
                        'tx_mbps' => $dummy['tx_mbps'],
                        'cpu_load' => $dummy['cpu_load'],
                        'ram_load' => $dummy['ram_load'],
                        'packet_loss' => $uptimePercentVal > 0 ? max(0.0, 100.0 - $uptimePercentVal) : $dummy['packet_loss'],
                        'last_ping_ms' => $latencyVal > 0 ? $latencyVal : $dummy['last_ping_ms'],
                        'connection_type' => 'lan',
                        'api_port' => 8728,
                        'api_username' => 'admin',
                        'interfaces' => $dummy['interfaces'],
                    ]);
                }
            } else {
                // Cek apakah baris ini merupakan node dari tabel monitoring_devices
                $monDevice = MonitoringDevice::where('hostname', $hostname)->first();
                if (! $monDevice && is_numeric($idVal) && (int) $idVal > 0) {
                    $monDevice = MonitoringDevice::find((int) $idVal);
                }
                if (! $monDevice && ! empty($ipAddress) && $ipAddress !== '-') {
                    $monDevice = MonitoringDevice::where('ip_address', $ipAddress)->first();
                }

                if ($monDevice) {
                    $monDevice->hostname = $hostname;
                    if ($type && $type !== '-') {
                        $monDevice->unit_type = $type;
                    }
                    if ($ipAddress && $ipAddress !== '-') {
                        $monDevice->ip_address = $ipAddress;
                    }
                    if ($customerId) {
                        $monDevice->customer_id = $customerId;
                        $monDevice->customer = $resolvedCustomerLabel;
                    }
                    if ($finalLocation) {
                        $monDevice->rack_location = $finalLocation;
                    }
                    $monDevice->status = $statusState;
                    $monDevice->status_label = strtoupper($statusState);
                    if ($uptimeText && $uptimeText !== '-') {
                        $monDevice->uptime = $uptimeText;
                    }
                    if ($latencyVal > 0) {
                        $monDevice->last_ping_ms = $latencyVal;
                    }
                    if ($uptimePercentVal > 0) {
                        $monDevice->packet_loss = max(0.0, 100.0 - $uptimePercentVal);
                    }
                    $monDevice->save();
                    $this->updatedCount++;
                } else {
                    $resolvedIp = ($ipAddress && $ipAddress !== '-') ? $ipAddress : '10.0.0.1';
                    $resolvedLocation = $finalLocation ?: 'Rak Server Utama';
                    $resolvedType = $type ?: 'Router';

                    // Buat Device baru di inventaris
                    Device::create([
                        'hostname' => $hostname,
                        'type' => $resolvedType,
                        'ip_address' => $resolvedIp,
                        'customer_id' => $customerId,
                        'rack_location' => $resolvedLocation,
                        'status' => $normalizedStatus,
                        'status_state' => $statusState,
                    ]);

                    // Daftarkan juga ke monitoring_devices agar langsung muncul di Laporan (/reports) dan Monitoring
                    $dummy = MonitoringController::generateInitialDummyMetrics($statusState);
                    MonitoringDevice::create([
                        'hostname' => $hostname,
                        'unit_type' => $resolvedType,
                        'ip_address' => $resolvedIp,
                        'customer_id' => $customerId,
                        'customer' => $resolvedCustomerLabel,
                        'project' => 'Dedicated SLA Monitoring',
                        'model' => $resolvedType.' Node',
                        'rack_location' => $resolvedLocation,
                        'status' => $statusState,
                        'status_label' => strtoupper($statusState),
                        'uptime' => ($uptimeText && $uptimeText !== '-') ? $uptimeText : $dummy['uptime'],
                        'rx_mbps' => $dummy['rx_mbps'],
                        'tx_mbps' => $dummy['tx_mbps'],
                        'cpu_load' => $dummy['cpu_load'],
                        'ram_load' => $dummy['ram_load'],
                        'packet_loss' => $uptimePercentVal > 0 ? max(0.0, 100.0 - $uptimePercentVal) : $dummy['packet_loss'],
                        'last_ping_ms' => $latencyVal > 0 ? $latencyVal : $dummy['last_ping_ms'],
                        'connection_type' => 'lan',
                        'api_port' => 8728,
                        'api_username' => 'admin',
                        'interfaces' => $dummy['interfaces'],
                    ]);

                    $this->importedCount++;
                }
            }
        }
    }
}
