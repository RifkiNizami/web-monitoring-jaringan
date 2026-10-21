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
        // Admin user
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

        // Akun Perwakilan Customer B2B
        $customerSeeders = [
            [
                'name' => 'Bambang Kurniawan, S.Kom',
                'username' => 'pic_mandiri',
                'email' => 'pic.noc@bankmandiri.co.id',
                'password' => Hash::make('password123'),
                'customer_id' => 1,
                'role' => 'customer_representative',
                'is_active' => true,
                'phone' => '0812-8877-6655',
            ],
            [
                'name' => 'Jessica Tanuwijaya',
                'username' => 'noc_shopee',
                'email' => 'noc-corp@shopee.co.id',
                'password' => Hash::make('password123'),
                'customer_id' => 2,
                'role' => 'customer_representative',
                'is_active' => true,
                'phone' => '0813-1122-3344',
            ],
            [
                'name' => 'Ir. Hendra Wijaya, M.T.',
                'username' => 'disdik_jkt',
                'email' => 'admin.jaringan@disdik.jakarta.go.id',
                'password' => Hash::make('password123'),
                'customer_id' => 3,
                'role' => 'customer_representative',
                'is_active' => false,
                'phone' => '0815-9988-1234',
            ],
            [
                'name' => 'Rian Saputra',
                'username' => 'it_xyz_tech',
                'email' => 'infra@xyzteknologi.id',
                'password' => Hash::make('password123'),
                'customer_id' => 4,
                'role' => 'customer_representative',
                'is_active' => true,
                'phone' => '0821-4455-6677',
            ],
            [
                'name' => 'dr. Anton Pratama, Sp.A',
                'username' => 'medika_simrs',
                'email' => 'simrs@medikacenter.org',
                'password' => Hash::make('password123'),
                'customer_id' => 5,
                'role' => 'customer_representative',
                'is_active' => true,
                'phone' => '0818-7766-5544',
            ],
        ];

        foreach ($customerSeeders as $custUser) {
            User::firstOrCreate(
                ['email' => $custUser['email']],
                $custUser
            );
        }
    }
}
