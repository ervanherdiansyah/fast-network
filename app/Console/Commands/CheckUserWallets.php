<?php

namespace App\Console\Commands;

use App\Models\Courier;
use App\Models\Reward;
use App\Models\User;
use App\Models\UserKomisiHistory;
use App\Models\UserWallet;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        try {
            //code...
            $now = Carbon::now();
            $sixMonthsAgo = $now->subMonths(6);

            // Get users whose total_harga is 100jt or more within the last 6 months
            $users = DB::table('order_total_enam_bulan')
                ->where('total_harga', '>=', 100000000)
                ->where('updated_at', '>=', $sixMonthsAgo)
                ->get();

            foreach ($users as $user) {
                // Give bonus to user
                $affliator = UserWallet::where('user_id', $users->id)->first();
                $affliator->total_balance += 2500000;
                $affliator->current_balance += 2500000;
                $affliator->save();

                // Reset total_harga to 0
                DB::table('order_total_enam_bulan')
                    ->where('user_id', $user->user_id)
                    ->update(['total_harga' => 0, 'updated_at' => now()]);
            }

            // Reset total_harga to 0 for users whose last update was more than 6 months ago
            DB::table('order_total_enam_bulan')
                ->where('updated_at', '<', $sixMonthsAgo)
                ->update(['total_harga' => 0, 'updated_at' => now()]);

            $this->info('Order totals checked and bonuses given where applicable.');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
