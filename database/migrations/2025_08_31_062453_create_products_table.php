<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Product owner (vendor/user)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('name');
            $table->string('sku')->unique()->index();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->default(10);

            // Category relation
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();

            $table->string('barcode')->unique()->nullable()->index();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Record creator (admin/staff)
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes (custom names for rollback safety)
            $table->index('name', 'products_name_index');
            $table->index('status', 'products_status_index');
            $table->index('category_id', 'products_category_id_index');
            $table->index('stock', 'products_stock_index');
            $table->index(['status', 'stock'], 'products_status_stock_index');
            $table->index(['category_id', 'status'], 'products_category_status_index');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['category_id']);

            // Drop indexes
            $table->dropIndex('products_name_index');
            $table->dropIndex('products_status_index');
            $table->dropIndex('products_category_id_index');
            $table->dropIndex('products_stock_index');
            $table->dropIndex('products_status_stock_index');
            $table->dropIndex('products_category_status_index');
        });

        Schema::dropIfExists('products');
    }
};
