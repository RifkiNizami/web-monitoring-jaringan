<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Import;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FullReportImport implements Import, WithMultipleSheets, SkipsUnknownSheets
{
    use Importable;

    protected DevicesReportImport $devicesImport;
    protected TicketsReportImport $ticketsImport;
    protected array $actualSheets = [];

    public function __construct(array $actualSheets = [])
    {
        $this->devicesImport = new DevicesReportImport();
        $this->ticketsImport = new TicketsReportImport();
        $this->actualSheets = $actualSheets;
    }

    public function sheets(): array
    {
        $map = [];

        // Jika daftar nama sheet dari file diberikan, cocokkan secara fleksibel
        if (! empty($this->actualSheets)) {
            foreach ($this->actualSheets as $name) {
                $lower = strtolower($name);
                if (str_contains($lower, 'perangkat') || str_contains($lower, 'device')) {
                    $map[$name] = $this->devicesImport;
                } elseif (str_contains($lower, 'tiket') || str_contains($lower, 'ticket') || str_contains($lower, 'insiden')) {
                    $map[$name] = $this->ticketsImport;
                }
            }
        }

        // Default mapping standar ekspor NetFly
        if (! isset($map['Daftar Perangkat'])) {
            $map['Daftar Perangkat'] = $this->devicesImport;
        }
        if (! isset($map['Riwayat Tiket & Insiden'])) {
            $map['Riwayat Tiket & Insiden'] = $this->ticketsImport;
        }

        // Cadangan jika pengguna hanya menyisakan sheet bawaan seperti "Sheet1", "Sheet2"
        if (empty($map)) {
            $map[0] = $this->devicesImport;
        }

        return $map;
    }

    public function onUnknownSheet(string|int $sheetName): void
    {
        // Abaikan sheet yang tidak dikenali dengan aman tanpa melempar exception
    }

    public function getDevicesImport(): DevicesReportImport
    {
        return $this->devicesImport;
    }

    public function getTicketsImport(): TicketsReportImport
    {
        return $this->ticketsImport;
    }
}
