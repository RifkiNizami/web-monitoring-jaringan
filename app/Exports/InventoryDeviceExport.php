<?php

namespace App\Exports;

use App\Models\Device;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryDeviceExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    /**
     * Mengambil koleksi data inventaris perangkat dari database Eloquent
     */
    public function collection(): Collection
    {
        return Device::orderBy('id', 'asc')->get();
    }

    /**
     * Definisi Judul Kolom (WithHeadings)
     */
    public function headings(): array
    {
        return [
            'No',
            'Hostname / Kode Unit',
            'Tipe Perangkat',
            'Merk / Model',
            'Serial Number',
            'Stok (Unit)',
            'Status Inventaris',
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
            $device->brand_model ?? '-',
            $device->serial_number ?? '-',
            (int) ($device->quantity ?? 0),
            $device->status ?? '-',
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
            'B' => 28,  // Hostname
            'C' => 18,  // Tipe
            'D' => 32,  // Merk / Model
            'E' => 24,  // Serial Number
            'F' => 14,  // Stok
            'G' => 20,  // Status
            'H' => 20,  // Tanggal
        ];
    }

    /**
     * Menambahkan Format & Menebalkan Baris Judul (WithStyles)
     */
    public function styles(Worksheet $sheet): ?array
    {
        $lastRow = max(Device::count() + 1, 2);

        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B'); // Slate 800
        $sheet->getStyle('A1:H1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Alignment kolom numerik dan kode
        $sheet->getStyle('A1:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:H'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Border grid
        $sheet->getStyle('A1:H'.$lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');

        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
