<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketsReportExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected Collection $tickets;

    protected string $customerName;

    protected string $period;

    public function __construct(array $tickets, string $customerName = 'Semua Mitra', string $period = '')
    {
        $this->tickets = collect($tickets);
        $this->customerName = $customerName;
        $this->period = $period;
    }

    public function title(): string
    {
        return 'Riwayat Tiket & Insiden';
    }

    public function collection(): Collection
    {
        return $this->tickets;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nomor Tiket',
            'Subjek / Ringkasan Gangguan',
            'Prioritas',
            'Teknisi NOC',
            'Status Penanganan',
            'Durasi Penanganan',
            'Waktu Lapor',
        ];
    }

    public function map(mixed $row): array
    {
        $ticket = is_array($row) ? $row : (array) $row;

        return [
            $ticket['id'] ?? '-',
            $ticket['ticket_number'] ?? '-',
            $ticket['full_subject'] ?? ($ticket['subject'] ?? '-'),
            $ticket['priority'] ?? 'P2 - High',
            $ticket['technician'] ?? '-',
            $ticket['status'] ?? 'Open',
            $ticket['duration'] ?? '-',
            $ticket['report_date'] ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 18,  // Nomor Tiket
            'C' => 45,  // Subjek
            'D' => 16,  // Prioritas
            'E' => 24,  // Teknisi
            'F' => 18,  // Status
            'G' => 20,  // Durasi
            'H' => 22,  // Waktu Lapor
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        $lastRow = max($this->tickets->count() + 1, 2);

        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A'); // Slate 900
        $sheet->getStyle('A1:H1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->getStyle('A1:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:B'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:H'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('A1:H'.$lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');

        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}
