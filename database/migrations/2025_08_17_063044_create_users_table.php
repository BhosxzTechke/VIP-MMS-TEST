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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // Referral fields
            $table->string('referral_code')->unique()->nullable();
            $table->unsignedBigInteger('referred_by')->nullable();
            $table->string('phone')->nullable();

            $table->timestamps();

            $table->foreign('referred_by')
                ->references('id')->on('users')
                ->nullOnDelete(); // if referrer is deleted, keep user but null this
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
