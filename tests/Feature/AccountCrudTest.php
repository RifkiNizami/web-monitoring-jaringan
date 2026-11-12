<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Tests\TestCase;

class AccountCrudTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@netfly.id'],
            [
                'name' => 'Administrator NOC',
                'username' => 'admin_noc',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    public function test_guest_cannot_access_accounts(): void
    {
        $this->get(route('accounts.index'))->assertRedirect(route('login'));
        $this->get(route('accounts.create'))->assertRedirect(route('login'));
        $this->get('/accounts/102/edit')->assertRedirect(route('login'));
    }

    public function test_admin_can_view_accounts_index_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('accounts.index'));

        $response->assertStatus(200);
        $response->assertSee(route('accounts.create'));
        $response->assertDontSee('id="createAccountModal"', false);
    }

    public function test_admin_can_view_create_account_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('accounts.create'));

        $response->assertStatus(200);
        $response->assertSee('Buat Akun Customer Baru');
        $response->assertSee('action="'.route('accounts.store').'"', false);
        $response->assertDontSee('LANGKAH 1');
        $response->assertDontSee('LANGKAH 2');
        $response->assertDontSee('LANGKAH 3');
    }

    public function test_admin_can_store_corporate_customer_account(): void
    {
        $customer = Customer::where('customer_type', 'perusahaan')->first();
        $customerId = $customer ? $customer->id : 1;

        $response = $this->actingAs($this->admin)->post(route('accounts.store'), [
            'customer_type' => 'perusahaan',
            'customer_id' => $customerId,
            'name' => 'Bambang PIC Test',
            'username' => 'pic_test_'.rand(100, 999),
            'email' => 'pic.test@company.co.id',
            'password' => 'secret123',
            'phone' => '081234567890',
            'address' => 'Jl. Jend Sudirman Kav. 1, Jakarta',
            'latitude' => -6.2145,
            'longitude' => 106.8201,
            'is_active' => 1,
            'must_change_password' => 1,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success');
    }

    public function test_admin_can_store_residential_customer_account(): void
    {
        $customer = Customer::where('customer_type', 'rumahan')->first();
        $customerId = $customer ? $customer->id : 101;

        $response = $this->actingAs($this->admin)->post(route('accounts.store'), [
            'customer_type' => 'rumahan',
            'customer_id' => $customerId,
            'name' => 'Warga Home Test',
            'username' => 'home_test_'.rand(100, 999),
            'email' => 'home.test@gmail.com',
            'password' => 'secret123',
            'phone' => '081987654321',
            'address' => 'Komplek Permata Indah No. 5',
            'latitude' => -6.4021,
            'longitude' => 106.8123,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success');
    }

    public function test_admin_can_view_edit_account_page(): void
    {
        // First get accounts from index to ensure session exists
        $this->actingAs($this->admin)->get(route('accounts.index'));

        $accounts = session('customer_accounts', []);
        $targetId = ! empty($accounts) ? $accounts[0]['id'] : 102;

        $response = $this->actingAs($this->admin)->get(route('accounts.edit', $targetId));

        $response->assertStatus(200);
        $response->assertSee('Edit Akun Customer:');
        $response->assertSee(route('accounts.update', $targetId));
        $response->assertDontSee('LANGKAH 1');
    }

    public function test_admin_can_update_account(): void
    {
        $this->actingAs($this->admin)->get(route('accounts.index'));
        $accounts = session('customer_accounts', []);
        $targetId = ! empty($accounts) ? $accounts[0]['id'] : 102;

        $response = $this->actingAs($this->admin)->put(route('accounts.update', $targetId), [
            'customer_type' => 'perusahaan',
            'customer_id' => 1,
            'name' => 'PIC Terupdate Nama',
            'username' => 'pic_updated_'.rand(100, 999),
            'email' => 'updated.pic@perusahaan.co.id',
            'phone' => '081299998888',
            'address' => 'Gedung Baru, Lantai 10',
            'latitude' => -6.2100,
            'longitude' => 106.8300,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success');
    }

    public function test_admin_can_toggle_account_status(): void
    {
        $this->actingAs($this->admin)->get(route('accounts.index'));
        $accounts = session('customer_accounts', []);
        $targetId = ! empty($accounts) ? $accounts[0]['id'] : 102;

        $response = $this->actingAs($this->admin)->put(route('accounts.toggle-status', $targetId));

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success');
    }

    public function test_admin_can_delete_account(): void
    {
        $this->actingAs($this->admin)->get(route('accounts.index'));
        $accounts = session('customer_accounts', []);
        $targetId = ! empty($accounts) ? $accounts[0]['id'] : 102;

        $response = $this->actingAs($this->admin)->delete(route('accounts.destroy', $targetId));

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success');
    }
}
