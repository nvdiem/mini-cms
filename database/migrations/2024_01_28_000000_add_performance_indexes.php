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
            $table->index(['created_at', 'type']); // Compound index for feed
        });

        Schema::table('page_packages', function (Blueprint $table) {
            $table->index(['slug', 'is_active']); // For public lookup
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->index('status'); // For filtering
            $table->index('source'); // For filtering
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
             $table->dropIndex(['created_at', 'type']);
        });

        Schema::table('page_packages', function (Blueprint $table) {
             $table->dropIndex(['slug', 'is_active']);
        });

        Schema::table('leads', function (Blueprint $table) {
             $table->dropIndex(['status']);
             $table->dropIndex(['source']);
        });
    }
};
