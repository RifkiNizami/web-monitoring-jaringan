<?php

namespace Tests\Unit;

use App\Services\MikrotikService;
use PHPUnit\Framework\TestCase;

class MikrotikServiceTest extends TestCase
{
    /**
     * Memastikan service dapat diinisialisasi dengan benar.
     */
    public function test_service_instantiates(): void
    {
        $service = new MikrotikService();
        $this->assertInstanceOf(MikrotikService::class, $service);
        $this->assertNull($service->getLastError());
        $this->assertEquals(0.0, $service->getLatencyMs());
    }

    /**
     * Memastikan testConnection menangani IP unreachable secara anggun tanpa exception.
     */
    public function test_connection_fails_gracefully_on_unreachable_ip(): void
    {
        $service = new MikrotikService();
        // Coba konek ke IP reserved TEST-NET yang dijamin timeout / unreachable
        $result = $service->testConnection('192.0.2.1', 'admin', 'password', 8728, 1);

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['message']);
        $this->assertNull($result['resource']);
        $this->assertNotNull($service->getLastError());
    }
}
