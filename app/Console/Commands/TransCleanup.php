<?php

namespace App\Console\Commands;

use App\Models\Transactions;

use Illuminate\Console\Command;

class TransCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:trans-cleanup';

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
        $cleaned = Transactions::whereIn('action', ['deposit', 'withdraw'])
            ->where('status', 'failed')
            // ->whereIn('note', ['Duplicate receipt', null, ''])
            ->update(['morph' => '1']);

        \Log::channel('bet')->info('Trans:' . $cleaned);
    }
}
