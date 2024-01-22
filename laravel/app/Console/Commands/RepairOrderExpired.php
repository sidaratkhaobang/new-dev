<?php

namespace App\Console\Commands;

use App\Models\RepairOrder;
use Illuminate\Console\Command;
use App\Enums\RepairStatusEnum;
use App\Models\Repair;
use App\Models\RepairOrderDate;


class RepairOrderExpired extends Command
{
    protected $signature = 'command:repair_order_expired';
    protected $description = 'Command Update Status RepairOrder Expired';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $prev_day = date('Y-m-d', strtotime("-7 days"));

        $repair_order_date_arr = RepairOrderDate::leftJoin('repair_orders', 'repair_orders.id', '=', 'repair_order_date.repair_order_id')
            ->leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
            ->whereDate('repairs.repair_date', '<=', $prev_day)
            ->whereNull('repair_order_date.center_date')->where('repair_order_date.status', STATUS_ACTIVE)
            ->pluck('repair_order_date.repair_order_id')->toArray();

        foreach ($repair_order_date_arr as $key => $item) {
            $repair_order = RepairOrder::where('id', $item)->where('status', RepairStatusEnum::PENDING_REPAIR)->first();
            if ($repair_order) {
                $repair_order->status = RepairStatusEnum::EXPIRED;
                $repair_order->save();

                $repair = Repair::find($repair_order->repair_id);
                $repair->status = $repair_order->status;
                $repair->save();
            }
        }
        return true;
    }
}
