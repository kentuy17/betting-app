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
        Schema::table('referrals', function (Blueprint $table) {
            $table->boolean('promo_done')->after('user_id')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn('promo_done');
        });
    }
};
