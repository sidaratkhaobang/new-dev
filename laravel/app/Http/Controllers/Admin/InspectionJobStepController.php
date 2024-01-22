<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImportCarLine;
use App\Models\CarParkTransfer;
use App\Models\Car;
use App\Models\CarStatus;
use App\Models\InspectionFlow;
use App\Models\InspectionStep;
use Illuminate\Support\Facades\DB;
use App\Models\InspectionForm;
use App\Models\InspectionJob;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Enums\TransferTypeEnum;
use App\Models\InspectionJobStep;
use App\Models\PurchaseOrder;
use App\Models\Rental;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Validation\Rule;

class InspectionJobStepController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarInspection);
        $check = null;
        if ($request->transfer_type == TransferTypeEnum::IN) {
            if ($request->is_need_customer_sign_in == STATUS_ACTIVE) {
                $check = true;
            }
        } else {
            if ($request->is_need_customer_sign_out == STATUS_ACTIVE) {
                $check = true;
            }
        }

        if ($check != null) {
            $check_signature = Media::where('model_id', $request->job_id)
                ->first();
            $step_form_status = InspectionJob::where('id', $request->job_id)
                ->first();

            if ($request->inspection_must_date == null) {
                $validator = Validator::make($request->all(), [
                    'recipient_staff_name' => [
                        'required',
                        // Rule::when($step_form_status->recipient_staff_name == null && $request->recipient_staff_name != null, ['required']),
                    ],
                    'recipient_staff_tel' => [
                        'required', 'min:10',
                        // Rule::when($step_form_status->recipient_staff_tel == null && $request->recipient_staff_tel != null, ['required','min:10']),
                    ],
                    'signature' => [
                        Rule::when($check_signature == null, ['required']),
                    ],
                ], [], [

                    'recipient_staff_name' => __('inspection_cars.recipient_staff_name'),
                    'recipient_staff_tel' => __('inspection_cars.recipient_staff_tel'),
                    'signature' => __('inspection_cars.signature_customer'),
                ]);


                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }
            }

            $step_form_status->recipient_staff_name = $request->recipient_staff_name ? $request->recipient_staff_name : null;
            $step_form_status->recipient_staff_tel = $request->recipient_staff_tel ? $request->recipient_staff_tel : null;
            $step_form_status->save();
        }
        if (!empty($request->signature__pending_delete_ids)) {
            $pending_delete_ids = $request->signature__pending_delete_ids;
            $step_form_status->deleteMedia($pending_delete_ids);
        }

        if ($request->hasFile('signature')) {
            if ($request->file('signature')->isValid()) {
                $step_form_status->addMedia($request->file('signature'))->toMediaCollection('signature');
            }
            // }
        }
        $redirect_route = route('admin.inspection-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(InspectionJob $inspection_job_step)
    {
        $this->authorize(Actions::View . '_' . Resources::CarInspection);
        $creditor = PurchaseOrder::where('id', $inspection_job_step->item_id)->first();
        $rental = Rental::where('id', $inspection_job_step->item_id)->first();
        $step_form = $inspection_job_step->InspectionJobSteps;
        $form_detail = InspectionFlow::where('id', $inspection_job_step->inspection_flow_id)->select('inspection_flows.*')->first();
        $car = Car::leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_parts', 'car_parts.id', '=', 'car_classes.gear_id')
            ->leftjoin('car_tires', 'car_tires.id', '=', 'car_classes.car_tire_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftjoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftjoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            ->where('cars.id', $inspection_job_step->car_id)
            ->select(
                'cars.id as car_id',
                'cars.rental_type',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                'car_classes.full_name as car_class_name',
                'car_classes.engine_size',
                'car_colors.name as car_colors_name',
                'car_categories.name as car_categories_name',
                'car_parts.name as car_gear_name',
                'car_tires.name as car_tire_name',
                'car_classes.oil_type',
            )->first();

        // if ($car->license_plate) {
        //     $car_name = $car->license_plate;
        // } else if ($car->engine_no) {
        //     $car_name = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
        // } else if ($car->chassis_no) {
        //     $car_name = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
        // }

        if ($car->license_plate) {
            $car_name = $car->license_plate;
        } else {
            $car_name = '';
        }

        if ((count($inspection_job_step->getMedia('signature')) != 0)) {
            $signature = $inspection_job_step->getMedia('signature');
            $signature_get_media = get_medias_detail($signature);
            $signature_get_media = $signature_get_media[0];
            $signature = $signature[0];
        } else {
            $signature = [];
            $signature_get_media = [];
        }
        // $job_check_condition = InspectionJob::leftjoin('inspection_flows', 'inspection_flows.id', '=', 'inspection_jobs.inspection_flow_id')
        //     ->where('inspection_jobs.id', $inspection_job_step->id)
        //     ->first();
        $job_check_condition = InspectionJob::where('id', $inspection_job_step->id)->first();

        $car2 = Car::select('id', 'license_plate as name')->get();
        $car_type_list = InspectionFlow::select('id', 'name')->get();
        $inspection_type = InspectionFlow::select('id', 'name')->get();
        $rental_type_list = $this->getRentalType();

        $page_title = __('lang.view') . __('car_park_transfers.license_table');
        return view('admin.inspection-job-steps.form',  [
            'inspection_job' => $inspection_job_step,
            'd' => $car,
            'form_detail' => $form_detail,
            'list' => $step_form,
            'view' => true,
            'show' => true,
            'car' => $car2,
            'car_type_list' => $car_type_list,
            'inspection_type' => $inspection_type,
            'page_title' => $page_title,
            'rental_type_list' => $rental_type_list,
            'signature' => $signature,
            'signature_get_media' => $signature_get_media,
            'job_check_condition' => $job_check_condition,
            'creditor' => $creditor,
            'rental' => $rental,
            'car_name' => $car_name,
        ]);
    }

    public function edit(InspectionJob $inspection_job_step)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarInspection);
        $creditor = PurchaseOrder::where('id', $inspection_job_step->item_id)->first();
        $rental = Rental::where('id', $inspection_job_step->item_id)->first();
        $step_form = $inspection_job_step->InspectionJobSteps;
        $form_detail = InspectionFlow::where('id', $inspection_job_step->inspection_flow_id)->select('inspection_flows.*')->first();
        $car = Car::leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_parts', 'car_parts.id', '=', 'car_classes.gear_id')
            ->leftjoin('car_tires', 'car_tires.id', '=', 'car_classes.car_tire_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftjoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftjoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            ->where('cars.id', $inspection_job_step->car_id)
            ->select(
                'cars.id as car_id',
                'cars.rental_type',
                'cars.license_plate as license_plate',
                'cars.engine_no as engine_no',
                'cars.chassis_no as chassis_no',
                'car_classes.full_name as car_class_name',
                'car_classes.engine_size',
                'car_colors.name as car_colors_name',
                'car_categories.name as car_categories_name',
                'car_parts.name as car_gear_name',
                'car_tires.name as car_tire_name',
                'car_classes.oil_type',
            )->first();
        if ($car->license_plate) {
            $car_name = $car->license_plate;
        } else {
            $car_name = '';
        }

        if ((count($inspection_job_step->getMedia('signature')) != 0)) {
            $signature = $inspection_job_step->getMedia('signature');
            $signature_get_media = get_medias_detail($signature);
            $signature_get_media = $signature_get_media[0];
            $signature = $signature[0];
        } else {

            $signature = [];
            $signature_get_media = [];
        }

        // $job_check_condition = InspectionJob::leftjoin('inspection_flows', 'inspection_flows.id', '=', 'inspection_jobs.inspection_flow_id')
        //     ->where('inspection_jobs.id', $inspection_job_step->id)
        //     ->first();

        $job_check_condition = InspectionJob::where('id', $inspection_job_step->id)->first();

        $car2 = Car::select('id', 'license_plate as name')->get();
        $car_type_list = InspectionFlow::select('id', 'name')->get();
        $inspection_type = InspectionFlow::select('id', 'name')->get();
        $rental_type_list = $this->getRentalType();

        $page_title = __('lang.edit') . __('car_park_transfers.license_table');
        return view('admin.inspection-job-steps.form',  [
            'inspection_job' => $inspection_job_step,
            'd' => $car,
            'form_detail' => $form_detail,
            'list' => $step_form,
            'car' => $car2,
            'car_type_list' => $car_type_list,
            'inspection_type' => $inspection_type,
            'page_title' => $page_title,
            'rental_type_list' => $rental_type_list,
            'signature' => $signature,
            'signature_get_media' => $signature_get_media,
            'job_check_condition' => $job_check_condition,
            'creditor' => $creditor,
            'rental' => $rental,
            'car_name' => $car_name,
        ]);
    }

    public static function getRentalType()
    {
        $rental_type = collect([
            (object) [
                'id' => RentalTypeEnum::SHORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::SHORT),
                'value' => RentalTypeEnum::SHORT,
            ],
            (object) [
                'id' => RentalTypeEnum::LONG,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::LONG),
                'value' => RentalTypeEnum::LONG,
            ],
            (object) [
                'id' => RentalTypeEnum::REPLACEMENT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::REPLACEMENT),
                'value' => RentalTypeEnum::REPLACEMENT,
            ],
            (object) [
                'id' => RentalTypeEnum::TRANSPORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::TRANSPORT),
                'value' => RentalTypeEnum::TRANSPORT,
            ],
            (object) [
                'id' => RentalTypeEnum::OTHER,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::OTHER),
                'value' => RentalTypeEnum::OTHER,
            ],
        ]);
        return $rental_type;
    }
}
