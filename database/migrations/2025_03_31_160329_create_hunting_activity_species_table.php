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
        Schema::create('hunting_activity_species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hunting_activity_id')->constrained()->onDelete('cascade');
            $table->foreignId('species_id')->constrained()->onDelete('cascade');
            $table->integer('off_take');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hunting_activity_species');
    }
};
