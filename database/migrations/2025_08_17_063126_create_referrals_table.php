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
        Schema::create('referrals', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('referrer_id'); // user who referred
            $table->unsignedBigInteger('referred_id'); // user who was referred

            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);

            $table->enum('status', ['pending','approved','paid'])->default('pending');
            $table->enum('trigger_event', ['membership_upgrade'])->default('membership_upgrade');

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('referrer_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('referred_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->unique(['referrer_id','referred_id']); // prevent duplicates
        });
    }

    public function down(): void {
        Schema::dropIfExists('referrals');
    }


    
};
