<?php

namespace App\Http\Controllers;

use App\Models\LandingClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LandingClientController extends Controller
{
    /**
     * Tampilkan halaman daftar klien landing page.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $status = $request->query('status');

        $query = LandingClient::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if (!empty($category) && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($status !== null && $status !== '' && $status !== 'all') {
            $query->where('is_active', $status === '1' || $status === 'true');
        }

        $clients = $query->orderBy('order', 'asc')->orderBy('id', 'desc')->get();

        $categories = LandingClient::select('category')->distinct()->pluck('category')->filter()->values();
        $totalClients = LandingClient::count();
        $activeClients = LandingClient::where('is_active', true)->count();
        $inactiveClients = $totalClients - $activeClients;

        return view('admin.landing_clients', compact(
            'clients',
            'categories',
            'totalClients',
            'activeClients',
            'inactiveClients',
            'search',
            'category',
            'status'
        ));
    }

    /**
     * Simpan data klien baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
            'website_url' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:3072',
            'is_active' => 'nullable',
        ], [
            'name.required' => 'Nama instansi/klien wajib diisi.',
            'category.required' => 'Kategori industri wajib diisi.',
            'location.required' => 'Lokasi/Kota wajib diisi.',
            'logo.image' => 'File logo harus berupa gambar yang valid (PNG, JPG, SVG, WebP).',
            'logo.max' => 'Ukuran file logo maksimal 3 MB.',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'client_' . time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/clients');
            
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            
            $file->move($destination, $filename);
            $logoPath = 'uploads/clients/' . $filename;
        }

        LandingClient::create([
            'name' => $request->name,
            'category' => $request->category,
            'location' => $request->location,
            'logo' => $logoPath,
            'website_url' => $request->website_url,
            'order' => $request->input('order', 0) ?? 0,
            'is_active' => $request->has('is_active'),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('landing-clients.index')
            ->with('success', "Klien '{$request->name}' berhasil ditambahkan ke landing page.");
    }

    /**
     * Perbarui data klien.
     */
    public function update(Request $request, $id)
    {
        $client = LandingClient::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
            'website_url' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:3072',
            'is_active' => 'nullable',
        ], [
            'name.required' => 'Nama instansi/klien wajib diisi.',
            'category.required' => 'Kategori industri wajib diisi.',
            'location.required' => 'Lokasi/Kota wajib diisi.',
            'logo.image' => 'File logo harus berupa gambar yang valid.',
        ]);

        $data = [
            'name' => $request->name,
            'category' => $request->category,
            'location' => $request->location,
            'website_url' => $request->website_url,
            'order' => $request->input('order', 0) ?? 0,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'client_' . time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/clients');

            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            // Hapus file lama jika ada di uploads/clients
            if ($client->logo && Str::startsWith($client->logo, 'uploads/clients/')) {
                $oldPath = public_path($client->logo);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $file->move($destination, $filename);
            $data['logo'] = 'uploads/clients/' . $filename;
        }

        $client->update($data);

        return redirect()->route('landing-clients.index')
            ->with('success', "Data klien '{$client->name}' berhasil diperbarui.");
    }

    /**
     * Hapus data klien dari landing page.
     */
    public function destroy($id)
    {
        $client = LandingClient::findOrFail($id);
        $clientName = $client->name;

        // Hapus file logo jika ada di uploads/clients
        if ($client->logo && Str::startsWith($client->logo, 'uploads/clients/')) {
            $path = public_path($client->logo);
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $client->delete();

        return redirect()->route('landing-clients.index')
            ->with('success', "Klien '{$clientName}' berhasil dihapus dari landing page.");
    }

    /**
     * Toggle status aktif/nonaktif cepat.
     */
    public function toggleStatus($id)
    {
        $client = LandingClient::findOrFail($id);
        $client->is_active = !$client->is_active;
        $client->save();

        $statusText = $client->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('landing-clients.index')
            ->with('success', "Status klien '{$client->name}' berhasil {$statusText}.");
    }
}
