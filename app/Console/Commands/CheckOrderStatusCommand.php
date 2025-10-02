<?php

namespace App\Console\Commands;

use App\Jobs\CheckOrderStatus;
use Illuminate\Console\Command;

class CheckOrderStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update status of pending orders from Gateway';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching order status check job...');

        CheckOrderStatus::dispatch();

        $this->info('Order status check job dispatched successfully.');

        return Command::SUCCESS;
    }
}
