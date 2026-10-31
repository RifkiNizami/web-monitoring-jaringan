<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\LandingClient;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminKontenTest extends TestCase
{
    protected function getAdminKonten(): User
    {
        return User::firstOrCreate(
            ['email' => 'konten_test@netfly.id'],
            [
                'name' => 'Admin Konten Test',
                'username' => 'kontentest',
                'password' => bcrypt('password123'),
                'role' => 'admin_konten',
                'is_active' => true,
            ]
        );
    }

    protected function getAdminBiasa(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin_biasa_test@netfly.id'],
            [
                'name' => 'Admin Biasa Test',
                'username' => 'adminbiasatest',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    public function test_guest_cannot_access_content_admin_routes(): void
    {
        $this->get('/landing-clients')->assertRedirect('/login');
        $this->get('/announcements')->assertRedirect('/login');
    }

    public function test_admin_konten_can_access_dashboard_konten(): void
    {
        $user = $this->getAdminKonten();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('admin-konten.konten_dashboard');
        $response->assertSee('Pusat Pengelolaan Konten');
        $response->assertSee('Dashboard Konten');
        $response->assertSee('Klien Landing Page');

        // Test route langsung /admin-konten
        $this->actingAs($user)->get('/admin-konten')
            ->assertStatus(200)
            ->assertViewIs('admin-konten.konten_dashboard');
    }

    public function test_admin_konten_cannot_access_noc_routes(): void
    {
        $user = $this->getAdminKonten();

        // Admin Konten tidak boleh mengakses manajemen perangkat jaringan, teknisi, dll.
        $this->actingAs($user)->get('/devices')->assertRedirect('/dashboard');
        $this->actingAs($user)->get('/monitoring')->assertRedirect('/dashboard');
        $this->actingAs($user)->get('/teknisi')->assertRedirect('/dashboard');
    }

    public function test_admin_konten_can_view_landing_clients_crud(): void
    {
        $user = $this->getAdminKonten();

        // Route Bahasa Indonesia
        $response = $this->actingAs($user)->get('/klien');
        $response->assertStatus(200);
        $response->assertViewIs('admin-konten.konten_klien');
        $response->assertSee('Kelola Klien Landing Page');
        $response->assertSee('Tambah Klien Baru');

        // Alias Route
        $this->actingAs($user)->get('/landing-clients')
            ->assertStatus(200)
            ->assertViewIs('admin-konten.konten_klien');
    }

    public function test_admin_konten_can_create_and_delete_landing_client(): void
    {
        $user = $this->getAdminKonten();

        $response = $this->actingAs($user)->post('/klien', [
            'name' => 'PT Mitra Sejahtera Test',
            'category' => 'Fintech',
            'location' => 'Jakarta Pusat',
            'order' => 99,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/klien');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('landing_clients', [
            'name' => 'PT Mitra Sejahtera Test',
            'category' => 'Fintech',
            'location' => 'Jakarta Pusat',
        ]);

        $client = LandingClient::where('name', 'PT Mitra Sejahtera Test')->first();
        $this->actingAs($user)->delete("/klien/{$client->id}")
            ->assertRedirect('/klien')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('landing_clients', [
            'id' => $client->id,
        ]);
    }

    public function test_admin_konten_can_create_announcement(): void
    {
        $user = $this->getAdminKonten();

        $response = $this->actingAs($user)->post('/pemberitahuan', [
            'title' => 'Maintenance Rutin Core Node Jakarta',
            'category' => 'maintenance',
            'message' => 'Akan dilakukan perawatan berkala sistem power suplai.',
            'priority' => 'tinggi',
            'status' => 'aktif',
        ]);

        $response->assertRedirect('/pemberitahuan');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('announcements', [
            'title' => 'Maintenance Rutin Core Node Jakarta',
            'category' => 'maintenance',
        ]);
    }

    public function test_admin_biasa_can_view_announcements_menu(): void
    {
        $admin = $this->getAdminBiasa();

        // Route Bahasa Indonesia
        $response = $this->actingAs($admin)->get('/pemberitahuan');
        $response->assertStatus(200);
        $response->assertViewIs('admin-konten.konten_pemberitahuan_tinjau');
        $response->assertSee('Pusat Pemberitahuan');
        $response->assertSee('Jadwal Pemeliharaan');
        $response->assertSee('Pemberitahuan');

        // Alias Route
        $this->actingAs($admin)->get('/announcements')
            ->assertStatus(200)
            ->assertViewIs('admin-konten.konten_pemberitahuan_tinjau');
    }

    public function test_admin_konten_can_view_announcements_crud(): void
    {
        $user = $this->getAdminKonten();

        // Route Bahasa Indonesia
        $response = $this->actingAs($user)->get('/pemberitahuan');
        $response->assertStatus(200);
        $response->assertViewIs('admin-konten.konten_pemberitahuan');
        $response->assertSee('Kelola Pemberitahuan');

        // Alias Route
        $this->actingAs($user)->get('/announcements')
            ->assertStatus(200)
            ->assertViewIs('admin-konten.konten_pemberitahuan');
    }

    public function test_landing_page_renders_clients_from_database(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Pelanggan NetFly');
        $response->assertSee('Prime Royal Hotel');
        $response->assertSee('Bebek Tepi Sawah');
    }
}
