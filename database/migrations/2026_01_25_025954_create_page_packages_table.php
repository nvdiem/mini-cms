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
        Schema::create('page_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('zip_path');
            $table->string('public_dir');
            $table->string('version');
            $table->string('entry_file')->default('index.html');
            $table->boolean('is_active')->default(false);
            $table->boolean('wire_contact')->default(true);
            $table->string('wire_selector')->nullable()->default('[data-contact-form],#contactForm,.js-contact');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_packages');
    }
};
