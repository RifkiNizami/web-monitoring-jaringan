<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminViewsTest extends TestCase
{
    /**
     * Memastikan seluruh halaman admin di views/admin/ dapat diakses dengan respons 200 OK.
     */
    public function test_all_admin_views_render_properly(): void
    {
        $adminRoutes = [
            '/login' => 'admin.login',
            '/dashboard' => 'admin.dashboard',
            '/admin' => 'admin.dashboard',
            '/customers' => 'admin.customers',
            '/projects' => 'admin.customers',
            '/accounts' => 'admin.accounts',
            '/devices' => 'admin.devices',
            '/tickets' => 'admin.tickets',
            '/monitoring' => 'admin.monitoring',
            '/reports' => 'admin.reports',
        ];

        foreach ($adminRoutes as $uri => $expectedView) {
            $response = $this->get($uri);
            $response->assertStatus(200);
            $response->assertViewIs($expectedView);
        }
    }
}
