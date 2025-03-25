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
        Schema::create('human_resource_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade');
            $table->year('period');
            
            $table->integer('wildlife_managers')->default(0);
            $table->integer('game_scouts')->default(0);
            $table->integer('rangers')->default(0);
            $table->enum('employed_by', ['community', 'organisation'])->default('organisation');
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('human_resource_records');
    }
};
