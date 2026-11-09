<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_devices', function (Blueprint $table) {
            $table->id();
            $table->string('hostname', 100)->index();
            $table->string('unit_type', 50)->default('Router');
            $table->string('ip_address', 100);
            $table->string('customer', 150);
            $table->string('project', 150)->nullable()->default('Dedicated SLA Monitoring');
            $table->string('model', 100)->nullable();
            $table->string('rack_location', 100)->nullable()->default('POP Distribusi');
            $table->string('status', 30)->default('online');
            $table->string('status_label', 30)->default('ONLINE');
            $table->string('uptime', 80)->nullable()->default('Siap Pantau');
            $table->string('rx_mbps', 40)->nullable()->default('0.0 Mbps');
            $table->string('tx_mbps', 40)->nullable()->default('0.0 Mbps');
            $table->decimal('cpu_load', 5, 2)->default(0.00);
            $table->decimal('ram_load', 5, 2)->default(0.00);
            $table->string('temperature', 30)->nullable()->default('-');
            $table->string('voltage', 30)->nullable()->default('-');
            $table->decimal('packet_loss', 5, 2)->default(0.00);
            $table->integer('last_ping_ms')->default(0);
            $table->string('connection_type', 50)->default('lan');
            $table->integer('api_port')->default(8728);
            $table->string('api_username', 50)->nullable()->default('admin');
            $table->string('api_password', 150)->nullable();
            $table->json('interfaces')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_devices');
    }
};
