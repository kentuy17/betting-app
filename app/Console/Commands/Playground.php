<?php

namespace App\Console\Commands;

use App\Models\Bet;
use App\Models\Fight;
use Illuminate\Console\Command;
use App\Models\Transactions;
use App\Models\User;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
// use Redis;
// use Clue\React\Redis\Client;

class Playground extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:playground';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::find(1);
        $this->info(json_encode($user));
        $start = microtime(true);

        // Normal Query
        // $this->dbQuery();

        // Redis
        $this->redisEnabled();

        $time = microtime(true) - $start;
        $this->info($time);
    }

    public function dbQuery()
    {
        $fight = Fight::find(79878);

        $meron = Bet::where(['fight_id' => $fight->id, 'side' => 'M'])->sum('amount');
        $wala = Bet::where(['fight_id' => $fight->id, 'side' => 'W'])->sum('amount');

        $this->info(json_encode(['meron' => $meron, 'wala' => $wala]));
    }

    public function redisEnabled()
    {
        $fight = Fight::find(73984);

        Redis::set('meron:' . $fight->id, 0);
        Redis::set('wala:' . $fight->id, 0);

        foreach ($fight->bet as $bet) {
            if ($bet->side == 'M') {
                Redis::incrby('meron:' . $fight->id, $bet->amount);
            }

            if ($bet->side == 'W') {
                Redis::incrby('wala:' . $fight->id, $bet->amount);
            }
        }

        // Redis::set('user:' . $papawa->id, json_encode($papawa), 60 * 5);

        $meron = Redis::get('meron:' . $fight->id);
        $wala  = Redis::get('wala:' . $fight->id);

        $this->info(json_encode(['meron' => $meron, 'wala' => $wala]));
    }
}
