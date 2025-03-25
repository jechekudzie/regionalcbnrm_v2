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
        Schema::create('crop_conflict_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('species_id');
            $table->unsignedBigInteger('crop_type_id');
            $table->decimal('hectrage_destroyed', 10, 2);
            $table->year('period');
            $table->timestamps();

            // Add a unique constraint to prevent duplicate records
            $table->unique(['organisation_id', 'species_id', 'crop_type_id', 'period'], 'crop_conflict_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_conflict_records');
    }
};
