<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hunting_concessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('organisation_id'); // Foreign key for organisation
            $table->unsignedBigInteger('safari_id')->nullable(); // for safari operator
            $table->string('hectarage')->nullable(); //size of hunting concession
            $table->longText('description')->nullable(); //description of hunting concession
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('slug')->nullable(); // URL friendly version of name
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hunting_concessions');
    }
};
