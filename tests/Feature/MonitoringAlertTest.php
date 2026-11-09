<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MonitoringAlertTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        config(['activity_logger.enabled_in_tests' => true]);
        $this->adminUser = User::where('email', 'admin@netfly.id')->first() ?? User::factory()->create([
            'email' => 'admin@netfly.id',
            'role' => 'admin',
        ]);

        // Inisialisasi mock devices di database untuk unit/feature testing
        \App\Http\Controllers\Admin\MonitoringController::saveTelemetryDevicesList([
            1 => [
                'id' => 1,
                'hostname' => 'AP-OUTDOOR-DISDIK-01',
                'unit_type' => 'Access Point',
                'ip_address' => '10.200.120.15',
                'customer' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'project' => 'Smart City Backbone Sekolah Tahap II',
                'model' => 'MikroTik NetMetal 5',
                'rack_location' => 'Pole A-Disdik',
                'status' => 'warning',
                'status_label' => 'WARNING',
                'uptime' => '4h 0j 0m',
                'rx_mbps' => '45.2M',
                'tx_mbps' => '30.1M',
                'cpu_load' => 78.5,
                'ram_load' => 82.0,
                'temperature' => '54°C',
                'voltage' => '24.1 V',
                'packet_loss' => 14.5,
                'last_ping_ms' => 45,
                'interfaces' => [],
            ],
            4 => [
                'id' => 4,
                'hostname' => 'FW-EDGE-XYZ-01',
                'unit_type' => 'Firewall',
                'ip_address' => '10.200.40.1',
                'customer' => 'PT. XYZ Teknologi',
                'status' => 'online',
                'status_label' => 'ONLINE',
                'uptime' => '89h 4j 12m',
                'rx_mbps' => '120M',
                'tx_mbps' => '95M',
                'cpu_load' => 34.0,
                'ram_load' => 52.0,
                'last_ping_ms' => 6,
                'packet_loss' => 0.0,
                'interfaces' => [],
            ],
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
    }

    /**
     * Memastikan halaman Monitoring menampilkan empty state saat data perangkat belum ada.
     */
    public function test_monitoring_page_renders_empty_state_cleanly(): void
    {
        \App\Http\Controllers\Admin\MonitoringController::saveTelemetryDevicesList([]);
        $response = $this->actingAs($this->adminUser)->get('/monitoring');

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Perangkat Monitoring', false);
        $response->assertSee('Semua Jaringan Normal', false);
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

    /**
     * Memastikan halaman formulir tambah perangkat dapat diakses.
     */
    public function test_create_device_view_renders_successfully(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/monitoring/devices/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.monitoring.create');
        $response->assertSee('Tambah Perangkat Monitoring', false);
        $response->assertSee('CONTOH: RTR-CORE-SBY-01', false);
    }

    /**
     * Memastikan halaman formulir edit perangkat dapat diakses.
     */
    public function test_edit_device_view_renders_successfully(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/monitoring/devices/1/edit');

        $response->assertStatus(200);
        $response->assertViewIs('admin.monitoring.edit');
        $response->assertSee('Edit Perangkat Monitoring', false);
        $response->assertSee('AP-OUTDOOR-DISDIK-01', false);
    }

    /**
     * Memastikan admin dapat memperbarui data perangkat secara lengkap di database.
     */
    public function test_admin_can_update_device_in_monitoring(): void
    {
        $response = $this->actingAs($this->adminUser)->putJson('/monitoring/devices/1', [
            'hostname' => 'AP-OUTDOOR-DISDIK-UPDATED',
            'unit_type' => 'Access Point',
            'ip_address' => '10.200.120.99',
            'customer' => 'Dinas Pendidikan Provinsi DKI Jakarta',
            'status' => 'online',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonPath('device.hostname', 'AP-OUTDOOR-DISDIK-UPDATED');

        $this->assertDatabaseHas('monitoring_devices', [
            'id' => 1,
            'hostname' => 'AP-OUTDOOR-DISDIK-UPDATED',
            'ip_address' => '10.200.120.99',
            'status' => 'online',
        ]);
    }

    /**
     * Memastikan admin dapat mendaftarkan perangkat baru langsung ke database monitoring.
     */
    public function test_admin_can_add_device_to_monitoring(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson('/monitoring/devices', [
            'hostname' => 'RTR-EDGE-TEST-99',
            'unit_type' => 'Router',
            'ip_address' => '10.200.99.1',
            'customer' => 'PT Mitra Baru Mandiri',
            'project' => 'Dedicated Core Interconnect',
            'status' => 'online',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonPath('device.hostname', 'RTR-EDGE-TEST-99');

        $this->assertDatabaseHas('monitoring_devices', [
            'hostname' => 'RTR-EDGE-TEST-99',
            'ip_address' => '10.200.99.1',
            'customer' => 'PT Mitra Baru Mandiri',
        ]);
    }

    /**
     * Memastikan admin dapat menghapus perangkat dari monitoring database secara mandiri.
     */
    public function test_admin_can_delete_device_from_monitoring(): void
    {
        $response = $this->actingAs($this->adminUser)->deleteJson('/monitoring/devices/4');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseMissing('monitoring_devices', [
            'id' => 4,
        ]);
    }

    /**
     * Memastikan admin dapat memperbarui konfigurasi IP/Port dan kredensial API perangkat monitoring di database.
     */
    public function test_admin_can_save_monitoring_device_settings(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson('/monitoring/settings', [
            'device_id' => 1,
            'ip_address' => '10.200.120.99',
            'api_port' => 8728,
            'api_username' => 'testuser',
            'api_password' => 'secret123',
            'connection_type' => 'zerotier',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Konfigurasi IP & Kredensial API MikroTik berhasil diperbarui.',
        ]);
        $response->assertJsonPath('data.ip_address', '10.200.120.99');

        $this->assertDatabaseHas('monitoring_devices', [
            'id' => 1,
            'ip_address' => '10.200.120.99',
            'api_username' => 'testuser',
            'connection_type' => 'zerotier',
        ]);
    }
}
