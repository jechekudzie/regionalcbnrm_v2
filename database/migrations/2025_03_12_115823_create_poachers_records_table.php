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
        Schema::create('poachers_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('species_id');
            $table->integer('arrested')->default(0);
            $table->integer('bailed')->default(0);
            $table->integer('sentenced')->default(0);
            $table->year('period');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add a unique constraint to prevent duplicate records
            $table->unique(['organisation_id', 'species_id', 'period'], 'poachers_record_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poachers_records');
    }
};
