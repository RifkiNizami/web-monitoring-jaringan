<?php

namespace App\Http\Controllers\AdminKonten;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KontenPemberitahuanController extends Controller
{
    /**
     * Tampilkan daftar pemberitahuan / announcement.
     * Admin Konten mendapatkan halaman kelola penuh (CRUD),
     * Admin Biasa mendapatkan halaman tinjau/lihat informasi.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $status = $request->query('status');

        $query = Announcement::with('creator')->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if (!empty($category) && $category !== 'all') {
            $query->where('category', $category);
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        $announcements = $query->paginate(12)->withQueryString();

        $totalCount = Announcement::count();
        $maintenanceCount = Announcement::where('category', 'maintenance')->where('status', 'aktif')->count();
        $activeCount = Announcement::where('status', 'aktif')->count();
        $systemUpdateCount = Announcement::where('category', 'pembaruan')->where('status', 'aktif')->count();

        // Tampilan khusus sesuai peran pengguna
        if (Auth::user()->role === 'admin_konten') {
            return view('admin-konten.konten_pemberitahuan', compact(
                'announcements',
                'totalCount',
                'activeCount',
                'maintenanceCount',
                'systemUpdateCount',
                'search',
                'category',
                'status'
            ));
        }

        // Tampilan tinjau untuk Admin Biasa
        return view('admin-konten.konten_pemberitahuan_tinjau', compact(
            'announcements',
            'totalCount',
            'activeCount',
            'maintenanceCount',
            'systemUpdateCount',
            'search',
            'category',
            'status'
        ));
    }

    /**
     * Simpan pengumuman baru (khusus Admin Konten).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:maintenance,gangguan,pembaruan,info',
            'message' => 'required|string',
            'priority' => 'required|in:rendah,normal,tinggi,urgent',
            'scheduled_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:scheduled_at',
            'status' => 'nullable|in:aktif,selesai,diarsipkan',
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'category.required' => 'Kategori pengumuman wajib dipilih.',
            'message.required' => 'Detail isi pemberitahuan wajib diisi.',
            'priority.required' => 'Tingkat urgensi / prioritas wajib dipilih.',
            'expires_at.after_or_equal' => 'Waktu selesai harus setelah atau sama dengan waktu mulai.',
        ]);

        Announcement::create([
            'title' => $request->title,
            'category' => $request->category,
            'message' => $request->message,
            'priority' => $request->priority,
            'status' => $request->input('status', 'aktif') ?? 'aktif',
            'scheduled_at' => $request->scheduled_at,
            'expires_at' => $request->expires_at,
            'created_by' => Auth::id(),
        ]);

        $redirectRoute = $request->routeIs('pemberitahuan.*') ? 'pemberitahuan.index' : 'announcements.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Pemberitahuan baru berhasil dipublikasikan.');
    }

    /**
     * Perbarui pengumuman (khusus Admin Konten).
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:maintenance,gangguan,pembaruan,info',
            'message' => 'required|string',
            'priority' => 'required|in:rendah,normal,tinggi,urgent',
            'scheduled_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:scheduled_at',
            'status' => 'required|in:aktif,selesai,diarsipkan',
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'category.required' => 'Kategori pengumuman wajib dipilih.',
            'message.required' => 'Detail isi pemberitahuan wajib diisi.',
        ]);

        $announcement->update([
            'title' => $request->title,
            'category' => $request->category,
            'message' => $request->message,
            'priority' => $request->priority,
            'status' => $request->status,
            'scheduled_at' => $request->scheduled_at,
            'expires_at' => $request->expires_at,
        ]);

        $redirectRoute = $request->routeIs('pemberitahuan.*') ? 'pemberitahuan.index' : 'announcements.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Pemberitahuan berhasil diperbarui.');
    }

    /**
     * Hapus pengumuman (khusus Admin Konten).
     */
    public function destroy(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        $title = $announcement->title;
        $announcement->delete();

        $redirectRoute = $request->routeIs('pemberitahuan.*') ? 'pemberitahuan.index' : 'announcements.index';

        return redirect()->route($redirectRoute)
            ->with('success', "Pemberitahuan '{$title}' telah dihapus.");
    }

    /**
     * Ubah status pengumuman (aktif/selesai).
     */
    public function toggleStatus(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->status = ($announcement->status === 'aktif') ? 'selesai' : 'aktif';
        $announcement->save();

        $redirectRoute = $request->routeIs('pemberitahuan.*') ? 'pemberitahuan.index' : 'announcements.index';

        return redirect()->route($redirectRoute)
            ->with('success', "Status pemberitahuan berhasil diubah menjadi '{$announcement->status}'.");
    }
}
