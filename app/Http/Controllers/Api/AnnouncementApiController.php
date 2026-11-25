<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementApiController extends Controller
{
    /**
     * Ambil daftar pengumuman / pemberitahuan aktif untuk aplikasi mobile NetFly.
     * Dapat difilter berdasarkan status atau kategori.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::with('creator')->latest('id');

        if ($request->filled('status') && $request->query('status') !== 'all') {
            $query->where('status', $request->query('status'));
        } else {
            // Default: ambil pengumuman aktif dan terbaru
            $query->whereIn('status', ['aktif', 'selesai']);
        }

        if ($request->filled('category') && $request->query('category') !== 'all') {
            $query->where('category', $request->query('category'));
        }

        $limit = max(1, min((int) ($request->query('limit', 20)), 50));
        $announcements = $query->limit($limit)->get();

        $formatted = $announcements->map(function ($item) {
            return [
                'id' => (string) $item->id,
                'code' => 'ANN-'.str_pad((string) $item->id, 3, '0', STR_PAD_LEFT),
                'title' => $item->title,
                'category' => $item->category,
                'category_label' => match (strtolower($item->category)) {
                    'maintenance' => 'Maintenance',
                    'gangguan' => 'Gangguan Jaringan',
                    'pembaruan' => 'Pembaruan Sistem',
                    default => 'Pengumuman Info',
                },
                'message' => $item->message,
                'priority' => $item->priority,
                'priority_label' => match (strtolower($item->priority)) {
                    'urgent' => 'Mendesak',
                    'tinggi' => 'Tinggi',
                    'rendah' => 'Rendah',
                    default => 'Normal',
                },
                'status' => $item->status,
                'scheduled_at' => $item->scheduled_at?->format('Y-m-d H:i:s'),
                'expires_at' => $item->expires_at?->format('Y-m-d H:i:s'),
                'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
                'time_ago' => $item->created_at?->diffForHumans() ?? 'Baru saja',
                'creator_name' => $item->creator?->name ?? 'Admin Konten NetFly',
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $formatted->count(),
            'data' => $formatted,
        ]);
    }

    /**
     * Detail pengumuman tunggal berdasarkan ID atau kode.
     */
    public function show(string $id): JsonResponse
    {
        $cleanId = preg_replace('/[^0-9]/', '', $id);
        $announcement = Announcement::with('creator')
            ->whereIn('status', ['aktif', 'selesai'])
            ->where('id', is_numeric($cleanId) && ! empty($cleanId) ? (int) $cleanId : 0)
            ->first();

        if (! $announcement) {
            return response()->json([
                'success' => false,
                'message' => "Pengumuman {$id} tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => (string) $announcement->id,
                'code' => 'ANN-'.str_pad((string) $announcement->id, 3, '0', STR_PAD_LEFT),
                'title' => $announcement->title,
                'category' => $announcement->category,
                'category_label' => match (strtolower($announcement->category)) {
                    'maintenance' => 'Maintenance',
                    'gangguan' => 'Gangguan Jaringan',
                    'pembaruan' => 'Pembaruan Sistem',
                    default => 'Pengumuman Info',
                },
                'message' => $announcement->message,
                'priority' => $announcement->priority,
                'priority_label' => match (strtolower($announcement->priority)) {
                    'urgent' => 'Mendesak',
                    'tinggi' => 'Tinggi',
                    'rendah' => 'Rendah',
                    default => 'Normal',
                },
                'status' => $announcement->status,
                'scheduled_at' => $announcement->scheduled_at?->format('Y-m-d H:i:s'),
                'expires_at' => $announcement->expires_at?->format('Y-m-d H:i:s'),
                'created_at' => $announcement->created_at?->format('Y-m-d H:i:s'),
                'time_ago' => $announcement->created_at?->diffForHumans() ?? 'Baru saja',
                'creator_name' => $announcement->creator?->name ?? 'Admin Konten NetFly',
            ],
        ]);
    }
}
