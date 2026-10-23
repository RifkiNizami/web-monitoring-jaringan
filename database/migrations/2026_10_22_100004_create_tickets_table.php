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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->string('client_name', 200)->nullable();
            $table->string('project_name', 200)->nullable();
            $table->string('node_code', 100)->nullable();
            $table->string('priority', 50)->default('P3 - MEDIUM');
            $table->string('priority_level', 50)->default('medium'); // critical, high, medium, low
            $table->string('subject', 255);
            $table->text('description');
            $table->string('assigned_technician', 150)->nullable();
            $table->boolean('is_tech_warning')->default(false);
            $table->string('status', 100)->default('1. Open (Baru)');
            $table->string('status_type', 50)->default('open'); // open, assigned, in_progress, resolved, closed
            $table->string('status_note', 255)->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
