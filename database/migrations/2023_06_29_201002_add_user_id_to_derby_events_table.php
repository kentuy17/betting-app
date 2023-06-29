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
        Schema::table('derby_event', function (Blueprint $table) {
            $table->integer('updated_by')->after('status')->nullable();
            $table->integer('added_by')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('derby_event', function (Blueprint $table) {
            $table->dropColumn('added_by');
            $table->dropColumn('updated_by');
        });
    }
};
