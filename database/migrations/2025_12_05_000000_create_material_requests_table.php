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
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            
            // Requester Information
            $table->unsignedBigInteger('requested_by_id');
            $table->string('requested_by_name');
            $table->string('requested_by_position');
            $table->string('department')->nullable();
            
            // Request Details
            $table->date('date_of_request');
            $table->date('date_needed');
            $table->string('project_name')->nullable();
            $table->string('project_location')->nullable();
            $table->text('purpose');
            
            // Materials (stored as JSON array)
            $table->json('materials');
            
            // Priority
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Status tracking
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing', 'completed'])->default('pending');
            $table->text('remarks')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Approval tracking
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->string('approved_by_type')->nullable(); // 'hr' or 'manager_coo'
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};

