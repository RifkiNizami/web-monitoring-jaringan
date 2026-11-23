<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseMigrationTest extends TestCase
{
    public function test_database_tables_exist_and_seed_data_is_present(): void
    {
        $this->assertTrue(Schema::hasTable('customers'));
        $this->assertTrue(Schema::hasTable('projects'));
        $this->assertTrue(Schema::hasTable('devices'));
        $this->assertTrue(Schema::hasTable('tickets'));
        $this->assertTrue(Schema::hasTable('monitoring_alerts'));
        $this->assertTrue(Schema::hasTable('monitoring_devices'));
        $this->assertTrue(Schema::hasTable('teknisi'));
        $this->assertTrue(Schema::hasTable('landing_clients'));
        $this->assertFalse(Schema::hasTable('network_sla_metrics'));

        $this->assertDatabaseHas('users', [
            'email' => 'admin@netfly.id',
        ]);
    }

    public function test_customer_model_relationships(): void
    {
        $customer = Customer::create([
            'customer_type' => 'perusahaan',
            'code' => 'CUST-TEST-01',
            'name' => 'PT. Test Relasi Mandiri',
            'pic_name' => 'Bambang PIC',
            'email' => 'relasi@test.com',
            'phone' => '021-998877',
            'address' => 'Jakarta',
            'status' => 'Aktif',
        ]);
        $project = $customer->projects()->create([
            'name' => 'Proyek Test Relasi',
            'branch_location' => 'Jakarta Hub',
            'service_type' => 'Dedicated Fiber 100 Mbps',
            'status' => 'Aktif',
        ]);

        $this->assertNotNull($customer);
        $this->assertGreaterThan(0, $customer->projects()->count());

        $project->delete();
        $customer->delete();
    }
}
