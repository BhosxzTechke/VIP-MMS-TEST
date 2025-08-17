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
        Schema::create('memberships', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->enum('tier', ['free','vip','platinum','diamond'])->default('free');

            $table->decimal('amount', 10, 2)->default(0); // price charged
            $table->unsignedBigInteger('transaction_id')->nullable(); // set after txn created

            $table->enum('payment_status', ['pending','paid','failed'])->default('pending');
            $table->string('payment_gateway_id')->nullable(); // gateway ref if any
            $table->json('payment_details')->nullable();

            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('memberships');
    }


    
};
