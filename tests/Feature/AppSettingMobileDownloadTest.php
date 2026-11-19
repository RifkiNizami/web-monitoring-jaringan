<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AppSettingMobileDownloadTest extends TestCase
{
    use DatabaseTransactions;

    public function test_landing_page_renders_download_app_button_and_modal(): void
    {
        AppSetting::setValue('mobile_app_download_url', 'https://netfly.id/downloads/netfly-client-v2.apk');

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Unduh App');
        $response->assertSee('downloadAppModal');
        $response->assertSee('https://netfly.id/downloads/netfly-client-v2.apk');
    }

    public function test_guest_cannot_access_app_setting_page(): void
    {
        $response = $this->get(route('app-setting.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_konten_can_view_app_setting_page(): void
    {
        $adminKonten = User::factory()->create([
            'role' => 'admin_konten',
        ]);

        $response = $this->actingAs($adminKonten)->get(route('app-setting.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Aplikasi Mobile');
        $response->assertSee('Formulir Tautan Unduh');
    }

    public function test_admin_can_update_mobile_download_url(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $newUrl = 'https://play.google.com/store/apps/details?id=com.netfly.monitoring';

        $response = $this->actingAs($admin)->put(route('app-setting.update'), [
            'download_url' => $newUrl,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals($newUrl, AppSetting::getValue('mobile_app_download_url'));

        // Pastikan tercatat di ActivityLog
        $this->assertDatabaseHas('activity_logs', [
            'module' => 'app_settings',
            'action' => 'update',
            'user_id' => $admin->id,
        ]);

        // Pastikan landing page kini memuat link baru
        $landingResponse = $this->get(route('home'));
        $landingResponse->assertSee($newUrl);
    }

    public function test_validation_fails_for_invalid_url(): void
    {
        $adminKonten = User::factory()->create([
            'role' => 'admin_konten',
        ]);

        $response = $this->actingAs($adminKonten)->put(route('app-setting.update'), [
            'download_url' => 'not-a-valid-url',
        ]);

        $response->assertSessionHasErrors(['download_url']);
    }
}
