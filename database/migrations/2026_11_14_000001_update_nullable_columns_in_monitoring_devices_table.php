<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_devices', function (Blueprint $table) {
            $table->decimal('cpu_load', 5, 2)->nullable()->default(0.00)->change();
            $table->decimal('ram_load', 5, 2)->nullable()->default(0.00)->change();
            $table->decimal('packet_loss', 5, 2)->nullable()->default(0.00)->change();
            $table->integer('last_ping_ms')->nullable()->default(0)->change();
            $table->integer('api_port')->nullable()->default(8728)->change();
            $table->string('api_username', 50)->nullable()->default(null)->change();
            $table->string('api_password', 150)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_devices', function (Blueprint $table) {
            $table->decimal('cpu_load', 5, 2)->default(0.00)->change();
            $table->decimal('ram_load', 5, 2)->default(0.00)->change();
            $table->decimal('packet_loss', 5, 2)->default(0.00)->change();
            $table->integer('last_ping_ms')->default(0)->change();
            $table->integer('api_port')->default(8728)->change();
            $table->string('api_username', 50)->nullable()->default('admin')->change();
            $table->string('api_password', 150)->nullable()->change();
        });
    }
};
