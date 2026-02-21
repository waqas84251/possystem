<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
           
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('product_id');

            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);

            $table->timestamps();

            // Custom foreign keys with explicit names
            $table->foreign('sale_id', 'fk_sale_items_sale')
                  ->references('id')->on('sales')
                  ->onDelete('cascade');

            $table->foreign('product_id', 'fk_sale_items_product')
                  ->references('id')->on('products')
                  ->onDelete('restrict');
            
            // Indexes
            $table->index('sale_id', 'sale_items_sale_id_index');
            $table->index('product_id', 'sale_items_product_id_index');
        });
    }

    public function down()
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign('fk_sale_items_sale');
            $table->dropForeign('fk_sale_items_product');
            $table->dropIndex('sale_items_sale_id_index');
            $table->dropIndex('sale_items_product_id_index');
        });

        Schema::dropIfExists('sale_items');
    }
};
