<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\Resources;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\TransferCarEnum;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarParkTransfer;
use App\Models\DrivingJob;
use App\Models\InspectionFlow;
use App\Models\InspectionJob;
use App\Models\InspectionStep;
use App\Models\TransferCar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\InspectionTrait;
use App\Factories\DrivingJobFactory;
use App\Factories\CarparkTransferFactory;
use App\Factories\InspectionJobFactory;

class TransferCarController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::TransferCar);
        $list = TransferCar::with('car')->sortable(['worksheet_no' => 'desc'])->branchFilter()->search($request)->paginate(PER_PAGE);
        $list->map(function ($item) {
            $item->can_edit = true;
            if (in_array($item->status, [TransferCarEnum::WAITING_RECEIVE, TransferCarEnum::IN_PROCESS, TransferCarEnum::SUCCESS])) {
                $item->can_edit = false;
            }


            return $item;
        });
        $page_title = __('transfer_cars.page_title');
        $transfer_car_report = true;
        $license_plate_list = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')->get();
        $license_plate_list->map(function ($item) {
            if ($item->license_plate) {
                $text = $item->license_plate;
            } else if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            }
            $item->id = $item->id;
            $item->name = $text;
            return $item;
        });
        $branch_lists = Branch::all();
        $status_lists = $this->getStatusTransferCarList();
        $car_id = $request->car_id;
        $status_id = $request->status;
        $from_branch_id = $request->from_branch_id;
        $to_branch_id = $request->to_branch_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;


        return view('admin.transfer-cars.index', [
            'list' => $list,
            's' => $request->s,
            'page_title' => $page_title,
            'transfer_car_report' => $transfer_car_report,
            'license_plate_list' => $license_plate_list,
            'car_id' => $car_id,
            'status_id' => $status_id,
            'from_branch_id' => $from_branch_id,
            'to_branch_id' => $to_branch_id,
            'branch_lists' => $branch_lists,
            'status_lists' => $status_lists,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::TransferCar);
        $d = new TransferCar();
        $d->request_date = date('Y-m-d');
        $optional_files = [];
        $branch_list = Branch::whereNotIn('id', [Auth::user()->branch_id])->select('name', 'id')->get();
        $car_lists = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->where('cars.branch_id', Auth::user()->branch_id)
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->name = $text;
                return $item;
            });
        $page_title = __('transfer_cars.add_new');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $url = 'admin.transfer-cars.index';
        return view('admin.transfer-cars.form',  [
            'd' => $d,
            'page_title' => $page_title,
            'optional_files' => $optional_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'url' => $url,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $transfer_car = TransferCar::firstOrNew(['id' => $request->id]);
        if (!isset($request->status_confirm)) { // สร้าง
            $validator = Validator::make($request->all(), [
                'car_id' => [
                    'required',
                ],
                'transfer_branch_id' => [
                    'required',
                ],

            ], [], [
                'car_id' => __('transfer_cars.license_plate_chassis'),
                'transfer_branch_id' => __('transfer_cars.branch_receive'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }

            $tc_count = TransferCar::where('branch_id', Auth::user()->branch_id)->count() + 1;
            $prefix = 'TC';
            if (!($transfer_car->exists)) {
                $transfer_car->worksheet_no = generateRecordNumber($prefix, $tc_count);
                $transfer_car->status = TransferCarEnum::WAITING_RECEIVE;
            } else {
                $transfer_car->status = $request->status;
            }
            $transfer_car->car_id = $request->car_id;
            $transfer_car->branch_id = Auth::user()->branch_id;
            $transfer_car->transfer_branch_id = $request->transfer_branch_id;
            $transfer_car->remark = $request->remark;


            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $transfer_car->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $image) {
                    if ($image->isValid()) {
                        $transfer_car->addMedia($image)->toMediaCollection('optional_files');
                    }
                }
            }
        } else if (isset($request->status_confirm) && strcmp($request->status_confirm, TransferCarEnum::CONFIRM_RECEIVE) === 0) { // อยู่ระหว่างการรับ/ส่งรถ
            $validator = Validator::make($request->all(), [
                'contact' => [
                    'required',
                ],
                'tel' => [
                    'required', 'numeric', 'digits:10',
                ],
                'is_driver' => [
                    'required',
                ],
                'place' => [Rule::when($request->is_driver == TransferCarEnum::CONFIRM_RECEIVE, ['required'])],
                'delivery_date' => [Rule::when($request->is_driver == TransferCarEnum::CONFIRM_RECEIVE, ['required'])],
            ], [], [
                'contact' => __('transfer_cars.contact'),
                'tel' => __('transfer_cars.tel'),
                'is_driver' => __('transfer_cars.is_need_driver'),
                'place' => __('transfer_cars.place'),
                'delivery_date' => __('transfer_cars.delivery_date'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            $transfer_car->is_driver = $request->is_driver == TransferCarEnum::CONFIRM_RECEIVE ? STATUS_ACTIVE : STATUS_INACTIVE;
            $transfer_car->place = $request->place;
            $transfer_car->contact = $request->contact;
            $transfer_car->tel = $request->tel;
            $transfer_car->delivery_date = $request->delivery_date;
            $transfer_car->status = TransferCarEnum::IN_PROCESS;
        } else if (isset($request->status_confirm) && strcmp($request->status_confirm, TransferCarEnum::IN_PROCESS) === 0) { // โอนรถสำเร็จ
            $validator = Validator::make($request->all(), [
                'pick_up_date' => [
                    'required',
                ],

            ], [], [
                'pick_up_date' => __('transfer_cars.pick_up_date'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            $transfer_car->pick_up_date = $request->pick_up_date;
            $transfer_car->pick_up_user_id = Auth::user()->id;
            $transfer_car->status = TransferCarEnum::SUCCESS;

            $car = Car::find($transfer_car->car_id);
            $car->branch_id = $transfer_car->transfer_branch_id;
            $car->save();
        } else if (strcmp($request->status_confirm, TransferCarEnum::REJECT_RECEIVE) === 0) {
            $validator = Validator::make($request->all(), [
                'transfer_branch_id' => [
                    'required',
                ],

            ], [], [
                'transfer_branch_id' => __('transfer_cars.branch_receive'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            $transfer_car->transfer_branch_id = $request->transfer_branch_id;
            $transfer_car->status = TransferCarEnum::WAITING_RECEIVE;
        }

        $transfer_car->save();


        if (strcmp($transfer_car->status, TransferCarEnum::IN_PROCESS) === 0) {
            // ใบงานคนขับรถ , ใบนำรถเข้าออก , ใบตรวจ
            $this->createAutoModel($transfer_car, $request->is_driver);
        }

        if (isset($request->status_confirm) && $request->status_confirm == TransferCarEnum::CONFIRM_RECEIVE) {
            $redirect_route = route('admin.transfer-cars.index');
        } elseif (isset($request->status_confirm) && ($request->status_confirm == TransferCarEnum::WAITING_RECEIVE || $request->status_confirm == TransferCarEnum::IN_PROCESS)) {
            $redirect_route = route('admin.transfer-car-receives.index');
        } else {
            $redirect_route = route('admin.transfer-cars.index');
        }

        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(TransferCar $transfer_car)
    {
        $this->authorize(Actions::View . '_' . Resources::TransferCar);
        $optional_files = [];
        $branch_list = Branch::select('name', 'id')->get();
        $car_lists = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->where('cars.branch_id', Auth::user()->branch_id)
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->name = $text;
                return $item;
            });
        $page_title = __('transfer_cars.view_tranfer');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $optional_files = $transfer_car->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $url = 'admin.transfer-cars.index';
        $driving_job = null;
        $car_park_transfer = null;
        $driving_job = DrivingJob::where('job_type', TransferCar::class)->where('job_id', $transfer_car->id)->first();
        if ($driving_job) {
            $car_park_transfer = CarParkTransfer::where('driving_job_id', $driving_job->id)->first();
        }
        $inspection_pickup = InspectionJob::where('item_type', TransferCar::class)->where('item_id', $transfer_car->id)->where('transfer_type', STATUS_INACTIVE)->first();
        $inspection_return = InspectionJob::where('item_type', TransferCar::class)->where('item_id', $transfer_car->id)->where('transfer_type', STATUS_ACTIVE)->first();

        if ($transfer_car->is_driver == STATUS_ACTIVE) {
            $transfer_car->is_driver = TransferCarEnum::CONFIRM_RECEIVE;
        } else {
            $transfer_car->is_driver = TransferCarEnum::REJECT_RECEIVE;
        }

        return view('admin.transfer-cars.form',  [
            'd' => $transfer_car,
            'page_title' => $page_title,
            'optional_files' => $optional_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'optional_files' => $optional_files,
            'view' => true,
            'url' => $url,
            'driving_job' => $driving_job,
            'car_park_transfer' => $car_park_transfer,
            'inspection_pickup' => $inspection_pickup,
            'inspection_return' => $inspection_return,
        ]);
    }

    public function edit(TransferCar $transfer_car)
    {
        $this->authorize(Actions::Manage . '_' . Resources::TransferCar);
        $optional_files = [];
        $branch_list = Branch::whereNotIn('id', [Auth::user()->branch_id])->select('name', 'id')->get();
        $car_lists = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->where('cars.branch_id', Auth::user()->branch_id)
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->name = $text;
                return $item;
            });
        $page_title = __('transfer_cars.edit_tranfer');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $optional_files = $transfer_car->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $status_confirm = $transfer_car->status;
        $url = 'admin.transfer-cars.index';
        $driving_job = null;
        $car_park_transfer = null;
        $driving_job = DrivingJob::where('job_type', TransferCar::class)->where('job_id', $transfer_car->id)->first();
        if ($driving_job) {
            $car_park_transfer = CarParkTransfer::where('driving_job_id', $driving_job->id)->first();
        }

        if ($transfer_car->is_driver == STATUS_ACTIVE) {
            $transfer_car->is_driver = TransferCarEnum::CONFIRM_RECEIVE;
        } else {
            $transfer_car->is_driver = TransferCarEnum::REJECT_RECEIVE;
        }

        $inspection_pickup = InspectionJob::where('item_type', TransferCar::class)->where('item_id', $transfer_car->id)->where('transfer_type', STATUS_INACTIVE)->first();
        $inspection_return = InspectionJob::where('item_type', TransferCar::class)->where('item_id', $transfer_car->id)->where('transfer_type', STATUS_ACTIVE)->first();

        return view('admin.transfer-cars.form',  [
            'd' => $transfer_car,
            'page_title' => $page_title,
            'optional_files' => $optional_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'optional_files' => $optional_files,
            'status_confirm' => $status_confirm,
            'url' => $url,
            'driving_job' => $driving_job,
            'car_park_transfer' => $car_park_transfer,
            'inspection_pickup' => $inspection_pickup,
            'inspection_return' => $inspection_return,
        ]);
    }

    private function getStatusList()
    {
        return collect([
            [
                'id' => TransferCarEnum::CONFIRM_RECEIVE,
                'value' => TransferCarEnum::CONFIRM_RECEIVE,
                'name' => __('transfer_cars.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => TransferCarEnum::REJECT_RECEIVE,
                'value' => TransferCarEnum::REJECT_RECEIVE,
                'name' => __('transfer_cars.status_' . STATUS_INACTIVE),
            ],
        ]);
    }

    private function getNeedDriverList()
    {
        return collect([
            [
                'id' => TransferCarEnum::CONFIRM_RECEIVE,
                'value' => TransferCarEnum::CONFIRM_RECEIVE,
                'name' => __('transfer_cars.need_driver_' . STATUS_ACTIVE),
            ],
            [
                'id' => TransferCarEnum::REJECT_RECEIVE,
                'value' => TransferCarEnum::REJECT_RECEIVE,
                'name' => __('transfer_cars.need_driver_' . STATUS_INACTIVE),
            ],
        ]);
    }

    public static function createAutoModel($transfer_car = null, $is_driver)
    {
        $transfer = TransferCar::find($transfer_car->id);
        if ($transfer) {
            $djf = new DrivingJobFactory(TransferCar::class, $transfer->id, $transfer->car_id, [
                'destination' => ($transfer->place) ? $transfer->place : null,
                'driver_name' => $is_driver == TransferCarEnum::CONFIRM_RECEIVE ? $transfer->contact : null,
            ]);
            $driving_job = $djf->create();

            $ctf = new CarparkTransferFactory($driving_job->id, $transfer->car_id);
            $ctf->create();

            $ijf = new InspectionJobFactory(InspectionTypeEnum::TRANSFER, TransferCar::class, $transfer->id, $transfer->car_id);
            $ijf->create();
        }

        return true;
    }

    public function updateTransferCarStatus(Request $request)
    {
        $transfer_car = TransferCar::find($request->transfer_car);
        $transfer_car->status = $request->tc_status;
        $transfer_car->reason = $request->reason;
        $transfer_car->confirmation_date = Carbon::now();
        $transfer_car->confirmation_user_id = Auth::user()->id;
        $transfer_car->save();

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' =>  $request->redirect,
        ]);
    }

    public function getStatusTransferCarList()
    {
        return collect([
            (object) [
                'id' => TransferCarEnum::WAITING_RECEIVE,
                'value' => TransferCarEnum::WAITING_RECEIVE,
                'name' => __('transfer_cars.status_' . TransferCarEnum::WAITING_RECEIVE . '_text'),
            ],
            (object) [
                'id' => TransferCarEnum::CONFIRM_RECEIVE,
                'value' => TransferCarEnum::CONFIRM_RECEIVE,
                'name' => __('transfer_cars.status_' . TransferCarEnum::CONFIRM_RECEIVE . '_text'),
            ],
            (object) [
                'id' => TransferCarEnum::REJECT_RECEIVE,
                'value' => TransferCarEnum::REJECT_RECEIVE,
                'name' => __('transfer_cars.status_' . TransferCarEnum::REJECT_RECEIVE . '_text'),
            ],
            (object) [
                'id' => TransferCarEnum::IN_PROCESS,
                'value' => TransferCarEnum::IN_PROCESS,
                'name' => __('transfer_cars.status_' . TransferCarEnum::IN_PROCESS . '_text'),
            ],
            (object) [
                'id' => TransferCarEnum::SUCCESS,
                'value' => TransferCarEnum::SUCCESS,
                'name' => __('transfer_cars.status_' . TransferCarEnum::SUCCESS . '_text'),
            ],
        ]);
    }

    public function printPdf(Request $request)
    {
        $transfer_car_id = $request->transfer_car_id;
        $worksheet_type = $request->worksheet_type;
        $transfer_car = TransferCar::find($transfer_car_id);
        if (!$transfer_car) {
            return abort(404);
        }

        $car = Car::find($transfer_car->car_id);
        if (!$car) {
            return abort(404);
        }
        $data = [];
        $data['worksheet_name'] = __('transfer_cars.worksheet_name');
        $data['worksheet_no'] = $transfer_car->worksheet_no;
        $data['customer_name'] = '';
        $data['customer_address'] = '';
        $data['customer_tel'] = '';
        $data['customer_fax'] = '';
        $data['contact_name'] = '';
        $data['contract_no'] = '';
        $data['period_of_time'] = '';
        $data['insurance_company'] = '';
        $data['contact_start_date'] = '';
        $data['contact_end_date'] = '';
        $data['policy_no'] = '';
        $data['insurance_start'] = '';
        $data['insurance_end'] = '';
        $data['car_class'] = $car->carClass?->name;
        $data['year_mfg'] = '';
        $data['car_color'] = $car->carColor?->name;
        $data['car_status'] = __('cars.status_' . $car->status);
        $data['chassis_no'] = $car->chassis_no;
        $data['engine_no'] = $car->engine_no;
        $data['license_plate'] = $car->license_plate;
        $data['mile_no'] = '';
        $data['fuel_tank'] = '';
        $data['delivery_place'] = $transfer_car->place ? $transfer_car->place : $transfer_car->branch->name;
        $data['delivery_date'] = $transfer_car->delivery_date ? get_thai_date_format($transfer_car->delivery_date, 'd/m/Y H:i') : '';
        $data['user_name'] = '';
        $pdf = PDF::loadView(
            'admin.layouts.pdf.worksheet',
            [
                'data' => $data
            ]
        );
        return $pdf->stream();
    }
}
