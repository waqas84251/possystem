<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // FAQs table
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->integer('order')->default(0)->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        // Support tickets table
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');

            // Ticket creator (nullable for guest tickets)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])
                  ->default('open')
                  ->index();

            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium')
                  ->index();

            $table->timestamps();
        });

        // Support ticket responses table
        Schema::create('support_ticket_responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                  ->constrained('support_tickets')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->text('response');
            $table->timestamps();

            // Indexes
            $table->index('ticket_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('support_ticket_responses');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('faqs');
    }
};
