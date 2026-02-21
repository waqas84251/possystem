<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['text', 'textarea', 'number', 'boolean', 'json', 'email'])
                  ->default('text');
            $table->string('group')->default('general');
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // ✅ Default settings insert directly in migration
        DB::table('settings')->insert([

            // Business Information
            [
                'key' => 'business_name',
                'value' => 'My POS Business',
                'type' => 'text',
                'group' => 'business',
                'label' => 'Business Name',
                'description' => 'The name of your business',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'business_address',
                'value' => '123 Business Street, City',
                'type' => 'textarea',
                'group' => 'business',
                'label' => 'Business Address',
                'description' => 'Your business physical address',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'business_phone',
                'value' => '+1 (555) 123-4567',
                'type' => 'text',
                'group' => 'business',
                'label' => 'Business Phone',
                'description' => 'Your business phone number',
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'business_email',
                'value' => 'business@example.com',
                'type' => 'email',
                'group' => 'business',
                'label' => 'Business Email',
                'description' => 'Your business email address',
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Receipt Settings
            [
                'key' => 'receipt_header',
                'value' => 'Thank you for your purchase!',
                'type' => 'textarea',
                'group' => 'receipt',
                'label' => 'Receipt Header',
                'description' => 'Text to show at the top of receipts',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'receipt_footer',
                'value' => 'Please come again!',
                'type' => 'textarea',
                'group' => 'receipt',
                'label' => 'Receipt Footer',
                'description' => 'Text to show at the bottom of receipts',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'receipt_show_logo',
                'value' => json_encode(true), // boolean as JSON
                'type' => 'boolean',
                'group' => 'receipt',
                'label' => 'Show Logo on Receipt',
                'description' => 'Display business logo on receipts',
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // System Settings
            [
                'key' => 'currency',
                'value' => '$',
                'type' => 'text',
                'group' => 'system',
                'label' => 'Currency Symbol',
                'description' => 'Currency symbol to use throughout the system',
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tax_rate',
                'value' => '0.00',
                'type' => 'number',
                'group' => 'system',
                'label' => 'Tax Rate (%)',
                'description' => 'Default tax rate percentage',
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'low_stock_threshold',
                'value' => '10',
                'type' => 'number',
                'group' => 'system',
                'label' => 'Low Stock Threshold',
                'description' => 'Minimum stock level before alert is triggered',
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
