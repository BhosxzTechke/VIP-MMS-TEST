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
                Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'referral_code')) {
                    $table->string('referral_code')->nullable();
                }
                });
            }

            public function down(): void
            {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['referred_by']);
                    $table->dropColumn('referred_by');
                });
            }
};
