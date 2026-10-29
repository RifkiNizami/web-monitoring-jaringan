<?php

namespace App\Http\Controllers;

use App\Models\Teknisi;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Tampilkan halaman antrean tiket komplain klien & insiden.
     */
    public function index(Request $request): View
    {
        // 1. Ambil data teknisi lapangan dari database (Model Teknisi)
        $technicians = Teknisi::orderBy('nama', 'asc')->get();

        if ($technicians->isEmpty()) {
            $technicians = collect([
                (object)['nama' => 'Agus Prasetyo, S.T.', 'spesialisasi' => 'Fiber Optic & Splicing OTB'],
                (object)['nama' => 'Budi Santoso', 'spesialisasi' => 'Wireless Microwave & Tower Climber'],
                (object)['nama' => 'Dimas Nugroho', 'spesialisasi' => 'Troubleshooting Last Mile & ONT/CPE'],
                (object)['nama' => 'Fajar Maulana', 'spesialisasi' => 'Emergency Response & Core NOC Support'],
                (object)['nama' => 'Rahmat Hidayat', 'spesialisasi' => 'Routing & Switching Core (Cisco/Mikrotik)'],
            ]);
        }

        // 2. Daftar status penanganan standar NOC
        $statusOptions = [
            '1. Open (Baru)',
            '2. Assigned (Ditugaskan)',
            '3. In Progress (Sedang Dikerjakan)',
            '4. Resolved (Terselesaikan)',
            '5. Closed (Ditutup)',
        ];

        // 3. Ambil data antrean tiket dari database
        $dbTickets = Ticket::orderBy('id', 'asc')->get();

        if ($dbTickets->isEmpty()) {
            $tickets = $this->getDefaultTickets();
        } else {
            $tickets = $dbTickets->map(function ($ticket) {
                $reportedAt = $ticket->reported_at ?? $ticket->created_at;
                return [
                    'id' => $ticket->ticket_number ?? 'INC-' . $ticket->id,
                    'db_id' => $ticket->id,
                    'priority' => $ticket->priority ?? 'P3 - MEDIUM',
                    'priority_level' => $ticket->priority_level ?? 'medium',
                    'client_name' => $ticket->client_name ?? 'Pelanggan Enterprise',
                    'project_name' => $ticket->project_name ?? 'Layanan Infrastruktur',
                    'node_code' => $ticket->node_code,
                    'subject' => $ticket->subject,
                    'description' => $ticket->description,
                    'assigned_technician' => $ticket->assigned_technician,
                    'is_tech_warning' => (bool)$ticket->is_tech_warning,
                    'status' => $ticket->status ?? '1. Open (Baru)',
                    'status_type' => $ticket->status_type ?? 'open',
                    'status_note' => $ticket->status_note,
                    'report_date' => $reportedAt ? $reportedAt->format('d M Y') : '10 Okt 2026',
                    'report_time' => $reportedAt ? $reportedAt->format('H:i') . ' WIB' : '04:06 WIB',
                ];
            })->toArray();
        }

        // 4. Hitung ringkasan status tiket dinamis
        $summary = [
            'all' => count($tickets),
            'open' => count(array_filter($tickets, fn($t) => strtolower($t['status_type']) === 'open' || str_contains($t['status'], 'Open'))),
            'assigned' => count(array_filter($tickets, fn($t) => strtolower($t['status_type']) === 'assigned' || str_contains($t['status'], 'Assigned'))),
            'in_progress' => count(array_filter($tickets, fn($t) => strtolower($t['status_type']) === 'in_progress' || str_contains($t['status'], 'In Progress'))),
            'resolved' => count(array_filter($tickets, fn($t) => strtolower($t['status_type']) === 'resolved' || str_contains($t['status'], 'Resolved'))),
            'closed' => count(array_filter($tickets, fn($t) => strtolower($t['status_type']) === 'closed' || str_contains($t['status'], 'Closed'))),
        ];

        return view('admin.tickets', compact('summary', 'technicians', 'statusOptions', 'tickets'));
    }

    /**
     * Verifikasi dan perbarui penugasan teknisi dan status tiket.
     * Dipanggil secara eksplisit melalui tombol Update, bukan dari dropdown langsung.
     */
    public function update(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $ticket = Ticket::where('ticket_number', $id)->orWhere('id', $id)->first();

        if (!$ticket) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Tiket {$id} tidak ditemukan."], 404);
            }
            return redirect()->route('tickets.index')->with('error', "Tiket {$id} tidak ditemukan.");
        }

        $validated = $request->validate([
            'assigned_technician' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'string', 'max:100'],
        ]);

        $assignedTech = !empty($validated['assigned_technician']) ? trim($validated['assigned_technician']) : null;
        $ticket->assigned_technician = $assignedTech;

        // Jika teknisi sudah ditunjuk, hilangkan warning wajib teknisi
        if (!empty($assignedTech)) {
            $ticket->is_tech_warning = false;
        }

        if (!empty($validated['status'])) {
            $ticket->status = $validated['status'];
            if (str_contains($validated['status'], 'Open')) {
                $ticket->status_type = 'open';
            } elseif (str_contains($validated['status'], 'Assigned')) {
                $ticket->status_type = 'assigned';
            } elseif (str_contains($validated['status'], 'In Progress')) {
                $ticket->status_type = 'in_progress';
            } elseif (str_contains($validated['status'], 'Resolved')) {
                $ticket->status_type = 'resolved';
                if (!$ticket->resolved_at) {
                    $ticket->resolved_at = now();
                    $ticket->status_note = 'Selesai: ' . now()->format('d/m H:i');
                }
            } elseif (str_contains($validated['status'], 'Closed')) {
                $ticket->status_type = 'closed';
            }
        } elseif (!empty($assignedTech) && ($ticket->status_type === 'open' || str_contains($ticket->status, 'Open'))) {
            // Naikkan status ke Assigned jika sebelumnya Open dan teknisi ditunjuk
            $ticket->status = '2. Assigned (Ditugaskan)';
            $ticket->status_type = 'assigned';
        }

        $ticket->save();

        $message = $assignedTech 
            ? "Teknisi '{$assignedTech}' berhasil ditugaskan untuk tiket {$ticket->ticket_number}."
            : "Data tiket {$ticket->ticket_number} berhasil diperbarui.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'assigned_technician' => $ticket->assigned_technician,
                'status' => $ticket->status,
                'status_type' => $ticket->status_type,
            ]);
        }

        return redirect()->route('tickets.index')->with('success', $message);
    }

    /**
     * Buat tiket baru ke antrean.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:200'],
            'project_name' => ['required', 'string', 'max:200'],
            'node_code' => ['nullable', 'string', 'max:100'],
            'priority' => ['required', 'string'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'assigned_technician' => ['nullable', 'string', 'max:150'],
        ]);

        $priorityLevel = match (strtolower($validated['priority'])) {
            'critical', 'p1' => 'critical',
            'high', 'p2' => 'high',
            default => 'medium',
        };

        $priorityFormatted = match ($priorityLevel) {
            'critical' => 'P1 - CRITICAL',
            'high' => 'P2 - HIGH',
            default => 'P3 - MEDIUM',
        };

        $assignedTech = !empty($validated['assigned_technician']) ? trim($validated['assigned_technician']) : null;
        $status = !empty($assignedTech) ? '2. Assigned (Ditugaskan)' : '1. Open (Baru)';
        $statusType = !empty($assignedTech) ? 'assigned' : 'open';

        $ticketNumber = 'INC-' . date('Y') . '-' . str_pad((string)(Ticket::count() + 1025), 4, '0', STR_PAD_LEFT);

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'client_name' => $validated['client_name'],
            'project_name' => $validated['project_name'],
            'node_code' => $validated['node_code'] ?? null,
            'priority' => $priorityFormatted,
            'priority_level' => $priorityLevel,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'assigned_technician' => $assignedTech,
            'is_tech_warning' => empty($assignedTech) && ($priorityLevel === 'high' || $priorityLevel === 'critical'),
            'status' => $status,
            'status_type' => $statusType,
            'reported_at' => now(),
        ]);

        $msg = "Tiket baru '{$ticketNumber}' berhasil ditambahkan ke antrean.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'ticket' => $ticket,
            ]);
        }

        return redirect()->route('tickets.index')->with('success', $msg);
    }

    /**
     * Hapus tiket dari antrean.
     */
    public function destroy(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $ticket = Ticket::where('ticket_number', $id)->orWhere('id', $id)->first();
        if ($ticket) {
            $ticketNumber = $ticket->ticket_number;
            $ticket->delete();
            $msg = "Tiket '{$ticketNumber}' berhasil dihapus dari antrean.";
        } else {
            $msg = "Tiket '{$id}' tidak ditemukan.";
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('tickets.index')->with('success', $msg);
    }

    /**
     * Default tickets fallback
     */
    protected function getDefaultTickets(): array
    {
        return [
            [
                'id' => 'INC-2026-1024',
                'priority' => 'P2 - HIGH',
                'priority_level' => 'high',
                'client_name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'project_name' => 'Smart City Backbone Sekolah Tahap II',
                'node_code' => 'AP-OUTDOOR-DISDIK-01',
                'subject' => 'Packet Loss Tinggi (>14%) Backbone Disdik Jakarta Pusat',
                'description' => 'Akses koneksi Internet di lokasi hub pusat mengalami degradasi kualitas dan loss tinggi.',
                'assigned_technician' => null,
                'is_tech_warning' => false,
                'status' => '3. In Progress (Sedang Dikerjakan)',
                'status_type' => 'in_progress',
                'status_note' => null,
                'report_date' => '10 Okt 2026',
                'report_time' => '04:06 WIB',
            ],
            [
                'id' => 'INC-2026-1030',
                'priority' => 'P1 - CRITICAL',
                'priority_level' => 'critical',
                'client_name' => 'PT. Shopee International Indonesia',
                'project_name' => 'Fulfillment Center Warehouse Cikarang',
                'node_code' => 'SW-AGG-BKS-02',
                'subject' => 'Switch Agg-Bks_04 Terputus (Site Warehouse Bekasi)',
                'description' => 'Unit switch padam total, tim teknisi lapangan sedang menuju lokasi untuk pengecekan fisik.',
                'assigned_technician' => null,
                'is_tech_warning' => false,
                'status' => '2. Assigned (Ditugaskan)',
                'status_type' => 'assigned',
                'status_note' => null,
                'report_date' => '10 Okt 2026',
                'report_time' => '04:06 WIB',
            ],
            [
                'id' => 'REQ-2026-0882',
                'priority' => 'P3 - MEDIUM',
                'priority_level' => 'medium',
                'client_name' => 'PT. Shopee International Indonesia',
                'project_name' => 'Dedicated Internet SCBD Hub',
                'node_code' => null,
                'subject' => 'Permintaan Bandwidth On-Demand (+500 Mbps) Flash Sale',
                'description' => 'Permintaan alokasi bandwidth sementara selama periode kampanye belanja online akbar.',
                'assigned_technician' => null,
                'is_tech_warning' => true,
                'status' => '1. Open (Baru)',
                'status_type' => 'open',
                'status_note' => null,
                'report_date' => '10 Okt 2026',
                'report_time' => '04:06 WIB',
            ],
            [
                'id' => 'INC-2026-0955',
                'priority' => 'P2 - HIGH',
                'priority_level' => 'high',
                'client_name' => 'PT. Bank Mandiri (Persero) Tbk',
                'project_name' => 'MPLS Core Interconnect Plaza Mandiri',
                'node_code' => 'RTR-CORE-MANDIRI-01',
                'subject' => 'High Latency Spike (>120ms) Core Link Plaza Mandiri',
                'description' => 'Terjadi fluktuasi latensi pada link utama. Rute telah berhasil distabilkan dengan backup link.',
                'assigned_technician' => null,
                'is_tech_warning' => false,
                'status' => '4. Resolved (Terselesaikan)',
                'status_type' => 'resolved',
                'status_note' => 'Selesai: 10/10 02:00',
                'report_date' => '10 Okt 2026',
                'report_time' => '04:06 WIB',
            ],
        ];
    }
}
