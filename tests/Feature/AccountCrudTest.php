<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AccountCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected Customer $testCorporateCustomer;

    protected Customer $testResidentialCustomer;

    protected User $testAccountUser;

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

        $this->testCorporateCustomer = Customer::firstOrCreate(
            ['code' => 'CUST-TEST-CORP'],
            [
                'customer_type' => 'perusahaan',
                'name' => 'PT. Test Akun Korporat',
                'pic_name' => 'Bambang PIC Test',
                'email' => 'pic.corp@test.com',
                'phone' => '021-998877',
                'address' => 'Plaza Mandiri Jakarta',
                'status' => 'Aktif',
            ]
        );

        $this->testResidentialCustomer = Customer::firstOrCreate(
            ['code' => 'CUST-TEST-HOME'],
            [
                'customer_type' => 'rumahan',
                'name' => 'Warga Test Rumah',
                'pic_name' => 'Warga Test Rumah',
                'email' => 'warga.home@test.com',
                'phone' => '08123456789',
                'address' => 'Depok Asri',
                'status' => 'Aktif',
            ]
        );

        $this->testAccountUser = User::firstOrCreate(
            ['username' => 'test_pic_user'],
            [
                'customer_id' => $this->testCorporateCustomer->id,
                'name' => 'Test PIC User',
                'email' => 'test_pic_user@test.com',
                'password' => bcrypt('secret123'),
                'role' => 'customer_representative',
                'is_active' => true,
            ]
        );
    }

    public function test_guest_cannot_access_accounts(): void
    {
        $this->get(route('accounts.index'))->assertRedirect(route('login'));
        $this->get(route('accounts.create'))->assertRedirect(route('login'));
        $this->get(route('accounts.edit', $this->testAccountUser->id))->assertRedirect(route('login'));
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
        $response->assertDontSee('Langsung aktifkan status akun setelah diterbitkan');
        $response->assertDontSee('Wajibkan pengguna mengubah kata sandi');
    }

    public function test_admin_can_store_corporate_customer_account(): void
    {
        $username = 'pic_test_'.rand(1000, 9999);
        $email = 'pic.test.'.rand(1000, 9999).'@company.co.id';

        $response = $this->actingAs($this->admin)->post(route('accounts.store'), [
            'customer_type' => 'perusahaan',
            'customer_id' => $this->testCorporateCustomer->id,
            'name' => 'Bambang PIC Test',
            'username' => $username,
            'email' => $email,
            'password' => 'secret123',
            'phone' => '081234567890',
            'address' => 'Jl. Jend Sudirman Kav. 1, Jakarta',
            'latitude' => -6.2145,
            'longitude' => 106.8201,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'username' => $username,
            'email' => $email,
            'customer_id' => $this->testCorporateCustomer->id,
            'role' => 'customer_representative',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_store_residential_customer_account(): void
    {
        $username = 'home_test_'.rand(1000, 9999);
        $email = 'home.test.'.rand(1000, 9999).'@gmail.com';

        $response = $this->actingAs($this->admin)->post(route('accounts.store'), [
            'customer_type' => 'rumahan',
            'customer_id' => $this->testResidentialCustomer->id,
            'name' => 'Warga Home Test',
            'username' => $username,
            'email' => $email,
            'password' => 'secret123',
            'phone' => '081987654321',
            'address' => 'Komplek Permata Indah No. 5',
            'latitude' => -6.4021,
            'longitude' => 106.8123,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'username' => $username,
            'email' => $email,
            'customer_id' => $this->testResidentialCustomer->id,
            'role' => 'customer_residential',
        ]);
    }

    public function test_admin_can_view_edit_account_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('accounts.edit', $this->testAccountUser->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Akun Customer:');
        $response->assertSee(route('accounts.update', $this->testAccountUser->id));
        $response->assertDontSee('LANGKAH 1');
    }

    public function test_admin_can_update_account(): void
    {
        $newUsername = 'pic_upd_'.rand(1000, 9999);
        $newEmail = 'upd.'.rand(1000, 9999).'@perusahaan.co.id';

        $response = $this->actingAs($this->admin)->put(route('accounts.update', $this->testAccountUser->id), [
            'customer_type' => 'perusahaan',
            'customer_id' => $this->testCorporateCustomer->id,
            'name' => 'PIC Terupdate Nama',
            'username' => $newUsername,
            'email' => $newEmail,
            'phone' => '081299998888',
            'address' => 'Gedung Baru, Lantai 10',
            'latitude' => -6.2100,
            'longitude' => 106.8300,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $this->testAccountUser->id,
            'username' => $newUsername,
            'email' => $newEmail,
        ]);
    }

    public function test_admin_can_toggle_account_status(): void
    {
        $initialStatus = $this->testAccountUser->is_active;

        $response = $this->actingAs($this->admin)->put(route('accounts.toggle-status', $this->testAccountUser->id));

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success');

        $this->testAccountUser->refresh();
        $this->assertNotEquals($initialStatus, $this->testAccountUser->is_active);
    }

    public function test_admin_can_delete_account(): void
    {
        $targetId = $this->testAccountUser->id;

        $response = $this->actingAs($this->admin)->delete(route('accounts.destroy', $targetId));

        $response->assertRedirect(route('accounts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $targetId]);
    }
}
