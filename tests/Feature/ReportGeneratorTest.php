<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReportGeneratorTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::where('email', 'admin@netfly.id')->first() ?? User::factory()->create([
            'email' => 'admin@netfly.id',
            'role' => 'admin',
        ]);

        // Customer & Project fixture untuk report generator (di-rollback otomatis oleh DatabaseTransactions)
        DB::table('customers')->updateOrInsert(
            ['id' => 1],
            [
                'code' => 'CUST-RPT-0001',
                'customer_type' => 'perusahaan',
                'name' => 'PT. Bank Mandiri (Persero) Tbk',
                'pic_name' => 'Bambang Pratama',
                'status' => 'Aktif',
                'email' => 'mandiri@bankmandiri.co.id',
                'phone' => '021-1234567',
                'address' => 'Plaza Mandiri Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('customers')->updateOrInsert(
            ['id' => 2],
            [
                'code' => 'CUST-RPT-0002',
                'customer_type' => 'perusahaan',
                'name' => 'PT. Shopee International Indonesia',
                'pic_name' => 'Indra Gunawan',
                'status' => 'Aktif',
                'email' => 'shopee@shopee.co.id',
                'phone' => '021-7654321',
                'address' => 'SCBD Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('customers')->updateOrInsert(
            ['id' => 3],
            [
                'code' => 'CUST-RPT-0003',
                'customer_type' => 'perusahaan',
                'name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'pic_name' => 'Drs. H. Mulyadi',
                'status' => 'Aktif',
                'email' => 'disdik@jakarta.go.id',
                'phone' => '021-998877',
                'address' => 'Jakarta Pusat',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('projects')->insertOrIgnore([
            [
                'id' => 1,
                'customer_id' => 3,
                'name' => 'Smart City Backbone Sekolah Tahap II',
                'branch_location' => 'Disdik Jakarta Hub',
                'service_type' => 'Dedicated Fiber 500 Mbps',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $devMandiri = Device::firstOrCreate(
            ['hostname' => 'RTR-CORE-MANDIRI-01'],
            [
                'type' => 'Router',
                'customer_id' => 1,
                'project_id' => null,
                'status' => 'Tersedia',
            ]
        );
        $devMandiri->update(['customer_id' => 1]);

        $devShopee = Device::firstOrCreate(
            ['hostname' => 'SW-AGG-BKS-04'],
            [
                'type' => 'Switch',
                'customer_id' => 2,
                'project_id' => null,
                'status' => 'Tersedia',
            ]
        );
        $devShopee->update(['customer_id' => 2]);

        $devDisdik = Device::firstOrCreate(
            ['hostname' => 'AP-OUTDOOR-DISDIK-01'],
            [
                'type' => 'Access Point',
                'customer_id' => 3,
                'project_id' => 1,
                'status' => 'Tersedia',
            ]
        );
        $devDisdik->update(['customer_id' => 3, 'project_id' => 1]);

        // Tiket uji untuk pengujian laporan eksekutif (di-rollback otomatis oleh DatabaseTransactions)
        Ticket::updateOrCreate(
            ['ticket_number' => 'INC-2026-1024'],
            [
                'customer_id' => 3,
                'project_id' => 1,
                'device_id' => $devDisdik->id,
                'client_name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'project_name' => 'Smart City Backbone Sekolah Tahap II',
                'priority' => 'P2 - HIGH',
                'priority_level' => 'high',
                'subject' => 'Packet Loss Tinggi (>14%) Backbone Disdik Jakarta Pusat',
                'description' => 'Akses koneksi Internet di lokasi hub pusat mengalami degradasi kualitas dan loss tinggi.',
                'status' => '3. In Progress (Sedang Dikerjakan)',
                'status_type' => 'in_progress',
                'reported_at' => now(),
            ]
        );
    }

    /**
     * Uji pemuatan halaman Executive Report Generator dengan status 200 OK.
     */
    public function test_report_generator_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Executive Report Generator');
        $response->assertDontSee('KETERSEDIAAN SLA BULANAN');
        $response->assertSee('TOTAL INSIDEN & TIKET', false);
        $response->assertSee('CAKUPAN NODE HARDWARE');
        $response->assertSee('MITRA YANG DIEVALUASI');
        $response->assertSee('RTR-CORE-MANDIRI-01');
        $response->assertSee('SW-AGG-BKS-04');
        $response->assertSee('AP-OUTDOOR-DISDIK-01');
        $response->assertSee('INC-2026-1024');
        $response->assertSee('Export / Download PDF Resmi');
    }

    /**
     * Uji pemfilteran dinamis berdasarkan mitra pelanggan, bulan, dan tahun.
     */
    public function test_report_generator_filters_customer_data_properly(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('reports.index', [
            'customer' => 'mandiri',
            'month' => 10,
            'year' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertSee('PT. Bank Mandiri (Persero) Tbk');
        $response->assertSee('RTR-CORE-MANDIRI-01');
    }

    /**
     * Uji pemuatan antarmuka ringkas laporan eksekutif tanpa komponen tracker yang rumit.
     */
    public function test_report_generator_renders_clean_executive_layout(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('reports.index', [
            'customer' => 'all',
            'month' => 10,
            'year' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Executive Report Generator');
        $response->assertDontSee('KETERSEDIAAN SLA BULANAN');
        $response->assertSee('Daftar Perangkat Dalam Laporan');
        $response->assertSee('Riwayat Tiket Penanganan Periode Ini');
        $response->assertDontSee('Uptime Timeline Tracker');
    }

    /**
     * Uji ekspor dan generasi berkas PDF resmi berhasil dengan Content-Type application/pdf.
     */
    public function test_export_pdf_generates_valid_pdf_download(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('reports.exportPdf', [
            'customer' => 'all',
            'month' => 10,
            'year' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.pdf', $response->headers->get('content-disposition'));

        // Memastikan payload PDF tidak kosong dan diawali marker standar %PDF
        $content = $response->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('%PDF', $content);
    }

    /**
     * Uji fitur preview PDF di tab browser baru (inline display).
     */
    public function test_export_pdf_preview_mode(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('reports.exportPdf', [
            'customer' => 'mandiri',
            'month' => 10,
            'year' => 2026,
            'preview' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }

    /**
     * Uji ekspor Excel perangkat pada Executive Report Generator.
     */
    public function test_export_excel_in_report_generator_downloads_valid_xlsx(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('reports.exportExcel', [
            'customer' => 'mandiri',
            'month' => 10,
            'year' => 2026,
        ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    /**
     * Uji proteksi keamanan route export excel (wajib auth role admin).
     */
    public function test_guest_cannot_export_excel(): void
    {
        $response = $this->get(route('reports.exportExcel'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Uji ekspor Excel pada halaman inventaris perangkat.
     */
    public function test_devices_inventory_export_excel_downloads_valid_xlsx(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('devices.exportExcel'));

        $response->assertStatus(200);
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }
}
