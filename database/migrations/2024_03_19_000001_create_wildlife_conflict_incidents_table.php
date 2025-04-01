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
        Schema::create('wildlife_conflict_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->year('period');
            $table->date('incident_date');
            $table->time('incident_time');
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->string('location_description')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('conflict_type_id')->nullable();
            $table->timestamps();
        });

        // Pivot table for species involved in the incident
        Schema::create('wildlife_conflict_incident_species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wildlife_conflict_incident_id')->constrained('wildlife_conflict_incidents', 'id', 'fk_wc_incident_id')->onDelete('cascade');
            $table->foreignId('species_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wildlife_conflict_incident_species');
        Schema::dropIfExists('wildlife_conflict_incidents');
    }
}; 