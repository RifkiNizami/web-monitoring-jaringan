<?php

namespace App\Exports;

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

class DevicesReportExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected Collection $devices;
    protected string $customerName;
    protected string $period;

    /**
     * @param array $devices
     * @param string $customerName
     * @param string $period
     */
    public function __construct(array $devices, string $customerName = 'Semua Mitra', string $period = '')
    {
        $this->devices = collect($devices);
        $this->customerName = $customerName;
        $this->period = $period;
    }

    /**
     * Koleksi data perangkat yang diexport
     */
    public function collection(): Collection
    {
        return $this->devices;
    }

    /**
     * Definisi Judul Kolom (Headings)
     */
    public function headings(): array
    {
        return [
            'No',
            'Hostname / Perangkat',
            'Tipe Perangkat',
            'IP Address',
            'Mitra Customer',
            'Lokasi Pemasangan',
            'Posisi Rak / Pole',
            'Status Operasional',
            'Uptime SLA (%)',
            'Durasi Uptime',
            'Latensi RTT (ms)',
        ];
    }

    /**
     * Transformasi pemetaan baris data (Mapping)
     */
    public function map(mixed $row): array
    {
        static $number = 0;
        $number++;

        $device = is_array($row) ? $row : (array) $row;

        return [
            $number,
            $device['hostname'] ?? '-',
            $device['type'] ?? '-',
            $device['ip_address'] ?? '-',
            $device['customer_name'] ?? $this->customerName,
            $device['location'] ?? ($device['rack_location'] ?? '-'),
            $device['rack_position'] ?? ($device['rack_location'] ?? '-'),
            $device['status'] ?? 'Online',
            isset($device['uptime_percent']) ? number_format((float) $device['uptime_percent'], 2) . '%' : '-',
            $device['uptime_text'] ?? ($device['uptime'] ?? '-'),
            isset($device['rtt_ms']) ? $device['rtt_ms'] . ' ms' : (isset($device['last_ping_ms']) ? $device['last_ping_ms'] . ' ms' : '-'),
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
            'E' => 34,  // Mitra Customer
            'F' => 38,  // Lokasi Pemasangan
            'G' => 24,  // Posisi Rak
            'H' => 20,  // Status
            'I' => 16,  // Uptime %
            'J' => 20,  // Durasi Uptime
            'K' => 18,  // Latensi RTT
        ];
    }

    /**
     * Menambahkan Styling & Tebalkan Baris Judul (WithStyles)
     */
    public function styles(Worksheet $sheet): ?array
    {
        $lastRow = $this->devices->count() + 1; // +1 untuk baris judul (row 1)

        // Styling baris 1 (Header/Baris Judul)
        $sheet->getStyle('A1:K1')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A'); // Slate 900
        $sheet->getStyle('A1:K1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Center align kolom No, Tipe, IP, Status, Uptime %, Latensi
        $sheet->getStyle('A1:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H2:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K2:K' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Border tipis untuk semua data
        $sheet->getStyle('A1:K' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');

        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
