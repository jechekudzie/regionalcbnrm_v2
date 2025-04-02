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
        Schema::table('poachers', function (Blueprint $table) {
            $table->enum('status', ['suspected', 'arrested', 'bailed', 'sentenced', 'released'])->nullable()->after('age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poachers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
