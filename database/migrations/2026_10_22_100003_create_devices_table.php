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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('hostname', 100)->unique();
            $table->string('type', 50); // Router, Switch, Firewall, Access Point, Server
            $table->string('ip_address', 45);
            $table->string('mac_address', 50)->nullable();
            $table->string('status', 100)->default('Online (Beroperasi Normal)');
            $table->string('status_state', 50)->default('online'); // online, offline, warning, maintenance
            $table->string('rack_location', 100)->nullable();
            $table->string('snmp_community', 100)->nullable()->default('netfly-ro-noc');
            $table->integer('snmp_port')->default(161);
            $table->string('uptime', 100)->nullable();
            $table->integer('last_ping_ms')->nullable();
            $table->decimal('packet_loss', 5, 2)->default(0.00);
            $table->decimal('cpu_load', 5, 2)->default(0.00);
            $table->decimal('ram_load', 5, 2)->default(0.00);
            $table->string('rx_rate', 50)->nullable();
            $table->string('tx_rate', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
