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
        DB::statement($this->createResultView());
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
        DB::statement($this->createResultView());
    }

    private function createResultView(): string
    {
        return DB::statement(' create view BetResult as 
        select multi_auth.fights.fight_no, bets.side, SUM(bets.amount) as total 
		from bets JOIN fights ON bets.fight_no = fights.fight_no 
        WHERE fights.fight_no=(SELECT max(fights.fight_no) FROM fights)
        group by bets.side ORDER BY fights.fight_no DESC LIMIT 2 ;');
    }
    
    private function dropView(): string
    {
        Schema::dropIfExists('BetResult');
    }
};
