<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\InspectionRemarkEnum;
use App\Enums\OperationKeyAddressEnum;
use App\Enums\OperationKeyEnum;
use App\Enums\OperationTransferTypeEnum;
use App\Enums\ReceiptTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\Resources;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\TransferTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarParkTransfer;
use App\Models\Contracts;
use App\Models\Customer;
use App\Models\DrivingJob;
use App\Models\DrivingJobLog;
use App\Models\InspectionJob;
use App\Models\InspectionJobStep;
use App\Models\Receipt;
use App\Models\Rental;
use App\Models\RentalLine;
use App\Models\RentalProductAdditional;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Validation\Rule;
use App\Traits\RentalTrait;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Operation);
        $customer_name = null;
        $branch_id = $request->branch_id;
        $worksheet_id = $request->worksheet_id;
        $customer_id = $request->customer_id;
        $service_type_id = $request->service_type_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $status_id = $request->status;
        $lists = Rental::sortable(['worksheet_no' => 'desc'])
            ->leftJoin('branches', 'branches.id', '=', 'rentals.branch_id')
            ->leftJoin('service_types', 'service_types.id', '=', 'rentals.service_type_id')
            ->leftJoin('quotations', 'quotations.id', '=', 'rentals.quotation_id')
            ->whereNotIn('rentals.status', [RentalStatusEnum::DRAFT, RentalStatusEnum::PENDING, RentalStatusEnum::CANCEL])
            ->select('rentals.*', 'branches.name as branch_name', 'service_types.name as service_type_name', 'quotations.qt_no')
            ->search($request->s, $request)->paginate(PER_PAGE);
        $branch_lists = Branch::all();
        $worksheet_lists = Rental::select('id', 'worksheet_no as name')->get();
        $customer_lists = Rental::select('id', 'customer_name as name')->get();
        $service_type_lists = ServiceType::all();
        $model = Rental::class;
        $status_list = $this->getStatusOperationList();
        return view('admin.operations.index', [
            'customer_name' => $customer_name,
            'lists' => $lists,
            'branch_id' => $branch_id,
            'worksheet_id' => $worksheet_id,
            'customer_id' => $customer_id,
            'service_type_id' => $service_type_id,
            'service_type_lists' => $service_type_lists,
            'worksheet_lists' => $worksheet_lists,
            's' => $request->s,
            'branch_lists' => $branch_lists,
            'customer_lists' => $customer_lists,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'model' => $model,
            'status_list' => $status_list,
            'status_id' => $status_id,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Operation);
        $rental = Rental::find($request->rental_id);
        $rental->contract_no = $request->contract_no;
        $rental->receipt_no = $request->receipt_no;
        $rental->status = $request->status ? $request->status : $rental->status;
        $rental->save();

        $contract_file = $rental->getMedia('contract_file');
        $contract_file = get_medias_detail($contract_file);

        $receipt_file = $rental->getMedia('receipt_file');
        $receipt_file = get_medias_detail($receipt_file);


        $validator = Validator::make($request->all(), [
            'contract_no' => [
                'required', 'max:255',
            ],
            // 'receipt_no' => [
            //     'required', 'max:255',
            // ],
            'contract_file' => [
                Rule::when(empty($contract_file), ['required']),
            ],
            // 'receipt_file' => [
            //     Rule::when(empty($receipt_file), ['required']),
            // ],


        ], [], [
            'contract_no' => __('operations.contract_no'),
            // 'receipt_no' => __('operations.receipt_no'),
            'contract_file' => __('operations.contract_file'),
            // 'receipt_file' => __('operations.receipt_file'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        // File upload
        if ($request->contract_file__pending_delete_ids) {
            $pending_delete_ids = $request->contract_file__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $rental->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('contract_file')) {
            foreach ($request->file('contract_file') as $image) {
                if ($image->isValid()) {
                    $rental->addMedia($image)->toMediaCollection('contract_file');
                }
            }
        }

        if ($request->receipt_file__pending_delete_ids) {
            $pending_delete_ids = $request->receipt_file__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $rental->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('receipt_file')) {
            foreach ($request->file('receipt_file') as $image) {
                if ($image->isValid()) {
                    $rental->addMedia($image)->toMediaCollection('receipt_file');
                }
            }
        }
        $actual_prepare_date_temp = null;
        $actual_end_date_temp = null;
        $driving_job_id_send_temp = null;
        foreach ($request->data as $index => $data) {
            $driving_job = DrivingJob::find($data['driving_job_id']);
            if ($driving_job) {
                $driving_job->pick_up_keys = isset($data['key']) ? $data['key'] : null;
                // Estimate date
                $driving_job->estimate_start_date = isset($data['estimate_start_date']) ? $data['estimate_start_date'] : null;
                $driving_job->estimate_end_job_date = isset($data['estimate_end_job_date']) ? $data['estimate_end_job_date'] : null;
                $driving_job->estimate_arrive_date = isset($data['estimate_arrive_date']) ? $data['estimate_arrive_date'] : null;
                $driving_job->estimate_end_date = isset($data['estimate_end_date']) ? $data['estimate_end_date'] : null;
                $driving_job->estimate_prepare_date = isset($data['estimate_prepare_date']) ? $data['estimate_prepare_date'] : null;
                // Actual date
                $driving_job->actual_start_date = isset($data['actual_start_date']) ? $data['actual_start_date'] : null;
                $driving_job->actual_end_job_date = isset($data['actual_end_job_date']) ? $data['actual_end_job_date'] : null;
                $driving_job->actual_arrive_date = isset($data['actual_arrive_date']) ? $data['actual_arrive_date'] : null;
                // Selfdrive actual_end_date and actual_prepare_date save both sheet
                if ($data['self_drive_type'] != SelfDriveTypeEnum::SEND && $data['driving_job_type'] == DrivingJobTypeStatusEnum::MAIN_JOB) {
                    $driving_job->actual_end_date = isset($data['actual_end_date']) ? $data['actual_end_date'] : null;
                    $driving_job_id_send_temp_to_pickup = isset($driving_job_id_send_temp) ? $driving_job_id_send_temp : null;
                    $actual_end_date_temp = isset($data['actual_end_date']) ? $data['actual_end_date'] : null;
                    if(isset($data['actual_end_date'])){
                        $rental->status = RentalStatusEnum::SUCCESS;
                        $rental->save();
                    }
                }

                if ($data['self_drive_type'] != SelfDriveTypeEnum::SEND && $data['driving_job_type'] == DrivingJobTypeStatusEnum::SIDE_JOB) {
                    $driving_job->actual_end_date = isset($data['actual_end_date']) ? $data['actual_end_date'] : null;
                }

                if ($data['self_drive_type'] != SelfDriveTypeEnum::PICKUP && $data['driving_job_type'] == DrivingJobTypeStatusEnum::MAIN_JOB) { // SEND
                    $driving_job->actual_prepare_date = isset($data['actual_prepare_date']) ? $data['actual_prepare_date'] : null;
                } elseif ($data['self_drive_type'] == SelfDriveTypeEnum::PICKUP && $data['driving_job_type'] == DrivingJobTypeStatusEnum::MAIN_JOB) {
                    $driving_job->actual_prepare_date = isset($actual_prepare_date_temp) ? $actual_prepare_date_temp : null;
                }

                if ($data['self_drive_type'] != SelfDriveTypeEnum::PICKUP && $data['driving_job_type'] == DrivingJobTypeStatusEnum::SIDE_JOB) {
                    $driving_job->actual_prepare_date = isset($data['actual_prepare_date']) ? $data['actual_prepare_date'] : null;
                }

                $driving_job->alcohol_check = isset($data['alcohol']) ? $data['alcohol'] : null;
                $driving_job->alcohol = isset($data['alcohol_val']) ? $data['alcohol_val'] : null;
                if ($data['actual_prepare_date'] && (isset($data['driving_job_type']) && $data['driving_job_type'] == DrivingJobTypeStatusEnum::MAIN_JOB)) {
                    $actual_prepare_date_temp = isset($data['actual_prepare_date']) ? $data['actual_prepare_date'] : null;
                    $driving_job_id_send_temp = isset($data['driving_job_id']) ? $data['driving_job_id'] : null;
                }
                $driving_job->save();
            }

            //Change status rental form PAID to PREPARE
            if ($rental->status == RentalStatusEnum::PAID) {
                if (isset($data['self_drive_type']) && $data['self_drive_type'] == SelfDriveTypeEnum::SEND) {
                    //Selfdrive
                    $driving_job_change_status_rental = DrivingJob::where('job_id', $request->rental_id)->where('driving_job_type', DrivingJobTypeStatusEnum::MAIN_JOB)
                        ->where('self_drive_type', SelfDriveTypeEnum::SEND)->get();
                    if ($driving_job_change_status_rental) {
                        $date_not_null = $driving_job_change_status_rental->whereNotNull('actual_prepare_date');
                        if (count($driving_job_change_status_rental) == count($date_not_null)) {
                            $rental->status = RentalStatusEnum::PREPARE;
                            $rental->save();
                        }
                    }
                } else {
                    //Other
                    $driving_job_change_status_rental = DrivingJob::where('job_id', $request->rental_id)->where('driving_job_type', DrivingJobTypeStatusEnum::MAIN_JOB)->get();
                    if ($driving_job_change_status_rental) {
                        $date_not_null = $driving_job_change_status_rental->whereNotNull('actual_prepare_date');
                        if (count($driving_job_change_status_rental) == count($date_not_null)) {
                            $rental->status = RentalStatusEnum::PREPARE;
                            $rental->save();
                        }
                    }
                }
            }

            //Change status rental form AWAIT_RETURN to SUCCESS
            if ($rental->status == RentalStatusEnum::AWAIT_RETURN) {
                //Selfdrive
                if ($data['self_drive_type'] == SelfDriveTypeEnum::PICKUP) {
                    $driving_job_change_status_rental_return = DrivingJob::where('job_id', $request->rental_id)->where('driving_job_type', DrivingJobTypeStatusEnum::MAIN_JOB)
                        ->where('self_drive_type', SelfDriveTypeEnum::PICKUP)->get();
                    if ($driving_job_change_status_rental_return) {
                        $date_not_null_return = $driving_job_change_status_rental_return->whereNotNull('actual_end_date');
                        if (count($driving_job_change_status_rental_return) == count($date_not_null_return)) {
                            $rental->status = RentalStatusEnum::SUCCESS;
                            $rental->save();
                        }
                    }
                } else {
                    //Other
                    $driving_job_change_status_rental_return = DrivingJob::where('job_id', $request->rental_id)->where('driving_job_type', DrivingJobTypeStatusEnum::MAIN_JOB)
                        ->get();
                    if ($driving_job_change_status_rental_return) {
                        $date_not_null_return = $driving_job_change_status_rental_return->whereNotNull('actual_end_date');
                        if (count($driving_job_change_status_rental_return) == count($date_not_null_return)) {
                            $rental->status = RentalStatusEnum::SUCCESS;
                            $rental->save();
                        }
                    }
                }
            }

            if ($data['self_drive_type'] != SelfDriveTypeEnum::OTHER) {
                // Key address
                $car = Car::find($data['car_id']);
                if ($car) {
                    if ($index == 0) {
                        if ($car->keys_address == null) {
                            $car->keys_address = isset($data['key_address']) ? $data['key_address'] : null;
                            $car->save();
                        } else {
                            if ($car->keys_address != null && $car->keys_address != $data['key_address']) {
                                $car->keys_address = isset($data['key_address']) ? $data['key_address'] : null;
                                $car->save();
                            }
                        }
                    } elseif (isset($data['key_address']) && $car->keys_address != null && (strcmp($car->keys_address, $data['key_address'] != 0))) {
                        $car->keys_address = isset($data['key_address']) ? $data['key_address'] : null;
                        $car->save();
                    }
                }
            } else {
                $car = Car::find($data['car_id']);
                if ($car) {
                    $car->keys_address = isset($data['key_address']) ? $data['key_address'] : null;
                    $car->save();
                }
            }

            // Check product additionnal
            if (isset($data['product'])) {
                foreach ($data['product'] as $index2 => $data2) {
                    $product_add = RentalProductAdditional::find($index2);
                    if ($product_add) {
                        if ($data['self_drive_type'] != SelfDriveTypeEnum::PICKUP) {
                            $product_add->outbound_is_check = isset($data2['check_out']) ? STATUS_ACTIVE : STATUS_DEFAULT;
                        }
                        if ($data['self_drive_type'] != SelfDriveTypeEnum::SEND) {
                            $product_add->inbound_approve = isset($data2['check_in']) ? $data2['check_in'] : null;
                            if ($data2['check_in'] != STATUS_ACTIVE) {
                                $product_add->inbound_remark = isset($data2['inbound_remark']) ? $data2['inbound_remark'] : null;
                            } else {
                                $product_add->inbound_remark = null;
                            }
                        }
                        $product_add->save();
                    }
                }
            }
        }

        $driving_job_reverse = DrivingJob::find($driving_job_id_send_temp_to_pickup);
        if ($driving_job_reverse != null && (isset($data['driving_job_type']) && $data['driving_job_type'] == DrivingJobTypeStatusEnum::MAIN_JOB)) {
            $driving_job_reverse->actual_end_date = isset($actual_end_date_temp) ? $actual_end_date_temp : null;
            $driving_job_reverse->save();
        }

        $redirect_route = route('admin.operations.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function edit(Rental $operation)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Operation);

        if (in_array($operation->status, [RentalStatusEnum::DRAFT, RentalStatusEnum::PENDING, RentalStatusEnum::CANCEL])) {
            return redirect()->back();
        }

        $contract_file = $operation->getMedia('contract_file');
        $contract_file = get_medias_detail($contract_file);

        $receipt_file = $operation->getMedia('receipt_file');
        $receipt_file = get_medias_detail($receipt_file);

        $operation_new = Rental::leftjoin('branches', 'branches.id', '=', 'rentals.branch_id')
            ->leftjoin('products', 'products.id', '=', 'rentals.product_id')
            ->leftjoin('locations as origin', 'origin.id', '=', 'rentals.origin_id')
            ->leftjoin('locations as destination', 'destination.id', '=', 'rentals.destination_id')
            ->leftjoin('customers', 'customers.id', '=', 'rentals.customer_id')
            ->leftjoin('driving_jobs', 'driving_jobs.job_id', '=', 'rentals.id')
            ->leftjoin('cars', 'cars.id', '=', 'driving_jobs.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('drivers', 'drivers.id', '=', 'driving_jobs.driver_id')
            ->where('rentals.id', $operation->id)
            ->select(
                'rentals.worksheet_no',
                'rentals.status',
                'rentals.pickup_date',
                'rentals.return_date',
                'branches.name as branch_name',
                'products.name as product_name',
                'origin.name as origin_name',
                'destination.name as destination_name',
                'customers.customer_type',
                'customers.customer_code',
                'rentals.customer_name',
                'rentals.customer_email',
                'rentals.customer_tel',
                'rentals.customer_address',
                'rentals.id',
                'rentals.remark',
                'rentals.status',
                'drivers.name as driver_name',
                'driving_jobs.worksheet_no as dj_worksheet',
                'driving_jobs.id as driving_job_id',
                'driving_jobs.car_id',
                'driving_jobs.pick_up_keys',
                'driving_jobs.pick_up_keys',
                'driving_jobs.pick_up_keys',
                'cars.keys_address',
                'driving_jobs.alcohol_check',
                'driving_jobs.alcohol',
                'driving_jobs.actual_prepare_date',
                'driving_jobs.actual_start_date',
                'driving_jobs.actual_end_date',
                'driving_jobs.actual_end_job_date',
                'driving_jobs.actual_arrive_date',
                'driving_jobs.atk_check',
                'driving_jobs.driving_job_type',
                'driving_jobs.remark',
                'driving_jobs.self_drive_type',
                'cars.id as car_id',
                'cars.license_plate',
                'car_classes.name as class_name',
                'car_classes.full_name as class_full_name',
            )->orderBy('driving_jobs.self_drive_type', 'desc')->get();

        $operation_new->map(function ($item) {
            $car = Car::find($item->car_id);
            if ($car) {
                $car_image = $car->getMedia('car_images');
                $car_image = get_medias_detail($car_image);
                if (count($car_image) > 0) {
                    $item->car_image = $car_image[0]['url'];
                }
            }
            $driving_job = DrivingJobLog::where('driving_job_id', $item->driving_job_id)
                ->where('rental_id', $item->id)
                ->get();

            if ($driving_job != null) {
                $inspection_arr = [];
                if (!$driving_job->isEmpty()) {
                    foreach ($driving_job as $index_car => $data) {
                        $inspection = [];
                        $car_operation = Car::find($data->car_id);

                        if ($car_operation) {
                            $car_operation_img = $car_operation->getMedia('car_images');
                            $car_operation_img = get_medias_detail($car_operation_img);

                            if (count($car_operation_img) > 0) {
                                $inspection['car_image'] = $car_operation_img[0]['url'];
                            }
                            $inspection['license_plate'] = $car_operation->license_plate;
                            $inspection['car_class'] = $car_operation->carClass ? $car_operation->carClass->full_name : '-';
                            $inspection['license_plate'] = $car_operation->license_plate;
                            $inspection_job = InspectionJob::where('car_id', $data->car_id)->where('item_id', $data->rental_id)->first();
                            if (isset($inspection_job)) {
                                $inspection_job_step = InspectionJobStep::where('inspection_job_id', $inspection_job->id)->first();
                                $inspection['status'] = $inspection_job_step->inspection_status ? $inspection_job_step->inspection_status : '';
                                $inspection['remark_reason'] = $inspection_job_step->remark_reason ? $inspection_job_step->remark_reason : '';
                            }
                        }
                        if (count($inspection) > 0) {
                            $inspection_arr[] = $inspection;
                        }
                    }
                }

                $item->operation = $inspection_arr;
            } else {
                $item->operation = null;
            }

            // main car
            if ($item->self_drive_type == SelfDriveTypeEnum::PICKUP) {
                $inspection_job_main = InspectionJob::where('car_id', $item->car_id)->where('item_id', $item->id)->where('transfer_type', OperationTransferTypeEnum::PICKUP)->first();
            } else if ($item->self_drive_type == SelfDriveTypeEnum::SEND) {
                $inspection_job_main = InspectionJob::where('car_id', $item->car_id)->where('item_id', $item->id)->where('transfer_type', OperationTransferTypeEnum::SEND)->first();
            } else {
                $inspection_job_main = InspectionJob::where('car_id', $item->car_id)->where('item_id', $item->id)->orderBy('transfer_type', 'desc')->get();
            }

            if (!is_countable($inspection_job_main) && $inspection_job_main != null) {
                $inspection_job_step_main = InspectionJobStep::where('inspection_job_id', $inspection_job_main->id)->first();
                if ($inspection_job_step_main) {
                    $item->status_inspection = !is_null($inspection_job_step_main->inspection_status) && $inspection_job_step_main->inspection_status ? $inspection_job_step_main->inspection_status : '';
                    $item->remark_reason = !is_null($inspection_job_step_main->remark_reason) && $inspection_job_step_main->remark_reason ? $inspection_job_step_main->remark_reason : '';
                }
            } else if (is_countable($inspection_job_main)) {
                $status_arr = [];
                foreach ($inspection_job_main as $data2) {
                    $status = [];
                    $inspection_job_step_main = InspectionJobStep::where('inspection_job_id', $data2->id)->first();
                    $inspection_job_main = InspectionJob::find($inspection_job_step_main->inspection_job_id);
                    if ($inspection_job_main) {
                        $status['status_inspection'] = !is_null($inspection_job_step_main->inspection_status) && $inspection_job_step_main->inspection_status ? $inspection_job_step_main->inspection_status : '';
                        $status['status_inspection_job'] = !is_null($inspection_job_main->inspection_status) && $inspection_job_main->inspection_status ? $inspection_job_main->inspection_status : '';
                        $status['remark_reason'] = !is_null($inspection_job_step_main->remark_reason) && $inspection_job_step_main->remark_reason ? $inspection_job_step_main->remark_reason : '';
                        $status['transfer_type'] = !is_null($inspection_job_step_main->transfer_type) && $inspection_job_step_main->transfer_type ? $inspection_job_step_main->transfer_type : '';
                        if (count($status) > 0) {
                            $status_arr[] = $status;
                        }
                    }
                }
                $item->status_detail = $status_arr;
            }


            $product_additional = RentalProductAdditional::where('rental_id', $item->id)
                ->where('car_id', $item->car_id)->get();
            $item->product = $product_additional;

            $inspection_job = InspectionJob::where('car_id', $item->car_id)
                ->where('item_id', $item->id)->get();
            if ($inspection_job) {
                $inpection_job_list = [];
                foreach ($inspection_job as $data) {
                    $inspection = [];
                    $inspection['worksheet'] = $data->worksheet_no;
                    $inspection['transfer_type'] = $data->transfer_type;
                    $inspection['id'] = $data->id;
                    $inpection_job_list[] = $inspection;
                }
                $item->inspection_sheet = $inpection_job_list;
            }

            $car_park_transfer = CarParkTransfer::where('car_id', $item->car_id)
                ->where('driving_job_id', $item->driving_job_id)->first();
            if ($car_park_transfer) {
                $item->car_park_transfer_no = $car_park_transfer->worksheet_no ? $car_park_transfer->worksheet_no : null;
                $item->car_park_transfer_id = $car_park_transfer->id;
            }

            return $item;
        });
        $contract = Contracts::where('job_type', Rental::class)->where('job_id', $operation->id)->first();
        $receipt = Receipt::where('receipt_type', ReceiptTypeEnum::CAR_RENTAL)
            ->where('reference_type', Rental::class)
            ->where('reference_id', $operation->id)
            ->first();
        $rental_line = RentalLine::where('rental_id', $operation->id)->get();
        $rental_line = $rental_line->pluck('id')->toArray();

        $cars = Car::leftjoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->whereIn('rental_lines.id', $rental_line)
            ->select(
                'cars.id',
                'cars.license_plate',
                'car_classes.name as class_name',
                'car_classes.full_name as class_full_name',
                'rental_lines.rental_id'
            )->get();

        // product additional rental
        $cars->map(function ($item) {
            $product_additional_main = RentalProductAdditional::where('rental_id', $item->rental_id)
                ->where('car_id', $item->id)->get();
            $product_arr = [];
            foreach ($product_additional_main as $product_add) {
                $product_list = [];
                $product_list['name'] = $product_add->name;
                $product_list['amount'] = $product_add->amount;
                $product_arr[] = $product_list;
            }
            $item->product_main = $product_arr;

            $car_operation_image = Car::find($item->id);
            $car_image_list = null;

            if ($car_operation_image) {
                $car_image = $car_operation_image->getMedia('car_images');
                $car_image = get_medias_detail($car_image);
            }
            $item->car_image_view = $car_image;

            return $item;
        });

        $service_type = ($operation->serviceType) ? $operation->serviceType->service_type : null;
        $allow_product_transport = RentalTrait::canAddProductTransport($service_type);
        $product_transport_list = null;
        $product_transport_return_list = null;
        if ($allow_product_transport) {
            $product_transport_list = RentalTrait::getRentalProductTransportList($operation->id, TransferTypeEnum::OUT);
            $product_transport_return_list = RentalTrait::getRentalProductTransportReturnList($operation->id, TransferTypeEnum::IN);

            $product_transport_list->map(function ($item) {
                // $item->product_files = null;
                $product_file_medias = $item->getMedia('product_file');
                $product_files = get_medias_detail($product_file_medias);
                $product_files = collect($product_files)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files = $product_files;
                return $item;
            });

            $product_transport_return_list->map(function ($item) {
                // $item->product_files = null;
                $product_file_medias = $item->getMedia('product_file_return');
                $product_files_return = get_medias_detail($product_file_medias);
                $product_files_return = collect($product_files_return)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files_return = $product_files_return;
                return $item;
            });
        }

        $key_lists = $this->getKeyList();
        $status_lists = $this->getStatusList();
        $key_address_lists = $this->getKeyAddressList();
        $list = Rental::select('id', 'customer_name as name')->get();
        $value = null;
        $page_title = __('lang.edit') . __('short_term_rentals.sheet');
        return view('admin.operations.view',  [
            'page_title' => $page_title,
            'operation' => $operation,
            'cars' => $cars,
            'contract_file' => $contract_file,
            'receipt_file' => $receipt_file,
            'list' => $list,
            'value' => $value,
            'key_lists' => $key_lists,
            'key_address_lists' => $key_address_lists,
            'operation_new' => $operation_new,
            'receipt_file' => $receipt_file,
            'status_lists' => $status_lists,
            'service_type' => $service_type,
            'product_transport_list' => $product_transport_list,
            'product_transport_return_list' => $product_transport_return_list,
            'contract' => $contract,
            'receipt' => $receipt
        ]);
    }

    public static function getKeyList()
    {
        $key_lists = collect([
            (object) [
                'id' => OperationKeyEnum::PICKUP,
                'name' => __('operations.pickup_key_' . OperationKeyEnum::PICKUP),
                'value' => OperationKeyEnum::PICKUP,
            ],
            (object) [
                'id' => OperationKeyEnum::RETURN,
                'name' => __('operations.return_key_' . OperationKeyEnum::RETURN),
                'value' => OperationKeyEnum::RETURN,
            ],
        ]);
        return $key_lists;
    }

    public static function getKeyAddressList()
    {
        $key_address_lists = collect([
            (object) [
                'id' => OperationKeyAddressEnum::DRIVER,
                'name' => __('operations.driver' . OperationKeyAddressEnum::DRIVER),
                'value' => OperationKeyAddressEnum::DRIVER,
            ],
            (object) [
                'id' => OperationKeyAddressEnum::OPERATION,
                'name' => __('operations.operation' . OperationKeyAddressEnum::OPERATION),
                'value' => OperationKeyAddressEnum::OPERATION,
            ],
        ]);
        return $key_address_lists;
    }

    public static function getStatusList()
    {
        $status_lists = collect([
            (object) [
                'id' => RentalStatusEnum::AWAIT_RECEIVE,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::AWAIT_RECEIVE),
                'value' => RentalStatusEnum::AWAIT_RECEIVE,
            ],
            (object) [
                'id' => RentalStatusEnum::ACTIVE,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::ACTIVE),
                'value' => RentalStatusEnum::ACTIVE,
            ],
            (object) [
                'id' => RentalStatusEnum::AWAIT_RETURN,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::AWAIT_RETURN),
                'value' => RentalStatusEnum::AWAIT_RETURN,
            ],
        ]);
        return $status_lists;
    }

    public function show(Rental $operation)
    {
        $this->authorize(Actions::View . '_' . Resources::Operation);
        if (in_array($operation->status, [RentalStatusEnum::DRAFT, RentalStatusEnum::PENDING, RentalStatusEnum::CANCEL])) {
            return redirect()->back();
        }
        $contract_file = $operation->getMedia('contract_file');
        $contract_file = get_medias_detail($contract_file);
        $receipt_file = $operation->getMedia('receipt_file');
        $receipt_file = get_medias_detail($receipt_file);

        $operation_new = Rental::leftjoin('branches', 'branches.id', '=', 'rentals.branch_id')
            ->leftjoin('products', 'products.id', '=', 'rentals.product_id')
            ->leftjoin('locations as origin', 'origin.id', '=', 'rentals.origin_id')
            ->leftjoin('locations as destination', 'destination.id', '=', 'rentals.destination_id')
            ->leftjoin('customers', 'customers.id', '=', 'rentals.customer_id')
            ->leftjoin('driving_jobs', 'driving_jobs.job_id', '=', 'rentals.id')
            ->leftjoin('cars', 'cars.id', '=', 'driving_jobs.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('drivers', 'drivers.id', '=', 'driving_jobs.driver_id')
            ->where('rentals.id', $operation->id)
            ->select(
                'rentals.worksheet_no',
                'rentals.status',
                'rentals.pickup_date',
                'rentals.return_date',
                'branches.name as branch_name',
                'products.name as product_name',
                'origin.name as origin_name',
                'destination.name as destination_name',
                'customers.customer_type',
                'customers.customer_code',
                'rentals.customer_name',
                'rentals.customer_email',
                'rentals.customer_tel',
                'rentals.customer_address',
                'rentals.id',
                'rentals.remark',
                'rentals.status',
                'drivers.name as driver_name',
                'driving_jobs.worksheet_no as dj_worksheet',
                'driving_jobs.id as driving_job_id',
                'driving_jobs.car_id',
                'driving_jobs.pick_up_keys',
                'driving_jobs.pick_up_keys',
                'driving_jobs.pick_up_keys',
                'cars.keys_address',
                'driving_jobs.alcohol_check',
                'driving_jobs.alcohol',
                'driving_jobs.actual_prepare_date',
                'driving_jobs.actual_start_date',
                'driving_jobs.actual_end_date',
                'driving_jobs.actual_end_job_date',
                'driving_jobs.actual_arrive_date',
                'driving_jobs.atk_check',
                'driving_jobs.driving_job_type',
                'driving_jobs.remark',
                'driving_jobs.self_drive_type',
                'cars.id as car_id',
                'cars.license_plate',
                'car_classes.name as class_name',
                'car_classes.full_name as class_full_name',
            )->orderBy('driving_jobs.self_drive_type', 'desc')->get();

        $operation_new->map(function ($item) {
            $car = Car::find($item->car_id);
            if ($car) {
                $car_image = $car->getMedia('car_images');
                $car_image = get_medias_detail($car_image);
                if (count($car_image) > 0) {
                    $item->car_image = $car_image[0]['url'];
                }
            }
            $driving_job = DrivingJobLog::where('driving_job_id', $item->driving_job_id)
                ->where('rental_id', $item->id)
                ->get();

            if ($driving_job != null) {
                $inspection_arr = [];
                if (!$driving_job->isEmpty()) {
                    foreach ($driving_job as $index_car => $data) {
                        $inspection = [];
                        $car_operation = Car::find($data->car_id);

                        if ($car_operation) {
                            $car_operation_img = $car_operation->getMedia('car_images');
                            $car_operation_img = get_medias_detail($car_operation_img);

                            if (count($car_operation_img) > 0) {
                                $inspection['car_image'] = $car_operation_img[0]['url'];
                            }
                            $inspection['license_plate'] = $car_operation->license_plate;
                            $inspection['car_class'] = $car_operation->carClass ? $car_operation->carClass->full_name : '-';
                            $inspection['license_plate'] = $car_operation->license_plate;
                            $inspection_job = InspectionJob::where('car_id', $data->car_id)->where('item_id', $data->rental_id)->first();
                            if (isset($inspection_job)) {
                                $inspection_job_step = InspectionJobStep::where('inspection_job_id', $inspection_job->id)->first();
                                $inspection['status'] = $inspection_job_step->inspection_status ? $inspection_job_step->inspection_status : '';
                                $inspection['remark_reason'] = $inspection_job_step->remark_reason ? $inspection_job_step->remark_reason : '';
                            }
                        }
                        if (count($inspection) > 0) {
                            $inspection_arr[] = $inspection;
                        }
                    }
                }

                $item->operation = $inspection_arr;
            } else {
                $item->operation = null;
            }

            // main car
            if ($item->self_drive_type == SelfDriveTypeEnum::PICKUP) {
                $inspection_job_main = InspectionJob::where('car_id', $item->car_id)->where('item_id', $item->id)->where('transfer_type', OperationTransferTypeEnum::PICKUP)->first();
            } elseif ($item->self_drive_type == SelfDriveTypeEnum::SEND) {
                $inspection_job_main = InspectionJob::where('car_id', $item->car_id)->where('item_id', $item->id)->where('transfer_type', OperationTransferTypeEnum::SEND)->first();
            } else {
                $inspection_job_main = InspectionJob::where('car_id', $item->car_id)->where('item_id', $item->id)->orderBy('transfer_type', 'desc')->get();
            }
            if (!is_countable($inspection_job_main) && $inspection_job_main != null) {
                $inspection_job_step_main = InspectionJobStep::where('inspection_job_id', $inspection_job_main->id)->first();
                if ($inspection_job_step_main) {
                    $item->status_inspection = !is_null($inspection_job_step_main->inspection_status) && $inspection_job_step_main->inspection_status ? $inspection_job_step_main->inspection_status : '';
                    $item->remark_reason = !is_null($inspection_job_step_main->remark_reason) && $inspection_job_step_main->remark_reason ? $inspection_job_step_main->remark_reason : '';
                }
            } else if (is_countable($inspection_job_main)) {
                $status_arr = [];
                foreach ($inspection_job_main as $data2) {
                    $status = [];
                    $inspection_job_step_main = InspectionJobStep::where('inspection_job_id', $data2->id)->first();
                    $inspection_job_main = InspectionJob::find($inspection_job_step_main->inspection_job_id);
                    if ($inspection_job_main) {
                        $status['status_inspection'] = !is_null($inspection_job_step_main->inspection_status) && $inspection_job_step_main->inspection_status ? $inspection_job_step_main->inspection_status : '';
                        $status['status_inspection_job'] = !is_null($inspection_job_main->inspection_status) && $inspection_job_main->inspection_status ? $inspection_job_main->inspection_status : '';
                        $status['remark_reason'] = !is_null($inspection_job_step_main->remark_reason) && $inspection_job_step_main->remark_reason ? $inspection_job_step_main->remark_reason : '';
                        $status['transfer_type'] = !is_null($inspection_job_step_main->transfer_type) && $inspection_job_step_main->transfer_type ? $inspection_job_step_main->transfer_type : '';
                        if (count($status) > 0) {
                            $status_arr[] = $status;
                        }
                    }
                }
                $item->status_detail = $status_arr;
            }
            $product_additional = RentalProductAdditional::where('rental_id', $item->id)
                ->where('car_id', $item->car_id)->get();
            if ($product_additional) {
                $item->product = $product_additional;
            }


            $inspection_job = InspectionJob::where('car_id', $item->car_id)
                ->where('item_id', $item->id)->get();
            if ($inspection_job) {
                $inpection_job_list = [];
                foreach ($inspection_job as $data) {
                    $inspection = [];
                    $inspection['worksheet'] = $data->worksheet_no;
                    $inspection['transfer_type'] = $data->transfer_type;
                    $inspection['id'] = $data->id;
                    $inpection_job_list[] = $inspection;
                }
                $item->inspection_sheet = $inpection_job_list;
            }

            $car_park_transfer = CarParkTransfer::where('car_id', $item->car_id)
                ->where('driving_job_id', $item->driving_job_id)->first();
            if ($car_park_transfer) {
                $item->car_park_transfer_no = $car_park_transfer->worksheet_no ? $car_park_transfer->worksheet_no : null;
                $item->car_park_transfer_id = $car_park_transfer->id;
            }

            return $item;
        });
        $contract = Contracts::where('job_type', Rental::class)->where('job_id', $operation->id)->first();
        $receipt = Receipt::where('receipt_type', ReceiptTypeEnum::CAR_RENTAL)
            ->where('reference_type', Rental::class)
            ->where('reference_id', $operation->id)
            ->first();
        $rental_line = RentalLine::where('rental_id', $operation->id)->get();
        if ($rental_line) {
            $rental_line = $rental_line->pluck('id')->toArray();

            $cars = Car::leftjoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
                ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
                ->whereIn('rental_lines.id', $rental_line)
                ->select(
                    'cars.id',
                    'cars.license_plate',
                    'car_classes.name as class_name',
                    'car_classes.full_name as class_full_name',
                    'rental_lines.rental_id'
                )->get();

            // product additional rental
            $cars->map(function ($item) {
                $product_additional_main = RentalProductAdditional::where('rental_id', $item->rental_id)
                    ->where('car_id', $item->id)->get();
                $product_arr = [];
                foreach ($product_additional_main as $product_add) {
                    $product_list = [];
                    $product_list['name'] = $product_add->name;
                    $product_list['amount'] = $product_add->amount;
                    $product_arr[] = $product_list;
                }
                $item->product_main = $product_arr;

                $car_operation_image = Car::find($item->id);

                if ($car_operation_image) {
                    $car_image = $car_operation_image->getMedia('car_images');
                    $car_image = get_medias_detail($car_image);
                }
                $item->car_image_view = $car_image;

                return $item;
            });
        }

        $service_type = ($operation->serviceType) ? $operation->serviceType->service_type : null;
        if($service_type){
        $allow_product_transport = RentalTrait::canAddProductTransport($service_type);
        }
        $product_transport_list = null;
        $product_transport_return_list = null;
        if (isset($allow_product_transport) && $allow_product_transport) {
            $product_transport_list = RentalTrait::getRentalProductTransportList($operation->id, TransferTypeEnum::OUT);
            $product_transport_return_list = RentalTrait::getRentalProductTransportReturnList($operation->id, TransferTypeEnum::IN);

            $product_transport_list->map(function ($item) {
                // $item->product_files = null;
                $product_file_medias = $item->getMedia('product_file');
                $product_files = get_medias_detail($product_file_medias);
                $product_files = collect($product_files)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files = $product_files;
                return $item;
            });

            $product_transport_return_list->map(function ($item) {
                // $item->product_files = null;
                $product_file_medias = $item->getMedia('product_file_return');
                $product_files_return = get_medias_detail($product_file_medias);
                $product_files_return = collect($product_files_return)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files_return = $product_files_return;
                return $item;
            });
        }

        $key_lists = $this->getKeyList();
        $status_lists = $this->getStatusList();
        $key_address_lists = $this->getKeyAddressList();
        $list = Rental::select('id', 'customer_name as name')->get();
        $value = null;
        $page_title = __('lang.view') . __('short_term_rentals.sheet');
        return view('admin.operations.view',  [
            'page_title' => $page_title,
            'operation' => $operation,
            'cars' => $cars,
            // 'car_image' => $car_image_list,
            'contract_file' => $contract_file,
            'receipt_file' => $receipt_file,
            'list' => $list,
            'value' => $value,
            'key_lists' => $key_lists,
            'key_address_lists' => $key_address_lists,
            'operation_new' => $operation_new,
            'receipt_file' => $receipt_file,
            'view' => true,
            'status_lists' => $status_lists,
            'service_type' => $service_type,
            'product_transport_list' => $product_transport_list,
            'product_transport_return_list' => $product_transport_return_list,
            'contract' => $contract,
            'receipt' => $receipt
        ]);
    }

    public function getStatusOperationList()
    {
        return collect([
            // (object) [
            //     'id' => RentalStatusEnum::DRAFT,
            //     'value' => RentalStatusEnum::DRAFT,
            //     'name' => __('short_term_rentals.status_' . RentalStatusEnum::DRAFT),
            // ],
            // (object) [
            //     'id' => RentalStatusEnum::PENDING,
            //     'value' => RentalStatusEnum::PENDING,
            //     'name' => __('short_term_rentals.status_' . RentalStatusEnum::PENDING),
            // ],
            (object) [
                'id' => RentalStatusEnum::PAID,
                'value' => RentalStatusEnum::PAID,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::PAID),
            ],
            (object) [
                'id' => RentalStatusEnum::PREPARE,
                'value' => RentalStatusEnum::PREPARE,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::PREPARE),
            ],
            (object) [
                'id' => RentalStatusEnum::AWAIT_RECEIVE,
                'value' => RentalStatusEnum::AWAIT_RECEIVE,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::AWAIT_RECEIVE),
            ],
            (object) [
                'id' => RentalStatusEnum::ACTIVE,
                'value' => RentalStatusEnum::ACTIVE,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::ACTIVE),
            ],
            (object) [
                'id' => RentalStatusEnum::AWAIT_RETURN,
                'value' => RentalStatusEnum::AWAIT_RETURN,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::AWAIT_RETURN),
            ],
            (object) [
                'id' => RentalStatusEnum::SUCCESS,
                'value' => RentalStatusEnum::SUCCESS,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::SUCCESS),
            ],
            // (object) [
            //     'id' => RentalStatusEnum::CANCEL,
            //     'value' => RentalStatusEnum::CANCEL,
            //     'name' => __('short_term_rentals.status_' . RentalStatusEnum::CANCEL),
            // ],
            (object) [
                'id' => RentalStatusEnum::TEMPORARY,
                'value' => RentalStatusEnum::TEMPORARY,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::TEMPORARY),
            ],
            (object) [
                'id' => RentalStatusEnum::REMARK,
                'value' => RentalStatusEnum::REMARK,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::REMARK),
            ],
            (object) [
                'id' => RentalStatusEnum::CHANGE,
                'value' => RentalStatusEnum::CHANGE,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::CHANGE),
            ],

        ]);
    }
}
