<?php

use App\Models\AppSetting;

if (! function_exists('__blade_extends')) {
    /**
     * IDE helper stub for Blade @extends directive.
     */
    function __blade_extends(?string $layout = null): void {}
}

if (! function_exists('app_setting')) {
    /**
     * Dapatkan nilai pengaturan aplikasi dinamis.
     */
    function app_setting(string $key, ?string $default = null): ?string
    {
        return AppSetting::getValue($key, $default);
    }
}
