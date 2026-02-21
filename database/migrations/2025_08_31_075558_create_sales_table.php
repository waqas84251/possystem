<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            // Sale created by user (vendor/admin)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'fk_sales_user')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            // Customer (if you plan to add customers table later)
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->string('sale_number')->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);

            $table->string('payment_method')->default('cash');
            $table->string('payment_status')->default('completed');
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('customer_id', 'sales_customer_id_index');
            $table->index('payment_status', 'sales_payment_status_index');
            $table->index('payment_method', 'sales_payment_method_index');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign('fk_sales_user');
            $table->dropIndex('sales_customer_id_index');
            $table->dropIndex('sales_payment_status_index');
            $table->dropIndex('sales_payment_method_index');
        });

        Schema::dropIfExists('sales');
    }
};
