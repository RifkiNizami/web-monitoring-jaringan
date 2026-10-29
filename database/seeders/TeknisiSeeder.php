<?php

namespace Database\Seeders;

use App\Models\Teknisi;
use Illuminate\Database\Seeder;

class TeknisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teknisis = [
            [
                'nama' => 'Agus Prasetyo, S.T.',
                'alamat' => 'Jl. Tebet Barat Raya No. 14, Tebet, Jakarta Selatan',
                'nomor_hp' => '0812-8877-1234',
                
            ],
            [
                'nama' => 'Budi Santoso',
                'alamat' => 'Jl. Boulevard Kelapa Gading Blok M No. 8, Jakarta Utara',
                'nomor_hp' => '0813-9988-7766',
                
            ],
            [
                'nama' => 'Rahmat Hidayat',
                'alamat' => 'Jl. Margonda Raya No. 102, Beji, Depok',
                'nomor_hp' => '0857-1122-3344',
                
            ],
            [
                'nama' => 'Dimas Nugroho',
                'alamat' => 'Jl. Warung Buncit Raya No. 45, Mampang Prapatan, Jakarta Selatan',
                'nomor_hp' => '0878-5544-3322',
               
            ],
            [
                'nama' => 'Fajar Maulana',
                'alamat' => 'Perum Pondok Gede Permai Blok C4 No. 12, Bekasi',
                'nomor_hp' => '0812-3456-7890',
               
            ],
        ];

        foreach ($teknisis as $teknisi) {
            Teknisi::firstOrCreate(
                ['nomor_hp' => $teknisi['nomor_hp']],
                $teknisi
            );
        }
    }
}
