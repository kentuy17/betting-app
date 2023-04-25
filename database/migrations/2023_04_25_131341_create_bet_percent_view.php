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
        DB::statement($this->createBetPercentView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
        DB::statement($this->createBetPercentView());
    }

    private function createBetPercentView(): string
    {
        return DB::statement('  CREATE View BetPercent as SELECT betresult.fight_no, 
		betresult.side, 
        bettotal.RemainigBet/betresult.total as Percent,
		(bettotal.RemainigBet/betresult.total)*100 as SampleWinAmount,
        bettotal.RemainigBet - betresult.total as WinMoney
        from betresult JOIN BetTotal 
        ON bettotal.fight_no = betresult.fight_no 
                                group by betresult.side;');
    }
    
    private function dropView(): string
    {
        Schema::dropIfExists('BetPercent');
    }
};
