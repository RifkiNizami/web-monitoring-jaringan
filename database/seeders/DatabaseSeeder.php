<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Core Master Data
        $this->call([
            CustomerSeeder::class,
            ProjectSeeder::class,
            DeviceSeeder::class,
            TicketSeeder::class,
            MonitoringAlertSeeder::class,
            NetworkSlaMetricSeeder::class,
            TeknisiSeeder::class,
            LandingClientSeeder::class,
        ]);

        // 2. Admin users
        User::firstOrCreate(
            ['email' => 'admin@netfly.id'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '0811-9988-7766',
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin_gmail',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '0812-3456-7890',
            ]
        );

        // 3. Admin Konten (Peran Pengelola Konten Landing Page & Pengumuman)
        User::firstOrCreate(
            ['email' => 'konten@netfly.id'],
            [
                'name' => 'Admin Konten',
                'username' => 'adminkonten',
                'password' => Hash::make('password123'),
                'role' => 'admin_konten',
                'is_active' => true,
                'phone' => '0812-9900-1122',
            ]
        );

        // Seed Announcements setelah user admin konten terbuat
        $this->call([
            AnnouncementSeeder::class,
        ]);

    }
}
