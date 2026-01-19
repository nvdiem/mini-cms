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
        Schema::table('media', function (Blueprint $table) {
            $table->string('alt_text')->nullable()->after('uploaded_by');
            $table->text('caption')->nullable()->after('alt_text');
            $table->integer('width')->nullable()->after('size');
            $table->integer('height')->nullable()->after('width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['alt_text', 'caption', 'width', 'height']);
        });
    }
};
