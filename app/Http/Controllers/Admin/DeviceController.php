<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DeviceController extends Controller
{
    /**
     * Daftar opsi kategori perangkat.
     */
    public static function getDeviceTypes(): array
    {
        return [
            'Router',
            'Switch',
            'Firewall',
            'Access Point',
            'Server',
            'OLT / Optical',
        ];
    }

    /**
     * Daftar opsi status ketersediaan barang.
     */
    public static function getStatusOptions(): array
    {
        return [
            'Tersedia',
            'Terpakai',
            'Habis',
        ];
    }

    /**
     * Konversi label status ketersediaan ke state internal.
     */
    public static function resolveStatusState(string $status): string
    {
        return match ($status) {
            'Tersedia' => 'available',
            'Terpakai' => 'in_use',
            default => 'out_of_stock',
        };
    }

    /**
     * Ambil daftar barang inventaris dari session (dengan inisialisasi awal).
     */
    public static function getInventoryDevicesList(): array
    {
        if (!session()->has('inventory_devices_initialized')) {
            $defaults = [
                1 => [
                    'id' => 1,
                    'hostname' => 'RTR-CORE-01',
                    'type' => 'Router',
                    'brand_model' => 'MikroTik CCR2004-16G-2S+',
                    'serial_number' => 'SN-9A82B1002',
                    'quantity' => 4,
                    'status' => 'Tersedia',
                    'status_state' => 'available',
                    'photo' => null,
                ],
                2 => [
                    'id' => 2,
                    'hostname' => 'SW-AGG-02',
                    'type' => 'Switch',
                    'brand_model' => 'MikroTik CRS328-24P-4S+RM',
                    'serial_number' => 'SN-7C11D4098',
                    'quantity' => 8,
                    'status' => 'Terpakai',
                    'status_state' => 'in_use',
                    'photo' => null,
                ],
                3 => [
                    'id' => 3,
                    'hostname' => 'AP-OUTDOOR-01',
                    'type' => 'Access Point',
                    'brand_model' => 'Ubiquiti UniFi U6-Mesh Pro',
                    'serial_number' => 'SN-4F55E9210',
                    'quantity' => 12,
                    'status' => 'Tersedia',
                    'status_state' => 'available',
                    'photo' => null,
                ],
                4 => [
                    'id' => 4,
                    'hostname' => 'FW-EDGE-01',
                    'type' => 'Firewall',
                    'brand_model' => 'FortiGate 60F Next-Gen',
                    'serial_number' => 'SN-1B33C8841',
                    'quantity' => 0,
                    'status' => 'Habis',
                    'status_state' => 'out_of_stock',
                    'photo' => null,
                ],
            ];

            session([
                'inventory_devices_initialized' => true,
                'inventory_devices_list' => $defaults,
            ]);

            return $defaults;
        }

        return session('inventory_devices_list', []);
    }

    /**
     * Simpan pembaruan daftar barang inventaris ke session.
     */
    public static function saveInventoryDevicesList(array $devices): void
    {
        session([
            'inventory_devices_initialized' => true,
            'inventory_devices_list' => $devices,
        ]);
    }

    /**
     * Tampilkan halaman utama daftar inventaris barang perangkat.
     */
    public function index(Request $request): View
    {
        $deviceTypes = self::getDeviceTypes();
        $statusOptions = self::getStatusOptions();
        $devices = array_values(self::getInventoryDevicesList());

        $deviceCollection = collect($devices);

        // 4 Summary Metrics
        $summary = [
            'total_items' => $deviceCollection->count(),
            'total_units' => $deviceCollection->sum('quantity'),
            'available' => $deviceCollection->where('status_state', 'available')->count(),
            'in_use_or_empty' => $deviceCollection->whereIn('status_state', ['in_use', 'out_of_stock'])->count(),
        ];

        return view('admin.devices', compact('summary', 'deviceTypes', 'statusOptions', 'devices'));
    }

    /**
     * Tampilkan halaman formulir tambah barang inventaris baru.
     */
    public function create(): View
    {
        $deviceTypes = self::getDeviceTypes();
        $statusOptions = self::getStatusOptions();

        return view('admin.devices.create', compact('deviceTypes', 'statusOptions'));
    }

    /**
     * Simpan data barang inventaris baru.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'hostname' => 'required|string|max:100',
            'type' => 'required|string|max:50',
            'brand_model' => 'required|string|max:150',
            'serial_number' => 'required|string|max:100',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|string|in:Tersedia,Terpakai,Habis',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'hostname.required' => 'Nama / kode barang wajib diisi.',
            'type.required' => 'Kategori perangkat wajib dipilih.',
            'brand_model.required' => 'Merk / tipe seri barang wajib diisi.',
            'serial_number.required' => 'Nomor seri (SN) wajib diisi.',
            'quantity.required' => 'Jumlah barang wajib diisi.',
            'status.required' => 'Status ketersediaan wajib dipilih.',
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.mimes' => 'Format foto hanya mendukung JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto barang maksimal 10 MB.',
        ]);

        $devices = self::getInventoryDevicesList();
        $newId = empty($devices) ? 1 : (max(array_keys($devices)) + 1);

        $photoUrl = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');
            $filename = 'device_' . time() . '_' . Str::slug($validated['hostname']) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/devices');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $photoUrl = asset('uploads/devices/' . $filename);
        }

        $newDevice = [
            'id' => $newId,
            'hostname' => strtoupper(trim($validated['hostname'])),
            'type' => $validated['type'],
            'brand_model' => trim($validated['brand_model']),
            'serial_number' => strtoupper(trim($validated['serial_number'])),
            'quantity' => (int) $validated['quantity'],
            'status' => $validated['status'],
            'status_state' => self::resolveStatusState($validated['status']),
            'photo' => $photoUrl,
        ];

        $devices[$newId] = $newDevice;
        self::saveInventoryDevicesList($devices);

        $msg = "Barang inventaris '{$newDevice['hostname']}' berhasil ditambahkan.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'device' => $newDevice,
            ]);
        }

        return redirect()->route('devices.index')->with('success', $msg);
    }

    /**
     * Tampilkan halaman formulir edit data barang inventaris.
     */
    public function edit(int $id): View
    {
        $devices = self::getInventoryDevicesList();

        if (!isset($devices[$id])) {
            abort(404, 'Data barang inventaris tidak ditemukan.');
        }

        $device = $devices[$id];
        $deviceTypes = self::getDeviceTypes();
        $statusOptions = self::getStatusOptions();

        return view('admin.devices.edit', compact('device', 'deviceTypes', 'statusOptions'));
    }

    /**
     * Perbarui data barang inventaris.
     */
    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'hostname' => 'required|string|max:100',
            'type' => 'required|string|max:50',
            'brand_model' => 'required|string|max:150',
            'serial_number' => 'required|string|max:100',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|string|in:Tersedia,Terpakai,Habis',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'hostname.required' => 'Nama / kode barang wajib diisi.',
            'type.required' => 'Kategori perangkat wajib dipilih.',
            'brand_model.required' => 'Merk / tipe seri barang wajib diisi.',
            'serial_number.required' => 'Nomor seri (SN) wajib diisi.',
            'quantity.required' => 'Jumlah barang wajib diisi.',
            'status.required' => 'Status ketersediaan wajib dipilih.',
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.mimes' => 'Format foto hanya mendukung JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto barang maksimal 10 MB.',
        ]);

        $devices = self::getInventoryDevicesList();

        if (!isset($devices[$id])) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Data barang inventaris tidak ditemukan.'], 404);
            }
            return redirect()->route('devices.index')->with('error', 'Data barang inventaris tidak ditemukan.');
        }

        $photoUrl = $devices[$id]['photo'] ?? null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');
            $filename = 'device_' . time() . '_' . Str::slug($validated['hostname']) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/devices');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $photoUrl = asset('uploads/devices/' . $filename);
        }

        $devices[$id]['hostname'] = strtoupper(trim($validated['hostname']));
        $devices[$id]['type'] = $validated['type'];
        $devices[$id]['brand_model'] = trim($validated['brand_model']);
        $devices[$id]['serial_number'] = strtoupper(trim($validated['serial_number']));
        $devices[$id]['quantity'] = (int) $validated['quantity'];
        $devices[$id]['status'] = $validated['status'];
        $devices[$id]['status_state'] = self::resolveStatusState($validated['status']);
        $devices[$id]['photo'] = $photoUrl;

        self::saveInventoryDevicesList($devices);

        $msg = "Data barang '{$devices[$id]['hostname']}' berhasil diperbarui.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'device' => $devices[$id],
            ]);
        }

        return redirect()->route('devices.index')->with('success', $msg);
    }

    /**
     * Hapus data barang dari inventaris.
     */
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $devices = self::getInventoryDevicesList();

        if (isset($devices[$id])) {
            $hostname = $devices[$id]['hostname'];
            unset($devices[$id]);
            self::saveInventoryDevicesList($devices);

            $msg = "Barang '{$hostname}' berhasil dihapus dari inventaris.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            return redirect()->route('devices.index')->with('success', $msg);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Data barang inventaris tidak ditemukan.',
            ], 404);
        }

        return redirect()->route('devices.index')->with('error', 'Data barang inventaris tidak ditemukan.');
    }
}
