<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transactions;
use Carbon\Carbon;

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
       $papawa = Transactions::where('processedBy', 92966)
                    ->where('action', 'topup')
                    ->whereDate('created_at', Carbon::now())
		    ->sum('amount');

      $this->info(json_encode($papawa)); 
    }
}
