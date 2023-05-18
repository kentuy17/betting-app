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
        \DB::statement("ALTER TABLE `bet_history` CHANGE `status` `status` ENUM('P','W','L', 'D','C') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'P';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
