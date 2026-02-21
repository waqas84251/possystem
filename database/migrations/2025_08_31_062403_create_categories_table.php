<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Category creator (user / admin / guest null)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('image')->nullable();

            // Parent category (self relation)
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('categories')
                  ->cascadeOnDelete();

            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes (custom names to avoid rollback errors)
            $table->index('name', 'categories_name_index');
            $table->index('status', 'categories_status_index');
            $table->index('parent_id', 'categories_parent_id_index');
            $table->index('sort_order', 'categories_sort_order_index');
        });

        // Add sample categories
        $this->seedSampleCategories();
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['parent_id']);

            // Drop indexes
            $table->dropIndex('categories_name_index');
            $table->dropIndex('categories_status_index');
            $table->dropIndex('categories_parent_id_index');
            $table->dropIndex('categories_sort_order_index');
        });

        Schema::dropIfExists('categories');
    }

    private function seedSampleCategories()
    {
        $now = now();

        $categories = [
            [
                'user_id' => null, // default sample as guest
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'status' => 'active',
                'sort_order' => 1,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => null,
                'name' => 'Clothing',
                'description' => 'Clothes and fashion accessories',
                'status' => 'active',
                'sort_order' => 2,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => null,
                'name' => 'Food & Beverages',
                'description' => 'Food items and drinks',
                'status' => 'active',
                'sort_order' => 3,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => null,
                'name' => 'Books',
                'description' => 'Books, magazines and stationery',
                'status' => 'active',
                'sort_order' => 4,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => null,
                'name' => 'Home & Garden',
                'description' => 'Home and garden supplies',
                'status' => 'active',
                'sort_order' => 5,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('categories')->insert($categories);
    }
};
