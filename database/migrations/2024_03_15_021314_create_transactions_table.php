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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->string('transaction_type')->default('income');
            $table->string('customer_or_donor')->nullable();
            $table->string('currency')->default('USD');
            $table->string('status')->default('pending');
            $table->string('reference_number')->unique()->nullable();
            $table->decimal('amount', 10, 2)->nullable()->default(0.0 );
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
