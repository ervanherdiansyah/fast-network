<?php

namespace App\Console\Commands;

use App\Models\Courier;
use App\Models\User;
use Illuminate\Console\Command;

class CheckUserWallets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:user-wallets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user wallets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Courier::create([
            'courier_name' => 'test'
        ]);
    }
}
