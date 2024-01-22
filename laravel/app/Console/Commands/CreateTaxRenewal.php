<?php

namespace App\Console\Commands;

use App\Enums\InsuranceCarStatusEnum;
use App\Enums\OwnershipTransferStatusEnum;
use App\Enums\RegisterSignTypeEnum;
use App\Enums\TaxRenewalStatusEnum;
use Illuminate\Console\Command;
use App\Models\CMI;
use App\Jobs\ExpiredCmiJob;
use App\Models\Car;
use App\Models\ChangeRegistration;
use App\Models\HirePurchase;
use App\Models\OwnershipTransfer;
use App\Models\TaxRenewal;
use Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;

class CreateTaxRenewal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tls:create_tax_renewal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $addDay = date('Y-m-d', strtotime("+90 days"));
        $minuteDay = date('Y-m-d', strtotime($addDay . " -7 days"));
        $cars = Car::leftjoin('registereds', 'registereds.car_id', '=', 'cars.id')
            ->whereDate('cars.car_tax_exp_date', '<=', $addDay)
            ->whereDate('cars.car_tax_exp_date', '>=', $minuteDay)
            ->select(
                'cars.id',
                'cars.license_plate',
                'cars.car_tax_exp_date',
                'cars.registered_date',
                'cars.oil_type',
                'registereds.registered_sign'
            )
            ->get();

        if ($cars) {
            foreach ($cars as $key => $item) {
                $tax_renewal_check = TaxRenewal::where('car_id', $item->id)
                    ->whereNotIn('status', [TaxRenewalStatusEnum::SUCCESS])
                    ->first();

                if (!$tax_renewal_check) {
                    $car_tax_exp_date = new DateTime($item->car_tax_exp_date);
                    $registered_date = new DateTime($item->registered_date);
                    $count_years = 0;
                    if ($car_tax_exp_date && $registered_date) {
                        $diff_date = $car_tax_exp_date->diff($registered_date);
                        $count_years = $diff_date->y;
                    }

                    $tax_renewal = new TaxRenewal();
                    $tax_renewal->worksheet_no = generate_worksheet_no(TaxRenewal::class, false);
                    $tax_renewal->car_id = $item->id;
                    $tax_renewal->license_plate = $item->license_plate;
                    $tax_renewal->is_check_inspection = $count_years > 7 ? true : false;
                    $tax_renewal->is_check_lpg_ngv = in_array($item->oil_type, ['LPG', 'NGV']) ? true : false;
                    $tax_renewal->is_check_blue_sign = strcmp($item->registered_sign, RegisterSignTypeEnum::BLUE_SIGN) === 0 ? true : false;
                    $tax_renewal->is_check_yellow_sign = strcmp($item->registered_sign, RegisterSignTypeEnum::YELLOW_SIGN) === 0 ? true : false;
                    $tax_renewal->is_check_green_sign = strcmp($item->registered_sign, RegisterSignTypeEnum::GREEN_SERVICE_SIGN) === 0 ? true : false;
                    $tax_renewal->status = TaxRenewalStatusEnum::PREPARE_DOCUMENT;
                    $tax_renewal->save();
                }
            }
        } else {
            Log::channel('sentry')->alert('create tax renewal failed', []);
        }
        return true;
    }
}
