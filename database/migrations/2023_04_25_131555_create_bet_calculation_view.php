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
        DB::statement($this->createCalculationView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
        DB::statement($this->createCalculationView());
    }

    private function createCalculationView(): string
    {
        return DB::statement(' Create View BetCalculation as SELECT 
		bets.fight_no, 
        bets.user_id,
		bets.side, 
        SUM(bets.amount),
        betpercent.Percent,
        betpercent.Percent
        *bets.amount as totalWin
        FROM betpercent
        JOIN bets ON bets.fight_no = betpercent.fight_no where bets.side = betpercent.side group by bets.user_id, bets.side;');
    }
    
    private function dropView(): string
    {
        Schema::dropIfExists('BetCalculation');
    }
};
