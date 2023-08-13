<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DailyCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Daily Cron';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = date('m-d', strtotime('-1 day'));
        $file = storage_path('logs/'.$date.'-laravel.log');
        $new = storage_path('logs/laravel.log');
        rename($new, $file);
        fopen($new, 'w');
        chmod($new, 0777);  //changed to add the zero
    }
}
