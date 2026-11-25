<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Ticket;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TicketApiController extends Controller
{
    /**
     * Ambil daftar tiket (dapat difilter berdasarkan customer_id, nama klien, atau kata kunci).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::query()->orderBy('id', 'desc');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', (int) $request->query('customer_id'));
        } elseif ($request->filled('client_name')) {
            $client = trim($request->query('client_name'));
            $cleanClient = trim(preg_replace('/^(PT\.?|CV\.?|Dinas)\s+/i', '', $client));
            $query->where(function ($q) use ($client, $cleanClient) {
                $q->where('client_name', 'like', "%{$client}%");
                if (! empty($cleanClient)) {
                    $q->orWhere('client_name', 'like', "%{$cleanClient}%");
                }
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%");
            });
        }

        $tickets = $query->limit(50)->get();

        return response()->json([
            'success' => true,
            'total' => $tickets->count(),
            'data' => $tickets,
        ]);
    }

    /**
     * Buat tiket insiden baru dari aplikasi mobile / API.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ticket_number' => ['nullable', 'string', 'max:50'],
            'client_name' => ['nullable', 'string', 'max:200'],
            'project_name' => ['nullable', 'string', 'max:200'],
            'service_name' => ['nullable', 'string', 'max:200'],
            'node_code' => ['nullable', 'string', 'max:100'],
            'priority' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'customer_id' => ['nullable', 'integer'],
            'photo' => ['nullable'],
            'photo_base64' => ['nullable', 'string'],
        ]);

        $photoPath = $this->handleUploadedPhoto($request);

        $rawPriority = strtolower($validated['priority'] ?? 'medium');
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

        $clientName = trim($validated['client_name'] ?? 'Pelanggan Mobile NetFly');
        $projectName = trim($validated['project_name'] ?? $validated['service_name'] ?? 'Layanan Pelanggan');
        $category = ! empty($validated['category']) ? trim($validated['category']) : null;
        $subject = trim($validated['subject']);
        if ($category && ! str_contains(strtolower($subject), strtolower($category))) {
            $subject = "[{$category}] {$subject}";
        }

        $description = trim($validated['description'] ?? 'Tiket dilaporkan melalui Aplikasi NetFly Mobile.');

        // Hubungkan customer_id dari parameter atau lookup nama customer jika belum terisi
        $customerId = $validated['customer_id'] ?? null;
        if (! $customerId && ! empty($clientName)) {
            $cleanName = trim(preg_replace('/^(PT\.?|CV\.?|Dinas)\s+/i', '', $clientName));
            $customerId = Customer::where('name', 'like', "%{$cleanName}%")
                ->orWhere('name', 'like', "%{$clientName}%")
                ->value('id');
        }

        // Gunakan ticket_number dari mobile jika valid dan belum terpakai, atau generate nomor baru
        $ticketNumber = null;
        if (! empty($validated['ticket_number'])) {
            $candidate = trim($validated['ticket_number']);
            if (! Ticket::where('ticket_number', $candidate)->exists()) {
                $ticketNumber = $candidate;
            }
        }

        if (! $ticketNumber) {
            $nextId = (Ticket::max('id') ?? 0) + 1025;
            $ticketNumber = 'INC-'.date('Y').'-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
            while (Ticket::where('ticket_number', $ticketNumber)->exists()) {
                $nextId++;
                $ticketNumber = 'INC-'.date('Y').'-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
            }
        }

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'customer_id' => $customerId,
            'client_name' => $clientName,
            'project_name' => $projectName,
            'node_code' => $validated['node_code'] ?? null,
            'priority' => $priorityFormatted,
            'priority_level' => $priorityLevel,
            'subject' => $subject,
            'description' => $description,
            'photo' => $photoPath,
            'assigned_technician' => null,
            'is_tech_warning' => in_array($priorityLevel, ['high', 'critical'], true),
            'status' => '1. Open (Baru)',
            'status_type' => 'open',
            'reported_at' => now(),
        ]);

        ActivityLogger::log(
            'tiket',
            'create',
            "Tiket insiden baru diterima dari NetFly Mobile: '{$ticketNumber}' ({$ticket->subject})",
            $ticketNumber,
            (string) $ticket->id,
            [
                'ticket_number' => $ticketNumber,
                'source' => 'netfly_mobile_app',
                'priority' => $ticket->priority,
                'client_name' => $ticket->client_name,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'has_photo' => ! empty($photoPath),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Tiket baru '{$ticketNumber}' berhasil dibuat dan masuk ke antrean NOC.",
            'data' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'client_name' => $ticket->client_name,
                'project_name' => $ticket->project_name,
                'priority' => $ticket->priority,
                'priority_level' => $ticket->priority_level,
                'subject' => $ticket->subject,
                'description' => $ticket->description,
                'photo' => $ticket->photo,
                'photo_url' => $ticket->photo_url,
                'status' => $ticket->status,
                'status_type' => $ticket->status_type,
                'reported_at' => $ticket->reported_at?->toIso8601String() ?? now()->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Detail tiket tunggal.
     */
    public function show(string $id): JsonResponse
    {
        $ticket = Ticket::where('ticket_number', $id)
            ->orWhere('id', is_numeric($id) ? (int) $id : 0)
            ->first();

        if (! $ticket) {
            return response()->json([
                'success' => false,
                'message' => "Tiket {$id} tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }

    /**
     * Update detail tiket atau tambahkan respon / pesan penanganan tiket dari mobile.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $ticket = Ticket::where('ticket_number', $id)
            ->orWhere('id', is_numeric($id) ? (int) $id : 0)
            ->first();

        if (! $ticket) {
            return response()->json([
                'success' => false,
                'message' => "Tiket {$id} tidak ditemukan.",
            ], 404);
        }

        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:100'],
            'status_note' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string', 'max:1000'],
            'priority' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable'],
            'photo_base64' => ['nullable', 'string'],
        ]);

        $newPhotoPath = $this->handleUploadedPhoto($request);
        if ($newPhotoPath !== null) {
            $ticket->photo = $newPhotoPath;
        }

        if (! empty($validated['priority'])) {
            $rawPriority = strtolower($validated['priority']);
            $priorityLevel = match (true) {
                str_contains($rawPriority, 'critical') || str_contains($rawPriority, 'p1') => 'critical',
                str_contains($rawPriority, 'high') || str_contains($rawPriority, 'p2') => 'high',
                default => 'medium',
            };
            $ticket->priority_level = $priorityLevel;
            $ticket->priority = match ($priorityLevel) {
                'critical' => 'P1 - CRITICAL',
                'high' => 'P2 - HIGH',
                default => 'P3 - MEDIUM',
            };
            $ticket->is_tech_warning = in_array($priorityLevel, ['high', 'critical'], true);
        }

        if (! empty($validated['subject'])) {
            $ticket->subject = trim($validated['subject']);
        }

        if (! empty($validated['description'])) {
            $ticket->description = trim($validated['description']);
        }

        if (! empty($validated['message'])) {
            $newMessage = trim($validated['message']);
            $sender = trim($request->input('sender', 'Customer Mobile'));
            $timestamp = now()->format('d/m H:i');
            $formattedEntry = "[{$timestamp}] {$sender}: {$newMessage}";

            $existingNote = $ticket->status_note ? trim($ticket->status_note) : '';
            $ticket->status_note = $existingNote ? "{$existingNote}\n{$formattedEntry}" : $formattedEntry;
        }

        if (! empty($validated['status_note']) && empty($validated['message'])) {
            $ticket->status_note = trim($validated['status_note']);
        }

        if (! empty($validated['status'])) {
            $rawStatus = $validated['status'];
            $statusLower = strtolower($rawStatus);
            $statusType = match (true) {
                str_contains($statusLower, 'open') => 'open',
                str_contains($statusLower, 'assigned') => 'assigned',
                str_contains($statusLower, 'progress') => 'in_progress',
                str_contains($statusLower, 'resolved') => 'resolved',
                str_contains($statusLower, 'closed') => 'closed',
                default => 'in_progress',
            };

            $statusFormatted = match ($statusType) {
                'open' => '1. Open (Baru)',
                'assigned' => '2. Assigned (Ditugaskan)',
                'in_progress' => '3. In Progress (Sedang Dikerjakan)',
                'resolved' => '4. Resolved (Terselesaikan)',
                'closed' => '5. Closed (Ditutup)',
            };

            $ticket->status = $statusFormatted;
            $ticket->status_type = $statusType;

            if ($statusType === 'resolved' && ! $ticket->resolved_at) {
                $ticket->resolved_at = now();
            }
        }

        $ticket->save();

        ActivityLogger::log(
            'tiket',
            'update',
            "Tiket {$ticket->ticket_number} diperbarui dari aplikasi mobile: status '{$ticket->status}'",
            $ticket->ticket_number,
            (string) $ticket->id,
            [
                'ticket_number' => $ticket->ticket_number,
                'source' => 'netfly_mobile_app',
                'status' => $ticket->status,
                'status_type' => $ticket->status_type,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Tiket {$ticket->ticket_number} berhasil diperbarui.",
            'data' => $ticket,
        ]);
    }

    /**
     * Validasi dan simpan foto lampiran tiket (multipart file atau base64 gambar yang valid).
     */
    protected function handleUploadedPhoto(Request $request): ?string
    {
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ], [
                'photo.image' => 'Lampiran foto harus berupa file gambar yang valid (JPG, PNG, atau WebP).',
                'photo.mimes' => 'Format foto hanya mendukung JPG, JPEG, PNG, atau WebP.',
                'photo.max' => 'Ukuran lampiran foto maksimal 5 MB.',
            ]);

            $file = $request->file('photo');
            if ($file && $file->isValid()) {
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

                return 'uploads/tickets/'.$filename;
            }
        } elseif ($request->filled('photo') || $request->filled('photo_base64')) {
            $rawPhoto = trim((string) ($request->input('photo') ?: $request->input('photo_base64')));
            if ($rawPhoto === '') {
                return null;
            }

            $base64Data = null;
            if (str_starts_with($rawPhoto, 'data:image/') && str_contains($rawPhoto, ';base64,')) {
                $base64Data = substr($rawPhoto, strpos($rawPhoto, ',') + 1);
            } elseif (strlen($rawPhoto) > 100 && ! str_contains($rawPhoto, ' ')) {
                $base64Data = $rawPhoto;
            }

            if ($base64Data !== null) {
                $decoded = base64_decode($base64Data, true);
                if ($decoded !== false && strlen($decoded) > 0 && strlen($decoded) <= 5 * 1024 * 1024) {
                    $imageInfo = @getimagesizefromstring($decoded);
                    $mime = is_array($imageInfo) ? ($imageInfo['mime'] ?? '') : '';
                    $ext = match ($mime) {
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                        default => null,
                    };

                    if ($ext !== null) {
                        $filename = 'ticket_'.time().'_'.Str::random(8).'.'.$ext;
                        $destination = public_path('uploads/tickets');
                        if (! File::exists($destination)) {
                            File::makeDirectory($destination, 0755, true);
                        }
                        File::put($destination.'/'.$filename, $decoded);

                        return 'uploads/tickets/'.$filename;
                    }
                }
            }
        }

        return null;
    }
}
