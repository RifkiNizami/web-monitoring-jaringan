<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LogoutSecurityTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('email', 'admin@netfly.id')->first() ?? User::factory()->create([
            'email' => 'admin@netfly.id',
            'role' => 'admin',
        ]);
    }

    /**
     * Memastikan akses GET ke rute /logout ditolak (405 Method Not Allowed)
     * untuk mencegah serangan CSRF via tag <img>, <link>, atau hyperlink.
     */
    public function test_get_method_on_logout_is_forbidden(): void
    {
        $response = $this->actingAs($this->user)->get('/logout');

        $response->assertStatus(405);
        $this->assertAuthenticatedAs($this->user);
    }

    /**
     * Memastikan pengguna yang belum login tidak dapat mengakses endpoint logout.
     */
    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->post('/logout');

        $response->assertRedirect(route('login'));
    }

    /**
     * Memastikan pengguna yang terautentikasi dapat logout secara aman dengan metode POST.
     */
    public function test_authenticated_user_can_logout_safely_via_post(): void
    {
        $response = $this->actingAs($this->user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
