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
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('action', ['withdraw', 'deposit', 'refill', 'remit', 'topup'])->change();
            $table->enum('outlet', ['Gcash', 'Bank', 'Palawan', 'MLhuilier', 'Maya', 'Agent'])->default('Gcash')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('action', ['withdraw', 'deposit', 'refill', 'remit'])->change();
            $table->enum('outlet', ['Gcash', 'Bank', 'Palawan', 'MLhuilier', 'Maya'])->default('Gcash')->change();
        });
    }
};
