<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TicketCrudTest extends TestCase
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
        Ticket::where('ticket_number', 'like', 'INC-TEST-%')->delete();
    }

    public function test_unauthenticated_user_cannot_access_ticket_crud(): void
    {
        $this->get('/tickets/create')->assertRedirect(route('login'));
        $this->get('/tickets/INC-9999/edit')->assertRedirect(route('login'));
        $this->post('/tickets', [])->assertRedirect(route('login'));
        $this->put('/tickets/INC-9999', [])->assertRedirect(route('login'));
        $this->delete('/tickets/INC-9999')->assertRedirect(route('login'));
    }

    public function test_create_ticket_page_renders(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('tickets.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tickets.create');
        $response->assertSee('Buat Tiket Gangguan Baru');
        $response->assertSee(route('tickets.store'));
    }

    public function test_admin_can_store_ticket(): void
    {
        $payload = [
            'client_name' => 'PT Mitra Sejahtera Test',
            'project_name' => 'Dedicated Bandwidth 200M Test',
            'node_code' => 'RTR-TEST-01',
            'priority' => 'high',
            'assigned_technician' => 'Budi Santoso',
            'subject' => 'Kendala RTT Tinggi Jalur Backbone',
            'description' => 'Gejala degradasi dirasakan sejak 10 menit lalu pada perangkat router edge.',
        ];

        $response = $this->actingAs($this->adminUser)->post(route('tickets.store'), $payload);

        $response->assertRedirect(route('tickets.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'client_name' => 'PT Mitra Sejahtera Test',
            'project_name' => 'Dedicated Bandwidth 200M Test',
            'node_code' => 'RTR-TEST-01',
            'priority' => 'P2 - HIGH',
            'priority_level' => 'high',
            'assigned_technician' => 'Budi Santoso',
            'subject' => 'Kendala RTT Tinggi Jalur Backbone',
        ]);
    }

    public function test_edit_ticket_page_renders(): void
    {
        $ticket = Ticket::create([
            'ticket_number' => 'INC-TEST-001',
            'client_name' => 'PT Shopee Uji Coba',
            'project_name' => 'Warehouse Link Test',
            'node_code' => 'SW-TEST-01',
            'priority' => 'P1 - CRITICAL',
            'priority_level' => 'critical',
            'subject' => 'Switch Down Test',
            'description' => 'Switch mati mendadak.',
            'status' => '1. Open (Baru)',
            'status_type' => 'open',
            'reported_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('tickets.edit', $ticket->ticket_number));

        $response->assertStatus(200);
        $response->assertViewIs('admin.tickets.edit');
        $response->assertSee('INC-TEST-001');
        $response->assertSee('Edit Data & Eskalasi Tiket', false);
        $response->assertSee('PT Shopee Uji Coba');
    }

    public function test_admin_can_update_ticket_details(): void
    {
        $ticket = Ticket::create([
            'ticket_number' => 'INC-TEST-002',
            'client_name' => 'PT Bank Mandiri Uji',
            'project_name' => 'MPLS Link Mandiri',
            'node_code' => 'RTR-TEST-02',
            'priority' => 'P3 - MEDIUM',
            'priority_level' => 'medium',
            'subject' => 'Request Upgrade Bandwidth',
            'description' => 'Permintaan upgrade sementara.',
            'status' => '1. Open (Baru)',
            'status_type' => 'open',
            'reported_at' => now(),
        ]);

        $updatePayload = [
            'client_name' => 'PT Bank Mandiri Uji Updated',
            'project_name' => 'MPLS Link Mandiri Core',
            'node_code' => 'RTR-TEST-02B',
            'priority' => 'critical',
            'status' => '3. In Progress (Sedang Dikerjakan)',
            'assigned_technician' => 'Agus Prasetyo, S.T.',
            'status_note' => 'Teknisi sedang di lokasi OTB',
            'subject' => 'Request Upgrade & Maintenance',
            'description' => 'Deskripsi masalah diperbarui.',
        ];

        $response = $this->actingAs($this->adminUser)->put(route('tickets.update', $ticket->ticket_number), $updatePayload);

        $response->assertRedirect(route('tickets.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'client_name' => 'PT Bank Mandiri Uji Updated',
            'node_code' => 'RTR-TEST-02B',
            'priority' => 'P1 - CRITICAL',
            'priority_level' => 'critical',
            'status' => '3. In Progress (Sedang Dikerjakan)',
            'status_type' => 'in_progress',
            'assigned_technician' => 'Agus Prasetyo, S.T.',
            'status_note' => 'Teknisi sedang di lokasi OTB',
        ]);
    }

    public function test_admin_can_delete_ticket(): void
    {
        $ticket = Ticket::create([
            'ticket_number' => 'INC-TEST-003',
            'client_name' => 'PT Hapus Uji',
            'project_name' => 'Link Uji',
            'priority' => 'P3 - MEDIUM',
            'priority_level' => 'medium',
            'subject' => 'Tiket untuk dihapus',
            'description' => 'Deskripsi hapus.',
            'status' => '1. Open (Baru)',
            'status_type' => 'open',
            'reported_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('tickets.destroy', $ticket->ticket_number));

        $response->assertRedirect(route('tickets.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
        ]);
    }

    public function test_tickets_index_page_no_longer_has_sla_modal_and_uses_create_page_link(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('tickets.index'));

        $response->assertStatus(200);
        // Pastikan tombol SLA dan modal SLA sudah tidak ada
        $response->assertDontSee('id="btnSlaReport"', false);
        $response->assertDontSee('id="slaModal"', false);
        // Pastikan tombol buat tiket baru sekarang mengarah ke link create (halaman penuh)
        $response->assertSee(route('tickets.create'), false);
        $response->assertDontSee('id="createTicketModal"', false);
    }
}
