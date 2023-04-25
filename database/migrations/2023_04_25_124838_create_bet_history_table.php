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
        Schema::create('bet_history', function (Blueprint $table) {
            $table->bigIncrements('bethistory_no')->autoIncrement();
            $table->integer('user_id');
            $table->integer('fight_no');
            $table->enum('status', ['P','W','L'])->default('P');
            $table->enum('side', ['M','W']);
            $table->double('percent')->default(0);
            $table->double('winamount')->default(0);
            $table->double('betamount');
            $table->timestamp('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bet_history');
    }
};
