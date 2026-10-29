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
        Schema::create('landing_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('Umum');
            $table->string('location')->default('Indonesia');
            $table->string('logo')->nullable();
            $table->string('website_url')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_clients');
    }
};
