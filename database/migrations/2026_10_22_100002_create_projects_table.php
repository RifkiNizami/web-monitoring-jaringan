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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('subtitle', 255)->nullable();
            $table->string('branch_location', 255);
            $table->string('gps_lat', 50)->nullable();
            $table->string('gps_long', 50)->nullable();
            $table->string('service_type', 150);
            $table->string('status', 50)->default('Aktif');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
