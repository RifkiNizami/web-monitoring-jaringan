<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Daftar seluruh mitra pelanggan NetFly.
     */
    protected array $customers = [
        'all' => 'Semua Mitra Customer (Konsolidasi Jaringan)',
        'mandiri' => 'PT. Bank Mandiri (Persero) Tbk',
        'shopee' => 'PT. Shopee International Indonesia',
        'disdik' => 'Dinas Pendidikan Provinsi DKI Jakarta',
        'xyz' => 'PT. XYZ Teknologi',
        'rs_medika' => 'RS Medika Center',
    ];

    /**
     * Daftar nama bulan kalender Indonesia.
     */
    protected array $months = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Daftar tahun laporan yang tersedia.
     */
    protected array $years = [2024, 2025, 2026, 2027];

    /**
     * Tampilkan antarmuka Executive Report Generator.
     */
    public function index(Request $request): View
    {
        $reportData = $this->resolveReportData($request);

        return view('reports', $reportData);
    }

    /**
     * Ekspor laporan kinerja SLA ke dokumen PDF resmi berstempel digital.
     */
    public function exportPdf(Request $request): Response
    {
        $reportData = $this->resolveReportData($request);

        $pdf = Pdf::loadView('reports.pdf', $reportData)
            ->setPaper('a4', 'portrait')
            ->setWarnings(false)
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        $customerSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $reportData['evaluatedPartner']));
        $filename = sprintf(
            'NetFly_SLA_Report_%s_%s_%s.pdf',
            $customerSlug,
            $reportData['selectedMonthName'],
            $reportData['selectedYear']
        );

        // Jika ada query parameter stream=true, tampilkan langsung di browser (preview)
        if ($request->query('preview') === '1' || $request->query('stream') === '1') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Ekstraksi & agregasi data laporan berdasarkan parameter filter.
     */
    protected function resolveReportData(Request $request): array
    {
        $selectedCustomer = $request->query('customer', 'all');
        if (!array_key_exists($selectedCustomer, $this->customers)) {
            $selectedCustomer = 'all';
        }

        $selectedMonth = (int) $request->query('month', 10);
        if (!array_key_exists($selectedMonth, $this->months)) {
            $selectedMonth = 10;
        }

        $selectedYear = (int) $request->query('year', 2026);
        if (!in_array($selectedYear, $this->years)) {
            $selectedYear = 2026;
        }

        // Master Perangkat Jaringan (Sesuai Mockup UI & Master Inventory)
        $allDevices = [
            [
                'hostname' => 'RTR-CORE-MANDIRI-01',
                'type' => 'Router',
                'ip_address' => '10.200.45.1',
                'status' => 'Online',
                'status_state' => 'online',
                'customer_key' => 'mandiri',
                'customer_name' => 'PT. Bank Mandiri (Persero) Tbk',
                'location' => 'Plaza Mandiri Lt. 12, Gatot Subroto',
                'rack_position' => 'Rack Core-A, U-42',
                'uptime_percent' => 99.92,
                'uptime_text' => '45 hari 12 jam',
                'rtt_ms' => 1.8,
            ],
            [
                'hostname' => 'SW-AGG-BKS-04',
                'type' => 'Switch',
                'ip_address' => '10.200.88.10',
                'status' => 'Online',
                'status_state' => 'online',
                'customer_key' => 'shopee',
                'customer_name' => 'PT. Shopee International Indonesia',
                'location' => 'Warehouse Cikarang Hub, Bekasi',
                'rack_position' => 'Rack BKS-02, Slot 12U',
                'uptime_percent' => 97.45,
                'uptime_text' => '28 hari 04 jam',
                'rtt_ms' => 4.2,
            ],
            [
                'hostname' => 'AP-OUTDOOR-DISDIK-01',
                'type' => 'Access Point',
                'ip_address' => '10.200.120.25',
                'status' => 'Warning',
                'status_state' => 'warning',
                'customer_key' => 'disdik',
                'customer_name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'location' => 'Pole A-Disdik Outdoor Kuningan',
                'rack_position' => 'Outdoor Pole Tower',
                'uptime_percent' => 98.15,
                'uptime_text' => '12 hari 08 jam',
                'rtt_ms' => 24.5,
            ],
        ];

        // Master Riwayat Tiket Insiden & Layanan
        $allTickets = [
            [
                'ticket_number' => 'INC-2026-1024',
                'subject' => 'Packet Loss Tinggi (>14%) Back...',
                'full_subject' => 'Packet Loss Tinggi (>14%) Backbone Disdik Jakarta Pusat',
                'technician' => 'Fajar Ramadhan',
                'status' => 'In Progress',
                'status_type' => 'in_progress',
                'priority' => 'P2 - High',
                'priority_level' => 'high',
                'customer_key' => 'disdik',
                'customer_name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'report_date' => '10 Okt 2026, 04:06 WIB',
                'duration' => '1 Jam 15 Menit',
            ],
            [
                'ticket_number' => 'INC-2026-1030',
                'subject' => 'SwitchAgg-Bks_04 Terputus (Si...',
                'full_subject' => 'SwitchAgg-Bks_04 Terputus (Site Warehouse Bekasi)',
                'technician' => 'Rian Hidayat',
                'status' => 'Assigned',
                'status_type' => 'assigned',
                'priority' => 'P1 - Critical',
                'priority_level' => 'critical',
                'customer_key' => 'shopee',
                'customer_name' => 'PT. Shopee International Indonesia',
                'report_date' => '10 Okt 2026, 08:30 WIB',
                'duration' => '2 Jam 40 Menit',
            ],
            [
                'ticket_number' => 'REQ-2026-0882',
                'subject' => 'Permintaan Bandwidth On-Demand...',
                'full_subject' => 'Permintaan Bandwidth On-Demand (+500 Mbps) Flash Sale',
                'technician' => '-',
                'status' => 'Open',
                'status_type' => 'open',
                'priority' => 'P3 - Medium',
                'priority_level' => 'medium',
                'customer_key' => 'shopee',
                'customer_name' => 'PT. Shopee International Indonesia',
                'report_date' => '10 Okt 2026, 09:15 WIB',
                'duration' => '35 Menit',
            ],
            [
                'ticket_number' => 'INC-2026-0955',
                'subject' => 'High Latency Spike (>120ms) Co...',
                'full_subject' => 'High Latency Spike (>120ms) Core Mandiri Link 2',
                'technician' => 'NOC Administrator',
                'status' => 'Resolved',
                'status_type' => 'resolved',
                'priority' => 'P2 - High',
                'priority_level' => 'high',
                'customer_key' => 'mandiri',
                'customer_name' => 'PT. Bank Mandiri (Persero) Tbk',
                'report_date' => '10 Okt 2026, 11:20 WIB',
                'duration' => '38 Menit',
            ],
        ];

        // Filter Perangkat & Tiket jika customer spesifik dipilih
        if ($selectedCustomer === 'all') {
            $filteredDevices = $allDevices;
            $filteredTickets = $allTickets;
            $evaluatedPartner = 'Semua Mitra Enterprise NetFly';
            $targetSlaMin = '99.0%';
            $actualSla = 98.5;
            $slaStatusLabel = 'Tercapai';
            $slaStatusColor = 'emerald';
            $slaCommitmentText = 'Memenuhi Komitmen Kontrak SLA';
            $avgResolutionTime = '38 Menit';
        } else {
            $filteredDevices = array_values(array_filter($allDevices, fn($d) => $d['customer_key'] === $selectedCustomer));
            $filteredTickets = array_values(array_filter($allTickets, fn($t) => $t['customer_key'] === $selectedCustomer));
            $evaluatedPartner = $this->customers[$selectedCustomer];

            // Kalkulasi dinamis per mitra
            if ($selectedCustomer === 'mandiri') {
                $targetSlaMin = '99.8%';
                $actualSla = 99.92;
                $slaStatusLabel = 'Tercapai';
                $slaStatusColor = 'emerald';
                $slaCommitmentText = 'Memenuhi Komitmen Kontrak SLA (Tier 1)';
                $avgResolutionTime = '38 Menit';
            } elseif ($selectedCustomer === 'shopee') {
                $targetSlaMin = '99.5%';
                $actualSla = 97.45;
                $slaStatusLabel = 'Deviasi Terdeteksi';
                $slaStatusColor = 'amber';
                $slaCommitmentText = 'Dalam Penyesuaian Restitusi SLA Insiden';
                $avgResolutionTime = '2 Jam 40 Menit';
            } elseif ($selectedCustomer === 'disdik') {
                $targetSlaMin = '99.0%';
                $actualSla = 98.15;
                $slaStatusLabel = 'Tercapai';
                $slaStatusColor = 'emerald';
                $slaCommitmentText = 'Memenuhi Toleransi Operasional SLA';
                $avgResolutionTime = '1 Jam 15 Menit';
            } else {
                $targetSlaMin = '99.0%';
                $actualSla = 99.10;
                $slaStatusLabel = 'Tercapai';
                $slaStatusColor = 'emerald';
                $slaCommitmentText = 'Memenuhi Komitmen Kontrak SLA';
                $avgResolutionTime = '45 Menit';
            }
        }

        $totalDevices = count($filteredDevices);
        $normalDevices = count(array_filter($filteredDevices, fn($d) => $d['status_state'] === 'online'));
        $warningDevices = count(array_filter($filteredDevices, fn($d) => $d['status_state'] === 'warning'));
        $offlineDevices = count(array_filter($filteredDevices, fn($d) => $d['status_state'] === 'offline'));

        $totalTickets = count($filteredTickets);
        $resolvedTickets = count(array_filter($filteredTickets, fn($t) => in_array($t['status_type'], ['resolved', 'closed'])));

        // Nomor Registrasi Dokumen Resmi
        $reportDocumentNumber = sprintf(
            'NETFLY/SLA-%s/%02d/%04d',
            strtoupper($selectedCustomer === 'all' ? 'CONS' : substr($selectedCustomer, 0, 4)),
            $selectedMonth,
            $selectedYear
        );

        $selectedMonthName = $this->months[$selectedMonth];
        $formattedPeriod = $selectedMonthName . ' ' . $selectedYear;

        return [
            'customers' => $this->customers,
            'months' => $this->months,
            'years' => $this->years,
            'selectedCustomer' => $selectedCustomer,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedMonthName' => $selectedMonthName,
            'formattedPeriod' => $formattedPeriod,
            'evaluatedPartner' => $evaluatedPartner,
            'reportDocumentNumber' => $reportDocumentNumber,
            'actualSla' => $actualSla,
            'targetSlaMin' => $targetSlaMin,
            'slaStatusLabel' => $slaStatusLabel,
            'slaStatusColor' => $slaStatusColor,
            'slaCommitmentText' => $slaCommitmentText,
            'totalTickets' => $totalTickets,
            'resolvedTickets' => $resolvedTickets,
            'avgResolutionTime' => $avgResolutionTime,
            'totalDevices' => $totalDevices,
            'normalDevices' => $normalDevices,
            'warningDevices' => $warningDevices,
            'offlineDevices' => $offlineDevices,
            'devices' => $filteredDevices,
            'tickets' => $filteredTickets,
            'generatedAt' => now()->format('d F Y, H:i:s') . ' WIB',
        ];
    }
}
