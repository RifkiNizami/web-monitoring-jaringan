<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeviceInventoryCrudTest extends TestCase
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
    }

    public function test_create_inventory_device_page_renders(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/devices/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.devices.create');
        $response->assertSee('Tambah Barang Inventaris Baru', false);
    }

    public function test_admin_can_store_inventory_device(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/devices', [
            'hostname' => 'SW-DIST-99',
            'type' => 'Switch',
            'brand_model' => 'Cisco Catalyst 9300',
            'serial_number' => 'SN-CISCO9300X',
            'quantity' => 5,
            'status' => 'Tersedia',
        ]);

        $response->assertRedirect(route('devices.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('devices', [
            'hostname' => 'SW-DIST-99',
            'type' => 'Switch',
            'brand_model' => 'Cisco Catalyst 9300',
            'serial_number' => 'SN-CISCO9300X',
            'quantity' => 5,
            'status' => 'Tersedia',
            'status_state' => 'available',
        ]);

        $listResponse = $this->actingAs($this->adminUser)->get('/devices');
        $listResponse->assertSee('SW-DIST-99');
        $listResponse->assertSee('Cisco Catalyst 9300');
        $listResponse->assertSee('SN-CISCO9300X');
    }

    public function test_edit_inventory_device_page_renders(): void
    {
        $this->actingAs($this->adminUser)->get('/devices');

        $response = $this->actingAs($this->adminUser)->get('/devices/1/edit');

        $response->assertStatus(200);
        $response->assertViewIs('admin.devices.edit');
        $response->assertSee('Edit Data Barang Inventaris', false);
        $response->assertSee('RTR-CORE-01', false);
    }

    public function test_admin_can_update_inventory_device(): void
    {
        $this->actingAs($this->adminUser)->get('/devices');

        $response = $this->actingAs($this->adminUser)->put('/devices/1', [
            'hostname' => 'RTR-CORE-01-REV',
            'type' => 'Router',
            'brand_model' => 'MikroTik CCR2116-12G-4S+',
            'serial_number' => 'SN-UPDATED01',
            'quantity' => 10,
            'status' => 'Terpakai',
        ]);

        $response->assertRedirect(route('devices.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('devices', [
            'id' => 1,
            'hostname' => 'RTR-CORE-01-REV',
            'brand_model' => 'MikroTik CCR2116-12G-4S+',
            'serial_number' => 'SN-UPDATED01',
            'quantity' => 10,
            'status' => 'Terpakai',
            'status_state' => 'in_use',
        ]);

        $listResponse = $this->actingAs($this->adminUser)->get('/devices');
        $listResponse->assertSee('RTR-CORE-01-REV');
        $listResponse->assertSee('MikroTik CCR2116-12G-4S+');
    }

    public function test_admin_can_delete_inventory_device(): void
    {
        $this->actingAs($this->adminUser)->get('/devices');

        $response = $this->actingAs($this->adminUser)->delete('/devices/1');

        $response->assertRedirect(route('devices.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('devices', [
            'id' => 1,
        ]);
    }
}
