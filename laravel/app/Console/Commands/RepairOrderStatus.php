<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;


class RepairOrderStatus extends Command
{
    protected $signature = 'command:repair_order_status';
    protected $description = 'Command Update Status In RepairOrder';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Artisan::call('command:repair_order_expired');
        Artisan::call('command:repair_order_in_process');
        Artisan::call('command:repair_order_completed');
    }
}
