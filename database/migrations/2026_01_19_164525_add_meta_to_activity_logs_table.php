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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Check if column exists to avoid errors if re-run
            if (!Schema::hasColumn('activity_logs', 'meta')) {
                $table->json('meta')->nullable()->after('message');
            }
            
            // Indexes exist already, skipping creation to avoid errors.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('meta');
            // Don't drop indexes here if they were part of original table structure or we decide to keep them
        });
    }
};
