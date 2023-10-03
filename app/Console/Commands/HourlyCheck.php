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
use Shetabit\Visitor\Models\Visit;

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

        $auth_key = 'e0c6c349e2fee92e00ca';
        $secret_key = 'e4aa957c59f83b4f373e';
        $app_id = '1618692';

        $pusher = new Pusher($auth_key, $secret_key, $app_id, ['cluster' => 'ap1']);

        $users = User::whereNotIn('id', [9, 2])->get();
        foreach ($users as $key => $user) {
            # code...
            if (!$user->isOnline() && $user->active) {
                $pusher->terminateUserConnections($user->id);
                $user->update([
                    'active' => false,
                    'timestamps' => false,
                ]);
            }
        }

        $date = Carbon::now()->subHours(1);
        $delete = Visit::where('created_at', '<=', $date)->delete();

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
