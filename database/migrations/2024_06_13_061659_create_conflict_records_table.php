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
        Schema::create('conflict_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('species_id');
            $table->integer('period');
            $table->integer('crop_damage_cases')->default(0);
            $table->integer('hectarage_destroyed')->default(0);
            $table->integer('human_injured')->default(0);
            $table->integer('human_death')->default(0);
            $table->integer('livestock_killed_injured')->default(0);
            $table->integer('infrastructure_destroyed')->default(0);
            $table->integer('threat_to_human_life')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conflict_records');
    }
};
