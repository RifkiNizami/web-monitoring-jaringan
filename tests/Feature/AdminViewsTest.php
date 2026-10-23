<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminViewsTest extends TestCase
{
    /**
     * Memastikan akses tanpa autentikasi diarahkan ke halaman login.
     */
    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $protectedRoutes = [
            '/dashboard',
            '/customers',
            '/projects',
            '/accounts',
            '/devices',
            '/tickets',
            '/monitoring',
            '/reports',
        ];

        foreach ($protectedRoutes as $uri) {
            $response = $this->get($uri);
            $response->assertRedirect(route('login'));
        }
    }

    /**
     * Memastikan seluruh halaman admin dapat diakses dengan respons 200 OK saat sudah login.
     */
    public function test_all_admin_views_render_properly_for_authenticated_users(): void
    {
        $admin = User::where('email', 'admin@netfly.id')->first() ?? User::factory()->create([
            'email' => 'admin@netfly.id',
            'role' => 'admin',
        ]);

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
            if ($uri === '/login') {
                // Untuk login saat authenticated, AuthController redirect ke dashboard
                $response = $this->get($uri);
                $response->assertStatus(200);
            } else {
                $response = $this->actingAs($admin)->get($uri);
                $response->assertStatus(200);
                $response->assertViewIs($expectedView);
            }
        }
    }
}
