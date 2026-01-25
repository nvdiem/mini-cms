<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PR-05: Add performance indexes to high-read tables
 */
return new class extends Migration
{
    public function up(): void
    {
        // activity_logs indexes
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('created_at', 'idx_activity_logs_created_at');
            $table->index('type', 'idx_activity_logs_type');
            $table->index(['subject_type', 'subject_id'], 'idx_activity_logs_subject');
        });

        // page_packages indexes
        Schema::table('page_packages', function (Blueprint $table) {
            $table->index(['slug', 'is_active'], 'idx_page_packages_slug_active');
        });

        // leads indexes
        Schema::table('leads', function (Blueprint $table) {
            $table->index('status', 'idx_leads_status');
            $table->index('created_at', 'idx_leads_created_at');
            $table->index('source', 'idx_leads_source');
        });

        // posts indexes
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['status', 'published_at'], 'idx_posts_status_published');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_activity_logs_created_at');
            $table->dropIndex('idx_activity_logs_type');
            $table->dropIndex('idx_activity_logs_subject');
        });

        Schema::table('page_packages', function (Blueprint $table) {
            $table->dropIndex('idx_page_packages_slug_active');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('idx_leads_status');
            $table->dropIndex('idx_leads_created_at');
            $table->dropIndex('idx_leads_source');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('idx_posts_status_published');
        });
    }
};
