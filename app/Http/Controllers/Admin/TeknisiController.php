<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teknisi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeknisiController extends Controller
{
    /**
     * Tampilkan halaman database dan manajemen teknisi
     */
    public function index(Request $request): View
    {
        $query = Teknisi::query()->latest();

        $searchQuery = $request->query('q');
        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('nama', 'like', "%{$searchQuery}%")
                  ->orWhere('alamat', 'like', "%{$searchQuery}%")
                  ->orWhere('nomor_hp', 'like', "%{$searchQuery}%");
            });
        }

        $teknisis = $query->get();

        return view('admin.teknisi', compact('teknisis', 'searchQuery'));
    }

    /**
     * Simpan teknisi baru ke dalam database (nama, alamat, nomor hp)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'alamat' => ['required', 'string', 'max:500'],
            'nomor_hp' => ['required', 'string', 'max:50'],
        ], [
            'nama.required' => 'Nama teknisi wajib diisi.',
            'alamat.required' => 'Alamat teknisi wajib diisi.',
            'nomor_hp.required' => 'Nomor HP teknisi wajib diisi.',
        ]);

        $teknisi = Teknisi::create($validated);

        return redirect()->route('teknisi.index')->with('success', "Teknisi '{$teknisi->nama}' berhasil ditambahkan ke database.");
    }

    /**
     * Perbarui data teknisi (nama, alamat, nomor hp)
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $teknisi = Teknisi::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'alamat' => ['required', 'string', 'max:500'],
            'nomor_hp' => ['required', 'string', 'max:50'],
        ], [
            'nama.required' => 'Nama teknisi wajib diisi.',
            'alamat.required' => 'Alamat teknisi wajib diisi.',
            'nomor_hp.required' => 'Nomor HP teknisi wajib diisi.',
        ]);

        $teknisi->update($validated);

        return redirect()->route('teknisi.index')->with('success', "Data teknisi '{$teknisi->nama}' berhasil diperbarui.");
    }

    /**
     * Hapus teknisi dari database
     */
    public function destroy(int $id): RedirectResponse
    {
        $teknisi = Teknisi::findOrFail($id);
        $nama = $teknisi->nama;
        $teknisi->delete();

        return redirect()->route('teknisi.index')->with('success', "Teknisi '{$nama}' berhasil dihapus dari sistem.");
    }

    /**
     * Redirect langsung admin ke WhatsApp teknisi
     */
    public function redirectToWhatsapp(int $id): RedirectResponse
    {
        $teknisi = Teknisi::findOrFail($id);
        return redirect()->away($teknisi->whatsapp_url);
    }
}
