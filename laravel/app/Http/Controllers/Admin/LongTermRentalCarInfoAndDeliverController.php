<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\ContractEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\QuotationStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\ContractLines;
use App\Models\Contracts;
use App\Models\InspectionJob;
use App\Models\LongTermRental;
use App\Models\LongTermRentalPRCar;
use App\Models\LongTermRentalPRLine;
use App\Traits\ContractTrait;
use Illuminate\Http\Request;

class LongTermRentalCarInfoAndDeliverController extends Controller
{

    public function isAllowConfirmCreatePR($lt_rental)
    {
        $confirmable = false;
        if (
            in_array($lt_rental->status, [
                LongTermRentalStatusEnum::QUOTATION,
                LongTermRentalStatusEnum::COMPLETE,
                LongTermRentalStatusEnum::CANCEL
            ])
            && $lt_rental->quotation
            && in_array($lt_rental->quotation->status, [QuotationStatusEnum::CONFIRM])
        ) {
            $confirmable = true;
        }
        return $confirmable;
    }

    public function showCarInfoAndDeliver(LongTermRental $long_term_rental)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRental);
        $contract_line_car = ContractLines::join('contracts', 'contracts.id', 'contract_lines.contract_id')
            ->where('contracts.job_type', LongTermRental::class)
            ->where('contracts.job_id', $long_term_rental->id)
            ->pluck('contract_lines.car_id')->toArray();

        $lt_pr_line = LongTermRentalPRLine::where('lt_rental_id', $long_term_rental->id)->pluck('id')->toArray();
        $car_list = collect();
        $lt_pr_line_car = LongTermRentalPRCar::join('cars', 'cars.id', 'lt_rental_pr_lines_cars.car_id')
//            ->whereNotNull('cars.license_plate')
            ->whereIn('lt_rental_pr_line_id', $lt_pr_line)->get()
            ->map(function ($item) use ($long_term_rental, $car_list, $contract_line_car) {
                $contract_line = ContractLines::select([
                    'contracts.worksheet_no',
                    'contract_lines.contract_id',
                    'contract_lines.car_user',
                    'contract_lines.tell',
                    'contract_lines.pick_up_date',
                    'contract_lines.return_date'
                ])
                    ->join('contracts', 'contracts.id', 'contract_lines.contract_id')
                    ->where('contracts.job_type', LongTermRental::class)
                    ->where('contracts.job_id', $long_term_rental->id)
                    ->where('contract_lines.car_id', $item->car_id)
                    ->first();

                $item->contract_line = $contract_line;

                $dd = InspectionJob::where('item_type', LongTermRental::class)
                    ->where('item_id', $long_term_rental->id)
                    ->where('car_id', $item->car_id)
                    ->where('transfer_type', STATUS_INACTIVE)->first();
                $item->inspection_job_deliver = $dd;
                $item->inspection_job_receive = InspectionJob::where('item_type', LongTermRental::class)
                    ->where('item_id', $long_term_rental->id)
                    ->where('car_id', $item->car_id)
                    ->where('transfer_type', STATUS_ACTIVE)->first();

                $car = Car::find($item->car_id);
                $car_images_files = $car->getMedia('car_images');
                $car_images_files = get_medias_detail($car_images_files);
                $item->carImage = $car_images_files[0] ?? null;

                $tempCar = [
                    'id' => $item->car->id,
                    'name' => $item->car->license_plate . ' - ' . $item->car->chassis_no,
                ];

                if (!$car_list->where('id', $item->car->id)->first() && !in_array($item->car->id, $contract_line_car)) {
                    $car_list->push((object)$tempCar);
                }

                return $item;
            });

        $route_group = [
            'route_lt_rental' => route('admin.long-term-rentals.show', ['long_term_rental' => $long_term_rental]),
            'route_pr_line' => route('admin.long-term-rentals.pr-lines.show', ['long_term_rental' => $long_term_rental]),
            'route_car_contract' => route('admin.long-term-rentals.car-info-and-deliver.show', ['long_term_rental' => $long_term_rental])
        ];

        $allow_confirm = $this->isAllowConfirmCreatePR($long_term_rental);

        return view('admin.long-term-rental-car-info-and-deliver.view', [
            'd' => $long_term_rental,
            'route_group' => $route_group,
            'allow_confirm' => $allow_confirm,
            'lt_pr_line_car' => $lt_pr_line_car,
            'car_list' => $car_list,
        ]);
    }

    public function storeCarInfoAndDeliver(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRental);

        $long_term_rental = LongTermRental::find($request->lt_rental_id);
        if (isset($request->contract_start_date)) {
            $long_term_rental->contract_start_date = $request->contract_start_date;
        }
        if (isset($request->contract_end_date)) {
            $long_term_rental->contract_end_date = $request->contract_end_date;
        }
        $long_term_rental->date_delivery = $request->date_deliver;
        $long_term_rental->location_delivery = $request->location_deliver;
        $long_term_rental->name_user_receive = $request->user_receive;
        $long_term_rental->phone_user_receive = $request->phone_number;
        $long_term_rental->save();

        return $this->responseValidateSuccess(route('admin.long-term-rentals.car-info-and-deliver.show', ['long_term_rental' => $long_term_rental]));
    }

    public function createContractLongTermRental(Request $request)
    {
        $lt_rental = LongTermRental::find($request->lt_rental_id);
        if ($lt_rental) {
            $contract = new Contracts();
            $contract->worksheet_no = ContractTrait::getWorkSheetNumber();
            $contract->job_type = LongTermRental::class;
            $contract->job_id = $lt_rental->id;
            $contract->customer_id = $lt_rental->customer?->id;
            $contract->start_rent = ContractEnum::START_RENT_PICKUP_DATE;
            $contract->end_rent = ContractEnum::END_RENT_EXPIRE_DATE;
            $contract->status = ContractEnum::REQUEST_CONTRACT;
            $contract->save();

            foreach ($request->carList as $item) {
                $contract_list = new ContractLines();
                $contract_list->contract_id = $contract->id;
                $contract_list->car_id = $item['car_id'];
                $contract_list->car_user = $item['car_user'];
                $contract_list->tell = $item['car_tel'];
                $contract_list->pick_up_date = $item['pick_up_date'];
                $contract_list->expected_return_date = $item['return_date'];
                $contract_list->status = ContractEnum::REQUEST_CONTRACT;
                $contract_list->save();
            }
            return $this->responseValidateSuccess(route('admin.long-term-rentals.car-info-and-deliver.show', ['long_term_rental' => $lt_rental]));
        }
    }
}
