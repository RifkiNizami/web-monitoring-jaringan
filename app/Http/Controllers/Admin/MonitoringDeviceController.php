<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\MonitoringDevice;
use App\Models\Project;
use App\Services\ActivityLogger;
use App\Services\MikrotikService;
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
        $customers = Customer::with('monitoringDevices')->orderBy('name', 'asc')->get();
        $projects = Project::orderBy('name', 'asc')->get();

        return view('admin.monitoring.create', compact('customers', 'projects'));
    }

    /**
     * Daftarkan target perangkat monitoring baru ke database.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $inputPort = $request->filled('api_port') ? (int) $request->input('api_port') : null;
        $isNonMikrotik = MikrotikService::isNonMikrotik(
            $request->input('unit_type'),
            $inputPort,
            $request->input('connection_type')
        );

        $validated = $request->validate([
            'hostname' => 'required|string|max:100',
            'unit_type' => 'required|string|max:50',
            'ip_address' => 'required|string|max:100',
            'customer_id' => 'nullable|exists:customers,id',
            'project_id' => 'nullable|exists:projects,id',
            'customer' => 'nullable|string|max:150',
            'project' => 'nullable|string|max:150',
            'model' => 'nullable|string|max:100',
            'rack_location' => 'nullable|string|max:100',
            'connection_type' => 'nullable|string|max:50',
            'api_port' => 'nullable|numeric',
            'api_username' => $isNonMikrotik ? 'nullable|string|max:50' : 'required|string|max:50',
            'api_password' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:online,warning,offline',
        ], [
            'hostname.required' => 'Nama hostname/perangkat target wajib diisi.',
            'unit_type.required' => 'Jenis perangkat wajib dipilih.',
            'ip_address.required' => 'IP Address target monitoring wajib diisi.',
            'customer_id.exists' => 'Mitra customer yang dipilih tidak valid.',
            'project_id.exists' => 'Proyek yang dipilih tidak valid.',
            'api_username.required' => 'Username API MikroTik wajib diisi untuk perangkat RouterOS.',
        ]);

        $customerId = ! empty($validated['customer_id']) ? (int) $validated['customer_id'] : null;
        $customerRecord = $customerId ? Customer::find($customerId) : null;
        if (! $customerRecord && ! empty($validated['customer'])) {
            $customerRecord = Customer::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validated['customer']))])->first();
            $customerId = $customerRecord?->id;
        }
        $customerName = $customerRecord ? $customerRecord->name : (! empty($validated['customer']) ? trim($validated['customer']) : 'Infrastruktur Backbone NetFly');

        // Enforce kebijakan sistem: 1 User / Pelanggan = 1 Router Monitoring
        if ($customerId) {
            $existingRouter = MonitoringDevice::where('customer_id', $customerId)->first();
            if ($existingRouter) {
                $err = "Customer '{$customerName}' sudah memiliki router monitoring ({$existingRouter->hostname}). Sesuai kebijakan 1 user 1 router, satu pelanggan hanya dapat memiliki 1 router monitoring.";
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $err,
                        'errors' => ['customer_id' => [$err]],
                    ], 422);
                }

                return back()->withInput()->withErrors(['customer_id' => $err]);
            }
        }

        $projectId = ! empty($validated['project_id']) ? (int) $validated['project_id'] : null;
        $projectRecord = $projectId ? Project::find($projectId) : null;
        $projectName = $projectRecord ? $projectRecord->name : (! empty($validated['project']) ? trim($validated['project']) : 'Dedicated SLA Monitoring');

        $port = $isNonMikrotik
            ? MikrotikService::resolveNonMikrotikPort($inputPort)
            : ($inputPort ?? 8728);
        $apiUsername = ! empty($validated['api_username'])
            ? trim((string) $validated['api_username'])
            : ($isNonMikrotik ? null : 'admin');
        $apiPassword = (string) ($validated['api_password'] ?? '');

        $status = $validated['status'] ?? 'online';
        $cpu = 0.0;
        $ram = 0.0;
        $rxMbps = '0.0 Mbps';
        $txMbps = '0.0 Mbps';
        $uptime = $isNonMikrotik ? "HTTP/TCP Port {$port} Siap Pantau" : 'Siap Pantau';
        $latency = $status === 'offline' ? null : 0;
        $model = ! empty($validated['model']) ? trim($validated['model']) : ($validated['unit_type'].' Node');
        $interfaces = [
            ['name' => $isNonMikrotik ? 'eth0' : 'ether1', 'status' => 'up', 'speed' => $isNonMikrotik ? '100 Mbps' : '1 Gbps', 'rx' => '0 Mbps', 'tx' => '0 Mbps'],
        ];
        $connectNotice = '';

        // Pengecekan koneksi perangkat secara real-time saat pendaftaran (Kondisi A vs Kondisi B)
        if (! empty($validated['ip_address']) && ! app()->runningUnitTests()) {
            $mikrotikService = new MikrotikService;
            $ip = trim($validated['ip_address']);

            if ($isNonMikrotik) {
                // Kondisi B: Non-MikroTik / TOTOLINK HTTP & Ping (Port 80/8080 atau Access Point / Generic Router)
                $check = $mikrotikService->checkHttpSocket($ip, $port, 2);

                if ($check['online']) {
                    $status = 'online';
                    $cpu = 0.0;
                    $ram = 0.0;
                    $uptime = $check['uptime'];
                    $latency = $check['latency_ms'];
                    $interfaces = $check['interfaces'];
                    $connectNotice = " — Berhasil terhubung via HTTP/TCP Port {$port} ({$latency} ms).";
                } else {
                    $status = 'offline';
                    $cpu = 0.0;
                    $ram = 0.0;
                    $uptime = 'Offline (Timeout)';
                    $latency = null;
                    $connectNotice = " — Perangkat disimpan (Port {$port} tidak merespons dalam 2 detik).";
                }
            } elseif (! empty($apiUsername)) {
                // Kondisi A: MikroTik RouterOS API (Port 8728 / 8729)
                $telemetry = $mikrotikService->getDeviceTelemetry(
                    $ip,
                    $apiUsername,
                    $apiPassword,
                    $port,
                    2,
                    $validated['unit_type']
                );

                if ($telemetry) {
                    $status = 'online';
                    $cpu = $telemetry['resource']['cpu_load'];
                    $ram = $telemetry['resource']['ram_load'];
                    $rxMbps = $telemetry['rx_mbps'] ?? '0.01 Mbps';
                    $txMbps = $telemetry['tx_mbps'] ?? '0.01 Mbps';
                    $uptime = $telemetry['resource']['uptime'];
                    $latency = (int) $telemetry['latency_ms'];
                    if (empty($validated['model'])) {
                        $model = $telemetry['resource']['board_name'].' (v'.$telemetry['resource']['version'].')';
                    }
                    if (! empty($telemetry['interfaces'])) {
                        $interfaces = $telemetry['interfaces'];
                    }
                    $connectNotice = " — Berhasil terhubung ke {$telemetry['resource']['board_name']} (RouterOS v{$telemetry['resource']['version']}).";
                } else {
                    $status = 'offline';
                    $latency = null;
                    $uptime = 'Gagal Konek (Timeout)';
                    $connectNotice = " — Perangkat disimpan (Belum dapat terhubung ke port API {$port}: ".($mikrotikService->getLastError() ?: 'Periksa IP/Kredensial').').';
                }
            }
        }

        $deviceModel = MonitoringDevice::create([
            'customer_id' => $customerId,
            'project_id' => $projectId,
            'hostname' => strtoupper(trim($validated['hostname'])),
            'unit_type' => $validated['unit_type'],
            'ip_address' => trim($validated['ip_address']),
            'customer' => $customerName,
            'project' => $projectName,
            'model' => $model,
            'rack_location' => ! empty($validated['rack_location']) ? trim($validated['rack_location']) : 'POP Distribusi',
            'status' => $status,
            'status_label' => strtoupper($status),
            'uptime' => $uptime,
            'rx_mbps' => $rxMbps,
            'tx_mbps' => $txMbps,
            'cpu_load' => $cpu,
            'ram_load' => $ram,
            'temperature' => '-',
            'voltage' => '-',
            'packet_loss' => $status === 'offline' ? 100.0 : 0.0,
            'last_ping_ms' => $latency,
            'connection_type' => $validated['connection_type'] ?? ($isNonMikrotik ? 'http' : 'lan'),
            'api_port' => $port,
            'api_username' => $apiUsername,
            'api_password' => $apiPassword,
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
        $deviceModel = MonitoringDevice::with(['customerRelation', 'projectRelation'])->find($id);

        if (! $deviceModel) {
            abort(404, 'Perangkat monitoring tidak ditemukan.');
        }

        $device = MonitoringController::formatMonitoringDevice($deviceModel);
        $customers = Customer::with('monitoringDevices')->orderBy('name', 'asc')->get();
        $projects = Project::orderBy('name', 'asc')->get();

        return view('admin.monitoring.edit', compact('device', 'customers', 'projects'));
    }

    /**
     * Perbarui data perangkat monitoring di database.
     */
    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $inputPort = $request->filled('api_port') ? (int) $request->input('api_port') : null;
        $isNonMikrotik = MikrotikService::isNonMikrotik(
            $request->input('unit_type'),
            $inputPort,
            $request->input('connection_type')
        );

        $validated = $request->validate([
            'hostname' => 'required|string|max:100',
            'unit_type' => 'required|string|max:50',
            'ip_address' => 'required|string|max:100',
            'customer_id' => 'nullable|exists:customers,id',
            'project_id' => 'nullable|exists:projects,id',
            'customer' => 'nullable|string|max:150',
            'project' => 'nullable|string|max:150',
            'model' => 'nullable|string|max:100',
            'rack_location' => 'nullable|string|max:100',
            'connection_type' => 'nullable|string|max:50',
            'api_port' => 'nullable|numeric',
            'api_username' => $isNonMikrotik ? 'nullable|string|max:50' : 'required|string|max:50',
            'api_password' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:online,warning,offline',
        ], [
            'hostname.required' => 'Nama hostname/perangkat target wajib diisi.',
            'unit_type.required' => 'Jenis perangkat wajib dipilih.',
            'ip_address.required' => 'IP Address target monitoring wajib diisi.',
            'customer_id.exists' => 'Mitra customer yang dipilih tidak valid.',
            'project_id.exists' => 'Proyek yang dipilih tidak valid.',
            'api_username.required' => 'Username API MikroTik wajib diisi untuk perangkat RouterOS.',
        ]);

        $deviceModel = MonitoringDevice::find($id);

        if (! $deviceModel) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Perangkat monitoring tidak ditemukan.'], 404);
            }

            return redirect()->route('monitoring.index')->with('error', 'Perangkat monitoring tidak ditemukan.');
        }

        $customerId = $request->filled('customer_id') ? (int) $request->input('customer_id') : null;
        $customerRecord = $customerId ? Customer::find($customerId) : null;
        if (! $customerRecord && ! empty($validated['customer'])) {
            $customerRecord = Customer::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validated['customer']))])->first();
            $customerId = $customerRecord?->id;
        }
        $customerName = $customerRecord ? $customerRecord->name : (! empty($validated['customer']) ? trim($validated['customer']) : ($deviceModel->customer ?: 'Infrastruktur Backbone NetFly'));

        // Enforce kebijakan sistem: 1 User / Pelanggan = 1 Router Monitoring
        if ($customerId) {
            $existingRouter = MonitoringDevice::where('customer_id', $customerId)
                ->where('id', '!=', $id)
                ->first();
            if ($existingRouter) {
                $err = "Customer '{$customerName}' sudah memiliki router monitoring ({$existingRouter->hostname}). Sesuai kebijakan 1 user 1 router, satu pelanggan hanya dapat memiliki 1 router monitoring.";
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $err,
                        'errors' => ['customer_id' => [$err]],
                    ], 422);
                }

                return back()->withInput()->withErrors(['customer_id' => $err]);
            }
        }

        $projectId = $request->filled('project_id') ? (int) $request->input('project_id') : null;
        $projectRecord = $projectId ? Project::find($projectId) : null;
        $projectName = $projectRecord ? $projectRecord->name : (! empty($validated['project']) ? trim($validated['project']) : ($deviceModel->project ?: 'Dedicated SLA Monitoring'));

        $status = $validated['status'] ?? $deviceModel->status ?? 'online';
        $ip = trim($validated['ip_address']);
        $port = $isNonMikrotik
            ? MikrotikService::resolveNonMikrotikPort($inputPort ?? $deviceModel->api_port)
            : ($inputPort ?? (int) ($deviceModel->api_port ?: 8728));
        $username = ! empty($validated['api_username'])
            ? trim((string) $validated['api_username'])
            : ($isNonMikrotik ? null : $deviceModel->api_username);
        $password = (array_key_exists('api_password', $validated) && $validated['api_password'] !== null && $validated['api_password'] !== '')
            ? $validated['api_password']
            : ($isNonMikrotik ? '' : $deviceModel->api_password);

        $updates = [
            'customer_id' => $customerId,
            'project_id' => $projectId,
            'hostname' => strtoupper(trim($validated['hostname'])),
            'unit_type' => $validated['unit_type'],
            'ip_address' => $ip,
            'customer' => $customerName,
            'project' => $projectName,
            'connection_type' => $validated['connection_type'] ?? ($deviceModel->connection_type ?: ($isNonMikrotik ? 'http' : 'lan')),
            'api_port' => $port,
            'api_username' => $username,
            'api_password' => $password,
        ];

        if (array_key_exists('model', $validated) && ! empty($validated['model'])) {
            $updates['model'] = trim($validated['model']);
        }
        if (array_key_exists('rack_location', $validated) && ! empty($validated['rack_location'])) {
            $updates['rack_location'] = trim($validated['rack_location']);
        }

        $connectNotice = '';
        if (! empty($ip) && ! app()->runningUnitTests()) {
            $mikrotikService = new MikrotikService;

            if ($isNonMikrotik) {
                // Kondisi B: Non-MikroTik / TOTOLINK HTTP & Ping
                $check = $mikrotikService->checkHttpSocket($ip, $port, 2);

                if ($check['online']) {
                    $status = 'online';
                    $updates['cpu_load'] = 0.0;
                    $updates['ram_load'] = 0.0;
                    $updates['uptime'] = $check['uptime'];
                    $updates['last_ping_ms'] = $check['latency_ms'];
                    $updates['packet_loss'] = 0.0;
                    $updates['interfaces'] = $check['interfaces'];
                    $connectNotice = " — Terhubung via HTTP/TCP Port {$port} ({$check['latency_ms']} ms)";
                } else {
                    $status = 'offline';
                    $updates['cpu_load'] = 0.0;
                    $updates['ram_load'] = 0.0;
                    $updates['uptime'] = 'Offline (Timeout)';
                    $updates['last_ping_ms'] = null;
                    $updates['packet_loss'] = 100.0;
                    $connectNotice = " — Peringatan: Port {$port} tidak merespons dalam 2 detik";
                }
            } elseif (! empty($username)) {
                // Kondisi A: MikroTik RouterOS API
                $telemetry = $mikrotikService->getDeviceTelemetry($ip, $username, (string) $password, $port, 2, $validated['unit_type']);

                if ($telemetry) {
                    $status = 'online';
                    $updates['cpu_load'] = $telemetry['resource']['cpu_load'];
                    $updates['ram_load'] = $telemetry['resource']['ram_load'];
                    $updates['uptime'] = $telemetry['resource']['uptime'];
                    $updates['last_ping_ms'] = (int) $telemetry['latency_ms'];
                    $updates['packet_loss'] = 0.0;
                    if (! empty($telemetry['interfaces'])) {
                        $updates['interfaces'] = $telemetry['interfaces'];
                    }
                    $connectNotice = " — Terhubung ke {$telemetry['resource']['board_name']} (RouterOS v{$telemetry['resource']['version']})";
                } else {
                    $status = 'offline';
                    $updates['last_ping_ms'] = null;
                    $updates['packet_loss'] = 100.0;
                    $connectNotice = ' — Peringatan: '.($mikrotikService->getLastError() ?: 'Koneksi API gagal');
                }
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
     * Simpan / Perbarui Konfigurasi IP & Kredensial API MikroTik / HTTP Socket (API Ajax cepat) ke database.
     */
    public function saveSettings(Request $request): JsonResponse
    {
        $inputPort = $request->filled('api_port') ? (int) $request->input('api_port') : null;
        $deviceModel = $request->filled('device_id') ? MonitoringDevice::find((int) $request->input('device_id')) : null;
        $isNonMikrotik = MikrotikService::isNonMikrotik($deviceModel?->unit_type, $inputPort);

        $validated = $request->validate([
            'device_id' => 'required',
            'ip_address' => 'required|string',
            'api_port' => 'required|numeric',
            'api_username' => $isNonMikrotik ? 'nullable|string' : 'required|string',
            'api_password' => 'nullable|string',
            'connection_type' => 'required|string',
        ]);

        $id = (int) $validated['device_id'];

        if ($deviceModel) {
            $updates = [
                'ip_address' => $validated['ip_address'],
                'connection_type' => $validated['connection_type'],
                'api_port' => (int) $validated['api_port'],
                'api_username' => $validated['api_username'] ?? ($isNonMikrotik ? null : 'admin'),
            ];

            if (! empty($validated['api_password'])) {
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
