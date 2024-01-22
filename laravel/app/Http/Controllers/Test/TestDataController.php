<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Factories\DrivingJobFactory;
use App\Models\Car;
use App\Models\Rental;
use App\Models\DrivingJob;
use App\Models\Driver;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\SelfDriveTypeEnum;

class TestDataController extends Controller
{
    function clearRentals()
    {
        DB::table('rentals')->delete();
        DB::table('rental_lines')->delete();
        DB::table('rental_product_additionals')->delete();
        DB::table('rental_product_transports')->delete();
        DB::table('rental_drivers')->delete();

        DB::table('quotations')->delete();
        DB::table('quotation_lines')->delete();
        DB::table('rental_bills')->delete();
        DB::table('receipts')->delete();

        dd('clearRentals');
    }

    function generateDrivingJobs()
    {
        // clear all test data
        DrivingJob::where('remark', 'mark_for_test')->forceDelete();

        // prepare data
        $rental_id = Rental::first()->id;
        $car_id = Car::first()->id;
        $driver = Driver::first();

        // rental
        $djf = new DrivingJobFactory(Rental::class, $rental_id, $car_id, [
            'self_drive_type' => SelfDriveTypeEnum::SEND,
            'start_date' => date('Y-m-d', strtotime('+1 days')),
            'end_date' => date('Y-m-d', strtotime('+3 days')),
            'origin' => null,
            'destination' => 'test destination',
            'driver_id' => $driver->id,
            'driver_name' => $driver->name,
            'remark' => 'mark_for_test',
        ]);
        $djf->create();

        $djf = new DrivingJobFactory(Rental::class, $rental_id, $car_id, [
            'self_drive_type' => SelfDriveTypeEnum::PICKUP,
            'start_date' => date('Y-m-d', strtotime('+1 days')),
            'end_date' => date('Y-m-d', strtotime('+3 days')),
            'origin' => 'test origin',
            'destination' => null,
            'driver_id' => $driver->id,
            'driver_name' => $driver->name,
            'remark' => 'mark_for_test',
        ]);
        $djf->create();

        $djf = new DrivingJobFactory(Rental::class, $rental_id, $car_id, [
            'self_drive_type' => SelfDriveTypeEnum::OTHER,
            'start_date' => date('Y-m-d', strtotime('+1 days')),
            'end_date' => date('Y-m-d', strtotime('+3 days')),
            'origin' => 'test origin',
            'destination' => 'test destination',
            'driver_id' => $driver->id,
            'driver_name' => $driver->name,
            'remark' => 'mark_for_test',
        ]);
        $djf->create();

        // other
        $djf = new DrivingJobFactory(DrivingJobTypeStatusEnum::OTHER, null, $car_id, [
            'self_drive_type' => SelfDriveTypeEnum::SEND,
            'start_date' => date('Y-m-d', strtotime('+1 days')),
            'end_date' => date('Y-m-d', strtotime('+3 days')),
            'origin' => null,
            'destination' => 'test destination',
            'driver_id' => $driver->id,
            'driver_name' => $driver->name,
            'remark' => 'mark_for_test',
        ]);
        $djf->create();

        $djf = new DrivingJobFactory(DrivingJobTypeStatusEnum::OTHER, null, $car_id, [
            'self_drive_type' => SelfDriveTypeEnum::PICKUP,
            'start_date' => date('Y-m-d', strtotime('+1 days')),
            'end_date' => date('Y-m-d', strtotime('+3 days')),
            'origin' => 'test origin',
            'destination' => null,
            'driver_id' => $driver->id,
            'driver_name' => $driver->name,
            'remark' => 'mark_for_test',
        ]);
        $djf->create();

        dd('generateDrivingJobs');
    }
}
