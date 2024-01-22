<?php

namespace App\Console\Commands;

use App\Models\RepairOrder;
use Illuminate\Console\Command;
use App\Enums\RepairStatusEnum;
use App\Models\Repair;


class RepairOrderCompleted extends Command
{
    protected $signature = 'command:repair_order_completed';
    protected $description = 'Command Update Status RepairOrder Completed';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $to_day = date('Y-m-d');

        $repair_order_arr = RepairOrder::whereDate('repair_date', '<=', $to_day)
            ->where('status', RepairStatusEnum::IN_PROCESS)->pluck('id')->toArray();

        foreach ($repair_order_arr as $key => $item) {
            $repair_order = RepairOrder::find($item);
            if ($repair_order) {
                $repair_order->status = RepairStatusEnum::COMPLETED;
                $repair_order->save();

                $repair = Repair::find($repair_order->repair_id);
                $repair->status = $repair_order->status;
                $repair->save();
            }
        }
        return true;
    }
}
