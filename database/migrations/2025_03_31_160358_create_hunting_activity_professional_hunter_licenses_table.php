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
        Schema::create('hunting_activity_professional_hunter_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hunting_activity_id');
            $table->string('license_number')->nullable();
            $table->string('hunter_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hunting_activity_professional_hunter_licenses');
    }
};
