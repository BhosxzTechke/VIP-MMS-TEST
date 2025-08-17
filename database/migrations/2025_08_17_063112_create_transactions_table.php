<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['membership','topup','product'])->default('membership');

            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('PHP');

            $table->string('payment_method')->nullable(); // gcash, card, etc.
            $table->json('payment_metadata')->nullable(); // raw gateway payload
            $table->string('external_payment_id')->nullable(); // PayMongo PI/PM id
            $table->enum('status', ['pending','paid','failed'])->default('pending');

            // Keep a separate transaction_date to match your earlier code paths
            $table->timestamp('transaction_date')->useCurrent();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};
