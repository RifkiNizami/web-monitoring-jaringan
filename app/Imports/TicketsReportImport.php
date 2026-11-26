<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class TicketsReportImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;

    public int $updatedCount = 0;

    public array $errors = [];

    protected ?int $defaultCustomerId = null;

    protected ?int $defaultMonth = null;

    protected ?int $defaultYear = null;

    public function __construct(?int $defaultCustomerId = null, ?int $defaultMonth = null, ?int $defaultYear = null)
    {
        $this->defaultCustomerId = $defaultCustomerId;
        $this->defaultMonth = $defaultMonth;
        $this->defaultYear = $defaultYear;
    }

    // [PANDUAN UBAH IMPORT EXCEL - TIKET]
    // - Nama key $row['...'] otomatis hasil snake_case dari judul header baris 1 Excel
    // - Urutan pencocokan tiket eksisting: 1) ID -> 2) Nomor Tiket (jika cocok = update, jika tidak = create baru)
    // - Normalisasi prioritas, status penanganan, dan parsing tanggal Bahasa Indonesia dikelola di dalam loop ini
    public function collection(Collection $rows): void
    {
        $allCustomers = Customer::all();

        foreach ($rows as $index => $row) {
            $idVal = trim((string) ($row['id'] ?? ''));
            $ticketNumber = trim((string) ($row['nomor_tiket'] ?? $row['ticket_number'] ?? $row['tiket'] ?? ''));
            $subject = trim((string) ($row['subjek_ringkasan_gangguan'] ?? $row['subjek'] ?? $row['subject'] ?? $row['gangguan'] ?? ''));

            if (empty($ticketNumber) && empty($subject) && empty($idVal)) {
                continue;
            }

            $priority = trim((string) ($row['prioritas'] ?? $row['priority'] ?? 'P3 - Medium'));
            $technician = trim((string) ($row['teknisi_noc'] ?? $row['teknisi'] ?? ''));
            $statusRaw = trim((string) ($row['status_penanganan'] ?? $row['status'] ?? 'Open'));
            $reportDateRaw = trim((string) ($row['waktu_lapor'] ?? $row['tanggal'] ?? $row['waktu'] ?? ''));

            // Normalisasi Priority Level
            $prioLower = strtolower($priority);
            $priorityLevel = match (true) {
                str_contains($prioLower, 'p1') || str_contains($prioLower, 'critical') || str_contains($prioLower, 'kritis') => 'critical',
                str_contains($prioLower, 'p2') || str_contains($prioLower, 'high') || str_contains($prioLower, 'tinggi') => 'high',
                str_contains($prioLower, 'p4') || str_contains($prioLower, 'low') || str_contains($prioLower, 'rendah') => 'low',
                default => 'medium',
            };

            // Normalisasi Status Type
            $stLower = strtolower($statusRaw);
            $statusType = match (true) {
                str_contains($stLower, 'progress') || str_contains($stLower, 'proses') || str_contains($stLower, 'dikerjakan') => 'in_progress',
                str_contains($stLower, 'assign') || str_contains($stLower, 'ditugaskan') => 'assigned',
                str_contains($stLower, 'resolve') || str_contains($stLower, 'selesai') || str_contains($stLower, 'closed') || str_contains($stLower, 'tutup') => 'resolved',
                default => 'open',
            };

            // Status Text yang bersih dan sesuai sistem
            $normalizedStatus = match ($statusType) {
                'in_progress' => '3. In Progress (Sedang Dikerjakan)',
                'assigned' => '2. Assigned (Ditugaskan ke Teknisi)',
                'resolved' => '4. Resolved (Selesai Penanganan)',
                default => '1. Open (Baru)',
            };

            // Parse waktu lapor
            $reportedAt = null;
            if (! empty($reportDateRaw) && $reportDateRaw !== '-') {
                if (is_numeric($reportDateRaw) && (float) $reportDateRaw > 30000) {
                    try {
                        $reportedAt = Carbon::instance(ExcelDate::excelToDateTimeObject((float) $reportDateRaw));
                    } catch (\Throwable $e) {
                        $reportedAt = null;
                    }
                } else {
                    try {
                        $clean = trim(preg_replace('/\b(WIB|WIT|WITA)\b/i', '', $reportDateRaw));
                        $indoMonths = [
                            'januari' => 'january', 'februari' => 'february', 'maret' => 'march', 'april' => 'april',
                            'mei' => 'may', 'juni' => 'june', 'juli' => 'july', 'agustus' => 'august',
                            'september' => 'september', 'oktober' => 'october', 'november' => 'november', 'desember' => 'december',
                            'okt' => 'oct', 'agu' => 'aug', 'des' => 'dec', 'nop' => 'nov'
                        ];
                        $clean = strtr(strtolower($clean), $indoMonths);
                        $reportedAt = Carbon::parse($clean);
                    } catch (\Throwable $e) {
                        $reportedAt = null;
                    }
                }
            }

            // PENCARIAN TIKET EKSISTING:
            // 1. Berdasarkan ID jika ada
            $ticket = null;
            if (is_numeric($idVal) && (int) $idVal > 0) {
                $ticket = Ticket::find((int) $idVal);
            }
            // 2. Berdasarkan nomor tiket jika belum ditemukan
            if (! $ticket && ! empty($ticketNumber) && $ticketNumber !== '-') {
                $ticket = Ticket::where('ticket_number', $ticketNumber)->first();
            }

            if ($ticket) {
                if (! empty($ticketNumber) && $ticketNumber !== '-') {
                    $ticket->ticket_number = $ticketNumber;
                }
                if (! empty($subject) && $subject !== '-') {
                    $ticket->subject = $subject;
                }
                $ticket->priority = $priority;
                $ticket->priority_level = $priorityLevel;
                if (! empty($technician) && $technician !== '-') {
                    $ticket->assigned_technician = $technician;
                }
                $ticket->status = $normalizedStatus;
                $ticket->status_type = $statusType;
                if ($reportedAt) {
                    $ticket->reported_at = $reportedAt;
                }
                if ($statusType === 'resolved' && ! $ticket->resolved_at) {
                    $ticket->resolved_at = now();
                }
                $ticket->save();
                $this->updatedCount++;
            } else {
                if (empty($ticketNumber) || $ticketNumber === '-') {
                    $ticketNumber = 'INC-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));
                }

                $defaultReportedAt = ($this->defaultYear && $this->defaultMonth)
                    ? Carbon::create($this->defaultYear, $this->defaultMonth, min((int) now()->day, Carbon::create($this->defaultYear, $this->defaultMonth, 1)->daysInMonth), now()->hour, now()->minute)
                    : now();
                $defaultCust = $this->defaultCustomerId ? $allCustomers->firstWhere('id', $this->defaultCustomerId) : null;

                Ticket::create([
                    'ticket_number' => $ticketNumber,
                    'customer_id' => $this->defaultCustomerId,
                    'client_name' => $defaultCust?->name,
                    'subject' => ! empty($subject) && $subject !== '-' ? $subject : 'Gangguan Jaringan Dilaporkan',
                    'description' => 'Tiket diimpor dari file Laporan Excel NetFly.',
                    'priority' => $priority ?: 'P3 - MEDIUM',
                    'priority_level' => $priorityLevel,
                    'assigned_technician' => (! empty($technician) && $technician !== '-') ? $technician : null,
                    'status' => $normalizedStatus,
                    'status_type' => $statusType,
                    'reported_at' => $reportedAt ?: $defaultReportedAt,
                    'resolved_at' => $statusType === 'resolved' ? now() : null,
                ]);
                $this->importedCount++;
            }
        }
    }
}
