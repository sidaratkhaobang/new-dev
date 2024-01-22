<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\BorrowCarEnum;
use App\Enums\BorrowTypeEnum;
use App\Enums\CarEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Enums\TransferCarEnum;
use App\Http\Controllers\Controller;
use App\Models\BorrowCar;
use App\Models\Branch;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class BorrowCarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::BorrowCar);
        $list = BorrowCar::leftjoin('cars', 'cars.id', 'borrow_cars.car_id')
        ->sortable(['worksheet_no' => 'desc'])
        ->search($request)
        ->select('borrow_cars.*','cars.license_plate','cars.chassis_no')
        ->selectRaw('borrow_cars.*, CASE WHEN borrow_cars.status = ? THEN 1 ELSE 0 END as can_edit', [BorrowCarEnum::PENDING_REVIEW])
        ->paginate(PER_PAGE);
    

        $page_title = __('borrow_cars.borrow_request');
        $show_btn_new = true;
        $license_plate_list = BorrowCar::leftjoin('cars', 'cars.id', 'borrow_cars.car_id')
            ->where('cars.status', CarEnum::READY_TO_USE)
            ->select('cars.id as car_id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->get();
        $license_plate_list->map(function ($item) {
            if ($item->license_plate) {
                $text = $item->license_plate;
            } else if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            }
            $item->id = $item->car_id;
            $item->name = $text;
            return $item;
        });
        $branch_lists = Branch::all();
        $worksheet_no_list = BorrowCar::select('id', 'worksheet_no as name')->get();
        $status_lists = $this->getStatusBorrowCarList();
        $car_id = $request->car_id;
        $status = $request->status;
        $worksheet_no = $request->worksheet_no;
        $borrow_type = $request->borrow_type;
        $pickup_date_start = $request->pickup_date_start;
        $pickup_date_end = $request->pickup_date_end;
        $return_date_start = $request->return_date_start;
        $return_date_end = $request->return_date_end;
        $borrow_type_list = $this->getBorrowTypeList();

        return view('admin.borrow-cars.index', [
            'list' => $list,
            's' => $request->s,
            'page_title' => $page_title,
            'show_btn_new' => $show_btn_new,
            'license_plate_list' => $license_plate_list,
            'car_id' => $car_id,
            'status' => $status,
            'worksheet_no' => $worksheet_no,
            'borrow_type' => $borrow_type,
            'branch_lists' => $branch_lists,
            'status_lists' => $status_lists,
            'worksheet_no_list' => $worksheet_no_list,
            'borrow_type_list' => $borrow_type_list,
            'pickup_date_start' => $pickup_date_start,
            'pickup_date_end' => $pickup_date_end,
            'return_date_start' => $return_date_start,
            'return_date_end' => $return_date_end,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::BorrowCar);
        $step_approve_management = new StepApproveManagement();
        $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::BORROW_CAR);
        if (!$is_configured) {
            return redirect()->back()->with('warning', __('lang.config_approve_warning') . __('borrow_cars.page_title'));
        }
        $d = new BorrowCar();
        $d->request_date = date('Y-m-d');
        $d->is_driver = STATUS_INACTIVE;
        $optional_borrow_files = [];
        $branch_list = Branch::select('name', 'id')->get();
        $car_lists = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->where('cars.branch_id', Auth::user()->branch_id)
            ->where('cars.rental_type', RentalTypeEnum::BORROW)
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
        $page_title = __('borrow_cars.add_sheet');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $borrow_type_list = $this->getBorrowTypeList();
        $url = 'admin.borrow-cars.index';
        return view('admin.borrow-cars.form',  [
            'd' => $d,
            'page_title' => $page_title,
            'optional_borrow_files' => $optional_borrow_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'url' => $url,
            'borrow_type_list' => $borrow_type_list,
        ]);
    }

    public function store(Request $request)
    {
        $borrow_car = BorrowCar::firstOrNew(['id' => $request->id]);
        $validator = Validator::make($request->all(), [
            'borrow_id' => [
                'required',
            ],
            'borrow_reason' => [
                'required',
            ],
            'start_date' => [
                'required',
            ],
            'end_date' => [Rule::when($request->borrow_id == BorrowTypeEnum::BORROW_EMPLOYEE, ['required'])],
            'tel_employee' => [Rule::when($request->borrow_id == BorrowTypeEnum::BORROW_EMPLOYEE, ['required', 'numeric', 'digits:10'])],
            'is_driver_employee' => [Rule::when($request->borrow_id == BorrowTypeEnum::BORROW_EMPLOYEE, ['required'])],

            'contact_other' => [Rule::when($request->borrow_id == BorrowTypeEnum::BORROW_OTHER, ['required'])],
            'tel_other' => [Rule::when($request->borrow_id == BorrowTypeEnum::BORROW_OTHER, ['required', 'numeric', 'digits:10'])],
            'is_driver_other' => [Rule::when($request->borrow_id == BorrowTypeEnum::BORROW_OTHER, ['required'])],

            'place_other' => [Rule::when($request->is_driver_other == STATUS_ACTIVE, ['required'])],
            'delivery_date_other' => [Rule::when($request->is_driver_other == STATUS_ACTIVE, ['required'])],

            'place_employee' => [Rule::when($request->is_driver_employee == STATUS_ACTIVE, ['required'])],
            'delivery_date_employee' => [Rule::when($request->is_driver_employee == STATUS_ACTIVE, ['required'])],

            'borrow_branch_id' => [Rule::when(!is_null($request->borrow_id), ['required'])],


        ], [], [
            'borrow_id' => __('borrow_cars.borrow_type'),
            'borrow_reason' => __('borrow_cars.borrow_reason'),
            'start_date' => __('borrow_cars.start_date'),
            'end_date' => __('borrow_cars.end_date'),
            'tel_employee' => __('borrow_cars.tel'),
            'is_driver_employee' => __('borrow_cars.is_need_driver'),

            'contact_other' => __('borrow_cars.fullname'),
            'tel_other' => __('borrow_cars.tel'),
            'is_driver_other' => __('borrow_cars.is_need_driver'),
            'place_other' => __('borrow_cars.place'),
            'delivery_date_other' => __('borrow_cars.delivery_date'),

            'place_employee' => __('borrow_cars.place'),
            'delivery_date_employee' => __('borrow_cars.delivery_date'),

            'branch_id' => __('borrow_cars.branch'),
            'borrow_branch_id' => __('borrow_cars.branch'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $user = Auth::user();
        $tc_count = BorrowCar::where('branch_id', $user->branch_id)->count() + 1;
        $prefix = 'BC';
        if (!($borrow_car->exists)) {
            $borrow_car->worksheet_no = generateRecordNumber($prefix, $tc_count);
            $borrow_car->status = BorrowCarEnum::PENDING_REVIEW;
        }

        $borrow_car->borrow_type = $request->borrow_id;
        $borrow_car->purpose = $request->borrow_reason;
        $borrow_car->start_date = $request->start_date;
        $borrow_car->end_date = $request->end_date;
        $borrow_car->remark = $request->remark;

        if (strcmp($request->borrow_id, BorrowTypeEnum::BORROW_EMPLOYEE) === 0) {
            $borrow_car->tel = $request->tel_employee;
            $borrow_car->is_driver = $request->is_driver_employee;
            $borrow_car->contact = $user->username;
            if (strcmp($request->is_driver_employee, STATUS_ACTIVE) === 0) {
                $borrow_car->place = $request->place_employee;
                $borrow_car->delivery_date = $request->delivery_date_employee;
            }
        } elseif (strcmp($request->borrow_id, BorrowTypeEnum::BORROW_OTHER) === 0) {
            $borrow_car->contact = $request->contact_other;
            $borrow_car->tel = $request->tel_other;
            $borrow_car->is_driver = $request->is_driver_other;

            if (strcmp($request->is_driver_other, STATUS_ACTIVE) === 0) {
                $borrow_car->place = $request->place_other;
                $borrow_car->delivery_date = $request->delivery_date_other;
            }
        }
        $borrow_car->branch_id = $user->branch_id;
        $borrow_car->borrow_branch_id = $request->borrow_branch_id;

        if ($request->optional_borrow_files__pending_delete_ids) {
            $pending_delete_ids = $request->optional_borrow_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $borrow_car->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('optional_borrow_files')) {
            foreach ($request->file('optional_borrow_files') as $image) {
                if ($image->isValid()) {
                    $borrow_car->addMedia($image)->toMediaCollection('optional_borrow_files');
                }
            }
        }

        $borrow_car->save();

        $bc_check = BorrowCar::find($request->id);
        if (!$bc_check) {
            $step_approve_management = new StepApproveManagement();
            $step_approve_management->createModelApproval(ConfigApproveTypeEnum::BORROW_CAR, BorrowCar::class, $borrow_car->id);
        }

        $redirect_route = route('admin.borrow-cars.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(BorrowCar $borrow_car)
    {
        $this->authorize(Actions::View . '_' . Resources::BorrowCar);
        $optional_borrow_files = [];
        $branch_list = Branch::select('name', 'id')->get();
        $car_lists = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->where('cars.branch_id', Auth::user()->branch_id)
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->name = $text;
                return $item;
            });

        $optional_borrow_files = $borrow_car->getMedia('optional_borrow_files');
        $optional_borrow_files = get_medias_detail($optional_borrow_files);
        $page_title = __('borrow_cars.view_sheet');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $borrow_type_list = $this->getBorrowTypeList();
        $url = 'admin.borrow-cars.index';
        return view('admin.borrow-cars.form',  [
            'd' => $borrow_car,
            'page_title' => $page_title,
            'optional_borrow_files' => $optional_borrow_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'url' => $url,
            'borrow_type_list' => $borrow_type_list,
            'view' => true,
        ]);
    }

    public function edit(BorrowCar $borrow_car)
    {
        $this->authorize(Actions::Manage . '_' . Resources::BorrowCar);
        $optional_borrow_files = [];
        $branch_list = Branch::select('name', 'id')->get();
        $car_lists = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->where('cars.branch_id', Auth::user()->branch_id)
            ->where('cars.rental_type', RentalTypeEnum::BORROW)
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->name = $text;
                return $item;
            });

        $optional_borrow_files = $borrow_car->getMedia('optional_borrow_files');
        $optional_borrow_files = get_medias_detail($optional_borrow_files);
        $page_title = __('borrow_cars.edit_sheet');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $borrow_type_list = $this->getBorrowTypeList();
        $url = 'admin.borrow-cars.index';
        return view('admin.borrow-cars.form',  [
            'd' => $borrow_car,
            'page_title' => $page_title,
            'optional_borrow_files' => $optional_borrow_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'url' => $url,
            'borrow_type_list' => $borrow_type_list,
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
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('transfer_cars.need_driver_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_INACTIVE,
                'value' => STATUS_INACTIVE,
                'name' => __('transfer_cars.need_driver_' . STATUS_INACTIVE),
            ],
        ]);
    }

    private function getBorrowTypeList()
    {
        return collect([
            (object)[
                'id' => BorrowTypeEnum::BORROW_EMPLOYEE,
                'value' => BorrowTypeEnum::BORROW_EMPLOYEE,
                'name' => __('borrow_cars.type_' . BorrowTypeEnum::BORROW_EMPLOYEE),
            ],
            (object)[
                'id' => BorrowTypeEnum::BORROW_OTHER,
                'value' => BorrowTypeEnum::BORROW_OTHER,
                'name' => __('borrow_cars.type_' . BorrowTypeEnum::BORROW_OTHER),
            ],
        ]);
    }

    public function getStatusBorrowCarList()
    {
        return collect([
            (object) [
                'id' => BorrowCarEnum::PENDING_REVIEW,
                'value' => BorrowCarEnum::PENDING_REVIEW,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::PENDING_REVIEW . '_text'),
            ],
            (object) [
                'id' => BorrowCarEnum::CONFIRM,
                'value' => BorrowCarEnum::CONFIRM,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::CONFIRM . '_text'),
            ],
            (object) [
                'id' => BorrowCarEnum::REJECT,
                'value' => BorrowCarEnum::REJECT,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::REJECT . '_text'),
            ],
            (object) [
                'id' => BorrowCarEnum::PENDING_DELIVERY,
                'value' => BorrowCarEnum::PENDING_DELIVERY,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::PENDING_DELIVERY . '_text'),
            ],
            (object) [
                'id' => BorrowCarEnum::IN_PROCESS,
                'value' => BorrowCarEnum::IN_PROCESS,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::IN_PROCESS . '_text'),
            ],
            (object) [
                'id' => BorrowCarEnum::SUCCESS,
                'value' => BorrowCarEnum::SUCCESS,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::SUCCESS . '_text'),
            ],
        ]);
    }

    public function printPdf(Request $request)
    {
        $borrow_car_id = $request->borrow_car_id;
        $worksheet_type = $request->worksheet_type;
        $borrow_car = BorrowCar::find($borrow_car_id);
        if (!$borrow_car_id) {
            return abort(404);
        }

        $car = Car::find($borrow_car->car_id);
        if (!$car) {
            return abort(404);
        }
        $data = [];
        $data['worksheet_name'] = __('transfer_cars.worksheet_name');
        $data['worksheet_no'] = $borrow_car->worksheet_no;
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
        $data['delivery_place'] = $borrow_car->place ? $borrow_car->place : $borrow_car->borrowBranch->name;
        $data['delivery_date'] = $borrow_car->delivery_date ? get_thai_date_format($borrow_car->delivery_date, 'd/m/Y H:i') : '';
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
