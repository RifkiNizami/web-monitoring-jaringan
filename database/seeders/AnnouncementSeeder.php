<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data dummy pengumuman telah dibersihkan.
        // Data pemberitahuan dikelola secara mandiri oleh Admin Konten melalui modul CRUD.
        Announcement::truncate();
    }
}
