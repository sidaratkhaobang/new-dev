<?php

namespace App\Console\Commands;

use App\Enums\CarParkStatusEnum;
use App\Models\CarPark;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseCarPark extends Command
{
    protected $signature = 'command:close_car_park';

    protected $description = 'Disable Car Park By Date';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $disable_car_park_list = CarPark::whereNotNull('start_disabled_date')
            ->whereDate('start_disabled_date', '<=', $today)
            ->get();
        foreach ($disable_car_park_list as $key => $item) {
            $car_park = CarPark::find($item->id);
            $car_park->status = CarParkStatusEnum::DISABLED;
            $car_park->save();
        }

        $enable_car_park_list = CarPark::whereNotNull('end_disabled_date')
            ->whereDate('end_disabled_date', $yesterday)
            ->get();
        foreach ($enable_car_park_list as $key => $item) {
            $car_park = CarPark::find($item->id);
            $car_park->start_disabled_date = NULL;
            $car_park->end_disabled_date = NULL;
            $car_park->is_permanent_disabled = NULL;
            $car_park->save();
        }
        return true;
    }
}
