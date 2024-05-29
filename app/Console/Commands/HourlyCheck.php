<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Bet;
use App\Models\DerbyEvent;
use App\Models\Fight;
use Pusher\Pusher;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HourlyCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:hourly-check';

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
        Log::channel('cron')->info("Cron Job running at " . now());

        // $auth_key = env('PUSHER_APP_KEY', 'fb67b0d962d7feef73c4');
        // $secret_key = env('PUSHER_APP_SECRET', '3b35a2c2dc7f286a5885');
        // $app_id = env('PUSHER_APP_ID', '1785417');;


        // PUSHER_APP_ID=1785417
        // PUSHER_APP_KEY=fb67b0d962d7feef73c4
        // PUSHER_APP_SECRET=3b35a2c2dc7f286a5885

        // $pusher = new Pusher($auth_key, $secret_key, $app_id, [
        //     'cluster' => 'ap1'
        // ]);

        $users = User::whereNotIn('id', [9, 2])->get();
        foreach ($users as $user) {
            # code...
            if ($user->active && $user->updated_at < Carbon::now()->subHour()) {
                $user->update([
                    'active' => false,
                    'timestamps' => false,
                ]);
            }
        }

        $ghost = User::find(9);
        if ($ghost->points < 2000000) {
            $ghost->points += 7000000;
            $ghost->save();
            Log::channel('cron')->info("Ghost added pts: " . $ghost->points);
        }

        $event = DerbyEvent::where('status', 'ACTIVE')->first();
        if ($event) {
            $fight = Fight::where('event_id', $event->id)
                ->orderBy('id', 'desc')->first();

            $bets = Bet::where('fight_id', '<', $fight->id - 5)
                ->where('user_id', 9)->delete();

            Log::channel('bet')->info('Delete: ' . $bets);
        }

        Log::channel('cron')->info("Delete: " . $delete);
    }
}
