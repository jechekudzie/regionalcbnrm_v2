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
        //
        Schema::create('organisation_type_organisation_type', function (Blueprint $table) {
            $table->id();
            $table->integer('organisation_type_id');
            $table->integer('child_id');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema drop if exists
        Schema::dropIfExists('organisation_type_organisation_type');
    }
};
