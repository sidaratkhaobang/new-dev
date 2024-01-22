<?php

namespace App\Factories;

use App\Models\CarParkTransfer;
use Illuminate\Support\Facades\DB;
use Exception;

class CarparkTransferFactory implements FactoryInterface
{
    public $driving_job_id;
    public $car_id;
    public $worksheet_no;
    public $branch_id;
    public $origin_branch_id;
    public $destination_branch_id;

    public function __construct($driving_job_id, $car_id, $optionals = [])
    {
        $this->worksheet_no = null;
        $this->driving_job_id = $driving_job_id;
        $this->car_id = $car_id;
        $this->branch_id = get_branch_id();
        $this->origin_branch_id = isset($optionals['origin_branch_id']) ? $optionals['origin_branch_id'] : null;
        $this->destination_branch_id = isset($optionals['destination_branch_id']) ? $optionals['destination_branch_id'] : null;
    }

    function generateWorkSheetNo()
    {
        $this->worksheet_no = generate_worksheet_no(CarParkTransfer::class, true);
    }

    function create()
    {
        $this->generateWorkSheetNo();
        $this->validate();

        $car_park_transfer = new CarParkTransfer();
        $car_park_transfer->worksheet_no = $this->worksheet_no;
        $car_park_transfer->driving_job_id = $this->driving_job_id;
        $car_park_transfer->car_id = $this->car_id;
        $car_park_transfer->branch_id = $this->branch_id;
        $car_park_transfer->origin_branch_id = $this->origin_branch_id;
        $car_park_transfer->destination_branch_id = $this->destination_branch_id;
        $car_park_transfer->save();
        return $car_park_transfer;
    }

    function validate()
    {
        if (empty($this->worksheet_no)) {
            throw new Exception('empty worksheet_no');
        }
    }
}
