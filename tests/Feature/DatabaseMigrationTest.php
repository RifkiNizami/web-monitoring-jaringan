<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Device;
use App\Models\MonitoringAlert;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseMigrationTest extends TestCase
{
    public function test_database_tables_exist_and_seed_data_is_present(): void
    {
        $this->assertDatabaseHas('customers', [
            'code' => 'CUST-0001',
            'name' => 'PT. Bank Mandiri (Persero) Tbk',
        ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Smart City Backbone Sekolah Tahap II',
        ]);

        $this->assertDatabaseHas('devices', [
            'hostname' => 'RTR-CORE-MANDIRI-01',
        ]);

        $this->assertDatabaseHas('tickets', [
            'ticket_number' => 'INC-2026-1024',
        ]);

        $this->assertDatabaseHas('monitoring_alerts', [
            'severity' => 'critical',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@netfly.id',
        ]);
    }

    public function test_customer_model_relationships(): void
    {
        $customer = Customer::where('code', 'CUST-0001')->first();
        $this->assertNotNull($customer);
        $this->assertGreaterThan(0, $customer->projects()->count());
        $this->assertGreaterThan(0, $customer->devices()->count());
    }
}
