<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();

            // Relation with products
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->integer('quantity')->nullable();
            $table->integer('low_stock_threshold')->default(5);

            // New fields
            $table->enum('type', ['initial', 'purchase', 'sale', 'adjustment'])
                  ->default('initial');
            $table->text('remarks')->nullable();

            // Relation with users (who created the entry)
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            // Indexes for performance
            $table->index(['product_id', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
