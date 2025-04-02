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
        Schema::create('problem_animal_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->onDelete('cascade');
            $table->foreignId('wildlife_conflict_incident_id')->constrained()->onDelete('cascade');
            $table->date('control_date');
            $table->time('control_time')->nullable();
            $table->integer('period')->comment('Year'); // Store the year
            $table->string('location');
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('estimated_number')->default(1);
            $table->timestamps();
        });

        // Create pivot table for control measures with a shorter name
        Schema::create('pac_control_measures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('problem_animal_control_id');
            $table->unsignedBigInteger('control_measure_id');
            
            // Create indexes for better performance
            $table->index('problem_animal_control_id', 'idx_pac_id');
            $table->index('control_measure_id', 'idx_cm_id');
            
            // Add foreign keys with explicit shorter names
            $table->foreign('problem_animal_control_id', 'pac_fk_1')
                ->references('id')
                ->on('problem_animal_controls')
                ->onDelete('cascade');
                
            $table->foreign('control_measure_id', 'pac_fk_2')
                ->references('id')
                ->on('control_measures')
                ->onDelete('cascade');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pac_control_measures');
        Schema::dropIfExists('problem_animal_controls');
    }
};
