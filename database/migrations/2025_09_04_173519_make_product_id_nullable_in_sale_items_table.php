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
    Schema::table('sale_items', function (Blueprint $table) {
        // Make product_id nullable
        $table->unsignedBigInteger('product_id')->nullable()->change();
        
        // Add the new columns if they don't exist
        if (!Schema::hasColumn('sale_items', 'name')) {
            $table->string('name')->nullable()->after('product_id');
        }
        if (!Schema::hasColumn('sale_items', 'is_manual')) {
            $table->boolean('is_manual')->default(false)->after('total_price');
        }
    });
}

public function down()
{
    Schema::table('sale_items', function (Blueprint $table) {
        // Revert product_id to not nullable
        $table->unsignedBigInteger('product_id')->nullable(false)->change();
        
        // Remove the added columns
        $table->dropColumn(['name', 'is_manual']);
    });
}
};
