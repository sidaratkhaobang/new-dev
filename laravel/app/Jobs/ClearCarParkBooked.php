<?php

namespace App\Jobs;

use App\Enums\CarParkStatusEnum;
use App\Models\CarPark;
use App\Models\CarParkTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearCarParkBooked implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $car_park_id;

    public function __construct($car_park_id)
    {
        $this->car_park_id = $car_park_id;
    }

    public function handle()
    {
        $car_park = CarPark::find($this->car_park_id);
        if (!$car_park) {
            return true;
        }
        if (strcmp($car_park->status, CarParkStatusEnum::BOOKING) != 0) {
            return true;
        }
        $car_park->status = CarParkStatusEnum::FREE;
        $car_park->save();
        return true;
    }
}
