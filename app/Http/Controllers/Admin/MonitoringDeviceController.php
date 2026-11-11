<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonitoringDevice;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringDeviceController extends Controller
{
    /**
     * Tampilkan halaman formulir tambah perangkat monitoring baru.
     */
    public function create(): View
    {
        return view('admin.monitoring.create');
    }

    /**
     * Daftarkan target perangkat monitoring baru ke database.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'hostname' => 'required|string|max:100',
            'unit_type' => 'required|string|max:50',
            'ip_address' => 'required|string|max:100',
            'customer' => 'required|string|max:150',
            'project' => 'nullable|string|max:150',
            'model' => 'nullable|string|max:100',
            'rack_location' => 'nullable|string|max:100',
            'connection_type' => 'nullable|string|max:50',
            'api_port' => 'nullable|numeric',
            'api_username' => 'required|string|max:50',
            'api_password' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:online,warning,offline',
        ], [
            'hostname.required' => 'Nama hostname/perangkat target wajib diisi.',
            'unit_type.required' => 'Jenis perangkat wajib dipilih.',
            'ip_address.required' => 'IP Address target monitoring wajib diisi.',
            'customer.required' => 'Nama mitra/pelanggan target wajib diisi.',
            'api_username.required' => 'Username API MikroTik wajib diisi.',
        ]);

        $status = $validated['status'] ?? 'online';
        $cpu = 0.0;
        $ram = 0.0;
        $uptime = 'Siap Pantau';
        $latency = 0;
        $model = !empty($validated['model']) ? trim($validated['model']) : ($validated['unit_type'] . ' Node');
        $interfaces = [
            ['name' => 'ether1', 'status' => 'up', 'speed' => '1 Gbps', 'rx' => '0 Mbps', 'tx' => '0 Mbps'],
        ];
        $connectNotice = '';

        // Hubungi router MikroTik secara real jika IP & kredensial diberikan
        if (!empty($validated['ip_address']) && !empty($validated['api_username']) && !app()->runningUnitTests()) {
            $mikrotikService = new \App\Services\MikrotikService();
            $telemetry = $mikrotikService->getDeviceTelemetry(
                trim($validated['ip_address']),
                trim($validated['api_username']),
                (string) ($validated['api_password'] ?? ''),
                (int) ($validated['api_port'] ?? 8728),
                2
            );

            if ($telemetry) {
                $status = 'online';
                $cpu = $telemetry['resource']['cpu_load'];
                $ram = $telemetry['resource']['ram_load'];
                $uptime = $telemetry['resource']['uptime'];
                $latency = (int) $telemetry['latency_ms'];
                if (empty($validated['model'])) {
                    $model = $telemetry['resource']['board_name'] . ' (v' . $telemetry['resource']['version'] . ')';
                }
                if (!empty($telemetry['interfaces'])) {
                    $interfaces = $telemetry['interfaces'];
                }
                $connectNotice = " — Berhasil terhubung ke {$telemetry['resource']['board_name']} (RouterOS v{$telemetry['resource']['version']}).";
            } else {
                $status = 'offline';
                $uptime = 'Gagal Konek (Timeout)';
                $connectNotice = ' — Perangkat disimpan (Belum dapat terhubung ke port API 8728: ' . ($mikrotikService->getLastError() ?: 'Periksa IP/Kredensial') . ').';
            }
        }

        $deviceModel = MonitoringDevice::create([
            'hostname' => strtoupper(trim($validated['hostname'])),
            'unit_type' => $validated['unit_type'],
            'ip_address' => trim($validated['ip_address']),
            'customer' => trim($validated['customer']),
            'project' => !empty($validated['project']) ? trim($validated['project']) : 'Dedicated SLA Monitoring',
            'model' => $model,
            'rack_location' => !empty($validated['rack_location']) ? trim($validated['rack_location']) : 'POP Distribusi',
            'status' => $status,
            'status_label' => strtoupper($status),
            'uptime' => $uptime,
            'rx_mbps' => '0.0 Mbps',
            'tx_mbps' => '0.0 Mbps',
            'cpu_load' => $cpu,
            'ram_load' => $ram,
            'temperature' => '-',
            'voltage' => '-',
            'packet_loss' => $status === 'offline' ? 100.0 : 0.0,
            'last_ping_ms' => $latency,
            'connection_type' => $validated['connection_type'] ?? 'lan',
            'api_port' => (int) ($validated['api_port'] ?? 8728),
            'api_username' => $validated['api_username'] ?? 'admin',
            'api_password' => $validated['api_password'] ?? '',
            'interfaces' => $interfaces,
        ]);

        $newDevice = MonitoringController::formatMonitoringDevice($deviceModel);

        ActivityLogger::log(
            'monitoring',
            'create',
            "Mendaftarkan perangkat monitoring '{$newDevice['hostname']}' ({$newDevice['ip_address']})",
            $newDevice['hostname'],
            (string) $deviceModel->id,
            [
                'hostname' => $newDevice['hostname'],
                'unit_type' => $newDevice['unit_type'],
                'ip_address' => $newDevice['ip_address'],
                'customer' => $newDevice['customer'],
                'status' => $newDevice['status'],
            ]
        );

        $msg = "Perangkat monitoring '{$newDevice['hostname']}' berhasil ditambahkan{$connectNotice}";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'device' => $newDevice,
            ]);
        }

        return redirect()->route('monitoring.index')
            ->with('success', $msg);
    }

    /**
     * Tampilkan formulir edit perangkat monitoring.
     */
    public function edit(int $id): View
    {
        $deviceModel = MonitoringDevice::find($id);

        if (!$deviceModel) {
            abort(404, 'Perangkat monitoring tidak ditemukan.');
        }

        $device = MonitoringController::formatMonitoringDevice($deviceModel);
        return view('admin.monitoring.edit', compact('device'));
    }

    /**
     * Perbarui data perangkat monitoring di database.
     */
    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'hostname' => 'required|string|max:100',
            'unit_type' => 'required|string|max:50',
            'ip_address' => 'required|string|max:100',
            'customer' => 'required|string|max:150',
            'project' => 'nullable|string|max:150',
            'model' => 'nullable|string|max:100',
            'rack_location' => 'nullable|string|max:100',
            'connection_type' => 'nullable|string|max:50',
            'api_port' => 'nullable|numeric',
            'api_username' => 'required|string|max:50',
            'api_password' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:online,warning,offline',
        ], [
            'hostname.required' => 'Nama hostname/perangkat target wajib diisi.',
            'unit_type.required' => 'Jenis perangkat wajib dipilih.',
            'ip_address.required' => 'IP Address target monitoring wajib diisi.',
            'customer.required' => 'Nama mitra/pelanggan target wajib diisi.',
            'api_username.required' => 'Username API MikroTik wajib diisi.',
        ]);

        $deviceModel = MonitoringDevice::find($id);

        if (!$deviceModel) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Perangkat monitoring tidak ditemukan.'], 404);
            }
            return redirect()->route('monitoring.index')->with('error', 'Perangkat monitoring tidak ditemukan.');
        }

        $status = $validated['status'] ?? $deviceModel->status ?? 'online';
        $ip = trim($validated['ip_address']);
        $port = (int) ($validated['api_port'] ?? ($deviceModel->api_port ?: 8728));
        $username = !empty($validated['api_username']) ? $validated['api_username'] : $deviceModel->api_username;
        $password = (array_key_exists('api_password', $validated) && $validated['api_password'] !== null && $validated['api_password'] !== '')
            ? $validated['api_password']
            : $deviceModel->api_password;

        $updates = [
            'hostname' => strtoupper(trim($validated['hostname'])),
            'unit_type' => $validated['unit_type'],
            'ip_address' => $ip,
            'customer' => trim($validated['customer']),
            'project' => !empty($validated['project']) ? trim($validated['project']) : ($deviceModel->project ?: 'Dedicated SLA Monitoring'),
            'connection_type' => $validated['connection_type'] ?? ($deviceModel->connection_type ?: 'lan'),
            'api_port' => $port,
            'api_username' => $username,
            'api_password' => $password,
        ];

        if (array_key_exists('model', $validated) && !empty($validated['model'])) {
            $updates['model'] = trim($validated['model']);
        }
        if (array_key_exists('rack_location', $validated) && !empty($validated['rack_location'])) {
            $updates['rack_location'] = trim($validated['rack_location']);
        }

        $connectNotice = '';
        if (!empty($ip) && !empty($username) && !app()->runningUnitTests()) {
            $mikrotikService = new \App\Services\MikrotikService();
            $telemetry = $mikrotikService->getDeviceTelemetry($ip, $username, (string) $password, $port, 2);

            if ($telemetry) {
                $status = 'online';
                $updates['cpu_load'] = $telemetry['resource']['cpu_load'];
                $updates['ram_load'] = $telemetry['resource']['ram_load'];
                $updates['uptime'] = $telemetry['resource']['uptime'];
                $updates['last_ping_ms'] = (int) $telemetry['latency_ms'];
                $updates['packet_loss'] = 0.0;
                if (!empty($telemetry['interfaces'])) {
                    $updates['interfaces'] = $telemetry['interfaces'];
                }
                $connectNotice = " — Terhubung ke {$telemetry['resource']['board_name']} (RouterOS v{$telemetry['resource']['version']})";
            } else {
                $status = 'offline';
                $updates['packet_loss'] = 100.0;
                $connectNotice = ' — Peringatan: ' . ($mikrotikService->getLastError() ?: 'Koneksi API gagal');
            }
        }

        $updates['status'] = $status;
        $updates['status_label'] = strtoupper($status);

        $deviceModel->update($updates);
        $updatedDevice = MonitoringController::formatMonitoringDevice($deviceModel);

        ActivityLogger::log(
            'monitoring',
            'update',
            "Memperbarui data perangkat monitoring '{$updatedDevice['hostname']}'",
            $updatedDevice['hostname'],
            (string) $id,
            [
                'hostname' => $updatedDevice['hostname'],
                'unit_type' => $updatedDevice['unit_type'],
                'ip_address' => $updatedDevice['ip_address'],
                'customer' => $updatedDevice['customer'],
                'status' => $updatedDevice['status'],
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Perangkat monitoring '{$updatedDevice['hostname']}' berhasil diperbarui{$connectNotice}.",
                'device' => $updatedDevice,
            ]);
        }

        return redirect()->route('monitoring.index')
            ->with('success', "Perangkat monitoring '{$updatedDevice['hostname']}' berhasil diperbarui{$connectNotice}.");
    }

    /**
     * Hapus perangkat dari target monitoring telemetri di database.
     */
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $deviceModel = MonitoringDevice::find($id);

        if ($deviceModel) {
            $hostname = $deviceModel->hostname;
            $ipAddress = $deviceModel->ip_address;

            $deviceModel->delete();

            ActivityLogger::log(
                'monitoring',
                'delete',
                "Menghapus perangkat monitoring '{$hostname}'",
                $hostname,
                (string) $id,
                [
                    'hostname' => $hostname,
                    'ip_address' => $ipAddress,
                ]
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Perangkat '{$hostname}' berhasil dihapus dari monitoring.",
                ]);
            }

            return redirect()->route('monitoring.index')
                ->with('success', "Perangkat '{$hostname}' berhasil dihapus dari monitoring.");
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat monitoring tidak ditemukan.',
            ], 404);
        }

        return redirect()->route('monitoring.index')
            ->with('error', 'Perangkat monitoring tidak ditemukan.');
    }

    /**
     * Simpan / Perbarui Konfigurasi IP & Kredensial API MikroTik (API Ajax cepat) ke database.
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

        $id = (int) $validated['device_id'];
        $deviceModel = MonitoringDevice::find($id);

        if ($deviceModel) {
            $updates = [
                'ip_address' => $validated['ip_address'],
                'connection_type' => $validated['connection_type'],
                'api_port' => (int) $validated['api_port'],
                'api_username' => $validated['api_username'],
            ];

            if (!empty($validated['api_password'])) {
                $updates['api_password'] = $validated['api_password'];
            }

            $deviceModel->update($updates);

            ActivityLogger::log(
                'monitoring',
                'update',
                "Memperbarui konfigurasi API MikroTik perangkat '{$deviceModel->hostname}'",
                $deviceModel->hostname ?? "ID: {$id}",
                (string) $id,
                [
                    'ip_address' => $validated['ip_address'],
                    'api_port' => $validated['api_port'],
                    'connection_type' => $validated['connection_type'],
                    'api_username' => $validated['api_username'],
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi IP & Kredensial API MikroTik berhasil diperbarui.',
            'data' => $validated,
        ]);
    }
}
