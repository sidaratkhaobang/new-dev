<?php

namespace App\Console\Commands;

use App\Models\RepairOrder;
use Illuminate\Console\Command;
use App\Enums\RepairStatusEnum;
use App\Models\Repair;
use App\Models\RepairOrderDate;


class RepairOrderInProcess extends Command
{
    protected $signature = 'command:repair_order_in_process';
    protected $description = 'Command Update Status RepairOrder InProcess';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $to_day = date('Y-m-d');

        $repair_order_date_arr = RepairOrderDate::whereDate('center_date', '<=', $to_day)
            ->where('status', STATUS_ACTIVE)->pluck('repair_order_id')->toArray();

        foreach ($repair_order_date_arr as $key => $item) {
            $repair_order = RepairOrder::where('id', $item)->where('status', RepairStatusEnum::PENDING_REPAIR)->first();
            if ($repair_order) {
                $repair_order->status = RepairStatusEnum::IN_PROCESS;
                $repair_order->save();

                $repair = Repair::find($repair_order->repair_id);
                $repair->status = $repair_order->status;
                $repair->save();
            }
        }
        return true;
    }
}
