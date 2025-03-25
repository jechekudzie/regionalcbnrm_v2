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
        Schema::create('income_use_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade');
            $table->year('period');
            $table->decimal('administration_amount', 15, 2)->default(0);
            $table->decimal('management_activities_amount', 15, 2)->default(0);
            $table->decimal('social_services_amount', 15, 2)->default(0);
            $table->decimal('law_enforcement_amount', 15, 2)->default(0);
            $table->decimal('other_amount', 15, 2)->default(0);
            $table->text('other_description')->nullable();
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
        Schema::dropIfExists('income_use_records');
    }
};
