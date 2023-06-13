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
        Schema::create('users-password-reset', function (Blueprint $table) {
            $table->id();
            $table->string('userid');
            $table->string('username');
            $table->string('phone_no');
            $table->enum('status',['pending','completed'])->default('pending');
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users-password-reset');
    }
};
