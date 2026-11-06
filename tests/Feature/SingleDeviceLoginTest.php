<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthenticateSingleSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SingleDeviceLoginTest extends TestCase
{
    use DatabaseTransactions;

    private const PASSWORD = 'SingleDevice#123';

    protected function setUp(): void
    {
        parent::setUp();

        // Gunakan driver database agar perilaku tabel `sessions` teruji seperti di produksi
        config(['session.driver' => 'database']);
        $this->app['session']->forgetDrivers();
    }

    protected function makeUser(): User
    {
        return User::create([
            'name' => 'User Single Device',
            'username' => 'single_device_test',
            'email' => 'single_device_test@netfly.id',
            'password' => bcrypt(self::PASSWORD),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    /**
     * Simulasikan request dari perangkat/browser tertentu (berdasarkan cookie sesi).
     * Guard & session store di-reset agar tidak ada state yang terbawa antar perangkat.
     */
    protected function fromDevice(?string $sessionId): static
    {
        $this->app['auth']->forgetGuards();
        $this->app['session']->forgetDrivers();
        $this->app->forgetInstance('session.store');
        $this->defaultCookies = [];

        if ($sessionId) {
            $this->withCookie(config('session.cookie'), $sessionId);
        }

        return $this;
    }

    /**
     * Login dari perangkat baru dan kembalikan ID sesi yang terbentuk.
     */
    protected function loginFromNewDevice(User $user): string
    {
        $response = $this->fromDevice(null)->post('/login', [
            'email' => $user->email,
            'password' => self::PASSWORD,
        ]);

        $response->assertRedirect(route('dashboard'));

        return $this->app['session']->driver()->getId();
    }

    protected function visitDashboard(string $sessionId): TestResponse
    {
        return $this->fromDevice($sessionId)->get('/dashboard');
    }

    public function test_login_on_second_device_kicks_first_device(): void
    {
        $user = $this->makeUser();

        // Skenario 1: Login di Device A dan akses dashboard
        $sessionA = $this->loginFromNewDevice($user);
        $this->visitDashboard($sessionA)->assertOk();
        $this->assertDatabaseHas('sessions', ['id' => $sessionA, 'user_id' => $user->id]);

        // Skenario 2: Login di Device B dengan password benar, Device B bisa akses dashboard
        $sessionB = $this->loginFromNewDevice($user);
        $this->assertNotSame($sessionA, $sessionB);
        $this->visitDashboard($sessionB)->assertOk();

        // Skenario 3: Record sesi Device A sudah dihapus dari tabel sessions
        $this->assertDatabaseMissing('sessions', ['id' => $sessionA, 'user_id' => $user->id]);
        $this->assertSame(1, \DB::table('sessions')->where('user_id', $user->id)->count());

        // Skenario 4: Device A diarahkan ke /login dengan pesan sesi berakhir
        $this->visitDashboard($sessionA)->assertRedirect(route('login'));
        $this->fromDevice($sessionA)->get('/login')
            ->assertOk()
            ->assertSee(AuthenticateSingleSession::KICKED_MESSAGE);

        // Device B tetap aktif
        $this->visitDashboard($sessionB)->assertOk();
    }

    public function test_session_with_stale_password_hash_is_logged_out_with_message(): void
    {
        $user = $this->makeUser();

        $this->fromDevice(null);
        $this->app['auth']->guard('web')->setUser($user);

        $this->withSession(['password_hash_web' => 'stale-hash-from-old-device'])
            ->get('/dashboard')
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', AuthenticateSingleSession::KICKED_MESSAGE);

        $this->app['auth']->guard('web')->forgetUser();
        $this->assertGuest();
    }

    public function test_authenticated_user_accessing_login_page_is_redirected_to_dashboard(): void
    {
        $user = $this->makeUser();
        $session = $this->loginFromNewDevice($user);

        // Pengguna yang sudah aktif jika membuka /login wajib diarahkan kembali ke /dashboard
        $this->fromDevice($session)->get('/login')
            ->assertRedirect(route('dashboard'));
    }

    public function test_login_does_not_store_password_in_activity_log(): void
    {
        config(['activity_logger.enabled_in_tests' => true]);
        $user = $this->makeUser();

        $this->loginFromNewDevice($user);

        $log = \App\Models\ActivityLog::where('action', 'login')
            ->where('subject_id', (string) $user->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString('Sesi perangkat lain otomatis ditutup', $log->description);
        $this->assertStringNotContainsString(self::PASSWORD, json_encode($log->properties ?? []));
    }
}
