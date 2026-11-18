<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FullReportExport implements Export, WithMultipleSheets
{
    protected array $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function sheets(): array
    {
        return [
            new DevicesReportExport(
                $this->reportData['devices'],
                $this->reportData['evaluatedPartner'],
                $this->reportData['formattedPeriod']
            ),
            new TicketsReportExport(
                $this->reportData['tickets'],
                $this->reportData['evaluatedPartner'],
                $this->reportData['formattedPeriod']
            ),
        ];
    }
}
