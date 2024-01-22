<?php

namespace App\Jobs;

use App\Classes\CarParkManagement;
use App\Enums\CarParkStatusEnum;
use App\Models\CarPark;
use App\Models\CarParkTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetCarParkBooked implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $car_id;
    protected $car_park_transfer_id;
    protected $branch_id;

    public function __construct($car_id, $car_park_transfer_id, $branch_id)
    {
        $this->car_id = $car_id;
        $this->car_park_transfer_id = $car_park_transfer_id;
        $this->branch_id = $branch_id;
    }

    public function handle()
    {
        $free_car_park = $this->getFreeCarParkSlot($this->car_id, $this->branch_id);
        if (!$free_car_park) {
            return true;
        }
        $free_car_park->status = CarParkStatusEnum::BOOKING;
        $free_car_park->save();
        $this->saveCarParkTransferBookedCarPark($this->car_park_transfer_id, $free_car_park->id);
    }

    public function getFreeCarParkSlot($car_id, $branch_id)
    {
        $cpm = new CarParkManagement($car_id, $branch_id);
        $free_car_parks = $cpm->getFreeSlots();
        $free_car_park = $free_car_parks->first();
        return $free_car_park;
    }

    public function saveCarParkTransferBookedCarPark($car_park_transfer_id, $car_park_id)
    {
        $car_park_transfer = CarParkTransfer::findOrFail($car_park_transfer_id);
        $car_park_transfer->car_park_id = $car_park_id;
        $car_park_transfer->save();
        return true;
    }
}
