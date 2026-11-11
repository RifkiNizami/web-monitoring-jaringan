<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\InventoryDeviceExport;
use App\Models\Device;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

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
     * Format instance Model Device menjadi struktur array untuk view & JSON.
     */
    public static function formatDeviceModel(Device $device): array
    {
        $status = in_array($device->status, self::getStatusOptions(), true)
            ? $device->status
            : 'Tersedia';

        $statusState = in_array($device->status_state, ['available', 'in_use', 'out_of_stock'], true)
            ? $device->status_state
            : self::resolveStatusState($status);

        return [
            'id' => $device->id,
            'hostname' => $device->hostname,
            'type' => $device->type ?? 'Router',
            'brand_model' => $device->brand_model ?? '-',
            'serial_number' => $device->serial_number ?? '-',
            'quantity' => (int) ($device->quantity ?? 0),
            'status' => $status,
            'status_state' => $statusState,
            'photo' => $device->photo,
        ];
    }

    /**
     * Ambil daftar barang inventaris dari database (dengan inisialisasi awal bila kosong).
     */
    public static function getInventoryDevicesList(): array
    {
        if (Device::count() === 0) {
            $defaults = [
                [
                    'hostname' => 'RTR-CORE-01',
                    'type' => 'Router',
                    'brand_model' => 'MikroTik CCR2004-16G-2S+',
                    'serial_number' => 'SN-9A82B1002',
                    'quantity' => 4,
                    'status' => 'Tersedia',
                    'status_state' => 'available',
                    'photo' => null,
                ],
                [
                    'hostname' => 'SW-AGG-02',
                    'type' => 'Switch',
                    'brand_model' => 'MikroTik CRS328-24P-4S+RM',
                    'serial_number' => 'SN-7C11D4098',
                    'quantity' => 8,
                    'status' => 'Terpakai',
                    'status_state' => 'in_use',
                    'photo' => null,
                ],
                [
                    'hostname' => 'AP-OUTDOOR-01',
                    'type' => 'Access Point',
                    'brand_model' => 'Ubiquiti UniFi U6-Mesh Pro',
                    'serial_number' => 'SN-4F55E9210',
                    'quantity' => 12,
                    'status' => 'Tersedia',
                    'status_state' => 'available',
                    'photo' => null,
                ],
                [
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

            foreach ($defaults as $item) {
                Device::create($item);
            }
        }

        return Device::orderBy('id', 'asc')
            ->get()
            ->mapWithKeys(fn (Device $device) => [$device->id => self::formatDeviceModel($device)])
            ->all();
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
     * Simpan data barang inventaris baru ke database.
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

        $photoUrl = null;
        $photoMeta = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');
            $photoMeta = [
                'file_name' => $file->getClientOriginalName(),
                'size_kb' => round($file->getSize() / 1024, 2),
                'mime_type' => $file->getClientMimeType(),
            ];
            $filename = 'device_' . time() . '_' . Str::slug($validated['hostname']) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/devices');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $photoUrl = asset('uploads/devices/' . $filename);
        }

        $deviceModel = Device::create([
            'hostname' => strtoupper(trim($validated['hostname'])),
            'type' => $validated['type'],
            'brand_model' => trim($validated['brand_model']),
            'serial_number' => strtoupper(trim($validated['serial_number'])),
            'quantity' => (int) $validated['quantity'],
            'status' => $validated['status'],
            'status_state' => self::resolveStatusState($validated['status']),
            'photo' => $photoUrl,
        ]);

        $newDevice = self::formatDeviceModel($deviceModel);

        $properties = [
            'hostname' => $newDevice['hostname'],
            'type' => $newDevice['type'],
            'brand_model' => $newDevice['brand_model'],
            'quantity' => $newDevice['quantity'],
            'status' => $newDevice['status'],
        ];
        if ($photoMeta) {
            $properties['photo'] = $photoMeta;
        }

        ActivityLogger::log(
            'inventaris',
            'create',
            "Menambahkan barang inventaris '{$newDevice['hostname']}' ({$newDevice['brand_model']})",
            $newDevice['hostname'],
            (string) $deviceModel->id,
            $properties
        );

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
        $deviceModel = Device::find($id);

        if (!$deviceModel) {
            abort(404, 'Data barang inventaris tidak ditemukan.');
        }

        $device = self::formatDeviceModel($deviceModel);
        $deviceTypes = self::getDeviceTypes();
        $statusOptions = self::getStatusOptions();

        return view('admin.devices.edit', compact('device', 'deviceTypes', 'statusOptions'));
    }

    /**
     * Perbarui data barang inventaris di database.
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

        $deviceModel = Device::find($id);

        if (!$deviceModel) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Data barang inventaris tidak ditemukan.'], 404);
            }
            return redirect()->route('devices.index')->with('error', 'Data barang inventaris tidak ditemukan.');
        }

        $photoUrl = $deviceModel->photo;
        $photoMeta = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');
            $photoMeta = [
                'file_name' => $file->getClientOriginalName(),
                'size_kb' => round($file->getSize() / 1024, 2),
                'mime_type' => $file->getClientMimeType(),
            ];
            $filename = 'device_' . time() . '_' . Str::slug($validated['hostname']) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/devices');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $photoUrl = asset('uploads/devices/' . $filename);
        }

        $deviceModel->update([
            'hostname' => strtoupper(trim($validated['hostname'])),
            'type' => $validated['type'],
            'brand_model' => trim($validated['brand_model']),
            'serial_number' => strtoupper(trim($validated['serial_number'])),
            'quantity' => (int) $validated['quantity'],
            'status' => $validated['status'],
            'status_state' => self::resolveStatusState($validated['status']),
            'photo' => $photoUrl,
        ]);

        $updatedDevice = self::formatDeviceModel($deviceModel);

        $properties = [
            'hostname' => $updatedDevice['hostname'],
            'type' => $updatedDevice['type'],
            'brand_model' => $updatedDevice['brand_model'],
            'quantity' => $updatedDevice['quantity'],
            'status' => $updatedDevice['status'],
        ];
        if ($photoMeta) {
            $properties['photo'] = $photoMeta;
        }

        ActivityLogger::log(
            'inventaris',
            'update',
            "Memperbarui data barang inventaris '{$updatedDevice['hostname']}'",
            $updatedDevice['hostname'],
            (string) $deviceModel->id,
            $properties
        );

        $msg = "Data barang '{$updatedDevice['hostname']}' berhasil diperbarui.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'device' => $updatedDevice,
            ]);
        }

        return redirect()->route('devices.index')->with('success', $msg);
    }

    /**
     * Hapus data barang dari inventaris database.
     */
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $deviceModel = Device::find($id);

        if ($deviceModel) {
            $hostname = $deviceModel->hostname;
            $type = $deviceModel->type;
            $brandModel = $deviceModel->brand_model;

            $deviceModel->delete();

            ActivityLogger::log(
                'inventaris',
                'delete',
                "Menghapus barang inventaris '{$hostname}'",
                $hostname,
                (string) $id,
                [
                    'hostname' => $hostname,
                    'type' => $type,
                    'brand_model' => $brandModel,
                ]
            );

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

    /**
     * Ekspor seluruh data inventaris perangkat ke Excel (.xlsx).
     */
    public function exportExcel(): Response
    {
        $filename = 'NetFly_Inventaris_Perangkat_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new InventoryDeviceExport(), $filename);
    }
}
