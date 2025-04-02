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
        Schema::create('conflict_outcome_dynamic_field_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wildlife_conflict_incident_id');
            $table->unsignedBigInteger('dynamic_field_id');
            $table->string('value'); // This could be the option_value from dynamic_field_options
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
