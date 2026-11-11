<?php

namespace App\Exports;

use App\Models\Device;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class InventoryDeviceExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    /**
     * Mengambil koleksi data Device dari database Eloquent
     */
    public function collection(): Collection
    {
        return Device::with(['customer', 'project'])->get();
    }

    /**
     * Definisi Judul Kolom (WithHeadings)
     */
    public function headings(): array
    {
        return [
            'No',
            'Hostname',
            'Tipe Perangkat',
            'IP Address',
            'MAC Address',
            'Pelanggan (Customer)',
            'Project Jaringan',
            'Lokasi Rak',
            'Status Operasional',
            'Uptime',
            'Latensi (ms)',
            'Packet Loss (%)',
            'CPU Load (%)',
            'RAM Load (%)',
            'Tanggal Registrasi',
        ];
    }

    /**
     * Pemetaan Baris Data Model (WithMapping)
     */
    public function map(mixed $device): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $device->hostname ?? '-',
            $device->type ?? '-',
            $device->ip_address ?? '-',
            $device->mac_address ?? '-',
            $device->customer->name ?? '-',
            $device->project->name ?? '-',
            $device->rack_location ?? '-',
            $device->status ?? '-',
            $device->uptime ?? '-',
            ($device->last_ping_ms ?? 0) . ' ms',
            number_format((float) ($device->packet_loss ?? 0), 2) . '%',
            number_format((float) ($device->cpu_load ?? 0), 1) . '%',
            number_format((float) ($device->ram_load ?? 0), 1) . '%',
            $device->created_at ? $device->created_at->format('d/m/Y H:i') : '-',
        ];
    }

    /**
     * Mengatur Lebar Kolom (WithColumnWidths)
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 26,  // Hostname
            'C' => 16,  // Tipe
            'D' => 18,  // IP Address
            'E' => 20,  // MAC Address
            'F' => 32,  // Customer
            'G' => 32,  // Project
            'H' => 22,  // Lokasi Rak
            'I' => 24,  // Status
            'J' => 18,  // Uptime
            'K' => 14,  // Latensi
            'L' => 16,  // Packet Loss
            'M' => 14,  // CPU
            'N' => 14,  // RAM
            'O' => 20,  // Tanggal
        ];
    }

    /**
     * Menambahkan Format & Menebalkan Baris Judul (WithStyles)
     */
    public function styles(Worksheet $sheet): ?array
    {
        $lastRow = max(Device::count() + 1, 2);

        $sheet->getStyle('A1:O1')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:O1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B'); // Slate 800
        $sheet->getStyle('A1:O1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Alignment kolom numerik dan kode
        $sheet->getStyle('A1:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K2:N' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('O2:O' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Border grid
        $sheet->getStyle('A1:O' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');

        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
