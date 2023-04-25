<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->createTotalView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
        DB::statement($this->createTotalView());
    }

    private function createTotalView(): string
    {
        return DB::statement(' Create View bettotal as 
                SELECT fight_no,
                SUM(total) as TotalBet, 
                SUM(total) * 0.075 as ServiceFee, 
                SUM(total) - (SUM(total) * 0.075) as RemainigBet 
                FROM betresult;');
    }
    
    private function dropView(): string
    {
        Schema::dropIfExists('bettotal');
    }
};
