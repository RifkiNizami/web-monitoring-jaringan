<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ReportGeneratorTest extends TestCase
{
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::where('email', 'admin@netfly.id')->first() ?? User::factory()->create([
            'email' => 'admin@netfly.id',
            'role' => 'admin',
        ]);
    }

    /**
     * Uji pemuatan halaman Executive Report Generator dengan status 200 OK.
     */
    public function test_report_generator_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Executive Report Generator');
        $response->assertSee('KETERSEDIAAN SLA BULANAN');
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
        $response->assertSee('99.9%');
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
}
