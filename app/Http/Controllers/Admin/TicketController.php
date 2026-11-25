<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Teknisi;
use App\Models\Ticket;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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

        // 2. Daftar status penanganan standar NOC
        $statusOptions = [
            '1. Open (Baru)',
            '2. Assigned (Ditugaskan)',
            '3. In Progress (Sedang Dikerjakan)',
            '4. Resolved (Terselesaikan)',
            '5. Closed (Ditutup)',
        ];

        // 3. Ambil data antrean tiket dari database (terbaru di atas)
        $dbTickets = Ticket::orderBy('id', 'desc')->get();

        $tickets = $dbTickets->map(function ($ticket) {
            $reportedAt = $ticket->reported_at ?? $ticket->created_at;

            return [
                'id' => $ticket->ticket_number ?? 'INC-'.$ticket->id,
                'db_id' => $ticket->id,
                'priority' => $ticket->priority ?? 'P3 - MEDIUM',
                'priority_level' => $ticket->priority_level ?? 'medium',
                'client_name' => $ticket->client_name ?? 'Pelanggan Enterprise',
                'project_name' => $ticket->project_name ?? 'Layanan Infrastruktur',
                'node_code' => $ticket->node_code,
                'subject' => $ticket->subject,
                'description' => $ticket->description,
                'photo' => $ticket->photo,
                'photo_url' => $ticket->photo_url,
                'assigned_technician' => $ticket->assigned_technician,
                'is_tech_warning' => (bool) $ticket->is_tech_warning,
                'status' => $ticket->status ?? '1. Open (Baru)',
                'status_type' => $ticket->status_type ?? 'open',
                'status_note' => $ticket->status_note,
                'report_date' => $reportedAt ? $reportedAt->format('d M Y') : '-',
                'report_time' => $reportedAt ? $reportedAt->format('H:i').' WIB' : '-',
            ];
        })->toArray();

        // 4. Hitung ringkasan status tiket dinamis
        $summary = [
            'all' => count($tickets),
            'open' => count(array_filter($tickets, fn ($t) => strtolower($t['status_type']) === 'open' || str_contains($t['status'], 'Open'))),
            'assigned' => count(array_filter($tickets, fn ($t) => strtolower($t['status_type']) === 'assigned' || str_contains($t['status'], 'Assigned'))),
            'in_progress' => count(array_filter($tickets, fn ($t) => strtolower($t['status_type']) === 'in_progress' || str_contains($t['status'], 'In Progress'))),
            'resolved' => count(array_filter($tickets, fn ($t) => strtolower($t['status_type']) === 'resolved' || str_contains($t['status'], 'Resolved'))),
            'closed' => count(array_filter($tickets, fn ($t) => strtolower($t['status_type']) === 'closed' || str_contains($t['status'], 'Closed'))),
        ];

        return view('admin.tickets', compact('summary', 'technicians', 'statusOptions', 'tickets'));
    }

    /**
     * Tampilkan formulir pembuatan tiket gangguan baru (halaman mandiri).
     */
    public function create(): View
    {
        $technicians = Teknisi::orderBy('nama', 'asc')->get();
        $customers = Customer::orderBy('name', 'asc')->get();

        return view('admin.tickets.create', compact('technicians', 'customers'));
    }

    /**
     * Tampilkan halaman edit data & eskalasi penanganan tiket (halaman mandiri).
     */
    public function edit(string $id): View
    {
        $ticket = Ticket::where('ticket_number', $id)->orWhere('id', $id)->firstOrFail();
        $technicians = Teknisi::orderBy('nama', 'asc')->get();
        $statusOptions = [
            '1. Open (Baru)',
            '2. Assigned (Ditugaskan)',
            '3. In Progress (Sedang Dikerjakan)',
            '4. Resolved (Terselesaikan)',
            '5. Closed (Ditutup)',
        ];

        $customers = Customer::orderBy('name', 'asc')->get();

        return view('admin.tickets.edit', compact('ticket', 'technicians', 'statusOptions', 'customers'));
    }

    /**
     * Verifikasi dan perbarui penugasan teknisi, status, maupun data detail tiket.
     */
    public function update(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $ticket = Ticket::where('ticket_number', $id)->orWhere('id', $id)->first();

        if (! $ticket) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Tiket {$id} tidak ditemukan."], 404);
            }

            return redirect()->route('tickets.index')->with('error', "Tiket {$id} tidak ditemukan.");
        }

        $validated = $request->validate([
            'client_name' => ['nullable', 'string', 'max:200'],
            'project_name' => ['nullable', 'string', 'max:200'],
            'node_code' => ['nullable', 'string', 'max:100'],
            'priority' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_technician' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'string', 'max:100'],
            'status_note' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');
            $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $ext = 'jpg';
            }
            $filename = 'ticket_'.time().'_'.Str::random(8).'.'.$ext;
            $destination = public_path('uploads/tickets');
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            if ($ticket->photo && File::exists(public_path($ticket->photo))) {
                @File::delete(public_path($ticket->photo));
            }
            $file->move($destination, $filename);
            $ticket->photo = 'uploads/tickets/'.$filename;
        } elseif ($request->boolean('remove_photo')) {
            if ($ticket->photo && File::exists(public_path($ticket->photo))) {
                @File::delete(public_path($ticket->photo));
            }
            $ticket->photo = null;
        }

        if (isset($validated['client_name']) && filled($validated['client_name'])) {
            $ticket->client_name = trim($validated['client_name']);
            $matchedCustomerId = Customer::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($ticket->client_name)])->value('id');
            if ($matchedCustomerId) {
                $ticket->customer_id = $matchedCustomerId;
            }
        }

        if (isset($validated['project_name']) && filled($validated['project_name'])) {
            $ticket->project_name = trim($validated['project_name']);
        }

        if (array_key_exists('node_code', $validated)) {
            $ticket->node_code = ! empty($validated['node_code']) ? trim($validated['node_code']) : null;
        }

        if (isset($validated['subject']) && filled($validated['subject'])) {
            $ticket->subject = trim($validated['subject']);
        }

        if (isset($validated['description']) && filled($validated['description'])) {
            $ticket->description = trim($validated['description']);
        }

        if (array_key_exists('status_note', $validated)) {
            $ticket->status_note = ! empty($validated['status_note']) ? trim($validated['status_note']) : null;
        }

        if (! empty($validated['priority'])) {
            $prio = strtolower($validated['priority']);
            $priorityLevel = match (true) {
                str_contains($prio, 'critical') || str_contains($prio, 'p1') => 'critical',
                str_contains($prio, 'high') || str_contains($prio, 'p2') => 'high',
                default => 'medium',
            };
            $priorityFormatted = match ($priorityLevel) {
                'critical' => 'P1 - CRITICAL',
                'high' => 'P2 - HIGH',
                default => 'P3 - MEDIUM',
            };
            $ticket->priority = $priorityFormatted;
            $ticket->priority_level = $priorityLevel;
        }

        $assignedTech = null;
        if ($request->has('assigned_technician')) {
            $assignedTech = ! empty($validated['assigned_technician']) ? trim($validated['assigned_technician']) : null;
            $ticket->assigned_technician = $assignedTech;

            if (! empty($assignedTech)) {
                $ticket->is_tech_warning = false;
            } else {
                $ticket->is_tech_warning = in_array($ticket->priority_level, ['high', 'critical'], true);
            }
        }

        if (! empty($validated['status'])) {
            $ticket->status = $validated['status'];
            if (str_contains($validated['status'], 'Open')) {
                $ticket->status_type = 'open';
            } elseif (str_contains($validated['status'], 'Assigned')) {
                $ticket->status_type = 'assigned';
            } elseif (str_contains($validated['status'], 'In Progress')) {
                $ticket->status_type = 'in_progress';
            } elseif (str_contains($validated['status'], 'Resolved')) {
                $ticket->status_type = 'resolved';
                if (! $ticket->resolved_at) {
                    $ticket->resolved_at = now();
                    if (empty($ticket->status_note)) {
                        $ticket->status_note = 'Selesai: '.now()->format('d/m H:i');
                    }
                }
            } elseif (str_contains($validated['status'], 'Closed')) {
                $ticket->status_type = 'closed';
            }
        } elseif (! empty($assignedTech) && ($ticket->status_type === 'open' || str_contains($ticket->status, 'Open'))) {
            // Naikkan status ke Assigned jika sebelumnya Open dan teknisi ditunjuk
            $ticket->status = '2. Assigned (Ditugaskan)';
            $ticket->status_type = 'assigned';
        }

        $ticket->save();

        ActivityLogger::log(
            'tiket',
            'update',
            "Memperbarui tiket {$ticket->ticket_number}: status '{$ticket->status}'".($ticket->assigned_technician ? ", teknisi: '{$ticket->assigned_technician}'" : ''),
            $ticket->ticket_number,
            (string) $ticket->id,
            [
                'ticket_number' => $ticket->ticket_number,
                'status' => $ticket->status,
                'status_type' => $ticket->status_type,
                'assigned_technician' => $ticket->assigned_technician,
                'priority' => $ticket->priority,
            ]
        );

        $message = "Data tiket {$ticket->ticket_number} berhasil diperbarui.";

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
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');
            $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $ext = 'jpg';
            }
            $filename = 'ticket_'.time().'_'.Str::random(8).'.'.$ext;
            $destination = public_path('uploads/tickets');
            if (! File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $photoPath = 'uploads/tickets/'.$filename;
        }

        $rawPriority = strtolower($validated['priority']);
        $priorityLevel = match (true) {
            str_contains($rawPriority, 'critical') || str_contains($rawPriority, 'p1') => 'critical',
            str_contains($rawPriority, 'high') || str_contains($rawPriority, 'p2') => 'high',
            default => 'medium',
        };

        $priorityFormatted = match ($priorityLevel) {
            'critical' => 'P1 - CRITICAL',
            'high' => 'P2 - HIGH',
            default => 'P3 - MEDIUM',
        };

        $assignedTech = ! empty($validated['assigned_technician']) ? trim($validated['assigned_technician']) : null;
        $status = ! empty($assignedTech) ? '2. Assigned (Ditugaskan)' : '1. Open (Baru)';
        $statusType = ! empty($assignedTech) ? 'assigned' : 'open';

        $nextId = (Ticket::max('id') ?? 0) + 1025;
        $ticketNumber = 'INC-'.date('Y').'-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
        while (Ticket::where('ticket_number', $ticketNumber)->exists()) {
            $nextId++;
            $ticketNumber = 'INC-'.date('Y').'-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
        }

        $clientName = trim($validated['client_name']);
        $customerId = $request->filled('customer_id')
            ? (int) $request->input('customer_id')
            : Customer::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($clientName)])->value('id');

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'customer_id' => $customerId,
            'client_name' => $clientName,
            'project_name' => $validated['project_name'],
            'node_code' => $validated['node_code'] ?? null,
            'priority' => $priorityFormatted,
            'priority_level' => $priorityLevel,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'photo' => $photoPath,
            'assigned_technician' => $assignedTech,
            'is_tech_warning' => empty($assignedTech) && ($priorityLevel === 'high' || $priorityLevel === 'critical'),
            'status' => $status,
            'status_type' => $statusType,
            'reported_at' => now(),
        ]);

        ActivityLogger::log(
            'tiket',
            'create',
            "Membuat tiket insiden baru '{$ticketNumber}' ({$ticket->subject})",
            $ticketNumber,
            (string) $ticket->id,
            [
                'ticket_number' => $ticketNumber,
                'priority' => $ticket->priority,
                'client_name' => $ticket->client_name,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
            ]
        );

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
        if (! $ticket) {
            $msg = "Tiket '{$id}' tidak ditemukan.";

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }

            return redirect()->route('tickets.index')->with('error', $msg);
        }

        $ticketNumber = $ticket->ticket_number;
        $ticketDbId = (string) $ticket->id;

        if ($ticket->photo && str_starts_with($ticket->photo, 'uploads/tickets/')) {
            $photoFile = public_path($ticket->photo);
            if (File::exists($photoFile)) {
                @File::delete($photoFile);
            }
        }

        $ticket->delete();

        ActivityLogger::log(
            'tiket',
            'delete',
            "Menghapus tiket '{$ticketNumber}'",
            $ticketNumber,
            $ticketDbId,
            ['ticket_number' => $ticketNumber]
        );

        $msg = "Tiket '{$ticketNumber}' berhasil dihapus dari antrean.";

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
        return [];
    }
}
