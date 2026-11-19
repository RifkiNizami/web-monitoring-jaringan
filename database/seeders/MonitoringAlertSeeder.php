<?php

namespace Database\Seeders;

use App\Models\MonitoringAlert;
use App\Models\MonitoringDevice;
use Illuminate\Database\Seeder;

class MonitoringAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data telemetri dan alert lama agar monitoring bersih tanpa data dummy
        MonitoringAlert::query()->delete();
        MonitoringDevice::query()->delete();
    }
}
