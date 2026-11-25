<?php

namespace App\Imports;

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

            // Lokasi rak gabungan jika ada posisi
            $finalLocation = $location !== '-' && ! empty($location) ? $location : null;
            if (! empty($rackPosition) && $rackPosition !== '-' && $rackPosition !== $finalLocation) {
                $finalLocation = $finalLocation ? "{$finalLocation} ({$rackPosition})" : $rackPosition;
            }

            // PENCARIAN DATA EKSISTING:
            // 1. Cari berdasarkan ID angka jika ada
            $device = null;
            if (is_numeric($idVal) && (int) $idVal > 0) {
                $device = Device::find((int) $idVal);
            }
            // 2. Jika belum ditemukan, cari berdasarkan Hostname
            if (! $device) {
                $device = Device::where('hostname', $hostname)->first();
            }
            // 3. Jika masih belum ditemukan dan IP ada, cocokkan berdasarkan IP
            if (! $device && ! empty($ipAddress) && $ipAddress !== '-') {
                $device = Device::where('ip_address', $ipAddress)->first();
            }

            if ($device) {
                $oldHostname = $device->hostname;

                // UPDATE HOSTNAME jika diubah di Excel
                if (! empty($hostname) && $hostname !== '-') {
                    $device->hostname = $hostname;
                }
                if ($type && $type !== '-') $device->type = $type;
                if ($ipAddress && $ipAddress !== '-') $device->ip_address = $ipAddress;
                if ($customerId) $device->customer_id = $customerId;
                if ($finalLocation) $device->rack_location = $finalLocation;
                $device->status = $normalizedStatus;
                $device->status_state = $statusState;
                if ($uptimeText && $uptimeText !== '-') $device->uptime = $uptimeText;
                if ($latencyVal > 0) $device->last_ping_ms = $latencyVal;
                if ($uptimePercentVal > 0) {
                    $device->packet_loss = max(0.0, 100.0 - $uptimePercentVal);
                }
                $device->save();
                $this->updatedCount++;

                // Sinkronkan juga ke MonitoringDevice jika ada node dengan hostname lama/baru
                $monDevice = MonitoringDevice::where('hostname', $oldHostname)
                    ->orWhere('hostname', $hostname)
                    ->first();
                if ($monDevice) {
                    $monDevice->hostname = $hostname;
                    if ($ipAddress && $ipAddress !== '-') $monDevice->ip_address = $ipAddress;
                    if ($customerId) {
                        $monDevice->customer_id = $customerId;
                        $monDevice->customer = $customerName ?: ($allCustomers->firstWhere('id', $customerId)?->name ?? $monDevice->customer);
                    }
                    if ($finalLocation) $monDevice->rack_location = $finalLocation;
                    $monDevice->status = $statusState;
                    $monDevice->status_label = strtoupper($statusState);
                    if ($uptimeText && $uptimeText !== '-') $monDevice->uptime = $uptimeText;
                    if ($latencyVal > 0) $monDevice->last_ping_ms = $latencyVal;
                    if ($uptimePercentVal > 0) {
                        $monDevice->packet_loss = max(0.0, 100.0 - $uptimePercentVal);
                    }
                    $monDevice->save();
                }
            } else {
                // Buat Device baru jika benar-benar entitas baru
                Device::create([
                    'hostname' => $hostname,
                    'type' => $type ?: 'Router',
                    'ip_address' => ($ipAddress && $ipAddress !== '-') ? $ipAddress : '10.0.0.1',
                    'customer_id' => $customerId,
                    'rack_location' => $finalLocation ?: 'Rak Server Utama',
                    'status' => $normalizedStatus,
                    'status_state' => $statusState,
                    'uptime' => ($uptimeText && $uptimeText !== '-') ? $uptimeText : '1 hari',
                    'last_ping_ms' => $latencyVal ?: 5,
                    'packet_loss' => $uptimePercentVal > 0 ? max(0.0, 100.0 - $uptimePercentVal) : 0.0,
                ]);
                $this->importedCount++;
            }
        }
    }
}
