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
        Schema::table('poaching_incidents', function (Blueprint $table) {
            $table->string('docket_number')->nullable()->after('longitude');
            $table->enum('docket_status', ['open', 'under investigation', 'closed', 'pending court', 'convicted'])->nullable()->after('docket_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poaching_incidents', function (Blueprint $table) {
            $table->dropColumn(['docket_number', 'docket_status']);
        });
    }
};
