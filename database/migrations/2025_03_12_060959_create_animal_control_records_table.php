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
        Schema::create('animal_control_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('species_id');
            $table->year('period');
            $table->integer('number_of_cases')->default(0);
            $table->integer('killed')->default(0);
            $table->integer('relocated')->default(0);
            $table->integer('scared')->default(0);
            $table->integer('injured')->default(0);
            $table->timestamps();

            // Add a unique constraint to prevent duplicate records
            $table->unique(['organisation_id', 'species_id', 'period'], 'animal_control_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_control_records');
    }
};
