<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\AccessoryTypeEnum;
use App\Enums\Actions;
use App\Enums\BorrowCarEnum;
use App\Enums\CarEnum;
use App\Enums\CarPartTypeEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\ImportCarLineStatusEnum;
use App\Enums\InspectionRemarkEnum;
use App\Enums\InspectionStatusEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\InstallEquipmentStatusEnum;
use App\Enums\LongTermRentalTypeAccessoryEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\Resources;
use App\Enums\TransferTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\BorrowCar;
use App\Models\Car;
use App\Models\CarAccessory;
use App\Models\CarPark;
use App\Models\CarStatus;
use App\Models\Driver;
use App\Models\IEInspectionInstallEquipment;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Models\InspectionFlow;
use App\Models\InspectionForm;
use App\Models\InspectionFormSection;
use App\Models\InspectionJob;
use App\Models\InspectionJobChecklist;
use App\Models\InspectionJobStep;
use App\Models\InspectionJobStepLog;
use App\Models\InstallEquipment;
use App\Models\InstallEquipmentInspection;
use App\Models\InstallEquipmentLine;
use App\Models\LongTermRental;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\PurchaseRequisitionLineAccessory;
use App\Models\Rental;
use App\Models\ReplacementCar;
use App\Models\User;
use App\Traits\InstallEquipmentTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use ReplacementCarStatusEnum;
use ReplacementTypeEnum;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class InspectionJobStepFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarInspection);
        $check_signature = Media::where('model_id', $request->job_step_id)
            ->first();
        $step_form_status = InspectionJobStep::where('id', $request->job_step_id)
            ->first();
        //        Car Picture Database Check
        $ListCarPictureName = [
            'front_car_images_out',
            'back_car_images_out',
            'right_car_images_out',
            'left_car_images_out',
            'top_car_images_out'
        ];
        //        Total Car Picture In Database
        $TotalCarPicture = Media::where('model_id', $request->job_step_id)
            ->wherein('collection_name', $ListCarPictureName)->count();
        if ($request->is_need_inspector_sign == STATUS_ACTIVE) {

            $validator = Validator::make($request->all(), [
                'inspector' => [
                    Rule::when($request->inspector == null && $request->inspector_fullname == null, ['required']),
                ],
                'inspector_fullname' => [
                    Rule::when($request->inspector == null && $request->inspector_fullname == null, ['required']),
                ],
                'remark_reason' => [
                    Rule::when($request->inspection_status == InspectionStatusEnum::NOT_PASS, ['required']),
                ],
                'remark' => [
                    Rule::when($request->inspection_status == InspectionStatusEnum::NOT_PASS, ['required']),
                ],
                'inspection_date' => 'required:date',
                'mileage' => ['required', 'string', 'max:10'],
                'inspection_status' => ['required',],
                // mileage
                // 'delivery_staff_name' => [
                //     'required',
                //     Rule::when($step_form_status->delivery_staff_name == null && $request->delivery_staff_name != null, ['required']),
                // ],

                'signature' => [
                    Rule::when($check_signature == null, ['required']),
                ],

            ], [], [
                'inspector' => __('inspection_cars.fullname_inspector'),
                'remark_reason' => __('inspection_cars.remark_reason'),
                'remark' => __('inspection_cars.reject_remark'),
                'delivery_staff_name' => __('inspection_cars.delivery_staff_name'),
                'signature' => __('inspection_cars.signature'),
                'inspection_date' => __('inspection_cars.inspection_date'),
                'mileage' => __('inspection_cars.mile_out'),
                'inspection_status' => __('inspection_cars.result_inspection'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            //          Validate Car Picture If Picture Require
            if ($step_form_status['is_need_images'] === 1) {
                $validator = Validator::make($request->all(), [
                    'front_car_images_out' => 'required_without_all:back_car_images_out,right_car_images_out,left_car_images_out,top_car_images_out|array',
                    'back_car_images_out' => 'required_without_all:front_car_images_out,right_car_images_out,left_car_images_out,top_car_images_out|array',
                    'right_car_images_out' => 'required_without_all:front_car_images_out,back_car_images_out,left_car_images_out,top_car_images_out|array',
                    'left_car_images_out' => 'required_without_all:front_car_images_out,back_car_images_out,right_car_images_out,top_car_images_out|array',
                    'top_car_images_out' => 'required_without_all:front_car_images_out,back_car_images_out,right_car_images_out,left_car_images_out|array',
                ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    if ($TotalCarPicture === 0) {
                        return $this->responseWithCode(false, __('inspection_cars.require_car_picture'), null, 422);
                    }
                }
            }
            //            Validate CheckList
            $validator = Validator::make($request->all(), [
                'data.*.radio' => 'required|in:0,1'
            ]);
            if ($validator->errors()->has('data.*.radio')) {
                return $this->responseWithCode(false, __('inspection_cars.require_checklist'), null, 422);
            }
        } else {
            $validator = Validator::make(
                $request->all(),
                [
                    'inspector' => [
                        Rule::when($request->inspector == null && $request->inspector_fullname == null, ['required']),
                    ],
                    'inspector_fullname' => [
                        Rule::when($request->inspector == null && $request->inspector_fullname == null, ['required']),
                    ],
                    'remark_reason' => [
                        Rule::when($request->inspection_status == InspectionStatusEnum::NOT_PASS, ['required']),
                    ],
                    'remark' => [
                        Rule::when($request->inspection_status == InspectionStatusEnum::NOT_PASS, ['required']),
                    ],
                    'inspection_date' => 'required:date',
                    'mileage' => ['required', 'string', 'max:10'],
                    'inspection_status' => ['required',],
                ],
                [],
                [
                    'inspector' => __('inspection_cars.fullname_inspector'),
                    'remark_reason' => __('inspection_cars.remark_reason'),
                    'remark' => __('inspection_cars.reject_remark'),
                    'inspection_date' => __('inspection_cars.inspection_date'),
                    'mileage' => __('inspection_cars.mile_out'),
                    'inspection_status' => __('inspection_cars.result_inspection'),
                ],
            );
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            //          Validate Car Picture If Picture Require
            if ($step_form_status['is_need_images'] === 1) {
                $validator = Validator::make($request->all(), [
                    'front_car_images_out' => 'required_without_all:back_car_images_out,right_car_images_out,left_car_images_out,top_car_images_out|array',
                    'back_car_images_out' => 'required_without_all:front_car_images_out,right_car_images_out,left_car_images_out,top_car_images_out|array',
                    'right_car_images_out' => 'required_without_all:front_car_images_out,back_car_images_out,left_car_images_out,top_car_images_out|array',
                    'left_car_images_out' => 'required_without_all:front_car_images_out,back_car_images_out,right_car_images_out,top_car_images_out|array',
                    'top_car_images_out' => 'required_without_all:front_car_images_out,back_car_images_out,right_car_images_out,left_car_images_out|array',
                ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    if ($TotalCarPicture === 0) {
                        return $this->responseWithCode(false, __('inspection_cars.require_car_picture'), null, 422);
                    }
                }
            }
            //          Validate CheckList
            $validator = Validator::make($request->all(), [
                'data.*.radio' => 'required|in:0,1'
            ]);
            if ($validator->errors()->has('data.*.radio')) {
                return $this->responseWithCode(false, __('inspection_cars.require_checklist'), null, 422);
            }
        }


        $inspection_job_check = InspectionJob::find($request->job_id);
        $car = Car::where('id', $inspection_job_check->car_id)->first();
        if ($inspection_job_check->inspection_type == InspectionTypeEnum::NEW_CAR) {
            if (strcmp($inspection_job_check->item_type, PurchaseOrder::class) === 0) {
                $import_car_check = ImportCar::where('po_id', $inspection_job_check->item_id)->first();
                if ($import_car_check) {
                    $import_car_line = ImportCarLine::where('import_car_id', $import_car_check->id)->where('id', $inspection_job_check->car_id)->first();
                }
                $import_car_line_check = ImportCarLine::where('import_car_id', $import_car_check->id)->first();
                $po_line_check = PurchaseOrderLine::find($import_car_line_check->po_line_id);
                $pr_line_accessory = PurchaseRequisitionLineAccessory::where('purchase_requisition_line_id', $po_line_check->pr_line_id)->where('type_accessories', LongTermRentalTypeAccessoryEnum::ADDITIONAL)->get();
                if (count($pr_line_accessory) > 0) {
                    $step_approve_management = new StepApproveManagement();
                    $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::EQUIPMENT_ORDER);
                    if (!$is_configured) {
                        return $this->responseWithCode(false, __('lang.config_approve_warning') . __('install_equipments.page_title'), null, 422);
                    }
                }
            }
        }

        // update editor in inspectionjob
        $inspection_job = InspectionJob::find($request->job_id);
        $inspection_job->inspection_department_id = $step_form_status->inspection_department_id;
        $inspection_job->transfer_reason = $step_form_status->transfer_reason;
        $inspection_job->inspection_date = Carbon::now();
        $inspection_job->save();

        if ($request->data != null) {
            foreach ($request->data as $index => $item) {
                $checklist = InspectionJobChecklist::find($index);
                if (isset($item['radio'])) {
                    $checklist->is_pass = $item['radio'];
                }
                $checklist->remark = $item['remark'];
                $checklist->save();
            }
        }

        $step_form_status->inspector_id = $request->inspector;
        $step_form_status->inspector_fullname = $request->inspector_fullname;
        $step_form_status->dpf_solution = $request->dpf_solution;
        $step_form_status->inspection_date = $request->inspection_date;
        $step_form_status->remark = $request->remark;
        $step_form_status->remark_reason = $request->remark_reason;
        if (isset($request->inspection_location)) {
            $step_form_status->inspection_location = $request->inspection_location;
        } else {
            $step_form_status->inspection_location = $request->inspection_location_text;
        }
        $step_form_status->oil_quantity = $request->oil_quantity;
        $mileage = floatval(str_replace(',', '', $request->mileage));
        $step_form_status->mileage = $mileage;
        $inspection_job = InspectionJob::find($request->job_id);
        if ($inspection_job) {
            $car = Car::find($inspection_job->car_id);
            if ($car) {
                $car->current_mileage = $mileage;
                $car->save();
            }
        }
        $step_form_status->delivery_staff_name = $request->delivery_staff_name;

        if (isset($request->inspection_status)) {
            $step_form_status->inspection_status = $request->inspection_status;
        }
        $step_form_status->save();

        if (isset($request->inspection_status)) {
            $inspection_job_step_log = InspectionJobStepLog::firstOrNew(['id' => $request->id]);
            $inspection_job_step_log->inspection_job_step_id = $step_form_status->id;
            $inspection_job_step_log->inspection_status = $step_form_status->inspection_status;
            $inspection_job_step_log->remark = $step_form_status->remark;
            $inspection_job_step_log->remark_reason = $step_form_status->remark_reason;
            $inspection_job_step_log->save();
        }

        if ($request->front_car_images_out__pending_delete_ids) {
            $pending_delete_ids = $request->front_car_images_out__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $step_form_status->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('front_car_images_out')) {
            foreach ($request->file('front_car_images_out') as $image) {
                if ($image->isValid()) {
                    $step_form_status->addMedia($image)->toMediaCollection('front_car_images_out');
                }
            }
        }

        if ($request->front_car_images_in__pending_delete_ids) {
            $pending_delete_ids = $request->front_car_images_in__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $step_form_status->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('front_car_images_in')) {
            foreach ($request->file('front_car_images_in') as $image) {
                if ($image->isValid()) {
                    $step_form_status->addMedia($image)->toMediaCollection('front_car_images_in');
                }
            }
        }

        if ($request->back_car_images_out__pending_delete_ids) {
            $pending_delete_ids = $request->back_car_images_out__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $step_form_status->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('back_car_images_out')) {
            foreach ($request->file('back_car_images_out') as $image) {
                if ($image->isValid()) {
                    $step_form_status->addMedia($image)->toMediaCollection('back_car_images_out');
                }
            }
        }

        if ($request->back_car_images_in__pending_delete_ids) {
            $pending_delete_ids = $request->back_car_images_in__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $step_form_status->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('back_car_images_in')) {
            foreach ($request->file('back_car_images_in') as $image) {
                if ($image->isValid()) {
                    $step_form_status->addMedia($image)->toMediaCollection('back_car_images_in');
                }
            }
        }

        if ($request->right_car_images_out__pending_delete_ids) {
            $pending_delete_ids = $request->right_car_images_out__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $step_form_status->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('right_car_images_out')) {
            foreach ($request->file('right_car_images_out') as $image) {
                if ($image->isValid()) {
                    $step_form_status->addMedia($image)->toMediaCollection('right_car_images_out');
                }
            }
        }

        if ($request->right_car_images_in__pending_delete_ids) {
            $pending_delete_ids = $request->right_car_images_in__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $step_form_status->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('right_car_images_in')) {
            foreach ($request->file('right_car_images_in') as $image) {
                if ($image->isValid()) {
                    $step_form_status->addMedia($image)->toMediaCollection('right_car_images_in');
                }
            }
        }


        if ($request->left_car_images_out__pending_delete_ids) {
            $pending_delete_ids = $request->left_car_images_out__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $step_form_status->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('left_car_images_out')) {
            foreach ($request->file('left_car_images_out') as $image) {
                if ($image->isValid()) {
                    $step_form_status->addMedia($image)->toMediaCollection('left_car_images_out');
                }
            }
        }

        if ($request->left_car_images_in__pending_delete_ids) {
            $pending_delete_ids = $request->left_car_images_in__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $step_form_status->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('left_car_images_in')) {
            foreach ($request->file('left_car_images_in') as $image) {
                if ($image->isValid()) {
                    $step_form_status->addMedia($image)->toMediaCollection('left_car_images_in');
                }
            }
        }


        if ($request->top_car_images_out__pending_delete_ids) {
            $pending_delete_ids = $request->top_car_images_out__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $step_form_status->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('top_car_images_out')) {
            foreach ($request->file('top_car_images_out') as $image) {
                if ($image->isValid()) {
                    $step_form_status->addMedia($image)->toMediaCollection('top_car_images_out');
                }
            }
        }

        if ($request->top_car_images_in__pending_delete_ids) {
            $pending_delete_ids = $request->top_car_images_in__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $step_form_status->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('top_car_images_in')) {
            foreach ($request->file('top_car_images_in') as $image) {
                if ($image->isValid()) {
                    $step_form_status->addMedia($image)->toMediaCollection('top_car_images_in');
                }
            }
        }


        if (!empty($request->signature__pending_delete_ids)) {
            $pending_delete_ids = $request->signature__pending_delete_ids;
            $step_form_status->deleteMedia($pending_delete_ids);
        }

        if ($request->hasFile('signature')) {
            if ($request->file('signature')->isValid()) {
                $step_form_status->addMedia($request->file('signature'))->toMediaCollection('signature');
            }
        }

        $checkAllPass = InspectionJobStep::where('inspection_job_id', $request->job_id)->get();
        $inspection_job = InspectionJob::find($request->job_id);
        if (count($checkAllPass) == count($checkAllPass->where('inspection_status', 'PASS'))) {
            $inspection_job->inspection_status = InspectionStatusEnum::PASS;
            $inspection_job->save();
            $car = Car::where('id', $inspection_job->car_id)->first();
            if ($inspection_job->inspection_type == InspectionTypeEnum::NEW_CAR) {
                if (strcmp($inspection_job->item_type, PurchaseOrder::class) === 0) {
                    $import_car = ImportCar::where('po_id', $inspection_job->item_id)->first();
                    if ($import_car) {
                        $import_car_line = ImportCarLine::where('import_car_id', $import_car->id)->where('id', $inspection_job->car_id)->first();
                        if ($import_car_line) {
                            $import_car_line->status_delivery = ImportCarLineStatusEnum::SUCCESS_DELIVERY;
                            $import_car_line->save();
                        }
                    }

                    // start create Install Equipment
                    $import_car_line = ImportCarLine::where('import_car_id', $import_car->id)->first();
                    $po_line = PurchaseOrderLine::find($import_car_line->po_line_id);
                    $pr_line_accessory = PurchaseRequisitionLineAccessory::where('purchase_requisition_line_id', $po_line->pr_line_id)->where('type_accessories', LongTermRentalTypeAccessoryEnum::ADDITIONAL)->get();
                    $accessory_arr = [];
                    if (count($pr_line_accessory) > 0) {
                        $car->status = CarEnum::EQUIPMENT; //รถติดตั้งอุปกรณ์
                        $car->start_date = Carbon::now();
                        $car->save();

                        foreach ($pr_line_accessory as $pr_line_acc) {
                            $accessory_data = [];
                            $accessory = Accessories::find($pr_line_acc->accessory_id);
                            $accessory_data['id'] = null;
                            $accessory_data['accessory_id'] = $pr_line_acc->accessory_id;
                            $accessory_data['supplier_id'] = $accessory->creditor_id;
                            $accessory_data['amount'] = $pr_line_acc->amount;
                            $accessory_data['price'] = $accessory->price;
                            $accessory_data['remark'] = $pr_line_acc->remark;

                            if ($accessory_data) {
                                $accessory_arr[] = $accessory_data;
                            }
                        }
                        $car_id = $car->id;
                        $po_id = $inspection_job->item_id;
                        $install_equipments = $accessory_arr;

                        // $created_install_equipment = InstallEquipmentTrait::createInstallEquipments($po_id, $car_id, $install_equipments, $remark = '', null);
                    } else {
                        $car->status = CarEnum::NEWCAR_PENDING; //กำลังเตรียมรถใหม่
                        $car->start_date = Carbon::now();
                        $car->save();
                    }

                    $car_id = $car->id;
                    $po_id = $inspection_job->item_id;
                    $install_equipments = $accessory_arr;

                    __log($install_equipments);
                    $created_install_equipment = InstallEquipmentTrait::createInstallEquipments($po_id, $car_id, $install_equipments, $remark = '', null);
                    // end Install

                }
            } else if ($inspection_job->inspection_type == InspectionTypeEnum::BORROWED) {
                $borrow_car = BorrowCar::find($inspection_job->item_id);
                if ($borrow_car) {
                    if (strcmp($inspection_job->transfer_type, STATUS_INACTIVE) === 0) {
                        $borrow_car->status = BorrowCarEnum::IN_PROCESS;
                    }
                    if (strcmp($inspection_job->transfer_type, STATUS_ACTIVE) === 0) {
                        $borrow_car->status = BorrowCarEnum::SUCCESS;
                    }
                }
                $borrow_car->save();
            }

            if (strcmp($inspection_job->inspection_type, InspectionTypeEnum::EQUIPMENT) === 0) {
                $this->updateInspectionTypeEquipment($inspection_job, true);
            }

            if (strcmp($inspection_job->item_type, ReplacementCar::class) === 0) {
                $this->updateReplacement($inspection_job);
            }
        } else {
            if ($request->remark_reason == InspectionRemarkEnum::CHANGE_CAR) {
                $inspection_job->inspection_status = InspectionStatusEnum::CANCEL;
                $rental = null;
                if ($inspection_job->item_type == Rental::class) {
                    $rental = Rental::find($inspection_job->item_id);
                    if ($rental) {
                        $rental->status = RentalStatusEnum::CHANGE;
                        $rental->save();
                    }
                }
                if ($inspection_job->item_type == PurchaseOrder::class) {
                    $rental = PurchaseOrder::find($inspection_job->item_id);
                }
                if ($inspection_job->item_type == LongTermRental::class) {
                    $rental = LongTermRental::find($inspection_job->item_id);
                }
                if ($rental) {
                    $inspection_job_other = InspectionJob::where('car_id', $inspection_job->car_id)->where('item_id', $rental->id)->get();
                    if ($inspection_job_other) {
                        foreach ($inspection_job_other as $item) {
                            $item->inspection_status = InspectionStatusEnum::CANCEL;
                            $item->save();
                            $inspection_job_step_other = InspectionJobStep::where('inspection_job_id', $item->id)->get();
                            if ($inspection_job_step_other) {
                                foreach ($inspection_job_step_other as $data) {
                                    $data->inspection_status = InspectionStatusEnum::NOT_PASS;
                                    $data->remark_reason = InspectionRemarkEnum::CHANGE_CAR;
                                    $data->save();
                                }
                            }
                        }
                    }
                }
            } else {
                $inspection_job->inspection_status = InspectionStatusEnum::DRAFT;
            }

            if (strcmp($inspection_job->inspection_type, InspectionTypeEnum::EQUIPMENT) === 0) {
                $this->updateInspectionTypeEquipment($inspection_job, false);
            }
            $inspection_job->save();
        }


        //        Check Have Accessory Gps


        $redirect_route = route('admin.inspection-job-steps.edit', $request->job_id);
        return $this->responseValidateSuccess($redirect_route);
    }

    public function updateInspectionTypeEquipment($inspection_job, $is_pass = true)
    {
        // job type out
        if (strcmp($inspection_job->item_type, InstallEquipment::class) === 0) {
            if (strcmp($inspection_job->transfer_type, TransferTypeEnum::OUT) === 0) {
                $install_equipment = InstallEquipment::find($inspection_job->item_id);
                if ($install_equipment) {
                    if ($is_pass) {
                        $install_equipment->status = InstallEquipmentStatusEnum::INSTALL_IN_PROCESS;
                        $install_equipment->start_date = Carbon::now();
                        $install_equipment->save();

                        $car = Car::find($install_equipment->car_id);
                        if ($car) {
                            $car->status = CarEnum::EQUIPMENT;
                            $car->save();
                        }
                    }
                }
            }
        }

        // job type in
        if (strcmp($inspection_job->item_type, InstallEquipmentInspection::class) === 0) {
            if (strcmp($inspection_job->transfer_type, TransferTypeEnum::IN) === 0) {
                $ie_inspection = InstallEquipmentInspection::find($inspection_job->item_id);
                if ($ie_inspection) {
                    $install_equipment_arr = IEInspectionInstallEquipment::where('ie_inspection_id', $ie_inspection->id)
                        ->pluck('install_equipment_id')->toArray();
                    if (sizeof($install_equipment_arr) > 0) {
                        if ($is_pass) {
                            foreach ($install_equipment_arr as $key => $install_equipment_id) {
                                $install_equipment = InstallEquipment::find($install_equipment_id);
                                if ($install_equipment) {
                                    $install_equipment->status = InstallEquipmentStatusEnum::COMPLETE;
                                    $install_equipment->save();
                                    $this->addAccessoriesToCars($install_equipment);
                                }
                            }
                        } else {
                            $updated_fail = InstallEquipmentTrait::updateInstallEquipmentStatus($install_equipment_arr, InstallEquipmentStatusEnum::INSPECT_FAIL);
                        }
                    }
                }
            }
        }
    }

    function addAccessoriesToCars($install_equipment)
    {
        $car_id = $install_equipment->car_id;
        $car = Car::find($car_id);
        if (!$car) {
            return false;
        }
        $install_equipment_lines = InstallEquipmentLine::where('install_equipment_id', $install_equipment->id)->get();
        $insert_data = [];
        $have_gps = false;
        $have_dvr = false;
        foreach ($install_equipment_lines as $key => $ie_line) {
            $insert_data[$key]['id'] = (string)Str::orderedUuid();
            $insert_data[$key]['car_id'] = $car_id;
            $insert_data[$key]['accessory_id'] = $ie_line->accessory_id;
            $insert_data[$key]['remark'] = $ie_line->remark;
            $insert_data[$key]['amount'] = $ie_line->amount;
            $insert_data[$key]['type_accessories'] = LongTermRentalTypeAccessoryEnum::ADDITIONAL;
            $insert_data[$key]['install_date'] = $install_equipment->end_date;

            $accessory = Accessories::find($ie_line->accessory_id);
            if ($accessory) {
                $have_gps = strcmp($accessory->accessory_type, AccessoryTypeEnum::GPS) == 0 ? true : $have_gps;
                $have_dvr = strcmp($accessory->accessory_type, AccessoryTypeEnum::DVR) == 0 ? true : $have_dvr;
            }
        }
        if ($have_gps) {
            $car->have_gps = 1;
            $car->save();
        }

        if ($have_dvr) {
            $car->have_dvr = 1;
            $car->save();
        }
        CarAccessory::insert($insert_data);
    }

    public function updateReplacement($inspection_job)
    {
        if (!$inspection_job) {
            return false;
        }

        $repalcement_car = ReplacementCar::find($inspection_job->id);
        if (!$repalcement_car) {
            return false;
        }
        if ($inspection_job->inspection_type == InspectionTypeEnum::REPLACEMENT) {
            $update_complete_list = [
                ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE,
                ReplacementTypeEnum::SEND_REPLACE,
                ReplacementTypeEnum::RECEIVE_REPLACE,
            ];
            $update_in_process_list = [ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN];
            if (in_array($repalcement_car->replacement_type, $update_complete_list)) {
                $repalcement_car->status = ReplacementCarStatusEnum::COMPLETE;
            }

            if (in_array($repalcement_car->replacement_type, $update_in_process_list)) {
                $repalcement_car->status = ReplacementCarStatusEnum::IN_PROCESS;
            }
            $repalcement_car->save();
        }
        if (in_array($inspection_job->inspection_type, [InspectionTypeEnum::ACCIDENT_RC, InspectionTypeEnum::MAINTENANCE_RC])) {
            $repalcement_car->status = ReplacementCarStatusEnum::COMPLETE;
            $repalcement_car->save();
        }

        if (in_array($inspection_job->inspection_type, [InspectionTypeEnum::ACCIDENT_DC, InspectionTypeEnum::MAINTENANCE_DC])) {
            if (strcmp($repalcement_car->replacement_type, ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE) === 0) {
                $repalcement_car->status = ReplacementCarStatusEnum::IN_PROCESS;
            }
            if (strcmp($repalcement_car->replacement_type, ReplacementTypeEnum::SEND_MAIN) === 0) {
                $repalcement_car->status = ReplacementCarStatusEnum::COMPLETE;
            }
            $repalcement_car->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(InspectionForm $inspection_job_step_form, Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarInspection);
        $job_id = $request->job_id;
        $job_step_id = $request->job_step_id;
        $list = InspectionFormSection::where('inspection_form_id', $inspection_job_step_form->id)->orderBy('seq', 'asc')->get();
        $list2 = $list->pluck('id')->toArray();
        $checklist = InspectionJobChecklist::leftjoin('inspection_job_steps', 'inspection_job_steps.id', '=', 'inspection_job_checklists.inspection_job_step_id')
            ->where('inspection_job_steps.id', $job_step_id)
            ->whereIn('inspection_form_section_id', $list2)->select('inspection_job_checklists.*', 'inspection_job_steps.inspection_job_id', 'inspection_job_steps.inspection_status')->get();

        $checklist->map(function ($item) {
            $item->is_pass = $item->is_pass;
            $item->remark = $item->remark;
            $item->id = $item->id;
            $item->name2 = $item->inspection_form_checklist_name;
            return $item;
        });
        $list->map(function ($item) use ($checklist, $job_id) {
            $checklist2 = $checklist->where('inspection_form_section_id', $item->id)->values();
            $item->subseq = $checklist2;
            $item->status_section = $item->status == STATUS_INACTIVE ? false : true;
            return $item;
        });

        $inspection_job_step_log = InspectionJobStepLog::where('inspection_job_step_id', $job_step_id)->get();
        $step_form_status = InspectionJobStep::where('id', $job_step_id)
            ->first();
        $inspection_job = InspectionJob::find($step_form_status->inspection_job_id);
        $car_park = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $inspection_job->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_parks.car_park_number', 'car_park_zones.code')->first();

        // $step_form_check_condition = InspectionJobStep::leftjoin('inspection_steps', 'inspection_steps.id', '=', 'inspection_job_steps.inspection_step_id')
        //     ->leftjoin('departments', 'departments.id', '=', 'inspection_job_steps.inspection_department_id')
        //     ->where('inspection_job_steps.id', $job_step_id)
        //     ->first();

        $step_form_check_condition = InspectionJobStep::where('inspection_job_steps.id', $job_step_id)
            ->leftjoin('departments', 'departments.id', '=', 'inspection_job_steps.inspection_department_id')->first();

        $front_image_files_out = $step_form_status->getMedia('front_car_images_out');
        $front_image_files_out = get_medias_detail($front_image_files_out);
        $front_image_files_in = $step_form_status->getMedia('front_car_images_in');
        $front_image_files_in = get_medias_detail($front_image_files_in);
        $back_image_files_out = $step_form_status->getMedia('back_car_images_out');
        $back_image_files_out = get_medias_detail($back_image_files_out);
        $back_image_files_in = $step_form_status->getMedia('back_car_images_in');
        $back_image_files_in = get_medias_detail($back_image_files_in);
        $right_image_files_out = $step_form_status->getMedia('right_car_images_out');
        $right_image_files_out = get_medias_detail($right_image_files_out);
        $right_image_files_in = $step_form_status->getMedia('right_car_images_in');
        $right_image_files_in = get_medias_detail($right_image_files_in);
        $left_image_files_out = $step_form_status->getMedia('left_car_images_out');
        $left_image_files_out = get_medias_detail($left_image_files_out);
        $left_image_files_in = $step_form_status->getMedia('left_car_images_in');
        $left_image_files_in = get_medias_detail($left_image_files_in);
        $top_image_files_out = $step_form_status->getMedia('top_car_images_out');
        $top_image_files_out = get_medias_detail($top_image_files_out);
        $top_image_files_in = $step_form_status->getMedia('top_car_images_in');
        $top_image_files_in = get_medias_detail($top_image_files_in);
        $signature = $step_form_status->getMedia('signature');
        $signature = get_medias_detail($signature);
        if ((count($step_form_status->getMedia('signature')) != 0)) {
            $signature = $step_form_status->getMedia('signature');
            $signature_get_media = get_medias_detail($signature);
            $signature_get_media = $signature_get_media[0];
            $signature = $signature[0];
        } else {

            $signature = [];
            $signature_get_media = [];
        }

        $car2 = Car::select('id', 'license_plate as name')->get();
        $car_status = CarStatus::select('id', 'name')->get();
        $inspection_type = InspectionFlow::select('id', 'name')->get();
        $car_park_number = [];
        $car_park_zone = [];
        $front_car_images_files = [];
        if ($step_form_status->is_need_send_mobile == STATUS_ACTIVE) {
            $users_select = Driver::select('id', 'name')->get();
        } else {
            $users_select = User::select('id', 'name')->get();
        }
        $places_select = (object)[
            (object)[
                'id' => '1',
                'name' => 'ทรูลิซซิ่ง',
            ],
            (object)[
                'id' => '2',
                'name' => 'อื่นๆ',
            ]
        ];
        $page_title = __('lang.view') . __('car_park_transfers.license_table');
        return view('admin.inspection-job-step-forms.form', [
            'list' => $list,
            'car' => $car2,
            'view' => true,
            'car_status' => $car_status,
            'inspection_type' => $inspection_type,
            'car_park_number' => $car_park_number,
            'car_park_zone' => $car_park_zone,
            'page_title' => $page_title,
            'front_car_images_files' => $front_car_images_files,
            'job_id' => $job_id,
            'job_step_id' => $job_step_id,
            'step_form_status' => $step_form_status,
            'users_select' => $users_select,
            'places_select' => $places_select,
            'front_image_files_out' => $front_image_files_out,
            'front_image_files_in' => $front_image_files_in,
            'back_image_files_out' => $back_image_files_out,
            'back_image_files_in' => $back_image_files_in,
            'right_image_files_out' => $right_image_files_out,
            'right_image_files_in' => $right_image_files_in,
            'left_image_files_out' => $left_image_files_out,
            'left_image_files_in' => $left_image_files_in,
            'top_image_files_out' => $top_image_files_out,
            'top_image_files_in' => $top_image_files_in,
            'signature' => $signature,
            'signature_get_media' => $signature_get_media,
            'step_form_check_condition' => $step_form_check_condition,
            'd' => $job_step_id,
            'car_park' => $car_park,
            'inspection_job_step_log' => $inspection_job_step_log,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(InspectionForm $inspection_job_step_form, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarInspection);
        $job_id = $request->job_id;
        $job_step_id = $request->job_step_id;

        $list = InspectionFormSection::where('inspection_form_sections.inspection_form_id', $inspection_job_step_form->id)
            ->where('inspection_form_sections.status', STATUS_ACTIVE)
            ->orderBy('inspection_form_sections.seq', 'asc')->get();
        $list2 = $list->pluck('id')->toArray();
        $checklist = $this->getCheckList($job_step_id, $job_id, $list2, true);
        $list->map(function ($item) use ($checklist, $job_id, $job_step_id, $list2) {
            if ($item->seq == 9999) {
                $checklist = $this->getCheckList($job_step_id, $job_id, $list2, false);
            }
            $checklist2 = $checklist->where('inspection_form_section_id', $item->id)->values();
            $item->subseq = [];

            if (sizeof($checklist2) > 0) {
                $item->subseq = $checklist2;
                $item->status_section = $item->status == STATUS_INACTIVE ? false : true;
            }
            return $item;
        });

        $inspection_job_step_log = InspectionJobStepLog::where('inspection_job_step_id', $job_step_id)->get();

        $step_form_status = InspectionJobStep::where('id', $job_step_id)->first();
        $inspection_job = InspectionJob::find($step_form_status->inspection_job_id);
        $car_park = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $inspection_job->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_parks.car_park_number', 'car_park_zones.code')->first();

        // $step_form_check_condition = InspectionJobStep::leftjoin('inspection_steps', 'inspection_steps.id', '=', 'inspection_job_steps.inspection_step_id')
        //     ->leftjoin('inspection_forms', 'inspection_forms.id', '=', 'inspection_job_steps.inspection_form_id')
        //     ->leftjoin('departments', 'departments.id', '=', 'inspection_job_steps.inspection_department_id')
        //     ->where('inspection_job_steps.id', $job_step_id)
        //     ->first();

        $step_form_check_condition = InspectionJobStep::where('inspection_job_steps.id', $job_step_id)
            ->leftjoin('departments', 'departments.id', '=', 'inspection_job_steps.inspection_department_id')->first();

        $front_image_files_out = $step_form_status->getMedia('front_car_images_out');
        $front_image_files_out = get_medias_detail($front_image_files_out);
        $front_image_files_in = $step_form_status->getMedia('front_car_images_in');
        $front_image_files_in = get_medias_detail($front_image_files_in);
        $back_image_files_out = $step_form_status->getMedia('back_car_images_out');
        $back_image_files_out = get_medias_detail($back_image_files_out);
        $back_image_files_in = $step_form_status->getMedia('back_car_images_in');
        $back_image_files_in = get_medias_detail($back_image_files_in);
        $right_image_files_out = $step_form_status->getMedia('right_car_images_out');
        $right_image_files_out = get_medias_detail($right_image_files_out);
        $right_image_files_in = $step_form_status->getMedia('right_car_images_in');
        $right_image_files_in = get_medias_detail($right_image_files_in);
        $left_image_files_out = $step_form_status->getMedia('left_car_images_out');
        $left_image_files_out = get_medias_detail($left_image_files_out);
        $left_image_files_in = $step_form_status->getMedia('left_car_images_in');
        $left_image_files_in = get_medias_detail($left_image_files_in);
        $top_image_files_out = $step_form_status->getMedia('top_car_images_out');
        $top_image_files_out = get_medias_detail($top_image_files_out);
        $top_image_files_in = $step_form_status->getMedia('top_car_images_in');
        $top_image_files_in = get_medias_detail($top_image_files_in);

        if ((count($step_form_status->getMedia('signature')) != 0)) {
            $signature = $step_form_status->getMedia('signature');
            $signature_get_media = get_medias_detail($signature);
            $signature_get_media = $signature_get_media[0];
            $signature = $signature[0];
        } else {

            $signature = [];
            $signature_get_media = [];
        }

        $car_status = CarStatus::select('id', 'name')->get();
        $inspection_type = InspectionFlow::select('id', 'name')->get();
        $car_park_number = [];
        $car_park_zone = [];
        if ($step_form_status->is_need_send_mobile == STATUS_ACTIVE) {
            $users_select = Driver::select('id', 'name')->get();
        } else {
            $users_select = User::select('id', 'name')->get();
        }

        $places_select = (object)[
            (object)[
                'id' => '1',
                'name' => 'ทรูลิซซิ่ง',
            ],
            (object)[
                'id' => '2',
                'name' => 'อื่นๆ',
            ]
        ];
        $car_part_type_list = $this->getCarPartTypeList();
        $remark_reason = $this->getRemarkReason();
        $page_title = __('lang.edit') . __('car_park_transfers.license_table');

        return view('admin.inspection-job-step-forms.form', [
            'list' => $list,
            'car_status' => $car_status,
            'inspection_type' => $inspection_type,
            'car_park_number' => $car_park_number,
            'car_park_zone' => $car_park_zone,
            'page_title' => $page_title,
            'job_id' => $job_id,
            'job_step_id' => $job_step_id,
            'step_form_status' => $step_form_status,
            'users_select' => $users_select,
            'places_select' => $places_select,
            'front_image_files_out' => $front_image_files_out,
            'front_image_files_in' => $front_image_files_in,
            'back_image_files_out' => $back_image_files_out,
            'back_image_files_in' => $back_image_files_in,
            'right_image_files_out' => $right_image_files_out,
            'right_image_files_in' => $right_image_files_in,
            'left_image_files_out' => $left_image_files_out,
            'left_image_files_in' => $left_image_files_in,
            'top_image_files_out' => $top_image_files_out,
            'top_image_files_in' => $top_image_files_in,
            'signature' => $signature,
            'signature_get_media' => $signature_get_media,
            'step_form_check_condition' => $step_form_check_condition,
            'd' => $job_step_id,
            'car_park' => $car_park,
            'remark_reason' => $remark_reason,
            'inspection_job_step_log' => $inspection_job_step_log,
        ]);
    }

    public function getCheckList($job_step_id, $job_id, $list2, $is_active)
    {
        $checklist = InspectionJobChecklist::leftjoin('inspection_job_steps', 'inspection_job_steps.id', '=', 'inspection_job_checklists.inspection_job_step_id')
            // ->leftjoin('inspection_form_checklists', 'inspection_form_checklists.id', '=', 'inspection_job_checklists.inspection_form_checklist_id')
            // ->when($is_active, function ($q) {
            //     $q->where('inspection_form_checklists.status', STATUS_ACTIVE);
            // })
            ->where('inspection_job_steps.id', $job_step_id)
            ->where('inspection_job_steps.inspection_job_id', $job_id)
            ->whereIn('inspection_job_checklists.inspection_form_section_id', $list2)
            ->select('inspection_job_checklists.*', 'inspection_job_steps.inspection_job_id')->get();
        $checklist->map(function ($item) {
            $item->is_pass = $item->is_pass;
            $item->remark = $item->remark;
            $item->id = $item->id;
            $item->name2 = $item->inspection_form_checklist_name;
            return $item;
        });
        return $checklist;
    }

    public function getCarPartTypeList()
    {
        $car_parts = [
            CarPartTypeEnum::GEAR,
            CarPartTypeEnum::DRIVE_SYSTEM,
            CarPartTypeEnum::CAR_SEAT,
            CarPartTypeEnum::SIDE_MIRROR,
            CarPartTypeEnum::AIR_BAG,
            CarPartTypeEnum::CENTRAL_LOCK,
            CarPartTypeEnum::FRONT_BRAKE,
            CarPartTypeEnum::REAR_BRAKE,
            CarPartTypeEnum::ABS,
            CarPartTypeEnum::ANTI_THIFT_SYSTEM,
            CarPartTypeEnum::OTHER,
            CarPartTypeEnum::BATTERY,
            CarPartTypeEnum::TIRE,
            CarPartTypeEnum::WIPER,
        ];

        return collect($car_parts)->map(function ($item) {
            return [
                'id' => $item,
                'name' => __('car_part_types.name_' . $item),
                'value' => $item,
            ];
        });
    }

    public static function getRemarkReason()
    {
        $reason = collect([
            (object)[
                'id' => InspectionRemarkEnum::REPEAT_INSPECTION,
                'name' => __('inspection_cars.repeat_inspection'),
                'value' => InspectionRemarkEnum::REPEAT_INSPECTION,
            ],
            (object)[
                'id' => InspectionRemarkEnum::CHANGE_CAR,
                'name' => __('inspection_cars.change_car'),
                'value' => InspectionRemarkEnum::CHANGE_CAR,
            ],
            (object)[
                'id' => InspectionRemarkEnum::OTHER,
                'name' => __('inspection_cars.other'),
                'value' => InspectionRemarkEnum::OTHER,
            ],
        ]);
        return $reason;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function printPdf(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarInspection);
        $data = InspectionJobStep::leftjoin('inspection_forms', 'inspection_forms.id', '=', 'inspection_job_steps.inspection_form_id')
            ->where('inspection_job_steps.id', $request->inspection_job_step_form)->first();
        $inspector_type = $data->inspector_type;
        // $step_form_check_condition = InspectionJobStep::leftjoin('inspection_steps', 'inspection_steps.id', '=', 'inspection_job_steps.inspection_step_id')
        //     ->leftjoin('inspection_forms', 'inspection_forms.id', '=', 'inspection_job_steps.inspection_form_id')
        //     ->leftjoin('departments', 'departments.id', '=', 'inspection_job_steps.inspection_department_id')
        //     ->where('inspection_job_steps.id', $request->inspection_job_step_form)
        //     ->first();
        $step_form_check_condition = InspectionJobStep::where('inspection_job_steps.id', $request->inspection_job_step_form)
            ->leftjoin('departments', 'departments.id', '=', 'inspection_job_steps.inspection_department_id')->first();
        $step_form_status = InspectionJobStep::when($inspector_type == User::class, function ($query) use ($inspector_type) {
            $query->leftjoin('users', 'users.id', '=', 'inspection_job_steps.inspector_id');
        })
            ->when($inspector_type == Driver::class, function ($query) use ($inspector_type) {
                $query->leftjoin('drivers as users', 'users.id', '=', 'inspection_job_steps.inspector_id');
            })
            // leftjoin('users', 'users.id', '=', 'inspection_job_steps.inspector')
            ->leftjoin('inspection_jobs', 'inspection_jobs.id', '=', 'inspection_job_steps.inspection_job_id')
            ->where('inspection_job_steps.id', $request->inspection_job_step_form)
            ->where('inspection_job_steps.inspection_job_id', $step_form_check_condition->inspection_job_id)
            ->select(
                // 'users.name',
                'inspection_jobs.inspection_date',
                'inspection_job_steps.oil_quantity',
                'inspection_job_steps.mileage',
                'inspection_job_steps.inspection_status',
                'inspection_jobs.car_id',
                'inspection_job_steps.inspection_job_id',
                'inspection_jobs.item_id',
                'inspection_job_steps.id',
                'inspection_job_steps.inspector_fullname'
            )
            ->first();
        $step_form_image = InspectionJobStep::where('inspection_job_steps.id', $request->inspection_job_step_form)
            ->first();
        $front_image_files_out = $step_form_image->getMedia('front_car_images_out');
        $front_image_files_in = $step_form_image->getMedia('front_car_images_in');
        $back_image_files_out = $step_form_image->getMedia('back_car_images_out');
        $back_image_files_in = $step_form_image->getMedia('back_car_images_in');
        $right_image_files_out = $step_form_image->getMedia('right_car_images_out');
        $right_image_files_in = $step_form_image->getMedia('right_car_images_in');
        $left_image_files_out = $step_form_image->getMedia('left_car_images_out');
        $left_image_files_in = $step_form_image->getMedia('left_car_images_in');
        $top_image_files_out = $step_form_image->getMedia('top_car_images_out');
        $top_image_files_in = $step_form_image->getMedia('top_car_images_in');
        if ((count($step_form_image->getMedia('signature')) != 0)) {
            $signature = $step_form_image->getMedia('signature');
            $signature_get_media = $signature[0];
            $signature = $signature[0];
        } else {
            $signature = [];
            $signature_get_media = [];
        }

        $list = InspectionFormSection::where('inspection_form_sections.inspection_form_id', $step_form_check_condition->inspection_form_id)
            ->where('inspection_form_sections.status', STATUS_ACTIVE)
            ->orderBy('inspection_form_sections.seq', 'asc')->get();
        $list2 = $list->pluck('id')->toArray();
        $job_id = $step_form_check_condition->inspection_job_id;
        $job_step_id = $step_form_status->id;
        $checklist = $this->getCheckList($job_step_id, $job_id, $list2, true);
        $list->map(function ($item) use ($checklist, $job_id, $job_step_id, $list2) {
            if ($item->seq == 9999) {
                $checklist = $this->getCheckList($job_step_id, $job_id, $list2, false);
            }
            $checklist2 = $checklist->where('inspection_form_section_id', $item->id)->values();
            $item->subseq = [];

            if (sizeof($checklist2) > 0) {
                $item->subseq = $checklist2;
                $item->status_section = $item->status == STATUS_INACTIVE ? false : true;
            }
            return $item;
        });

        $car = Car::leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_parts', 'car_parts.id', '=', 'car_classes.gear_id')
            ->leftjoin('car_tires', 'car_tires.id', '=', 'car_classes.car_tire_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftjoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftjoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->leftjoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            ->where('cars.id', $step_form_status->car_id)
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
                'car_brands.name as car_brand_name',
            )->first();

        $creditor = PurchaseOrder::where('id', $step_form_status->item_id)->first();
        $print_type = ($request->type) ? $request->type : '';
        $page_title = __('lang.edit') . __('car_park_transfers.license_table');
        $pdf = PDF::loadView(
            'admin.inspection-job-step-forms.component-pdf.pdf',
            [
                'page_title' => $page_title,
                'print_type' => $print_type,
                'data' => $data,
                'front_image_files_out' => $front_image_files_out,
                'front_image_files_in' => $front_image_files_in,
                'back_image_files_out' => $back_image_files_out,
                'back_image_files_in' => $back_image_files_in,
                'right_image_files_out' => $right_image_files_out,
                'right_image_files_in' => $right_image_files_in,
                'left_image_files_out' => $left_image_files_out,
                'left_image_files_in' => $left_image_files_in,
                'top_image_files_out' => $top_image_files_out,
                'top_image_files_in' => $top_image_files_in,
                'signature' => $signature,
                'signature_get_media' => $signature_get_media,
                'step_form_check_condition' => $step_form_check_condition,
                'step_form_status' => $step_form_status,
                'list' => $list,
                'car' => $car,
                'creditor' => $creditor,
            ]
        );
        return $pdf->stream();

        // return view('admin.inspection-job-step-forms.component-pdf.pdf',
        // [
        // ]);
    }
}
