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
        Schema::table('transactions', function (Blueprint $transactions) {
            $transactions->enum('outlet', ['Gcash', 'Bank', 'Palawan', 'MLhuilier', 'Maya'])->default('Gcash');
        });

        Schema::table('users', function (Blueprint $users) {
            $users->integer('role_id')->default('2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $transactions) {
            $transactions->enum('outlet', ['Gcash', 'Bank', 'Palawan', 'MLhuilier'])->default('Gcash');
        });

        Schema::table('users', function (Blueprint $users) {
            $users->integer('role_id')->default(null);
        });
    }
};
