<?php

namespace Tests\Feature;

use App\Models\Teknisi;
use App\Models\User;
use Tests\TestCase;

class TeknisiManagementTest extends TestCase
{
    protected function getAdminUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin_test@netfly.id'],
            [
                'name' => 'Admin Tester',
                'username' => 'admintester',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    public function test_guest_cannot_access_teknisi_page(): void
    {
        $response = $this->get('/teknisi');
        $response->assertRedirect('/login');

        $this->get('/teknisi/create')->assertRedirect('/login');
        $this->get('/teknisi/1/edit')->assertRedirect('/login');
    }

    public function test_admin_can_view_teknisi_page(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get('/teknisi');
        $response->assertStatus(200);
        $response->assertSee('Database Teknisi');
        $response->assertSee(route('teknisi.create'));
        $response->assertDontSee('id="createTeknisiModal"', false);
        $response->assertDontSee('id="editTeknisiModal"', false);
    }

    public function test_admin_can_view_create_teknisi_page(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('teknisi.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Teknisi Baru');
        $response->assertSee('action="'.route('teknisi.store').'"', false);
    }

    public function test_admin_can_view_edit_teknisi_page(): void
    {
        $admin = $this->getAdminUser();
        $teknisi = Teknisi::first() ?? Teknisi::create([
            'nama' => 'Teknisi Edit Test',
            'nomor_hp' => '0812-9999-8888',
            'alamat' => 'Jl. Kebon Jeruk No. 1',
        ]);

        $response = $this->actingAs($admin)->get(route('teknisi.edit', $teknisi->id));
        $response->assertStatus(200);
        $response->assertSee('Edit Data Teknisi:');
        $response->assertSee($teknisi->nama);
        $response->assertSee(route('teknisi.update', $teknisi->id));
    }

    public function test_admin_can_store_teknisi(): void
    {
        $admin = $this->getAdminUser();

        $payload = [
            'nama' => 'Hendra Setiawan',
            'nomor_hp' => '0812-9988-5544',
            'alamat' => 'Jl. RS Fatmawati No. 12, Cilandak, Jakarta Selatan',
        ];

        $response = $this->actingAs($admin)->post('/teknisi', $payload);
        $response->assertRedirect('/teknisi');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('teknisi', [
            'nama' => 'Hendra Setiawan',
            'nomor_hp' => '0812-9988-5544',
            'alamat' => 'Jl. RS Fatmawati No. 12, Cilandak, Jakarta Selatan',
        ]);
    }

    public function test_admin_can_update_teknisi(): void
    {
        $admin = $this->getAdminUser();
        $teknisi = Teknisi::first();

        $updatePayload = [
            'nama' => 'Budi Santoso Updated',
            'nomor_hp' => '0813-1111-2222',
            'alamat' => 'Alamat Baru No. 99',
        ];

        $response = $this->actingAs($admin)->put("/teknisi/{$teknisi->id}", $updatePayload);
        $response->assertRedirect('/teknisi');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('teknisi', [
            'id' => $teknisi->id,
            'nama' => $updatePayload['nama'],
            'nomor_hp' => '0813-1111-2222',
            'alamat' => 'Alamat Baru No. 99',
        ]);
    }

    public function test_whatsapp_redirect(): void
    {
        $admin = $this->getAdminUser();
        $teknisi = Teknisi::create([
            'nama' => 'WhatsApp Test User',
            'nomor_hp' => '0812-3456-7890',
            'alamat' => 'Jl. Sudirman Jakarta',
        ]);

        $response = $this->actingAs($admin)->get("/teknisi/{$teknisi->id}/whatsapp");
        $response->assertStatus(302);

        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('https://wa.me/6281234567890', $targetUrl);
    }

    public function test_admin_can_delete_teknisi(): void
    {
        $admin = $this->getAdminUser();
        $teknisi = Teknisi::create([
            'nama' => 'Delete Me Technician',
            'nomor_hp' => '0899-0000-1111',
            'alamat' => 'Jakarta Timur',
        ]);

        $response = $this->actingAs($admin)->delete("/teknisi/{$teknisi->id}");
        $response->assertRedirect('/teknisi');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('teknisi', [
            'id' => $teknisi->id,
        ]);
    }
}
