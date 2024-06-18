<?php

namespace App\Listeners;

use App\Events\SecuredBet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Redis;

class SendBetInfo
{
    /**
     * Create the event listener.
     */
    // public function __construct()
    // {

    // }

    /**
     * Handle the event.
     */
    public function handle(SecuredBet $event): void
    {
    }

    // public function withDelay(SecuredBet $event): int
    // {
    //     // return (int)Redis::get('delay');
    //     return 20;
    // }

    // public $delay = 20;
}
