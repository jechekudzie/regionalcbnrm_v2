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
        Schema::create('income_beneficiary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade');
            $table->year('period');
            $table->integer('households')->default(0);
            $table->integer('males')->default(0);
            $table->integer('females')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Add a unique constraint to prevent duplicate records for the same year and organisation
            $table->unique(['organisation_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_beneficiary_records');
    }
};
