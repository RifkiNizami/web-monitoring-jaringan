<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            // Drop unique constraint on hostname if exists to allow flexible inventory batch entries
            try {
                $table->dropUnique('devices_hostname_unique');
            } catch (\Throwable $e) {
                // Ignore if already dropped
            }

            $table->string('ip_address', 45)->nullable()->change();

            if (!Schema::hasColumn('devices', 'brand_model')) {
                $table->string('brand_model', 150)->nullable()->after('type');
            }
            if (!Schema::hasColumn('devices', 'serial_number')) {
                $table->string('serial_number', 100)->nullable()->after('brand_model');
            }
            if (!Schema::hasColumn('devices', 'quantity')) {
                $table->integer('quantity')->default(1)->after('serial_number');
            }
            if (!Schema::hasColumn('devices', 'photo')) {
                $table->string('photo', 255)->nullable()->after('status_state');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['brand_model', 'serial_number', 'quantity', 'photo'] as $col) {
                if (Schema::hasColumn('devices', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
