<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();

            // Snapshots — immutable after creation
            $table->string('product_title_snapshot');
            $table->string('variant_signature_snapshot')->nullable();
            $table->string('sku_snapshot')->nullable();
            $table->decimal('unit_price_snapshot', 12, 2);
            $table->unsignedInteger('qty');
            $table->decimal('line_total', 12, 2);

            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
