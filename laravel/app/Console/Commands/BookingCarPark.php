<?php

namespace App\Console\Commands;

use App\Classes\CarParkManagement;
use App\Jobs\ClearCarParkBooked;
use App\Jobs\SetCarParkBooked;
use App\Models\CarParkTransfer;
use DateTime;
use Illuminate\Console\Command;

class BookingCarPark extends Command
{
    protected $signature = 'command:booking_car_park';
    protected $description = 'Booking Car Park Slot';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $prev_day = date('Y-m-d', strtotime("-1 days"));
        $today = date('Y-m-d');

        $car_park_transfer_clear_list = $this->getCarParkTransferList($prev_day, false);
        foreach ($car_park_transfer_clear_list as $key => $item) {
            ClearCarParkBooked::dispatch($item->id);
        }

        $car_park_transfer_list = $this->getCarParkTransferList($today, true);
        foreach ($car_park_transfer_list as $key => $item) {
            SetCarParkBooked::dispatch($item->car_id, $item->id, $item->destination_branch_id);
        }
    }

    public function getCarParkTransferList($date, $is_car_park_null = false)
    {
        return CarParkTransfer::whereDate('est_transfer_date', $date)
            ->when($is_car_park_null, function ($query) {
                $query->whereNull('car_park_id');
            })
            ->get();
    }
}
