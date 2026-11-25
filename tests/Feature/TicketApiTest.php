<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use DatabaseTransactions;

    protected Customer $customer;

    protected User $mobileUser;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        Ticket::where('ticket_number', 'INC-2026-9988')->delete();

        $this->customer = Customer::create([
            'code' => 'CUST-TICKET-TEST',
            'customer_type' => 'perusahaan',
            'name' => 'PT. Shopee International Indonesia',
            'pic_name' => 'Jessica Tanuwijaya',
            'email' => 'jessica.ticket@shopee.test',
            'phone' => '081234500001',
            'address' => 'Jl. Jenderal Sudirman, Jakarta',
            'status' => 'Aktif',
        ]);

        $this->mobileUser = User::factory()->create([
            'name' => 'Jessica Tanuwijaya',
            'email' => 'jessica.ticket@shopee.test',
            'username' => 'jessica_shopee',
            'role' => 'customer_corporate',
            'customer_id' => $this->customer->id,
            'is_active' => true,
        ]);

        $this->token = $this->mobileUser->createApiToken('Test-Mobile');
    }

    protected function tearDown(): void
    {
        Ticket::where('ticket_number', 'INC-2026-9988')->delete();
        parent::tearDown();
    }

    public function test_can_list_tickets_via_api(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->getJson('/api/tickets');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'total',
                'data',
            ]);
    }

    public function test_mobile_app_can_create_ticket_via_api(): void
    {
        $customNumber = 'INC-2026-9988';
        $payload = [
            'ticket_number' => $customNumber,
            'service_name' => 'Dedicated Internet 500 Mbps',
            'node_code' => 'SW-AGG-BKS-04',
            'priority' => 'High',
            'category' => 'LOS / Putus Total',
            'subject' => 'Indikator PON Merah Berkedip pada Switch Gudang',
            'description' => 'Koneksi terputus sejak pukul 20:00 WIB, mohon bantuan teknisi lapangan.',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'User-Agent' => 'NetFly-Mobile/1.0 (Android)',
            'X-App-Platform' => 'NetFly-Mobile',
        ])->postJson('/api/tickets', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'ticket_number',
                    'client_name',
                    'priority',
                    'subject',
                    'status',
                ],
            ]);

        $ticketData = $response->json('data');
        $this->assertEquals($customNumber, $ticketData['ticket_number']);

        $this->assertDatabaseHas('tickets', [
            'ticket_number' => $customNumber,
            'customer_id' => $this->customer->id,
            'client_name' => 'PT. Shopee International Indonesia',
            'status' => '1. Open (Baru)',
            'priority_level' => 'high',
        ]);
    }

    public function test_validation_fails_when_subject_is_missing(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/tickets', [
                'client_name' => 'PT. Bank Mandiri',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject']);
    }

    public function test_mobile_app_can_update_ticket_via_api(): void
    {
        $customNumber = 'INC-2026-9988';
        $ticket = Ticket::create([
            'ticket_number' => $customNumber,
            'customer_id' => $this->customer->id,
            'client_name' => 'PT. Shopee International Indonesia',
            'subject' => 'Kendala Awal',
            'description' => 'Deskripsi kendala awal',
            'status' => '1. Open (Baru)',
            'status_type' => 'open',
            'priority' => 'P2 - HIGH',
            'priority_level' => 'high',
        ]);

        $updatePayload = [
            'message' => 'Kondisi terbaru: lampu modem sudah hijau namun latency masih tinggi.',
            'sender' => 'Jessica Tanuwijaya',
            'status' => 'In Progress',
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->putJson("/api/tickets/{$customNumber}", $updatePayload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('tickets', [
            'ticket_number' => $customNumber,
            'status' => '3. In Progress (Sedang Dikerjakan)',
            'status_type' => 'in_progress',
        ]);

        $ticket->refresh();
        $this->assertStringContainsString('Jessica Tanuwijaya: Kondisi terbaru', $ticket->status_note);
    }

    public function test_mobile_app_can_create_ticket_with_optional_photo(): void
    {
        $customNumber = 'INC-2026-9988';
        $dummyBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $payload = [
            'ticket_number' => $customNumber,
            'subject' => 'Kabel Dropcore Putus',
            'description' => 'Terkena pohon tumbang, terlampir foto bukti fisik kabel di tiang.',
            'photo' => $dummyBase64,
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$this->token)
            ->postJson('/api/tickets', $payload);

        $response->assertStatus(201);
        $data = $response->json('data');
        $this->assertNotNull($data['photo']);
        $this->assertNotNull($data['photo_url']);

        $ticket = Ticket::where('ticket_number', $customNumber)->first();
        $this->assertNotNull($ticket);
        $this->assertNotNull($ticket->photo);
        $this->assertFileExists(public_path($ticket->photo));

        if (File::exists(public_path($ticket->photo))) {
            File::delete(public_path($ticket->photo));
        }
    }
}
