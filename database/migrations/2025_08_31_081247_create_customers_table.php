<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Optional: Customer belongs to a specific user/vendor
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            
            $table->timestamps();

            // Indexes for fast search
            $table->index('name', 'customers_name_index');
            $table->index('phone', 'customers_phone_index');
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex('customers_name_index');
            $table->dropIndex('customers_phone_index');
        });

        Schema::dropIfExists('customers');
    }
};
