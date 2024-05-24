<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Pusher\Pusher;

class LoggedOutListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // $auth_key = 'e0c6c349e2fee92e00ca';
        // $secret_key = 'e4aa957c59f83b4f373e';
        // $app_id = '1618692';

        // $pusher = new Pusher($auth_key, $secret_key, $app_id,['cluster'=>'ap1']);
        // $pusher->terminateUserConnections($event->user->id);

        // $event->user->update([
        //     'active' => false
        // ]);
    }
}
