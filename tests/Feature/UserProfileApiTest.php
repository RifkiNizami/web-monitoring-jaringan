<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserProfileApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unauthenticated_profile_request_is_rejected(): void
    {
        $response = $this->getJson('/api/user/profile');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_get_user_profile_via_api_with_bearer_token(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi.test@netfly.net.id',
            'username' => 'buditest',
            'phone' => '081234567890',
            'role' => 'customer_corporate',
            'is_active' => true,
        ]);

        $token = $user->createApiToken('Test-Mobile');

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/user/profile');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => 'Budi Santoso',
                    'email' => 'budi.test@netfly.net.id',
                    'username' => 'buditest',
                    'phone' => '081234567890',
                ],
            ]);
    }

    public function test_can_update_user_profile_via_api(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Lama',
            'email' => 'update.test@netfly.net.id',
            'username' => 'updatetest',
            'phone' => '0811111111',
            'role' => 'customer_corporate',
            'is_active' => true,
        ]);

        $token = $user->createApiToken('Test-Mobile');

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/user/profile', [
                'name' => 'Nama Baru PIC',
                'phone' => '082222222222',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profil berhasil diperbarui.',
                'user' => [
                    'id' => $user->id,
                    'name' => 'Nama Baru PIC',
                    'phone' => '082222222222',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru PIC',
            'phone' => '082222222222',
        ]);
    }

    public function test_profile_update_validates_input(): void
    {
        $user = User::factory()->create([
            'email' => 'validasi.test@netfly.net.id',
            'name' => 'User Validasi',
            'role' => 'customer_corporate',
            'is_active' => true,
        ]);

        $token = $user->createApiToken('Test-Mobile');

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/user/profile', [
                'name' => 'A', // min 2 characters
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_profile_update_syncs_customer_record(): void
    {
        $customer = Customer::create([
            'code' => 'CUST-TEST-99',
            'customer_type' => 'perusahaan',
            'name' => 'PT Maju Bersama',
            'pic_name' => 'PIC Lama',
            'email' => 'maju.test@netfly.net.id',
            'phone' => '0833333333',
            'address' => 'Jl. Pemuda No. 10, Surabaya',
            'status' => 'Aktif',
        ]);

        $user = User::factory()->create([
            'name' => 'PIC Lama',
            'email' => 'maju.test@netfly.net.id',
            'username' => 'majutest',
            'phone' => '0833333333',
            'customer_id' => $customer->id,
            'role' => 'customer_corporate',
            'is_active' => true,
        ]);

        $token = $user->createApiToken('Test-Mobile');

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/user/profile', [
                'name' => 'PIC Baru Ditunjuk',
                'phone' => '089999999999',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'PIC Baru Ditunjuk',
            'phone' => '089999999999',
        ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'PT Maju Bersama', // nama perusahaan tetap aman
            'pic_name' => 'PIC Baru Ditunjuk',
            'phone' => '089999999999',
        ]);
    }
}
