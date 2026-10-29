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

        $announcements = [
            [
                'title' => 'Jadwal Pemeliharaan Terjadwal (Maintenance) Core Router Surabaya',
                'category' => 'maintenance',
                'message' => 'Diberitahukan kepada seluruh administrator dan tim operasional bahwa akan dilakukan pemeliharaan berkala pada perangkat Core Router (POP Surabaya Barat) pada Sabtu dini hari pukul 01:00 - 04:00 WIB. Jalur traffic akan dialihkan ke rute proteksi redundant.',
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
        ];

        foreach ($announcements as $announcement) {
            Announcement::firstOrCreate(
                ['title' => $announcement['title']],
                $announcement
            );
        }
    }
}
