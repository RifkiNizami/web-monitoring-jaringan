<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creator = User::where('role', 'admin_konten')->first() ?? User::where('role', 'admin')->first();
        $creatorId = $creator ? $creator->id : null;

        // Bersihkan duplikasi berulang dari pengujian otomatis sebelumnya
        $duplicateIds = Announcement::where('title', 'Maintenance Rutin Core Node Jakarta')
            ->orderBy('id', 'asc')
            ->skip(1)
            ->take(100)
            ->pluck('id');

        if ($duplicateIds->isNotEmpty()) {
            Announcement::whereIn('id', $duplicateIds)->delete();
        }

        $announcements = [
            [
                'title' => 'Maintenance Rutin Core Node Jakarta',
                'category' => 'maintenance',
                'message' => 'Akan dilakukan perawatan berkala sistem power suplai dan modul pendingin pada rak Core Node Jakarta Gedung Cyber 1. Seluruh link failover otomatis dialihkan ke link proteksi redundansi.',
                'priority' => 'tinggi',
                'status' => 'aktif',
                'scheduled_at' => now()->addDay()->setTime(1, 0),
                'expires_at' => now()->addDay()->setTime(4, 0),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Jadwal Pemeliharaan Terjadwal (Maintenance) Core Router Surabaya',
                'category' => 'maintenance',
                'message' => 'Diberitahukan kepada seluruh administrator dan tim operasional bahwa akan dilakukan pemeliharaan berkala pada perangkat Core Router (POP Surabaya Barat). Jalur traffic telah dialihkan ke rute proteksi redundant tanpa penurunan performa.',
                'priority' => 'tinggi',
                'status' => 'aktif',
                'scheduled_at' => now()->addDays(2)->setTime(1, 0),
                'expires_at' => now()->addDays(2)->setTime(5, 0),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Pembaruan Firmware Switch Aggregation Wilayah Timur',
                'category' => 'pembaruan',
                'message' => 'Proses update patch keamanan firmware untuk 12 unit Edge Switch telah selesai dilakukan dengan sukses. Peningkatan stabilitas latency dan perbaikan monitoring throughput kini sudah aktif.',
                'priority' => 'normal',
                'status' => 'aktif',
                'scheduled_at' => now()->subDay(),
                'expires_at' => now()->addDays(7),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Optimalisasi Bandwidth Peering SCBD Direct Datacenter',
                'category' => 'info',
                'message' => 'Kapasitas interkoneksi uplink direct peering SCBD telah ditingkatkan menjadi 20 Gbps. Monitoring SLA menunjukkan packet loss 0% dan respon latensi di bawah 2ms untuk seluruh segmen perbankan.',
                'priority' => 'normal',
                'status' => 'aktif',
                'scheduled_at' => now()->subHours(12),
                'expires_at' => now()->addDays(14),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Insiden Fiber Optik Cut Jalur Tol Cikampek KM 42 (Terselesaikan)',
                'category' => 'gangguan',
                'message' => 'Terjadi putus kabel fiber optik akibat pekerjaan konstruksi jalan di KM 42 Tol Jakarta-Cikampek. Tim teknisi darurat telah melakukan penarikan kabel pengganti dan penyambungan fusion splicing. Jalur dinyatakan 100% pulih normal.',
                'priority' => 'urgent',
                'status' => 'selesai',
                'scheduled_at' => now()->subHours(8),
                'expires_at' => now()->subHour(),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Penambahan Interkoneksi Redundant Datacenter DCI Cibitung',
                'category' => 'pembaruan',
                'message' => 'Kanal transmisi serat optik redundant link ke-3 menuju Datacenter Tier-IV DCI Cibitung telah resmi terpasang dan lolos uji OTDR & RFC2544 throughput test.',
                'priority' => 'normal',
                'status' => 'aktif',
                'scheduled_at' => now()->subDays(3),
                'expires_at' => now()->addDays(10),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Uji Beban Genset & Backup Power POP Medan',
                'category' => 'maintenance',
                'message' => 'Jadwal preventive maintenance bulanan untuk uji operasional genset cadangan dan kalibrasi unit UPS pada POP Pusat Distribusi Medan. Layanan jaringan pelanggan tidak mengalami gangguan.',
                'priority' => 'rendah',
                'status' => 'aktif',
                'scheduled_at' => now()->addDays(4)->setTime(23, 0),
                'expires_at' => now()->addDays(5)->setTime(2, 0),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Peningkatan Kapasitas Backbone Trans-Jawa 100G',
                'category' => 'info',
                'message' => 'Proyek modernisasi backbone transmisi DWDM 100 Gbps rute Jakarta - Semarang - Surabaya telah aktif dan siap menampung lonjakan kapasitas trafik kuartal akhir.',
                'priority' => 'normal',
                'status' => 'aktif',
                'scheduled_at' => now()->subDays(5),
                'expires_at' => now()->addDays(20),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Migrasi Subnet IP Pelanggan Korporasi Segmen SCBD',
                'category' => 'pembaruan',
                'message' => 'Tim NOC akan melakukan alokasi ulang blok subnet IP Public /24 untuk segmen pelanggan perbankan di SCBD guna mengoptimalkan routing BGP multihoming.',
                'priority' => 'tinggi',
                'status' => 'aktif',
                'scheduled_at' => now()->addDays(3)->setTime(2, 0),
                'expires_at' => now()->addDays(3)->setTime(4, 30),
                'created_by' => $creatorId,
            ],
            [
                'title' => 'Peringatan Cuaca Ekstrem & Kesiagaan Tim Reaksi Cepat NOC',
                'category' => 'info',
                'message' => 'Menyikapi peringatan BMKG terkait potensi hujan lebat dan angin kencang di Jabodetabek, seluruh tim teknisi lapangan dan NOC siaga 24/7 untuk pemantauan tower BTS dan perangkat outdoor.',
                'priority' => 'tinggi',
                'status' => 'aktif',
                'scheduled_at' => now()->subDay(),
                'expires_at' => now()->addDays(3),
                'created_by' => $creatorId,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::updateOrCreate(
                ['title' => $announcement['title']],
                $announcement
            );
        }
    }
}
