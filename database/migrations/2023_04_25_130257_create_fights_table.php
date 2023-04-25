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
        Schema::create('fights', function (Blueprint $oTable) {
            $oTable->bigIncrements('fight_no')->autoIncrement();
            $oTable->integer('user_id');
            $oTable->string('status', 1)->nullable();
            $oTable->string('game_winner', 1)->nullable();
            $oTable->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fights');
    }
};
