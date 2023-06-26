<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
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
        info("Cron Job running at " . now());
        Log::channel('cron')->info("Cron Job running at " . now());

        $auth_key = 'e0c6c349e2fee92e00ca';
        $secret_key = 'e4aa957c59f83b4f373e';
        $app_id = '1618692';

        $pusher = new Pusher($auth_key, $secret_key, $app_id,['cluster'=>'ap1']);

        $users = User::whereNotIn('id',[9,2])->get();
        foreach ($users as $key => $user) {
            # code...
            if(!$user->isOnline()) {
                $pusher->terminateUserConnections($user->id);
                $user->update([
                    'active' => false
                ]);
            }
        }

        $date = Carbon::now()->subHours(2);
        $delete = Visit::where('created_at', '<=', $date)->delete();
        Log::channel('cron')->info("Delete: " . $delete);
    }
}
