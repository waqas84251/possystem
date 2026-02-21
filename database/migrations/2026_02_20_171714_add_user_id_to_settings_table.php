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
        Schema::table('settings', function (Blueprint $table) {
            // Add user_id column
            $table->foreignId('user_id')
                  ->after('id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('cascade');

            // Drop old unique constraint on 'key'
            $table->dropUnique(['key']);

            // Add new unique constraint on 'key' + 'user_id'
            $table->unique(['key', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['key', 'user_id']);
            $table->unique('key');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
