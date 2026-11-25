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
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'status_note')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->text('status_note')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'status_note')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('status_note', 255)->nullable()->change();
            });
        }
    }
};
