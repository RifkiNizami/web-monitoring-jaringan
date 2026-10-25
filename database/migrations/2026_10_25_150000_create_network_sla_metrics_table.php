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
        Schema::create('network_sla_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('uptime_percentage', 5, 2)->default(100.00);
            $table->integer('downtime_minutes')->default(0);
            $table->integer('incidents_count')->default(0);
            $table->string('status', 50)->default('Normal'); // Normal, Warning, Incident
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_sla_metrics');
    }
};
