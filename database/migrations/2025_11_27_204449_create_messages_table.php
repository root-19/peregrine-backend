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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_type'); // 'user', 'hr', 'manager_coo'
            $table->unsignedBigInteger('sender_id');
            $table->string('receiver_type'); // 'user', 'hr', 'manager_coo'
            $table->unsignedBigInteger('receiver_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Index for faster queries
            $table->index(['sender_type', 'sender_id', 'receiver_type', 'receiver_id']);
            $table->index(['receiver_type', 'receiver_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
