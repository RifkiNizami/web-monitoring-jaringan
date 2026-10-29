<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class MonitoringAlertTest extends TestCase
{
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::where('email', 'admin@netfly.id')->first() ?? User::factory()->create([
            'email' => 'admin@netfly.id',
            'role' => 'admin',
        ]);
    }

    /**
     * Memastikan halaman Monitoring & Alert dapat diakses dan menampilkan komponen telemetri.
     */
    public function test_monitoring_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/monitoring');

        $response->assertStatus(200);
        $response->assertViewIs('admin.monitoring.index');
        $response->assertSee('Monitoring Perangkat', false);
        $response->assertSee('AP-OUTDOOR-DISDIK-01');
        $response->assertSee('RTR-CORE-MANDIRI-01');
        $response->assertSee('SW-AGG-BKS-04');
    }

    /**
     * Memastikan endpoint resolve alert menghasilkan respons JSON yang valid.
     */
    public function test_resolve_alert_endpoint_works(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson('/monitoring/alerts/1/resolve', [
            'resolved' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'alert_id' => 1,
        ]);
    }

    /**
     * Memastikan endpoint poller ICMP/SNMP ping mengembalikan diagnostik jaringan.
     */
    public function test_device_polling_ping_endpoint_works(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson('/monitoring/devices/1/ping');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'device_id',
            'data' => [
                'hostname',
                'ip',
                'rtt_avg',
                'rtt_min',
                'rtt_max',
                'transmitted',
                'received',
                'loss',
                'snmp_status',
                'quality',
            ],
            'timestamp',
        ]);
        $response->assertJson([
            'success' => true,
            'device_id' => 1,
        ]);
    }
}
