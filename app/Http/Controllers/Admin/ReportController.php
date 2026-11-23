<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FullReportExport;
use App\Http\Controllers\Controller;
use App\Imports\FullReportImport;
use App\Models\Customer;
use App\Models\Device;
use App\Models\MonitoringAlert;
use App\Models\MonitoringDevice;
use App\Models\NetworkSlaMetric;
use App\Models\Project;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Daftar default opsi filter mitra pelanggan NetFly.
     */
    protected array $customers = [
        'all' => 'Semua Mitra Customer (Konsolidasi Jaringan)',
    ];

    /**
     * Sinkronisasi daftar customer dinamis murni dari tabel database customers.
     */
    protected function getCustomerOptions(): array
    {
        $options = [
            'all' => 'Semua Mitra Customer (Konsolidasi Jaringan)',
        ];

        foreach (Customer::orderBy('name', 'asc')->get() as $cust) {
            $custName = $cust->name ?? $cust->company_name ?? '';
            if (empty($custName)) {
                continue;
            }
            $code = $cust->code ?? $cust->customer_code;
            $label = $custName.($code ? " ({$code})" : '');
            $options[(string) $cust->id] = $label;
        }

        return $options;
    }

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

        return view('admin.reports', $reportData);
    }

    /**
     * Endpoint REST API Laporan Operasional untuk Aplikasi Mobile NetFly.
     * Menarik data riil perangkat, tiket insiden, dan log peringatan dari database web.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $reportData = $this->resolveReportData($request);

        $devices = $reportData['devices'];
        // Jika untuk customer spesifik belum ada perangkat ber-customer_id, tampilkan perangkat monitoring aktif di web
        if (empty($devices)) {
            $allData = $this->resolveReportData(new Request([
                'customer' => 'all',
                'month' => $reportData['selectedMonth'],
                'year' => $reportData['selectedYear'],
            ]));
            $devices = $allData['devices'];
        }

        $totalDevices = count($devices);
        $normalDevices = count(array_filter($devices, fn ($d) => ($d['status_state'] ?? '') === 'online'));
        $warningDevices = count(array_filter($devices, fn ($d) => ($d['status_state'] ?? '') === 'warning'));
        $offlineDevices = count(array_filter($devices, fn ($d) => ($d['status_state'] ?? '') === 'offline'));

        $targetCustId = $reportData['selectedCustomer'] !== 'all' ? (int) $reportData['selectedCustomer'] : null;

        $alertsQuery = MonitoringAlert::orderBy('id', 'desc');
        $custAlerts = $targetCustId
            ? (clone $alertsQuery)->where('customer_id', $targetCustId)->limit(15)->get()
            : $alertsQuery->limit(15)->get();

        $alertsList = $custAlerts->map(function (MonitoringAlert $a) {
            return [
                'id' => (string) $a->id,
                'host' => $a->host ?: '-',
                'customer_name' => $a->customer_name ?: '-',
                'severity' => $a->severity ?: 'warning',
                'type' => $a->type ?: 'Notifikasi Monitoring',
                'message' => $a->message ?: '-',
                'is_resolved' => (bool) $a->is_resolved,
                'created_at' => $a->created_at ? $a->created_at->format('d M Y, H:i').' WIB' : '-',
            ];
        })->values()->all();

        $projectsQuery = Project::orderBy('id', 'desc');
        $custProjects = $targetCustId
            ? (clone $projectsQuery)->where('customer_id', $targetCustId)->get()
            : $projectsQuery->limit(10)->get();

        $projectsList = $custProjects->map(function (Project $p) {
            return [
                'id' => (string) $p->id,
                'name' => $p->name ?: '-',
                'branch_location' => $p->branch_location ?: '-',
                'service_type' => $p->service_type ?: '-',
                'status' => $p->status ?: '-',
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'report' => [
                'document_number' => str_replace('SLA-', 'RPT-', $reportData['reportDocumentNumber']),
                'evaluated_partner' => $reportData['evaluatedPartner'],
                'selected_customer' => $reportData['selectedCustomer'],
                'selected_month' => $reportData['selectedMonth'],
                'selected_month_name' => $reportData['selectedMonthName'],
                'selected_year' => $reportData['selectedYear'],
                'formatted_period' => $reportData['formattedPeriod'],
                'generated_at' => $reportData['generatedAt'],
                'summary' => [
                    'total_tickets' => $reportData['totalTickets'],
                    'resolved_tickets' => $reportData['resolvedTickets'],
                    'active_tickets' => max(0, $reportData['totalTickets'] - $reportData['resolvedTickets']),
                    'avg_resolution_time' => $reportData['totalTickets'] > 0 ? $reportData['avgResolutionTime'] : '-',
                    'total_devices' => $totalDevices,
                    'normal_devices' => $normalDevices,
                    'warning_devices' => $warningDevices,
                    'offline_devices' => $offlineDevices,
                    'total_alerts' => count($alertsList),
                    'unresolved_alerts' => count(array_filter($alertsList, fn ($a) => ! $a['is_resolved'])),
                ],
                'devices' => array_values($devices),
                'tickets' => array_values($reportData['tickets']),
                'alerts' => $alertsList,
                'projects' => $projectsList,
            ],
        ]);
    }

    /**
     * Ekspor laporan kinerja SLA ke dokumen PDF resmi berstempel digital.
     */
    public function exportPdf(Request $request): Response
    {
        $reportData = $this->resolveReportData($request);

        $pdf = Pdf::loadView('admin.reports.pdf', $reportData)
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

        if ($request->query('preview') === '1' || $request->query('stream') === '1') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Ekspor data laporan lengkap (perangkat & tiket) ke format spreadsheet Excel (.xlsx).
     */
    public function exportExcel(Request $request): Response
    {
        $reportData = $this->resolveReportData($request);

        $customerSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $reportData['evaluatedPartner']));
        $filename = sprintf(
            'NetFly_Report_%s_%s_%s.xlsx',
            $customerSlug,
            $reportData['selectedMonthName'],
            $reportData['selectedYear']
        );

        $export = new FullReportExport($reportData);

        return Excel::download(
            $export,
            $filename
        );
    }

    /**
     * Impor kembali data laporan Excel yang telah diedit secara manual.
     */
    public function importExcel(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'], // Maks 10MB
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.file' => 'Unggahan harus berupa file yang valid.',
            'file.mimes' => 'Format file harus berupa spreadsheet Excel (.xlsx, .xls) atau CSV.',
            'file.max' => 'Ukuran file Excel maksimal 10 MB.',
        ]);

        try {
            $uploadedFile = $request->file('file');
            $realPath = $uploadedFile->getRealPath();

            // Deteksi daftar sheet secara otomatis
            $actualSheetNames = [];
            try {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $actualSheetNames = $reader->listWorksheetNames($realPath);
            } catch (\Throwable $e) {
                // Abaikan jika bukan format xlsx standar
            }

            $import = new FullReportImport($actualSheetNames);
            Excel::import($import, $uploadedFile);

            $devicesImport = $import->getDevicesImport();
            $ticketsImport = $import->getTicketsImport();

            $totalUpdated = $devicesImport->updatedCount + $ticketsImport->updatedCount;
            $totalCreated = $devicesImport->importedCount + $ticketsImport->importedCount;

            $message = sprintf(
                'Data laporan berhasil diimpor: %d perangkat (%d diperbarui, %d baru) dan %d tiket (%d diperbarui, %d baru).',
                $devicesImport->updatedCount + $devicesImport->importedCount,
                $devicesImport->updatedCount,
                $devicesImport->importedCount,
                $ticketsImport->updatedCount + $ticketsImport->importedCount,
                $ticketsImport->updatedCount,
                $ticketsImport->importedCount
            );

            // Teruskan parameter filter yang sedang aktif agar user langsung melihat hasilnya
            $redirectParams = [];
            if ($request->filled('customer')) {
                $redirectParams['customer'] = $request->input('customer');
            }
            if ($request->filled('month')) {
                $redirectParams['month'] = $request->input('month');
            }
            if ($request->filled('year')) {
                $redirectParams['year'] = $request->input('year');
            }

            return redirect()->route('reports.index', $redirectParams)->with('success', $message);
        } catch (\Throwable $e) {
            return redirect()->route('reports.index')->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Ekstraksi & agregasi data laporan dinamis dari database berdasarkan parameter filter.
     */
    protected function resolveReportData(Request $request): array
    {
        $customerOptions = $this->getCustomerOptions();
        $rawCustomer = (string) $request->query('customer', 'all');

        $allDbCustomers = Customer::all();

        // Resolusi pilihan customer dari parameter URL (bisa berupa ID angka, slug code, atau slug nama)
        $matchedCustomer = null;
        if ($rawCustomer !== 'all' && trim($rawCustomer) !== '') {
            $cleanQuery = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $rawCustomer));

            $matchedCustomer = $allDbCustomers->first(function ($c) use ($rawCustomer, $cleanQuery) {
                if ((string) $c->id === (string) $rawCustomer) {
                    return true;
                }
                if (empty($cleanQuery)) {
                    return false;
                }
                $cCodeClean = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $c->code ?? $c->customer_code ?? ''));
                if (! empty($cCodeClean) && ($cCodeClean === $cleanQuery || str_contains($cCodeClean, $cleanQuery) || str_contains($cleanQuery, $cCodeClean))) {
                    return true;
                }
                $cNameClean = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $c->name ?? ''));
                if (! empty($cNameClean) && ($cNameClean === $cleanQuery || str_contains($cNameClean, $cleanQuery) || str_contains($cleanQuery, $cNameClean))) {
                    return true;
                }

                return false;
            });
        }

        if ($matchedCustomer) {
            $selectedCustomer = (string) $matchedCustomer->id;
            $targetCustomerId = (int) $matchedCustomer->id;
            $evaluatedPartner = $matchedCustomer->name;
        } else {
            $selectedCustomer = 'all';
            $targetCustomerId = null;
            $evaluatedPartner = 'Semua Mitra Enterprise NetFly';
        }

        $currentMonth = (int) now()->month;
        $currentYear = (int) now()->year;

        $selectedMonth = (int) $request->query('month', $currentMonth);
        if (! array_key_exists($selectedMonth, $this->months)) {
            $selectedMonth = $currentMonth;
        }

        $selectedYear = (int) $request->query('year', $currentYear);
        if (! in_array($selectedYear, $this->years)) {
            $selectedYear = $currentYear;
        }

        // 1. TARIK DATA PERANGKAT DINAMIS DARI DATABASE (Model Device)
        $dbDevices = Device::with('customer')->orderBy('id', 'asc')->get();
        $allDevices = [];

        foreach ($dbDevices as $d) {
            $resolvedCust = $d->customer;
            if (! $resolvedCust && ! empty($d->customer_id)) {
                $resolvedCust = $allDbCustomers->firstWhere('id', $d->customer_id);
            }
            if (! $resolvedCust && $allDbCustomers->isNotEmpty()) {
                $hostLower = strtolower($d->hostname ?? '');
                $snmpLower = strtolower($d->snmp_community ?? '');
                $rackLower = strtolower($d->rack_location ?? '');
                $resolvedCust = $allDbCustomers->first(function ($c) use ($hostLower, $snmpLower, $rackLower) {
                    $cName = strtolower($c->name ?? '');
                    $cCode = strtolower($c->code ?? $c->customer_code ?? '');

                    return (! empty($cName) && (str_contains($hostLower, $cName) || str_contains($snmpLower, $cName) || str_contains($rackLower, $cName)))
                        || (! empty($cCode) && (str_contains($hostLower, $cCode) || str_contains($snmpLower, $cCode)));
                });
            }

            $cId = $resolvedCust?->id ?? $d->customer_id;
            $custName = $resolvedCust?->name ?? ($cId ? '-' : '-');

            $rawStatus = strtolower($d->status ?? '');
            $statusState = match ($rawStatus) {
                'tersedia', 'online', 'available', 'terpakai', 'in_use' => 'online',
                'warning', 'perhatian', 'maintenance' => 'warning',
                default => 'offline',
            };
            $statusLabel = match ($statusState) {
                'online' => 'Online',
                'warning' => 'Warning',
                default => 'Offline',
            };

            $lossVal = (float) ($d->packet_loss ?? 0.0);
            $uptimeVal = match ($statusState) {
                'online' => round(max(0.0, 100.0 - $lossVal), 2),
                'warning' => round(max(0.0, 90.0 - $lossVal), 2),
                default => 0.0,
            };

            $allDevices[] = [
                'id' => $d->id,
                'hostname' => $d->hostname,
                'type' => $d->type ?: 'Router',
                'brand_model' => $d->brand_model ?: ($d->type ?: '-'),
                'serial_number' => $d->serial_number ?: '-',
                'ip_address' => $d->ip_address ?: '-',
                'status' => $statusLabel,
                'status_state' => $statusState,
                'customer_id' => $cId,
                'customer_key' => $cId ? (string) $cId : 'core',
                'customer_name' => $custName,
                'location' => $d->rack_location ?: '-',
                'rack_position' => $d->rack_location ?: '-',
                'uptime_percent' => $uptimeVal,
                'uptime_text' => $d->uptime ?: ($statusState === 'online' ? 'Online' : 'Offline'),
                'rtt_ms' => $d->last_ping_ms !== null ? (float) $d->last_ping_ms : null,
                'packet_loss' => (string) ($d->packet_loss ?? '0.00').'%',
                'rx_rate' => $d->rx_rate ?: '0.0 Mbps',
                'tx_rate' => $d->tx_rate ?: '0.0 Mbps',
                'cpu_load' => (float) ($d->cpu_load ?? 0.0),
                'ram_load' => (float) ($d->ram_load ?? 0.0),
            ];
        }

        // Sertakan juga node dari MonitoringDevice jika belum ada
        foreach (MonitoringDevice::all() as $m) {
            if (! collect($allDevices)->contains('hostname', $m->hostname)) {
                $st = strtolower($m->status ?? 'online');
                $state = ($st === 'online' || $st === 'tersedia') ? 'online' : ($st === 'warning' ? 'warning' : 'offline');

                $mCustResolved = null;
                if (! empty($m->customer_id)) {
                    $mCustResolved = $allDbCustomers->firstWhere('id', $m->customer_id);
                }
                if (! $mCustResolved && $allDbCustomers->isNotEmpty()) {
                    $mCustLower = strtolower($m->customer ?? '');
                    $mHostLower = strtolower($m->hostname ?? '');
                    $mCustResolved = $allDbCustomers->first(function ($c) use ($mCustLower, $mHostLower) {
                        $cName = strtolower($c->name ?? '');
                        $cCode = strtolower($c->code ?? $c->customer_code ?? '');

                        return (! empty($cName) && (str_contains($mCustLower, $cName) || str_contains($mHostLower, $cName)))
                            || (! empty($cCode) && str_contains($mHostLower, $cCode));
                    });
                }

                $lossValM = (float) ($m->packet_loss ?? 0.0);
                $uptimeValM = match ($state) {
                    'online' => round(max(0.0, 100.0 - $lossValM), 2),
                    'warning' => round(max(0.0, 90.0 - $lossValM), 2),
                    default => 0.0,
                };

                $allDevices[] = [
                    'id' => $m->id,
                    'hostname' => $m->hostname,
                    'type' => $m->unit_type ?: 'Router',
                    'brand_model' => $m->model ?: ($m->unit_type ? $m->unit_type.' Node' : '-'),
                    'serial_number' => '-',
                    'ip_address' => $m->ip_address ?: '-',
                    'status' => ucfirst($state),
                    'status_state' => $state,
                    'customer_id' => $mCustResolved?->id,
                    'customer_key' => $mCustResolved ? (string) $mCustResolved->id : 'monitoring',
                    'customer_name' => $mCustResolved?->name ?? ($m->customer ?: '-'),
                    'location' => $m->rack_location ?: '-',
                    'rack_position' => $m->rack_location ?: '-',
                    'uptime_percent' => $uptimeValM,
                    'uptime_text' => $m->uptime ?: ($state === 'online' ? 'Online' : 'Offline'),
                    'rtt_ms' => $m->last_ping_ms !== null ? (float) $m->last_ping_ms : null,
                    'packet_loss' => (string) ($m->packet_loss ?? '0').'%',
                    'rx_rate' => $m->rx_mbps ?: '0.0 Mbps',
                    'tx_rate' => $m->tx_mbps ?: '0.0 Mbps',
                    'cpu_load' => (float) ($m->cpu_load ?? 0.0),
                    'ram_load' => (float) ($m->ram_load ?? 0.0),
                ];
            }
        }

        // 2. TARIK DATA TIKET INSIDEN DINAMIS DARI DATABASE (Model Ticket)
        $dbTickets = Ticket::with('customer')->orderBy('id', 'desc')->get();
        $allTickets = [];

        foreach ($dbTickets as $t) {
            $resolvedCust = $t->customer;
            if (! $resolvedCust && ! empty($t->customer_id)) {
                $resolvedCust = $allDbCustomers->firstWhere('id', $t->customer_id);
            }
            if (! $resolvedCust && $allDbCustomers->isNotEmpty()) {
                $clientLower = strtolower($t->client_name ?? '');
                $subjLower = strtolower($t->subject ?? '');
                $resolvedCust = $allDbCustomers->first(function ($c) use ($clientLower, $subjLower) {
                    $cName = strtolower($c->name ?? '');
                    $cCode = strtolower($c->code ?? $c->customer_code ?? '');

                    return (! empty($cName) && (str_contains($clientLower, $cName) || str_contains($cName, $clientLower) || str_contains($subjLower, $cName)))
                        || (! empty($cCode) && (str_contains($clientLower, $cCode) || str_contains($subjLower, $cCode)));
                });
            }

            $cId = $resolvedCust?->id ?? $t->customer_id;
            $custName = $resolvedCust?->name ?? ($t->client_name ?: '-');

            $stLower = strtolower($t->status ?? '');
            $statusType = match (true) {
                str_contains($stLower, 'progress') => 'in_progress',
                str_contains($stLower, 'assigned') => 'assigned',
                str_contains($stLower, 'resolved') || str_contains($stLower, 'selesai') => 'resolved',
                default => 'open',
            };

            $statusText = match ($statusType) {
                'in_progress' => 'In Progress',
                'assigned' => 'Assigned',
                'resolved' => 'Resolved',
                default => 'Open',
            };

            $tDate = $t->reported_at ?? $t->created_at;

            $durationText = '-';
            $durationMinutes = 0;
            if ($t->reported_at) {
                $end = $t->resolved_at ?? (in_array($statusType, ['resolved', 'closed'], true) ? now() : null);
                if ($end) {
                    $diffMinutes = max(1, (int) $t->reported_at->diffInMinutes($end));
                    $durationMinutes = $diffMinutes;
                    $hours = floor($diffMinutes / 60);
                    $mins = $diffMinutes % 60;
                    $durationText = $hours > 0 ? "{$hours} Jam {$mins} Menit" : "{$mins} Menit";
                } else {
                    $durationText = 'Dalam Penanganan';
                }
            }

            $allTickets[] = [
                'id' => $t->id,
                'ticket_number' => $t->ticket_number,
                'subject' => Str::limit($t->subject, 32),
                'full_subject' => $t->subject,
                'technician' => $t->assigned_technician ?: '-',
                'status' => $statusText,
                'status_type' => $statusType,
                'priority' => $t->priority ?? 'P3 - Medium',
                'priority_level' => $t->priority_level ?? 'medium',
                'customer_id' => $cId,
                'customer_key' => $cId ? (string) $cId : 'general',
                'customer_name' => $custName,
                'date_raw' => $tDate,
                'report_date' => $tDate ? $tDate->format('d M Y, H:i').' WIB' : '-',
                'duration' => $durationText,
                'duration_minutes' => $durationMinutes,
            ];
        }

        // 3. FILTERING PER MITRA & PERIODE
        $periodTickets = array_values(array_filter($allTickets, function ($t) use ($selectedYear, $selectedMonth) {
            if (empty($t['date_raw'])) {
                return false;
            }

            return (int) $t['date_raw']->year === (int) $selectedYear && (int) $t['date_raw']->month === (int) $selectedMonth;
        }));

        $sourceTickets = $periodTickets;

        if ($selectedCustomer === 'all') {
            $filteredDevices = $allDevices;
            $filteredTickets = $sourceTickets;
            $evaluatedPartner = 'Semua Mitra Enterprise NetFly';
        } else {
            $filteredDevices = array_values(array_filter($allDevices, function ($d) use ($targetCustomerId) {
                return ! empty($d['customer_id']) && (int) $d['customer_id'] === (int) $targetCustomerId;
            }));

            $filteredTickets = array_values(array_filter($sourceTickets, function ($t) use ($targetCustomerId) {
                return ! empty($t['customer_id']) && (int) $t['customer_id'] === (int) $targetCustomerId;
            }));
        }

        // 4. METRIK & KALKULASI DINAMIS
        $totalDevices = count($filteredDevices);
        $normalDevices = count(array_filter($filteredDevices, fn ($d) => $d['status_state'] === 'online'));
        $warningDevices = count(array_filter($filteredDevices, fn ($d) => $d['status_state'] === 'warning'));
        $offlineDevices = count(array_filter($filteredDevices, fn ($d) => $d['status_state'] === 'offline'));

        $totalTickets = count($filteredTickets);
        $resolvedTickets = count(array_filter($filteredTickets, fn ($t) => in_array($t['status_type'], ['resolved', 'closed'])));

        // 5. KALKULASI TIMELINE UPTIME HARIAN (HISTORIS DINAMIS DARI DATABASE)
        $startOfMonth = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        $endOfMonth = Carbon::create($selectedYear, $selectedMonth, $daysInMonth)->endOfDay();

        // Tarik data SLA harian dari tabel network_sla_metrics
        $slaMetrics = NetworkSlaMetric::whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->date)->format('Y-m-d'));

        // Tarik tiket insiden untuk bulan dan pelanggan yang difilter
        $ticketsInMonth = Ticket::whereBetween('reported_at', [$startOfMonth, $endOfMonth])
            ->when($targetCustomerId, fn ($q) => $q->where('customer_id', $targetCustomerId))
            ->get();

        $dailyTimeline = [];
        $totalDowntimeMinutes = 0;
        $totalOutageDays = 0;
        $totalDegradedDays = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateObj = Carbon::create($selectedYear, $selectedMonth, $day);
            $dateKey = $dateObj->format('Y-m-d');
            $metric = $slaMetrics->get($dateKey);

            // Filter tiket insiden spesifik pada hari ini
            $dayTickets = $ticketsInMonth->filter(function ($t) use ($dateObj) {
                return $t->reported_at && $t->reported_at->isSameDay($dateObj);
            });

            $hasCriticalTicket = $dayTickets->contains(function ($t) {
                $p = strtolower($t->priority_level ?? $t->priority ?? '');

                return str_contains($p, 'p1') || str_contains($p, 'crit') || str_contains($p, 'high');
            });

            if ($dayTickets->count() > 0) {
                $firstTicket = $dayTickets->first();
                $ticketCount = $dayTickets->count();
                $downtimeMin = 0;
                foreach ($dayTickets as $dt) {
                    if ($dt->reported_at && $dt->resolved_at) {
                        $downtimeMin += max(15, (int) $dt->reported_at->diffInMinutes($dt->resolved_at));
                    } else {
                        $downtimeMin += 30;
                    }
                }
                $uptime = max(0.0, round((1440 - min(1440, $downtimeMin)) / 1440 * 100, 2));

                if ($hasCriticalTicket || $uptime < 98.0) {
                    $status = 'outage';
                    $statusLabel = 'Padam / Major Outage';
                    $barColor = 'bg-red-500 hover:bg-red-600';
                } else {
                    $status = 'warning';
                    $statusLabel = 'Degradasi / Flap';
                    $barColor = 'bg-amber-400 hover:bg-amber-500';
                }
                $notes = "Tiket #{$firstTicket->ticket_number}: {$firstTicket->subject}".($ticketCount > 1 ? ' (+'.($ticketCount - 1).' insiden lain)' : '');
            } elseif ($metric) {
                $rawStatus = strtolower($metric->status ?? 'normal');
                if ($rawStatus === 'outage' || $rawStatus === 'critical' || $metric->uptime_percentage < 98.0) {
                    $status = 'outage';
                    $statusLabel = 'Padam / Outage';
                    $barColor = 'bg-red-500 hover:bg-red-600';
                } elseif ($rawStatus === 'warning' || $metric->uptime_percentage < 99.9 || $metric->incidents_count > 0 || $metric->downtime_minutes > 0) {
                    $status = 'warning';
                    $statusLabel = 'Degradasi / Warning';
                    $barColor = 'bg-amber-400 hover:bg-amber-500';
                } else {
                    $status = 'normal';
                    $statusLabel = 'Normal 100%';
                    $barColor = 'bg-emerald-500 hover:bg-emerald-600';
                }
                $uptime = (float) $metric->uptime_percentage;
                $downtimeMin = (int) $metric->downtime_minutes;
                $notes = $metric->notes ?: 'Operasi Jaringan Normal';
            } elseif ($totalDevices === 0) {
                $status = 'nodata';
                $statusLabel = 'Nihil Node';
                $barColor = 'bg-slate-200';
                $uptime = 0.0;
                $downtimeMin = 0;
                $notes = 'Belum Ada Perangkat Terdaftar';
            } elseif ($dateObj->isFuture()) {
                $status = 'future';
                $statusLabel = 'Belum Berjalan';
                $barColor = 'bg-slate-100';
                $uptime = 0.0;
                $downtimeMin = 0;
                $notes = 'Periode Belum Berjalan';
            } else {
                $status = 'normal';
                $statusLabel = 'Normal 100%';
                $barColor = 'bg-emerald-500 hover:bg-emerald-600';
                $uptime = 100.0;
                $downtimeMin = 0;
                $notes = 'Ketersediaan Penuh (Tanpa Gangguan)';
            }

            if ($status === 'outage') {
                $totalOutageDays++;
            } elseif ($status === 'warning') {
                $totalDegradedDays++;
            }
            $totalDowntimeMinutes += $downtimeMin;

            $dailyTimeline[] = [
                'day' => $day,
                'date_string' => $dateKey,
                'formatted_date' => $day.' '.$this->months[$selectedMonth].' '.$selectedYear,
                'uptime_percentage' => number_format($uptime, 2),
                'downtime_minutes' => $downtimeMin,
                'incidents_count' => $dayTickets->count() ?: ($metric?->incidents_count ?? 0),
                'status' => $status,
                'status_label' => $statusLabel,
                'bar_color' => $barColor,
                'notes' => $notes,
            ];
        }

        $targetSlaMin = '99.0%';

        if ($totalDevices > 0) {
            $slaSum = array_sum(array_column($filteredDevices, 'uptime_percent'));
            $actualSla = round($slaSum / $totalDevices, 2);

            $minThreshold = (float) str_replace('%', '', $targetSlaMin);
            if ($actualSla >= $minThreshold) {
                $slaStatusLabel = 'Tercapai';
                $slaStatusColor = 'emerald';
                $slaCommitmentText = 'Memenuhi Komitmen Kontrak SLA';
            } else {
                $slaStatusLabel = 'Deviasi Terdeteksi';
                $slaStatusColor = 'amber';
                $slaCommitmentText = 'Dalam Penyesuaian Restitusi SLA Insiden';
            }
        } else {
            $actualSla = 0.0;
            $slaStatusLabel = 'Nihil Node';
            $slaStatusColor = 'slate';
            $slaCommitmentText = 'Belum Ada Node Terpantau';
        }

        // Waktu resolusi rata-rata
        $resolvedWithDuration = array_filter($filteredTickets, fn ($t) => in_array($t['status_type'], ['resolved', 'closed']) && ! empty($t['duration_minutes']));
        if (count($resolvedWithDuration) > 0) {
            $avgMinutes = round(array_sum(array_column($resolvedWithDuration, 'duration_minutes')) / count($resolvedWithDuration));
            $avgHours = floor($avgMinutes / 60);
            $remMins = $avgMinutes % 60;
            $avgResolutionTime = $avgHours > 0 ? "{$avgHours} Jam {$remMins} Menit" : "{$remMins} Menit";
        } else {
            $avgResolutionTime = '-';
        }

        // Nomor Registrasi Dokumen Resmi
        $docPrefix = 'CONS';
        if ($selectedCustomer !== 'all' && $targetCustomerId) {
            $docPrefix = sprintf('C%03d', $targetCustomerId);
        }

        $reportDocumentNumber = sprintf(
            'NETFLY/SLA-%s/%02d/%04d',
            $docPrefix,
            $selectedMonth,
            $selectedYear
        );

        $selectedMonthName = $this->months[$selectedMonth];
        $formattedPeriod = $selectedMonthName.' '.$selectedYear;

        // Resolusi Pejabat Penerbit / Penandatangan Laporan (Semi-Dinamis Enterprise)
        $currentUser = auth()->user();
        $defaultIssuerName = $currentUser ? $currentUser->name : 'DIMAS PRASETYO, M.Kom.';
        $defaultIssuerRole = $currentUser
            ? ($currentUser->role === 'admin' ? 'Network Operations Lead • NetFly NOC Division' : 'Technical Operations Specialist • NetFly Division')
            : 'Network Operations Lead • NetFly NOC Division';

        $issuerName = trim((string) $request->query('issuer_name', ''));
        if ($issuerName === '') {
            $issuerName = $defaultIssuerName;
        }

        $issuerRole = trim((string) $request->query('issuer_role', ''));
        if ($issuerRole === '') {
            $issuerRole = $defaultIssuerRole;
        }

        return [
            'customers' => $customerOptions,
            'months' => $this->months,
            'years' => $this->years,
            'selectedCustomer' => $selectedCustomer,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedMonthName' => $selectedMonthName,
            'formattedPeriod' => $formattedPeriod,
            'evaluatedPartner' => $evaluatedPartner,
            'reportDocumentNumber' => $reportDocumentNumber,
            'issuerName' => $issuerName,
            'issuerRole' => $issuerRole,
            'defaultIssuerName' => $defaultIssuerName,
            'defaultIssuerRole' => $defaultIssuerRole,
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
            'dailyTimeline' => $dailyTimeline,
            'daysInMonth' => $daysInMonth,
            'totalDowntimeMinutes' => $totalDowntimeMinutes,
            'totalOutageDays' => $totalOutageDays,
            'totalDegradedDays' => $totalDegradedDays,
            'generatedAt' => now()->format('d F Y, H:i:s').' WIB',
        ];
    }
}
