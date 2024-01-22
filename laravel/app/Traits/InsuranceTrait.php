<?php

namespace App\Traits;

use App\Enums\InsuranceCarStatusEnum;
use App\Enums\InsuranceTypeEnum;
use App\Enums\MITypeListEnum;
use App\Enums\RequestPremiumEnum;
use App\Models\CancelInsurance;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\CMI;
use App\Models\CustomerGroup;
use App\Models\CustomerGroupRelation;
use App\Models\InsuranceCompanies;
use App\Models\InsuranceLot;
use App\Models\Leasing;
use App\Models\LongTermRental;
use App\Models\LongTermRentalPRCar;
use App\Models\LongTermRentalPRLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Rental;
use App\Models\RequestPremium;
use App\Models\VMI;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use InsuranceStatusEnum;

trait InsuranceTrait
{

    static function getInsuranceLotNumber()
    {
        $insurance_lot_count = DB::table('insurance_lots')->count() + 1;
        $prefix = 'Lot-';
        $lot_no = generateRecordNumber($prefix, $insurance_lot_count);
        return $lot_no;
    }

    static function getTypeVMIList()
    {
        $vmi_arr = MITypeListEnum::VMI_LIST;
        $vmi_list = collect([]);
        foreach ($vmi_arr as $vmi) {
            $vmi_obj = (object)[
                'id' => $vmi,
                'value' => $vmi,
                'name' => $vmi,
            ];
            $vmi_list[] = $vmi_obj;
        }
        return $vmi_list;
    }

    static function getTypeCMIList()
    {
        $cmi_arr = MITypeListEnum::CMI_LIST;
        $cmi_list = collect([]);
        foreach ($cmi_arr as $cmi) {
            $cmi_obj = (object)[
                'id' => $cmi,
                'value' => $cmi,
                'name' => $cmi,
            ];
            $cmi_list[] = $cmi_obj;
        }
        return $cmi_list;
    }

    static function getCarClassInsranceList()
    {
        return CarClass::whereNotNull('model_insurance')
            ->select('model_insurance as id', 'model_insurance as name')
            ->get();
    }

    static function getInsurerList()
    {
        return InsuranceCompanies::where('status', STATUS_ACTIVE)
            ->select('id', 'insurance_name_th as name')
            ->get();
    }

    static function getInsuranceWorkSheetStatus()
    {
        return collect([
            (object)[
                'id' => InsuranceStatusEnum::PENDING,
                'value' => InsuranceStatusEnum::PENDING,
                'name' => __('cmi_cars.status_' . InsuranceStatusEnum::PENDING),
            ],
            (object)[
                'id' => InsuranceStatusEnum::IN_PROCESS,
                'value' => InsuranceStatusEnum::IN_PROCESS,
                'name' => __('cmi_cars.status_' . InsuranceStatusEnum::IN_PROCESS),
            ],
            (object)[
                'id' => InsuranceStatusEnum::COMPLETE,
                'value' => InsuranceStatusEnum::COMPLETE,
                'name' => __('cmi_cars.status_' . InsuranceStatusEnum::COMPLETE),
            ],
            (object)[
                'id' => InsuranceStatusEnum::CANCEL,
                'value' => InsuranceStatusEnum::CANCEL,
                'name' => __('cmi_cars.status_' . InsuranceStatusEnum::CANCEL),
            ],
        ]);
    }


    static function getCancelInsuranceStatus()
    {
        return collect([
            (object)[
                'id' => InsuranceCarStatusEnum::REQUEST_CANCEL,
                'value' => InsuranceCarStatusEnum::REQUEST_CANCEL,
                'name' => __('cmi_cars.status_' . InsuranceCarStatusEnum::REQUEST_CANCEL),
            ],
            (object)[
                'id' => InsuranceCarStatusEnum::CANCEL_POLICY,
                'value' => InsuranceCarStatusEnum::CANCEL_POLICY,
                'name' => __('cmi_cars.status_' . InsuranceCarStatusEnum::CANCEL_POLICY),
            ],
        ]);
    }

    static function getInsuranceTypeList()
    {
        return collect([
            (object)[
                'id' => InsuranceTypeEnum::FIRST,
                'value' => InsuranceTypeEnum::FIRST,
                'name' => __('vmi_cars.insurance_type_' . InsuranceTypeEnum::FIRST),
            ],
            (object)[
                'id' => InsuranceTypeEnum::SECOND,
                'value' => InsuranceTypeEnum::SECOND,
                'name' => __('vmi_cars.insurance_type_' . InsuranceTypeEnum::SECOND),
            ],
            (object)[
                'id' => InsuranceTypeEnum::THIRD,
                'value' => InsuranceTypeEnum::THIRD,
                'name' => __('vmi_cars.insurance_type_' . InsuranceTypeEnum::THIRD),
            ],
            (object)[
                'id' => InsuranceTypeEnum::FOURTH,
                'value' => InsuranceTypeEnum::FOURTH,
                'name' => __('vmi_cars.insurance_type_' . InsuranceTypeEnum::FOURTH),
            ],
        ]);
    }

    static function getPremiumPaymentStatus()
    {
        return collect([
            (object)[
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('cmi_cars.premium_status_' . STATUS_DEFAULT),
            ],
            (object)[
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('cmi_cars.premium_status_' . STATUS_ACTIVE),
            ],
        ]);
    }

    static function summaryPremium($premium = 0, $discount = 0, $stamp_duty = 0, $tax = 0)
    {
        $premium = floatval($premium);
        $discount = floatval($discount);
        $stamp_duty = floatval($stamp_duty);
        $tax = floatval($tax);
        $premium_total = floatval(0);
        $withholding_tax = floatval(0);
        $premium_total = $premium - $discount + $stamp_duty;
        $withholding_tax = (1 / 100) * $premium_total;

        return [
            'premium' => $premium ? floatval(number_format($premium, 2, '.', '')) : null,
            'discount' => $discount ? floatval(number_format($discount, 2, '.', '')) : null,
            'stamp_duty' => $stamp_duty ? floatval(number_format($stamp_duty, 2, '.', '')) : null,
            'tax' => $tax ? floatval(number_format($tax, 2, '.', '')) : null,
            'premium_total' => $premium_total ? floatval(number_format($premium_total, 2, '.', '')) : null,
            'withholding_tax' => $withholding_tax ? floatval(number_format($withholding_tax, 2, '.', '')) : null,

            'premium_text' => $premium ? number_format($premium, 2, '.', ',') : null,
            'discount_text' => $discount ? number_format($discount, 2, '.', ',') : null,
            'stamp_duty_text' => $stamp_duty ? number_format($stamp_duty, 2, '.', ',') : null,
            'tax_text' => $tax ? number_format($tax, 2, '.', ',') : null,
            'premium_total_text' => $premium_total ? number_format($premium_total, 2, '.', ',') : null,
            'withholding_tax_text' => $withholding_tax ? number_format($withholding_tax, 2, '.', ',') : null,
        ];
    }

    static function getLeasingList()
    {
        return Leasing::where('status', STATUS_ACTIVE)->get();
    }

    static function containsOnlyNull($input)
    {
        return empty(array_filter($input, function ($a) {
            return $a !== null;
        }));
    }

    static function validateDateTimeFormat($date_time)
    {
        try {
            $parsed_datetime = Carbon::createFromFormat('d/m/Y H:i:s', $date_time);
        } catch (Exception $e) {
            return false;
        }

        if ($parsed_datetime !== false && $parsed_datetime->format('d/m/Y H:i:s') === $date_time) {
            return true;
        }
        return false;
    }

    static function formatDateToDefault($date_time)
    {
        $carbon = Carbon::createFromFormat('d/m/Y H:i:s', $date_time);
        $formatted_date_time = $carbon->format('Y-m-d H:i:s');
        return $formatted_date_time;
    }

    static function getRentalDetail($model)
    {
        $rental = [];
        if (strcmp($model->job_type, PurchaseOrder::class) === 0) {
            $po = PurchaseOrder::find($model->job_id);
            if ($po) {
                $pr = PurchaseRequisition::find($po->pr_id);
                if ($pr) {
                    if (strcmp($pr->reference_type, LongTermRental::class) === 0) {
                        $rental['rental_type'] = LongTermRental::class;
                        $lt_rental = LongTermRental::find($pr->reference_id);
                        if ($lt_rental) {
                            $rental['customer_name'] = $lt_rental->customer_name;
                            $rental['customer_address'] = $lt_rental->customer_address;
                            $customer_id = $lt_rental->customer_id;
                            if ($customer_id) {
                                $customer_group_ids = CustomerGroupRelation::where('customer_id', $customer_id)->pluck('customer_group_id');
                                $customer_group = CustomerGroup::whereIn('id', $customer_group_ids)
                                    ->select(DB::raw("group_concat(name  SEPARATOR ', ')  as customer_group"))
                                    ->value('customer_group');
                                $rental['customer_group'] = $customer_group;
                            }
                            $car = Car::find($model->car_id);
                            if ($car) {
                                $lt_rental_pr_line_id = LongTermRentalPRCar::where('car_id', $car->id)->value('lt_rental_pr_line_id');
                                if ($lt_rental_pr_line_id) {
                                    $lt_rental_pr_line = LongTermRentalPRLine::find($lt_rental_pr_line_id);
                                    $rental['rental_duration'] = $lt_rental_pr_line?->ltMonth?->month . ' ' . __('lang.month');
                                }
                            }
                        }
                    }

                    if (strcmp($pr->reference_type, ShortTernRental::class) === 0) {
                        $rental['rental_type'] = ShortTernRental::class;
                        $rental['customer_name'] = 'บริษัท ทรู ลีสซิ่ง จำกัด (รถเช่าระยะสั้น)';
                        $short_term_rental = Rental::find($pr->reference_id);
                        if ($short_term_rental) {
                            $rental['customer_name'] = $short_term_rental->customer_name;
                            $rental['customer_address'] = $short_term_rental->customer_address;
                            $customer_id = $short_term_rental->customer_id;
                            if ($customer_id) {
                                $customer_group_ids = CustomerGroupRelation::where('customer_id', $customer_id)->pluck('customer_group_id');
                                $customer_group = CustomerGroup::whereIn('id', $customer_group_ids)
                                    ->select(DB::raw("group_concat(name  SEPARATOR ', ')  as customer_group"))
                                    ->value('customer_group');
                                $rental['customer_group'] = $customer_group;
                            }
                        }
                    }
                }
            }
        }
        return $rental;
    }

    static function createCancelInsurance($id, $type, $date = null, $reason = null)
    {
        if (!in_array($type, [CMI::class, VMI::class])) {
            return false;
        }
        $lot = new InsuranceLot();
        $insurance_lot_count = DB::table('insurance_lots')->count() + 1;
        $prefix = 'Lot-';
        $lot->lot_no = generateRecordNumber($prefix, $insurance_lot_count);
        $lot->save();

        $cancel_vmi_cmi_count = DB::table('cancel_vmi_cmis')->count() + 1;
        $prefix = null;
        if (strcmp($type, CMI::class) === 0) {
            $prefix = 'CCMI';
        }

        if (strcmp($type, VMI::class) === 0) {
            $prefix = 'CVMI';
        }
        $cancel_insurance = new CancelInsurance();
        $cancel_insurance->ref_type = $type;
        $cancel_insurance->ref_id = $id;
        $cancel_insurance->lot_id = $lot->id;
        $cancel_insurance->worksheet_no = generateRecordNumber($prefix, $cancel_vmi_cmi_count);
        $cancel_insurance->reason = $reason;
        $cancel_insurance->request_cancel_date = $date;
        $cancel_insurance->status = InsuranceCarStatusEnum::REQUEST_CANCEL;
        $cancel_insurance->save();
        return true;
    }

    static function createRequestPremium($idLongTermRental)
    {
        $statusCreate = false;
        $checkData = RequestPremium::where('job_id',$idLongTermRental)->count();
        if (!empty($idLongTermRental) && empty($checkData)) {
            $modelRequestpremium = new RequestPremium;
            $modelRequestpremium->job_id = $idLongTermRental;
            $modelRequestpremium->job_type = LongTermRental::class;
            $modelRequestpremium->status = RequestPremiumEnum::WAIT_PREMIUM;
            if ($modelRequestpremium->save()) {
                $statusCreate = true;
            }
        }
        return $statusCreate;
    }
}
