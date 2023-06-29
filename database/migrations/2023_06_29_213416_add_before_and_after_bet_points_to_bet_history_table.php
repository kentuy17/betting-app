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
        Schema::table('bet_history', function (Blueprint $table) {
            $table->double('points_before_bet',10,2)->after('winamount');
            $table->double('points_after_bet',10,2)->after('betamount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bet_history', function (Blueprint $table) {
            $table->dropColumn('points_before_bet');
            $table->dropColumn('points_after_bet');
        });
    }
};
