<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['activity_logger.enabled_in_tests' => true]);
    }
    protected function getAdminUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@netfly.id'],
            [
                'name' => 'Admin Utama NOC',
                'username' => 'admin_noc',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    protected function getAdminKontenUser(): User
    {
        return User::firstOrCreate(
            ['username' => 'kontentest'],
            [
                'name' => 'Admin Konten Test',
                'email' => 'konten_test@netfly.id',
                'password' => bcrypt('password123'),
                'role' => 'admin_konten',
                'is_active' => true,
            ]
        );
    }

    /**
     * Tamu tanpa autentikasi diarahkan ke login saat mengakses /activity-logs.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/activity-logs');
        $response->assertRedirect(route('login'));
    }

    /**
     * Role admin_konten diblokir dari rute /activity-logs (khusus role:admin).
     */
    public function test_admin_konten_is_blocked_from_activity_logs(): void
    {
        $adminKonten = $this->getAdminKontenUser();

        $response = $this->actingAs($adminKonten)->get('/activity-logs');
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Admin utama dapat mengakses halaman /activity-logs dengan sukses.
     */
    public function test_admin_can_access_activity_logs_page(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get('/activity-logs');
        $response->assertOk();
        $response->assertViewIs('admin.activity-logs.index');
        $response->assertSee('Log Aktivitas Sistem');
    }

    /**
     * Menambahkan perangkat inventaris baru mencatat log dengan modul 'inventaris' dan aksi 'create'.
     */
    public function test_storing_inventory_device_logs_activity_with_photo_metadata(): void
    {
        $admin = $this->getAdminUser();
        $fakePhoto = UploadedFile::fake()->image('router_test.png', 400, 400)->size(250);

        $payload = [
            'hostname' => 'RTR-TEST-LOG-01',
            'type' => 'Router',
            'brand_model' => 'MikroTik CCR2004 Test',
            'serial_number' => 'SN-LOG-999888',
            'quantity' => 5,
            'status' => 'Tersedia',
            'photo' => $fakePhoto,
        ];

        $response = $this->actingAs($admin)->post('/devices', $payload);
        $response->assertRedirect(route('devices.index'));

        // Pastikan baris log tercatat di database
        $this->assertDatabaseHas('activity_logs', [
            'module' => 'inventaris',
            'action' => 'create',
            'subject_label' => 'RTR-TEST-LOG-01',
            'user_name' => $admin->name,
            'user_role' => 'admin',
        ]);

        $log = ActivityLog::where('subject_label', 'RTR-TEST-LOG-01')
            ->where('action', 'create')
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->properties);
        $this->assertArrayHasKey('photo', $log->properties);
        $this->assertSame('router_test.png', $log->properties['photo']['file_name']);
        $this->assertSame('image/png', $log->properties['photo']['mime_type']);

        // Bersihkan file unggahan dummy hasil pengujian
        if (File::exists(public_path('uploads/devices'))) {
            File::cleanDirectory(public_path('uploads/devices'));
        }
    }

    /**
     * Percobaan login yang gagal tercatat sebagai 'login_failed' tanpa membocorkan kata sandi.
     */
    public function test_failed_login_is_logged_without_exposing_password(): void
    {
        $attemptEmail = 'intruder_test@example.com';
        $sensitivePassword = 'SuperSecretPlainPassword123!';

        $response = $this->post('/login', [
            'email' => $attemptEmail,
            'password' => $sensitivePassword,
        ]);

        $response->assertSessionHasErrors('email');

        // Pastikan tercatat di database
        $this->assertDatabaseHas('activity_logs', [
            'module' => 'auth',
            'action' => 'login_failed',
            'subject_label' => $attemptEmail,
        ]);

        $log = ActivityLog::where('module', 'auth')
            ->where('action', 'login_failed')
            ->where('subject_label', $attemptEmail)
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString($attemptEmail, $log->description);

        // Pastikan password rahasia sama sekali tidak tersimpan di deskripsi atau JSON properties
        $this->assertStringNotContainsString($sensitivePassword, $log->description);
        $this->assertStringNotContainsString($sensitivePassword, json_encode($log->properties ?? []));
    }

    /**
     * Filter rentang tanggal (date_from & date_to) bekerja dengan benar berdasarkan zona waktu WIB.
     */
    public function test_admin_can_filter_activity_logs_by_date_range(): void
    {
        $admin = $this->getAdminUser();

        // Log 1: Hari ini jam 08:00 WIB (yang dalam UTC adalah 01:00 UTC)
        $todayWib = \Carbon\Carbon::now('Asia/Jakarta')->setTime(8, 0, 0);
        $todayUtc = $todayWib->copy()->setTimezone('UTC');

        $logToday = ActivityLog::create([
            'user_name' => 'User Hari Ini',
            'user_role' => 'admin',
            'module' => 'inventaris',
            'action' => 'create',
            'description' => 'Aktivitas yang dibuat hari ini',
            'created_at' => $todayUtc,
        ]);

        // Log 2: 5 hari lalu jam 10:00 WIB
        $fiveDaysAgoWib = \Carbon\Carbon::now('Asia/Jakarta')->subDays(5)->setTime(10, 0, 0);
        $fiveDaysAgoUtc = $fiveDaysAgoWib->copy()->setTimezone('UTC');

        $logPast = ActivityLog::create([
            'user_name' => 'User Masa Lalu',
            'user_role' => 'admin',
            'module' => 'inventaris',
            'action' => 'delete',
            'description' => 'Aktivitas yang dibuat 5 hari lalu',
            'created_at' => $fiveDaysAgoUtc,
        ]);

        $todayDateString = \Carbon\Carbon::now('Asia/Jakarta')->toDateString();

        // 1. Filter Hari Ini: date_from = today, date_to = today
        $responseToday = $this->actingAs($admin)->get("/activity-logs?date_from={$todayDateString}&date_to={$todayDateString}");
        $responseToday->assertOk();
        $responseToday->assertSee('Aktivitas yang dibuat hari ini');
        $responseToday->assertDontSee('Aktivitas yang dibuat 5 hari lalu');

        // 2. Filter 7 Hari Terakhir: date_from = 6 hari lalu, date_to = today
        $sevenDaysAgoDateString = \Carbon\Carbon::now('Asia/Jakarta')->subDays(6)->toDateString();
        $response7Days = $this->actingAs($admin)->get("/activity-logs?date_from={$sevenDaysAgoDateString}&date_to={$todayDateString}");
        $response7Days->assertOk();
        $response7Days->assertSee('Aktivitas yang dibuat hari ini');
        $response7Days->assertSee('Aktivitas yang dibuat 5 hari lalu');

        // 3. Filter Rentang Masa Lalu: 10 hari lalu sampai 7 hari lalu
        $tenDaysAgoDateString = \Carbon\Carbon::now('Asia/Jakarta')->subDays(10)->toDateString();
        $responseOld = $this->actingAs($admin)->get("/activity-logs?date_from={$tenDaysAgoDateString}&date_to={$sevenDaysAgoDateString}");
        $responseOld->assertOk();
        $responseOld->assertDontSee('Aktivitas yang dibuat hari ini');
        $responseOld->assertDontSee('Aktivitas yang dibuat 5 hari lalu');
    }

    /**
     * Command prune membersihkan log yang lebih lama dari N hari.
     */
    public function test_prune_command_removes_old_logs(): void
    {
        // Buat log lama (100 hari lalu)
        $oldLog = ActivityLog::create([
            'user_name' => 'Sistem Lama',
            'user_role' => 'guest',
            'module' => 'inventaris',
            'action' => 'delete',
            'description' => 'Log lama yang harus dihapus',
            'created_at' => now()->subDays(100),
        ]);

        // Buat log baru (hari ini)
        $newLog = ActivityLog::create([
            'user_name' => 'Sistem Baru',
            'user_role' => 'admin',
            'module' => 'inventaris',
            'action' => 'create',
            'description' => 'Log baru yang harus tetap ada',
            'created_at' => now(),
        ]);

        $this->assertDatabaseHas('activity_logs', ['id' => $oldLog->id]);
        $this->assertDatabaseHas('activity_logs', ['id' => $newLog->id]);

        // Jalankan prune 90 hari
        Artisan::call('activity-logs:prune', ['--days' => 90]);

        $this->assertDatabaseMissing('activity_logs', ['id' => $oldLog->id]);
        $this->assertDatabaseHas('activity_logs', ['id' => $newLog->id]);
    }
}
