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
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            
            // Reporter Info
            $table->unsignedBigInteger('reported_by_id');
            $table->string('reported_by_name');
            $table->string('reported_by_position');
            $table->date('date_of_report');
            
            // Description of the Accident
            $table->string('location');
            $table->date('date_of_incident');
            $table->time('time_of_incident');
            $table->string('time_period')->default('AM'); // 'AM' or 'PM'
            $table->text('description_of_accident');
            
            // Injury Information
            $table->boolean('is_someone_injured')->default(false);
            $table->text('injury_description')->nullable();
            
            // People Involved (JSON array)
            $table->json('people_involved')->nullable();
            
            // Status
            $table->string('status')->default('pending'); // 'pending', 'reviewed', 'resolved'
            $table->text('resolution')->nullable();
            $table->unsignedBigInteger('reviewed_by_id')->nullable();
            $table->string('reviewed_by_type')->nullable(); // 'hr', 'manager_coo'
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            
            $table->timestamps();
            
            $table->index('reported_by_id');
            $table->index('status');
            $table->index('date_of_incident');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};

