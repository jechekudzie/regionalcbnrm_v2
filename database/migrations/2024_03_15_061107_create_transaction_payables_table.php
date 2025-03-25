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
        Schema::create('transaction_payables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('organisation_payable_item_id');
            $table->unsignedBigInteger('species_id')->nullable();
            $table->decimal('price', 10, 2)->nullable(); //price for the item
            $table->decimal('amount', 10, 2)->nullable()->default(0.0 );//amount paid
            $table->decimal('balance', 10, 2)->nullable()->default(0.0 );
            $table->string('currency')->default('USD');
            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();
            $table->string('reference_number')->unique()->nullable();
            $table->string('pop')->nullable();
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
        Schema::dropIfExists('transaction_payables');
    }
};
