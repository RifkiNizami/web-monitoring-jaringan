<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        config(['activity_logger.enabled_in_tests' => true]);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@netfly.id'],
            [
                'name' => 'Administrator',
                'username' => 'admin_test',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    public function test_admin_can_view_customers_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('customers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.customers');
        $response->assertSee('Customer Management');
        $response->assertSee('Customer Rumahan');
        $response->assertSee('Customer Perusahaan');
    }

    public function test_admin_can_store_residential_customer(): void
    {
        $payload = [
            'name' => 'Pak Joko Widodo',
            'status' => 'Aktif',
            'email' => 'jokowi@testmail.com',
            'phone' => '0812-9988-7711',
            'address' => 'Jl. Solo Raya No. 42, Surakarta',
            'note' => 'Paket Home Ultra Fiber 100Mbps',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('customers.store'), $payload);

        $response->assertRedirect(route('customers.index', ['tab' => 'customer']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'name' => 'Pak Joko Widodo',
            'customer_type' => 'rumahan',
            'email' => 'jokowi@testmail.com',
            'status' => 'Aktif',
        ]);

        $created = Customer::where('email', 'jokowi@testmail.com')->first();
        $this->assertNotNull($created);
        $this->assertStringStartsWith('HOME-', $created->code);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'kemitraan',
            'action' => 'create',
            'subject_id' => (string) $created->id,
        ]);
    }

    public function test_admin_can_update_residential_customer(): void
    {
        $customer = Customer::create([
            'customer_type' => 'rumahan',
            'code' => 'HOME-9999',
            'name' => 'Customer Lama',
            'pic_name' => '-',
            'email' => 'lama@test.com',
            'phone' => '0811-0000-1111',
            'address' => 'Alamat Lama No. 1',
            'status' => 'Aktif',
            'note' => 'Paket Reguler',
        ]);

        $payload = [
            'name' => 'Customer Baru Diperbarui',
            'status' => 'Negosiasi',
            'email' => 'baru@test.com',
            'phone' => '0811-2222-3333',
            'address' => 'Alamat Baru No. 99',
            'note' => 'Paket Premium Upgrade',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('customers.update', $customer->id), $payload);

        $response->assertRedirect(route('customers.index', ['tab' => 'customer']));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Customer Baru Diperbarui',
            'email' => 'baru@test.com',
            'status' => 'Negosiasi',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'kemitraan',
            'action' => 'update',
            'subject_id' => (string) $customer->id,
        ]);
    }

    public function test_admin_can_delete_residential_customer(): void
    {
        $customer = Customer::create([
            'customer_type' => 'rumahan',
            'code' => 'HOME-8888',
            'name' => 'Customer Hapus',
            'pic_name' => '-',
            'email' => 'hapus@test.com',
            'phone' => '0811-4444-5555',
            'address' => 'Alamat Hapus',
            'status' => 'Nonaktif',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('customers.destroy', $customer->id));

        $response->assertRedirect(route('customers.index', ['tab' => 'customer']));
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'kemitraan',
            'action' => 'delete',
            'subject_id' => (string) $customer->id,
        ]);
    }

    public function test_admin_can_store_corporate_customer(): void
    {
        $payload = [
            'name' => 'PT. Inovasi Digital Nusantara',
            'pic_name' => 'Andi Wijaya, M.Kom',
            'status' => 'Aktif',
            'email' => 'andi@inovasidigital.id',
            'phone' => '021-77889900',
            'address' => 'Menara Cyber Kuningan Lt. 15, Jakarta Selatan',
            'note' => 'Dedicated IP Transit & SLA 99.9% 1Gbps',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('projects.store'), $payload);

        $response->assertRedirect(route('customers.index', ['tab' => 'project']));

        $this->assertDatabaseHas('customers', [
            'name' => 'PT. Inovasi Digital Nusantara',
            'customer_type' => 'perusahaan',
            'pic_name' => 'Andi Wijaya, M.Kom',
            'email' => 'andi@inovasidigital.id',
        ]);

        $created = Customer::where('email', 'andi@inovasidigital.id')->first();
        $this->assertNotNull($created);
        $this->assertStringStartsWith('CUST-', $created->code);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'kemitraan',
            'action' => 'create',
            'subject_id' => (string) $created->id,
        ]);
    }

    public function test_admin_can_update_corporate_customer(): void
    {
        $corporate = Customer::create([
            'customer_type' => 'perusahaan',
            'code' => 'CUST-9999',
            'name' => 'PT. Korporat Lama',
            'pic_name' => 'PIC Lama',
            'email' => 'corplama@test.com',
            'phone' => '021-11223344',
            'address' => 'Gedung Lama Lt. 1',
            'status' => 'Aktif',
        ]);

        $payload = [
            'name' => 'PT. Korporat Baru',
            'pic_name' => 'PIC Baru M.T.',
            'status' => 'Negosiasi',
            'email' => 'corpbaru@test.com',
            'phone' => '021-99887766',
            'address' => 'Gedung Baru Lt. 10',
            'note' => 'Perubahan Kontrak Fiber',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('projects.update', $corporate->id), $payload);

        $response->assertRedirect(route('customers.index', ['tab' => 'project']));

        $this->assertDatabaseHas('customers', [
            'id' => $corporate->id,
            'name' => 'PT. Korporat Baru',
            'pic_name' => 'PIC Baru M.T.',
            'status' => 'Negosiasi',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'kemitraan',
            'action' => 'update',
            'subject_id' => (string) $corporate->id,
        ]);
    }

    public function test_admin_can_delete_corporate_customer(): void
    {
        $corporate = Customer::create([
            'customer_type' => 'perusahaan',
            'code' => 'CUST-8888',
            'name' => 'PT. Perusahaan Dihapus',
            'pic_name' => 'PIC Hapus',
            'email' => 'corphapus@test.com',
            'phone' => '021-55556666',
            'address' => 'Alamat Dihapus',
            'status' => 'Nonaktif',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('projects.destroy', $corporate->id));

        $response->assertRedirect(route('customers.index', ['tab' => 'project']));
        $this->assertDatabaseMissing('customers', ['id' => $corporate->id]);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'kemitraan',
            'action' => 'delete',
            'subject_id' => (string) $corporate->id,
        ]);
    }

    public function test_validation_errors_when_storing_customer(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('customers.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'phone', 'address', 'status']);
    }
}
