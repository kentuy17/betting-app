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
        Schema::create('bets', function (Blueprint $oTable) {
            $oTable->bigIncrements('bet_no')->autoIncrement();
            $oTable->integer('fight_no');
            $oTable->integer('user_id');
            $oTable->integer('amount');
            $oTable->string('side', 1);
            $oTable->string('status', 1);
            $oTable->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
