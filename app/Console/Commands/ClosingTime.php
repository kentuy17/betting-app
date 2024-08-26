<?php

namespace App\Console\Commands;

use App\Http\Controllers\FightController;
use App\Models\Fight;
use App\Events\Fight as FightEvent;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;

class ClosingTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:closing-time';

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
        try {
            $fight = Redis::get('fight');

            if ($fight != 260) {
                $cancel_request = new Request([
                    'status' => 'D',
                    'result' => 'C',
                ]);

                $fight_controller = new FightController();
                $info = Fight::where('fight_no', $fight)->orderBy('id', 'DESC')->first();

                $fight_controller->updateFight($cancel_request);

                $new_fight = Fight::create([
                    'user_id' => 1,
                    'fight_no' => $info->fight_no + 1,
                    'event_id' => $info->event_id,
                    'status' => 'C'
                ]);

                if ($new_fight) {
                    Redis::set('fight', $new_fight->fight_no);
                }

                event(new FightEvent([
                    'prev' => $info,
                    'curr' => $new_fight,
                ]));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
