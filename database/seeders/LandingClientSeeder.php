<?php

namespace Database\Seeders;

use App\Models\LandingClient;
use Illuminate\Database\Seeder;

class LandingClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Prime Royal Hotel',
                'category' => 'Perhotelan',
                'location' => 'Surabaya',
                'logo' => 'assets/images/clients/logo-prime-royal.png',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Bebek Tepi Sawah',
                'category' => 'Resto',
                'location' => 'Jawa Timur',
                'logo' => 'assets/images/clients/logo-bebek-tepi-sawah.png',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'PT. GKM Mandiri',
                'category' => 'Konstruksi',
                'location' => 'Sidoarjo',
                'logo' => 'assets/images/clients/logo-gkm.png',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Klinik Al-Azhar',
                'category' => 'Kesehatan',
                'location' => 'Surabaya',
                'logo' => 'assets/images/clients/logo-al-azhar.png',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'UM Surabaya',
                'category' => 'Pendidikan',
                'location' => 'Surabaya',
                'logo' => 'assets/images/clients/logo-um-surabaya.png',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Univ. Jember (UNEJ)',
                'category' => 'PTN',
                'location' => 'Jember',
                'logo' => 'assets/images/clients/logo-unej.png',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Inez Cosmetics',
                'category' => 'Manufaktur',
                'location' => 'Jawa Timur',
                'logo' => 'assets/images/clients/logo-inez.png',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Fuji Seal Indonesia',
                'category' => 'Manufaktur',
                'location' => 'Global',
                'logo' => 'assets/images/clients/logo-fuji-seal.png',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($clients as $client) {
            LandingClient::firstOrCreate(
                ['name' => $client['name']],
                $client
            );
        }
    }
}
