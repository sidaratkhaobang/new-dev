<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\Resources;
use App\Enums\SpecStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LongTermRental;
use App\Models\LongTermRentalMonth;
use App\Models\LongTermRentalType;
use App\Traits\CustomerTrait;
use App\Traits\LongTermRentalTrait;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use App\Traits\HistoryTrait;

class LongTermRentalSpecAccessoryController extends Controller
{
    use RentalTrait, CustomerTrait, LongTermRentalTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpecsAccessory);
        $customer_id = $request->customer;
        $worksheet_id = $request->worksheet_no;
        $spec_status_id = $request->spec_status;
        $lt_rental_type = $request->lt_rental_type;

        $lists = LongTermRental::sortable(['created_at' => 'desc'])
            ->select(
                'lt_rentals.id',
                'lt_rentals.worksheet_no',
                'lt_rentals.spec_status',
                'lt_rentals.customer_name',
                'lt_rentals.offer_date',
                'lt_rentals.status',
                'lt_rental_types.name as rental_type'
            )
            ->leftJoin('lt_rental_types', 'lt_rental_types.id', '=', 'lt_rentals.lt_rental_type_id')
            ->where('lt_rentals.status', LongTermRentalStatusEnum::SPECIFICATION)
            ->where('lt_rentals.spec_status', SpecStatusEnum::ACCESSORY_CHECK)
            ->search($request->s, $request)->paginate(PER_PAGE);

        $spec_status_list = RentalTrait::getSpecStatusList();
        $customer_list = LongTermRental::select('id', 'customer_name as name')
        ->bySpecStatusAccessory()->get();

        $worksheet_list = LongTermRental::select('id', 'worksheet_no as name')
        ->bySpecStatusAccessory()->get();

        $lt_rental_type_list = LongTermRentalType::withTrashed()->get();
        return view('admin.long-term-rental-specs.accessory-index', [
            's' => $request->s,
            'lists' => $lists,
            'customer_id' => $customer_id,
            'customer_list' => $customer_list,
            'worksheet_list' => $worksheet_list,
            'worksheet_id' => $worksheet_id,
            'spec_status_id' => $spec_status_id,
            'spec_status_list' => $spec_status_list,
            'lt_rental_type' => $lt_rental_type,
            'lt_rental_type_list' => $lt_rental_type_list,
            'from_offer_date' => $request->from_offer_date,
            'to_offer_date' => $request->to_offer_date,
        ]);
    }

    public function edit(LongTermRental $rental, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpecsAccessory);
        $lt_rental_id =  $rental->id;
        $long_term_rental = LongTermRental::find($lt_rental_id);
        if (empty($long_term_rental)) {
            return redirect()->route('admin.long-term-rentals.index');
        }
        $tor_files = $long_term_rental->getMedia('tor_file');
        $tor_files = get_medias_detail($tor_files);

        $customer_code = null;
        if ($long_term_rental->customer_id) {
            $customer = Customer::find($long_term_rental->customer_id);
            $customer_code = $customer->customer_code;
            $long_term_rental->customer_type = $customer->customer_type;
        }
        $customer_type_list = CustomerTrait::getCustomerType();
        $lt_rental_type_list = $this->getRentalJobTypeList();
        $with_trash = true;
        $lt_rental_type_list = LongTermRentalTrait::getRentalJobTypeList($with_trash);

        $tor_line_list = $this->getRentalTorLinesFromRentalId($lt_rental_id);
        $tor_line_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $accessory_list = $this->getAccessoriesByTorLineId($item->id);
            $item->accessory_list = $accessory_list;
            return $item;
        });

        $approve_line = HistoryTrait::getHistory(LongTermRental::class, $rental->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(LongTermRental::class, $rental->id);

        } else {
            $approve_line_owner = null;
        }

        $month_list = LongTermRentalMonth::where('lt_rental_id', $long_term_rental->id)
            ->selectRaw('id, CONCAT(month, " เดือน") as name')
            ->get();
        $month = $month_list->pluck('id')->toArray();
        $redirect_route = route('admin.long-term-rental.specs.accessories.index');
        $page_title = __('lang.edit') . __('long_term_rentals.spec_equipment');
        return view('admin.long-term-rental-specs.form-old', [
            'd' => $long_term_rental,
            'tor_files' => $tor_files,
            'page_title' => $page_title,
            'redirect_route' => $redirect_route,
            'tor_line_list' => $tor_line_list,
            'lt_rental_type_list' => $lt_rental_type_list,
            'customer_type_list' => $customer_type_list,
            'customer_code' => $customer_code,
            'accessory_controller' => true,
            'lt_rental_id' => $lt_rental_id,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
            'month_list' => $month_list,
            'month' => $month,
        ]);
    }
}
