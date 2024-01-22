<?php

namespace App\Http\Controllers\Admin;

use App\Classes\OrderManagement;
use App\Classes\Sap\SapProcess;
use App\Enums\Actions;
use App\Enums\OrderChannelEnum;
use App\Enums\OrderLineTypeEnum;
use App\Enums\ReceiptTypeEnum;
use App\Enums\ReceiptLineNameEnum;
use App\Enums\RentalBillTypeEnum;
use App\Enums\RentalStateEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\Resources;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\ServiceTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\GpsCheckSignal;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\RentalLine;
use App\Traits\RentalTrait;
use App\Traits\DayTrait;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Factories\DrivingJobFactory;
use App\Factories\CarparkTransferFactory;
use App\Factories\QuotationFactory;
use App\Factories\InspectionJobFactory;
use App\Factories\ReceiptFactory;
use App\Models\ProductAdditional;

class ShortTermRentalSummaryController extends Controller
{
    use RentalTrait, DayTrait;

    public function edit(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental = Rental::find($rental_id);
        if (empty($rental)) {
            return redirect()->route('admin.short-term-rentals.index');
        }

        // get lastest bill
        $rental_bill = RentalBill::where('rental_id', $rental_id)
            ->where('bill_type', RentalBillTypeEnum::PRIMARY)
            ->orderBy('created_at', 'desc')
            ->first();

        if (($rental_bill) && in_array($rental_bill->status, [RentalStatusEnum::PAID])) {
            abort(404);
        }

        $quotation = $rental->quotationPrimary;
        $show_regenerate_quickpay_link = false;
        if ((!empty($quotation)) && $this->isDateLessThan($quotation->payment_expire_date, date('Y-m-d H:i:s'))) {
            $show_regenerate_quickpay_link = true;
        }

        $ref_files = [];
        if ($rental) {
            $ref_files = $rental->getMedia('ref_sheet_image');
            $ref_files = get_medias_detail($ref_files);
        }

        $payment_gateway_list = $this->getPaymentGateWayList();

        $om = new OrderManagement($rental);
        $om->calculate();
        $summary = $om->getSummary();

        $cars = RentalTrait::getRentalLineCarList($rental_id, true, true);

        $payment_gateway_name = null;
        $payment_status_name = null;

        $withholding_tax_list = RentalTrait::getWithHodingTaxList();
        $order_channel = $rental->order_channel;
        $btn_status = null;
        if (!in_array($rental->status, [RentalStatusEnum::DRAFT])) {
            $btn_status = true;
        }
        $page_title = __('lang.edit') . __('short_term_rentals.sheet');
        $invoice_type_list = RentalTrait::getTypeInvoice();
        $invoice_date_length = RentalTrait::getInvoiceDateLength();
        return view('admin.short-term-rental-summary.form', [
            'd' => $rental,
            'rental_id' => $rental->id,
            'rental_bill' => $rental_bill,
            'rental_bill_id' => $rental_bill ? $rental_bill->id : null,
            'summary' => $summary,
            'payment_gateway_list' => $payment_gateway_list,
            'payment_gateway_name' => $payment_gateway_name,
            'payment_status_name' => $payment_status_name,
            'cars' => $cars,
            'order_channel' => $order_channel,
            'withholding_tax_list' => $withholding_tax_list,
            'ref_files' => $ref_files,
            'page_title' => $page_title,
            'btn_status' => $btn_status,
            'quotation' => $quotation,
            'show_regenerate_quickpay_link' => $show_regenerate_quickpay_link,
            'invoice_type_list' => $invoice_type_list,
            'invoice_date_length' => $invoice_date_length,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $is_draft = boolval($request->is_draft);

        // default
        $validator = Validator::make($request->all(), [
            'withholding_tax_value' => 'required_if:active_tax,=,1',
        ], [], [
            'active_tax' => __('short_term_rentals.active_tax'),
            'withholding_tax_value' => __('short_term_rentals.withholding_tax_value'),
        ]);

        // for website channel
        if ((!$is_draft) && (strcmp($request->order_channel, OrderChannelEnum::WEBSITE) == 0)) {
            $validator = Validator::make($request->all(), [
                'payment_gateway' => 'required',
                'payment_date' => 'required|date',
            ], [], [
                'payment_gateway' => __('short_term_rentals.payment_method'),
                'payment_date' => __('short_term_rentals.paid_date'),
            ]);
        }

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $rental_id = $request->rental_id;
        $rental = Rental::find($rental_id);
        if (!$rental) {
            return $this->responseWithCode(false, __('lang.not_found'), null, 422);
        }
        $rental_bill = null;

        if (strcmp($request->order_channel, OrderChannelEnum::SMARTCAR) == 0) {
            $rental->rental_remark = $request->rental_remark;
            if (!$is_draft) {
                $rental->status = RentalStatusEnum::PENDING;
                $this->createOrUpdateQuotation($request, $rental);
            }
        } else if (strcmp($request->order_channel, OrderChannelEnum::WEBSITE) == 0) {
            $rental->payment_gateway = $request->payment_gateway;
            $rental->payment_date = date('Y-m-d', strtotime($request->payment_date));
            $rental->payment_remark = $request->payment_remark;
            $rental->rental_remark = $request->rental_remark;
            if (!$is_draft) {
                $rental->status = RentalStatusEnum::PAID;
            }
            $rental->save();
            $this->saveRefSheetImage($request, $rental);

            // create data when status change to PAID
            if ((!$is_draft) && (strcmp($request->status, RentalStatusEnum::DRAFT) == 0)) {
                // gen receipt manual
                //ReceiptTrait::generateReceipt($rental->id, ReceiptTypeEnum::CAR_RENTAL, $rental_bill->id);
                $receipt_type = $rental->getReceiptType();
                $rcf = new ReceiptFactory($receipt_type, $rental, $rental);
                if (strcmp($receipt_type, ReceiptTypeEnum::TAX_INVOICE) == 0) {
                    $rcf->customer = $rcf->formatCustomerObjectFromBilling($rental);
                }
                $rcf->createWithLine($rental->id, Rental::class, ReceiptLineNameEnum::CAR_RENTAL);
                /* if (!in_array($rental_bill->bill_type, [RentalBillTypeEnum::PRIMARY])) {
                    // sap after service
                    $sap = new SapProcess();
                    $sap->afterServiceInform($rental);
                } */

                $sap = new SapProcess();
                $sap->afterPaymentBeforeService($rental->id, Rental::class);
            }
        }

        // check all channel if total <= 0 (change to PAID auto)
        if (in_array($request->order_channel, [OrderChannelEnum::SMARTCAR, OrderChannelEnum::WEBSITE])) {
            if ((!$is_draft) && (strcmp($request->status, RentalStatusEnum::DRAFT) == 0)) {
                if ($rental->total <= 0) {
                    $rental->status = RentalStatusEnum::PAID;
                    $this->createAutoModel($rental);
                    ContractsController::createAutoContract($rental);  // auto create contract
                }
            }
        }

        $rental->rental_state = RentalStateEnum::SUMMARY;
        $rental->save();

        $redirect_route = route('admin.short-term-rentals.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    function saveRefSheetImage($request, $rental)
    {
        // upload file
        if ($request->ref_sheet_image__pending_delete_ids) {
            $pending_delete_ids = $request->ref_sheet_image__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $rental->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('ref_sheet_image')) {
            foreach ($request->file('ref_sheet_image') as $image) {
                if ($image->isValid()) {
                    $rental->addMedia($image)->toMediaCollection('ref_sheet_image');
                }
            }
        }
    }

    function createOrUpdateQuotation($request, $rental)
    {
        $qtf = new QuotationFactory(Rental::class, $rental->id, $rental, $rental, $rental->rentalLines, $rental->serviceTypeEnum);
        $quotation = $rental->quotationPrimary;

        if ($quotation) {
            $quotation = $qtf->update($quotation);
            RentalTrait::updateQuickpayUrl($rental, $quotation);
        } else {
            $quotation = $qtf->create();
            RentalTrait::generateQuickpayUrl($rental, $quotation);
        }
    }

    function updateRentalCar(Request $request)
    {
        $car_id = $request->car_id;

        // check rental
        $rental = Rental::find($request->rental_id);
        if (!$rental) {
            return response()->json([
                'success' => false
            ]);
        }

        // check car
        $check = RentalLine::where('rental_id', $request->rental_id)->where('item_type', Product::class)->where('car_id', $car_id)->exists();
        if (!$check) {
            return response()->json([
                'success' => false
            ]);
        }

        RentalLine::where('id', $request->rental_line_id)
            ->where('item_type', Product::class)
            ->where('rental_id', $request->rental_id)
            ->where('car_id', $car_id)
            ->update([
                'unit_price' => floatval($request->unit_price),
            ]);

        $om = new OrderManagement($rental);
        $om->calculate();
        $summary = $om->getSummary();
        $cars = RentalTrait::getRentalLineCarList($rental->id, true, true);
        return response()->json([
            'success' => true,
            'summary' => $summary,
            'cars' => $cars
        ]);
    }

    function updateRentalExtra(Request $request)
    {
        $cars_selected = is_array($request->cars_selected) ? $request->cars_selected : [];

        // check rental
        $rental = Rental::find($request->rental_id);
        if (!$rental) {
            return response()->json([
                'success' => false
            ]);
        }

        // check cars_selected in order
        $check = RentalLine::where('rental_id', $request->rental_id)->where('item_type', Product::class)->whereIn('car_id', $cars_selected)->count();
        if ($check != sizeof($cars_selected)) {
            return response()->json([
                'success' => false
            ]);
        }

        // delete ids
        $pending_delete_extra_product_ids = $request->pending_delete_extra_product_ids;
        if (is_array($pending_delete_extra_product_ids) && (sizeof($pending_delete_extra_product_ids) > 0)) {
            RentalLine::where('rental_id', $request->rental_id)
                ->whereIn('id', $pending_delete_extra_product_ids)
                ->whereIn('car_id', $cars_selected)
                ->forceDelete();
        }

        // add items
        $extras = $request->extras;
        foreach ($cars_selected as $car_id) {
            if (empty($car_id)) {
                continue;
            }
            foreach ($extras as $extra) {
                if (empty($extra['name'])) {
                    continue;
                }
                if (empty($extra['rental_line_id'])) {
                    // create new line
                    $amount = intval($extra['amount']);
                    $amount = (($amount >= 0) ? $amount : 0);

                    $unit_price = floatval($extra['unit_price']);
                    $unit_price = (($unit_price >= 0) ? $unit_price : 0);

                    $uuid = (string)Str::orderedUuid();
                    $optionals = [
                        'car_id' => $car_id,
                        'name' => $extra['name'],
                    ];
                    RentalTrait::saveRentalLine($rental, OrderLineTypeEnum::EXTRA, $uuid, $amount, $unit_price, $optionals);
                } else {
                    // edit exists line
                    RentalLine::where('id', $extra['rental_line_id'])
                        ->where('item_type', OrderLineTypeEnum::EXTRA)
                        ->where('rental_id', $request->rental_id)
                        ->whereIn('car_id', $cars_selected)
                        ->update([
                            'name' => $extra['name'],
                            'amount' => intval($extra['amount']),
                            'unit_price' => floatval($extra['unit_price']),
                        ]);
                }
            }
        }

        $om = new OrderManagement($rental);
        $om->calculate();
        $summary = $om->getSummary();
        $cars = RentalTrait::getRentalLineCarList($rental->id, true, true);
        return response()->json([
            'success' => true,
            'summary' => $summary,
            'cars' => $cars
        ]);
    }

    function updateRentalProductAdditional(Request $request)
    {
        $cars_selected = is_array($request->cars_selected) ? $request->cars_selected : [];

        // check rental
        $rental = Rental::find($request->rental_id);
        if (!$rental) {
            return response()->json([
                'success' => false
            ]);
        }

        // check cars_selected in order
        $check = RentalLine::where('rental_id', $request->rental_id)->where('item_type', Product::class)->whereIn('car_id', $cars_selected)->count();
        if ($check != sizeof($cars_selected)) {
            return response()->json([
                'success' => false
            ]);
        }

        // add items
        $product_additionals = $request->product_additionals;
        foreach ($cars_selected as $car_id) {
            if (empty($car_id)) {
                continue;
            }
            foreach ($product_additionals as $product_additional) {
                if (empty($product_additional['rental_line_id'])) {
                    continue;
                }
                RentalLine::where('id', $product_additional['rental_line_id'])
                    ->where('item_type', ProductAdditional::class)
                    ->where('rental_id', $request->rental_id)
                    ->whereIn('car_id', $cars_selected)
                    ->update([
                        'unit_price' => floatval($product_additional['unit_price']),
                    ]);
            }
        }

        $om = new OrderManagement($rental);
        $om->calculate();
        $summary = $om->getSummary();
        $cars = RentalTrait::getRentalLineCarList($rental->id, true, true);
        return response()->json([
            'success' => true,
            'summary' => $summary,
            'cars' => $cars
        ]);
    }

    function updateWithholdingTax(Request $request)
    {
        $rental = Rental::find($request->rental_id);
        if (!$rental) {
            return response()->json([
                'success' => false
            ]);
        }
        $is_withholding_tax = boolval($request->is_withholding_tax);
        $withholding_tax_value = $is_withholding_tax ? intval($request->withholding_tax_value) : 0;

        $rental->is_withholding_tax = $is_withholding_tax;
        $rental->withholding_tax_value = $withholding_tax_value;
        $rental->save();
        $om = new OrderManagement($rental);
        $om->calculate();
        $summary = $om->getSummary();
        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);
    }

    public static function createAutoModel($rental = null)
    {
        $origin_remark = null;
        $destination_remark = null;
        $must_check_date = new DateTime($rental->pickup_date);
        $must_check_date->modify('-1 day');

        if ($rental->origin_remark) {
            $origin_remark = "($rental->origin_remark)";
        }
        if ($rental->destination_remark) {
            $destination_remark = "($rental->destination_remark)";
        }

        $rental_cars = RentalTrait::getRentalLineCars($rental->id);
        if ($rental_cars) {
            $service_type_rental = $rental->serviceType?->service_type;
            $self_drive_types = [SelfDriveTypeEnum::SEND, SelfDriveTypeEnum::PICKUP];

            foreach ($rental_cars as $index => $rental_line) {
                if (strcmp($service_type_rental, ServiceTypeEnum::SELF_DRIVE) == 0) {
                    foreach ($self_drive_types as $self_drive_type) {
                        $driving_job_destination = null;
                        $driving_job_origin = null;
                        if (strcmp($self_drive_type, SelfDriveTypeEnum::SEND) == 0) {
                            $driving_job_destination = ($rental->origin || $rental->origin_remark) ? $rental->origin->name . $origin_remark : $rental->origin_name . $origin_remark;
                        } else if (strcmp($self_drive_type, SelfDriveTypeEnum::PICKUP) == 0) {
                            $driving_job_origin = ($rental->destination || $rental->destination_remark) ? $rental->destination->name . $destination_remark : $rental->destination_name . $destination_remark;
                        }
                        $djf = new DrivingJobFactory(Rental::class, $rental->id, $rental_line->car_id, [
                            'self_drive_type' => $self_drive_type,
                            'branch_id' => $rental->branch_id,
                            'destination' => $driving_job_destination,
                            'origin' => $driving_job_origin,
                        ]);
                        $driving_job = $djf->create();

                        $ctf = new CarparkTransferFactory($driving_job->id, $rental_line->car_id);
                        $ctf->create();
                    }
                } else {
                    $djf = new DrivingJobFactory(Rental::class, $rental->id, $rental_line->car_id, [
                        'self_drive_type' => SelfDriveTypeEnum::OTHER,
                        'branch_id' => $rental->branch_id,
                        'destination' => ($rental->destination || $rental->destination_remark) ? $rental->destination->name . '(' . $rental->destination_remark . ')' : $rental->destination_name . '(' . $rental->destination_remark . ')',
                        'origin' => ($rental->origin || $rental->origin_remark) ? $rental->origin->name . '(' . $rental->origin_remark . ')' : $rental->origin_name . '(' . $rental->origin_remark . ')',
                    ]);
                    $driving_job = $djf->create();

                    $ctf = new CarparkTransferFactory($driving_job->id, $rental_line->car_id);
                    $ctf->create();
                }

                $ijf = new InspectionJobFactory($service_type_rental, null, $rental->id, $rental_line->car_id, [
                    'inspection_must_date_out' => $rental_line->pickup_date,
                    'inspection_must_date_in' => $rental_line->return_date,
                ]);
                $ijf->create();

                $gps_check = new GpsCheckSignal();
                $gps_check->job_type = Rental::class;
                $gps_check->job_id = $rental->id;
                $gps_check->branch_id = $rental->branch_id;
                $gps_check->car_id = $rental_line->car_id;
                $gps_check->status = RentalStatusEnum::PENDING;
                $gps_check->must_check_date = $must_check_date;
                $gps_check->save();
            }
        }
        return true;
    }
}
