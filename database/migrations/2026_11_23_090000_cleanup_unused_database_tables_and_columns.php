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
        // 1. Drop tabel yatim network_sla_metrics
        Schema::dropIfExists('network_sla_metrics');

        // 2. Drop kolom telemetri & SNMP mati di tabel devices (inventaris gudang)
        Schema::table('devices', function (Blueprint $table) {
            $colsToDrop = [
                'uptime',
                'last_ping_ms',
                'packet_loss',
                'cpu_load',
                'ram_load',
                'rx_rate',
                'tx_rate',
                'snmp_community',
                'snmp_port',
            ];
            foreach ($colsToDrop as $col) {
                if (Schema::hasColumn('devices', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // 3. Drop kolom sensor mati di monitoring_devices
        Schema::table('monitoring_devices', function (Blueprint $table) {
            if (Schema::hasColumn('monitoring_devices', 'temperature')) {
                $table->dropColumn('temperature');
            }
            if (Schema::hasColumn('monitoring_devices', 'voltage')) {
                $table->dropColumn('voltage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('network_sla_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('uptime_percentage', 5, 2)->default(100.00);
            $table->integer('downtime_minutes')->default(0);
            $table->integer('incidents_count')->default(0);
            $table->string('status', 50)->default('Normal');
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->string('snmp_community')->nullable();
            $table->integer('snmp_port')->nullable()->default(161);
            $table->string('uptime')->nullable();
            $table->integer('last_ping_ms')->nullable();
            $table->decimal('packet_loss', 5, 2)->default(0.00);
            $table->decimal('cpu_load', 5, 2)->default(0.00);
            $table->decimal('ram_load', 5, 2)->default(0.00);
            $table->string('rx_rate')->nullable();
            $table->string('tx_rate')->nullable();
        });

        Schema::table('monitoring_devices', function (Blueprint $table) {
            $table->string('temperature')->nullable();
            $table->string('voltage')->nullable();
        });
    }
};
