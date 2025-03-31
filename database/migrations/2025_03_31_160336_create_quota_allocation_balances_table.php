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
        Schema::create('quota_allocation_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quota_allocation_id')->constrained()->onDelete('cascade');
            $table->integer('total_off_take')->default(0);
            $table->integer('remaining_quota')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quota_allocation_balances');
    }
};
