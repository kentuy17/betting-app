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
        Schema::table('share_holders', function (Blueprint $table) {
            $table->decimal('current_commission', 10, 2)
                ->after('role_description')
                ->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('share_holders', function (Blueprint $table) {
            $table->dropColumn('current_commission');
        });
    }
};
