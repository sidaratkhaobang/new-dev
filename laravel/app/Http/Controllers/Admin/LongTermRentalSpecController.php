<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\Resources;
use App\Enums\SpecStatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\EmailJobSpec;
use App\Models\CarClass;
use App\Models\ConfigApprove;
use App\Models\Creditor;
use App\Models\Customer;
use App\Models\DealerCheckCar;
use App\Models\LongTermRental;
use App\Models\LongTermRentalMonth;
use App\Models\LongTermRentalTor;
use App\Models\LongTermRentalTorLine;
use App\Models\LongTermRentalTorLineAccessory;
use App\Models\LongTermRentalType;
use App\Traits\CustomerTrait;
use App\Traits\HistoryTrait;
use App\Traits\LongTermRentalTrait;
use App\Traits\NotificationTrait;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class LongTermRentalSpecController extends Controller
{
    use RentalTrait, CustomerTrait, LongTermRentalTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpec);
        $customer_id = $request->customer;
        $worksheet_id = $request->worksheet_no;
        $spec_status_id = $request->spec_status;
        $lt_rental_type = $request->lt_rental_type;
        $status = $request->status;

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
            ->whereIn('lt_rentals.status', [LongTermRentalStatusEnum::SPECIFICATION, LongTermRentalStatusEnum::CANCEL])
            ->whereIn('lt_rentals.spec_status', [SpecStatusEnum::DRAFT, SpecStatusEnum::REJECT, SpecStatusEnum::CHANGE_CAR, SpecStatusEnum::NO_DELIVERY])
            ->branch()
            ->search($request->s, $request)->paginate(PER_PAGE);

        $customer_list = LongTermRental::select('id', 'customer_name as name')
            ->bySpecStatus()->get();

        $worksheet_list = LongTermRental::select('id', 'worksheet_no as name')
            ->bySpecStatus()->get();

        $status_lists = $this->getStatusList();

        $lt_rental_type_list = LongTermRentalType::withTrashed()->get();
        $spec_status_list = RentalTrait::getSpecStatusList();
        return view('admin.long-term-rental-specs.index', [
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
            'status_lists' => $status_lists,
            'status' => $status,
        ]);
    }

    public static function getStatusList()
    {
        $status_lists = collect([
            (object)[
                'id' => SpecStatusEnum::DRAFT,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::DRAFT),
                'value' => SpecStatusEnum::DRAFT,
            ],
            (object)[
                'id' => SpecStatusEnum::REJECT,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::REJECT),
                'value' => SpecStatusEnum::REJECT,
            ],
            (object)[
                'id' => SpecStatusEnum::PENDING_CHECK,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::PENDING_CHECK),
                'value' => SpecStatusEnum::PENDING_CHECK,
            ],
            (object)[
                'id' => SpecStatusEnum::CHANGE_CAR,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::CHANGE_CAR),
                'value' => SpecStatusEnum::CHANGE_CAR,
            ],
        ]);
        return $status_lists;
    }

    public function edit(LongTermRental $rental, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpec);
        $lt_rental_id = $rental->id;
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
        $with_trash = true;
        $lt_rental_type_list = LongTermRentalTrait::getRentalJobTypeList($with_trash);

        $tor_list = $this->getRentalTorFromRentalId($lt_rental_id);

        $tor_list->map(function ($item) {
            $tor_line_list = $this->getTorLine($item->tor_id);
            $item->tor_line_list = $tor_line_list;
            $item->require_date_text = get_thai_date_format($item->actual_delivery_date, 'j F Y');
            $item->customer_require = date("m/Y", strtotime($item->actual_delivery_date));

            return $item;
        });

        $is_haves = $this->getHaveAccessory();

        if (isset($request->create)) {
            $redirect_route = route('admin.long-term-rentals.edit', ['long_term_rental' => $rental]);
        } else {
            $redirect_route = route('admin.long-term-rental.specs.index');
        }

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
        $car_class_list =  CarClass::select('id', 'name', 'full_name')
            ->orderBy('name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->full_name . ' - ' . $item->name
                ];
            });

        $page_title = __('lang.edit') . __('long_term_rentals.specs_and_equipment');
        return view('admin.long-term-rental-specs.form', [
            'd' => $long_term_rental,
            'tor_files' => $tor_files,
            'page_title' => $page_title,
            'redirect_route' => $redirect_route,
            'tor_list' => $tor_list,
            'lt_rental_type_list' => $lt_rental_type_list,
            'customer_type_list' => $customer_type_list,
            'customer_code' => $customer_code,
            'is_haves' => $is_haves,
            // 'dealer_check_cars' => $dealer_check_cars,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
            'month_list' => $month_list,
            'month' => $month,
            'car_class_list' => $car_class_list,
        ]);
    }

    public static function getHaveAccessory()
    {
        $is_haves = collect([
            [
                'id' => 1,
                'name' => 'ต้องซื้อ',
                'value' => 1,
            ],
            [
                'id' => 0,
                'name' => 'ไม่ต้องซื้อ',
                'value' => 0,
            ],
        ]);
        return $is_haves;
    }

    public function show(LongTermRental $rental, Request $request)
    {
        if (!isset($request->accessory_controller)) {
            $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpec);
        } else {
            $this->authorize(Actions::View . '_' . Resources::LongTermRentalSpecsAccessory);
        }
        $lt_rental_id = $rental->id;
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
        $with_trash = true;
        $lt_rental_type_list = LongTermRentalTrait::getRentalJobTypeList($with_trash);

        $tor_list = $this->getRentalTorFromRentalId($lt_rental_id);

        $tor_list->map(function ($item) {
            $tor_line_list = $this->getTorLine($item->tor_id);
            $item->tor_line_list = $tor_line_list;
            $item->require_date_text = get_thai_date_format($item->actual_delivery_date, 'j F Y');
            $item->customer_require = date("m/Y", strtotime($item->actual_delivery_date));

            return $item;
        });

        $is_haves = $this->getHaveAccessory();

        // $approve_line_list = new StepApproveManagement();
        // $approve_return = $approve_line_list->logApprove(LongTermRental::class, $rental->id);
        // $approve_line_list = $approve_return['approve_line_list'];
        // $approve = $approve_return['approve'];

        $approve_line = HistoryTrait::getHistory(LongTermRental::class, $rental->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];

        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            $approve_line_owner = $approve_line_owner->checkCanApprove(LongTermRental::class, $rental->id);

        } else {
            $approve_line_owner = null;
        }

        $month_list = LongTermRentalMonth::where('lt_rental_id', $long_term_rental->id)
            ->selectRaw('id, CONCAT(month, " เดือน") as name')
            ->get();
        $month = $month_list->pluck('id')->toArray();
        $car_class_list =  CarClass::select('id', 'name', 'full_name')
            ->orderBy('name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->full_name . ' - ' . $item->name
                ];
            });

        if (!isset($request->accessory_controller)) {
            $redirect_route = route('admin.long-term-rental.specs.index');
            $page_title = __('lang.view') . __('long_term_rentals.specs_and_equipment');
            $data = [
                'd' => $long_term_rental,
                'tor_files' => $tor_files,
                'page_title' => $page_title,
                'view_only' => true,
                'redirect_route' => $redirect_route,
                'tor_list' => $tor_list,
                'lt_rental_type_list' => $lt_rental_type_list,
                'customer_type_list' => $customer_type_list,
                'customer_code' => $customer_code,
                'approve_line_list' => $approve_line_list,
                'approve' => $approve,
                'approve_line_logs' => $approve_line_logs,
                'month' => $month,
                'month_list' => $month_list,
                'is_haves' => $is_haves,
                'car_class_list' => $car_class_list,
            ];
        } else {
            $redirect_route = route('admin.long-term-rental.specs.accessories.index');
            $page_title = __('lang.view') . __('long_term_rentals.spec_equipment');
            $data = [
                'd' => $long_term_rental,
                'tor_files' => $tor_files,
                'page_title' => $page_title,
                'view_only' => true,
                'redirect_route' => $redirect_route,
                'lt_rental_type_list' => $lt_rental_type_list,
                'customer_type_list' => $customer_type_list,
                'customer_code' => $customer_code,
                'accessory_controller' => true,
                'month' => $month,
                'month_list' => $month_list,
                'tor_list' => $tor_list,
                'is_haves' => $is_haves,
                'approve_line_list' => $approve_line_list,
                'approve' => $approve,
                'approve_line_owner' => $approve_line_owner,
                'approve_line_logs' => $approve_line_logs,
                'car_class_list' => $car_class_list,
            ];
        }
        return view('admin.long-term-rental-specs.form', $data);
    }

    public function store(Request $request)
    {
        // validate at least 1 car
        // dd($request->all());
        $lt_rental_tor = LongTermRentalTor::select('id')->where('lt_rental_id', $request->id)->get();
        $lt_rental_tor_line = LongTermRentalTorLine::whereIn('lt_rental_tor_id', $lt_rental_tor)->get();
        if (count($lt_rental_tor_line) == 0) {
            return $this->responseWithCode(false, 'กรุณากรอกข้อมูลรถ', null, 422);
        }
        if ($request->check_delivery == STATUS_ACTIVE) {
            if (!isset($request->ready_to_delivery)) {
                return $this->responseWithCode(false, 'กรุณาเลือกรถที่พร้อมส่งมอบ', null, 422);
            }
        }
        $lt_rental_id = $request->id;
        $lt_rental = LongTermRental::find($lt_rental_id);

        //temp status
        $temp_status = null;
        if ($lt_rental) {
            $temp_status = $lt_rental->spec_status;
        }
        if (strcmp($temp_status, SpecStatusEnum::ACCESSORY_CHECK) === 0) {
            $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpecsAccessory);
        } else {
            $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalSpec);
        }
        $tor_line_check_input = $request->tor_line_check_input;
        $tor_line_list = $this->getTorList($lt_rental_id);
        $is_required_check_accessory = false;
        foreach ($tor_line_list as $key => $lt_tor_line) {
            $lt_tor_line->is_rental_line = STATUS_DEFAULT;
            if ($tor_line_check_input) {
                if (in_array($lt_tor_line->id, $tor_line_check_input)) {
                    $lt_tor_line->is_rental_line = STATUS_ACTIVE;
                }
            }
            if ($lt_tor_line->have_accessories) {
                $is_required_check_accessory = true;
            }
            $lt_tor_line->save();
        }

        if (isset($request->ready_to_delivery)) {
            $lt_rental_tor_line_uncheck = DB::table('lt_rental_tor_lines')->update(['check_delivery' => STATUS_DEFAULT]);
            foreach ($request->ready_to_delivery as $key => $data) {
                $lt_rental_tor_line = LongTermRentalTorLine::find($key);
                $lt_rental_tor_line->check_delivery = STATUS_ACTIVE;
                $lt_rental_tor_line->save();
            }
        }

        if (strcmp($request->spec_status, SpecStatusEnum::PENDING_CHECK) === 0) {
            if (strcmp($temp_status, SpecStatusEnum::ACCESSORY_CHECK) === 0) {
                $status = SpecStatusEnum::PENDING_REVIEW;
            } else {
                $status = SpecStatusEnum::PENDING_CHECK;
            }

            $lt_rental->spec_status = $status;
        }

        $lt_rental->check_delivery = $request->check_delivery;
        $lt_rental->reason_delivery = $request->reason_delivery;
        $lt_rental->save();
        if (strcmp($temp_status, SpecStatusEnum::ACCESSORY_CHECK) === 0) {
            NotificationTrait::sendNotificationSpecAccessoryApprove($lt_rental?->id, $lt_rental, $lt_rental->worksheet_no);
            $redirect_route = route('admin.long-term-rental.specs.accessories.index');
            return $this->responseValidateSuccess($redirect_route);
        } else {
            $this->sendNotificationLognTermRentalCheck($lt_rental->worksheet_no, $lt_rental);
            $redirect_route = route('admin.long-term-rental.specs.index');
            return $this->responseValidateSuccess($redirect_route);
        }
    }

    public function sendNotificationLognTermRentalAccessorie($dataWorkSheetNo, $modelLongtermRental)
    {
        $notiUserId = getSpecAccessoryApproveUserId();
        $url = route('admin.long-term-rental.specs-approve.show', ['rental' => $modelLongtermRental]);
        $notiTypeChange = new NotificationManagement('อนุมัติสเปครถและอุปกรณ์', 'ใบขอเช่ายาว ' . $dataWorkSheetNo . ' พิจารณาอนุมัติสเปครถและอุปกรณ์', $url, NotificationScopeEnum::USER, $notiUserId, []);
        $notiTypeChange->send();
    }

    public function sendNotificationLognTermRentalCheck($dataWorkSheetNo, $modelLongtermRental)
    {
        $dataDepartment = [
            DepartmentEnum::PCD_PURCHASE,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        $notiUserId = NotificationTrait::getUserId($notiDepartmentId);
        $url = route('admin.long-term-rental.spec-check-cars.edit', ['rental' => $modelLongtermRental]);
        $notiTypeChange = new NotificationManagement('เช็กรถ', 'ใบขอเช่ายาว ' . $dataWorkSheetNo . ' กรุณาเช็กรถ', $url, NotificationScopeEnum::USER, $notiUserId, []);
        $notiTypeChange->send();
    }

    function getDefaultBomCar(Request $request)
    {
        $bom_id = $request->bom_id;
        $data = DB::table('bom_lines')
            ->join('car_classes', 'car_classes.id', '=', 'bom_lines.car_class_id')
            ->join('car_colors', 'car_colors.id', '=', 'bom_lines.car_color_id')
            ->where('bom_lines.bom_id', $bom_id)
            ->select(
                'car_classes.full_name as car_name',
                'car_colors.name as color_name',
                'bom_lines.amount',
                'bom_lines.id as bom_line_id',
                'bom_lines.car_class_id',
                'bom_lines.car_color_id',
                'bom_lines.remark'
            )
            ->get()->toArray();
        return [
            'success' => true,
            'bom_id' => $request->bom_id,
            'data' => $data
        ];
    }

    function getDefaultBomAccessory(Request $request)
    {
        $bom_accessory_id = $request->bom_accessory_id;
        $data = DB::table('bom_accessories')
            ->join('accessories', 'accessories.id', '=', 'bom_accessories.accessories_id')
            ->where('bom_accessories.bom_id', $bom_accessory_id)
            ->select(
                'accessories.name as accessory_name',
                'bom_accessories.amount as amount_per_car',
                'bom_accessories.id as bom_line_id',
                'bom_accessories.accessories_id as accessory_id',
            )
            ->get()->toArray();
        return [
            'success' => true,
            'bom_accessory_id' => $request->bom_accessory_id,
            'data' => $data
        ];
    }

    public function storeBomCar(Request $request)
    {
        $lt_rental_id = $request->lt_rental_id;
        $lt_rental = LongTermRental::find($lt_rental_id);
        $tor_lines = $request->tor_lines;
        if ($tor_lines) {
            $tor = LongTermRentalTor::firstOrNew(['id' => $request->tor_id]);
            $tor->lt_rental_id = $lt_rental->id;
            $tor->remark_tor = $request->remark_tor;
            $tor->save();
            foreach ($tor_lines as $item_car) {
                $tor_line = LongTermRentalTorLine::firstOrNew(['id' => $item_car['tor_line_id']]);
                $tor_line->lt_rental_tor_id = $tor->id;
                $tor_line->car_class_id = $item_car['car_class_id'];
                $tor_line->car_color_id = $item_car['car_color_id'];
                $tor_line->amount = $item_car['amount'];
                $tor_line->have_accessories = boolval($item_car['is_have']);
                $tor_line->remark = $item_car['remark'];
                $tor_line->save();
            }

            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ]);
        }
    }

    public function storeDealer(Request $request)
    {
        $tor_line_list = $this->getTorList($request->lt_rental_id);
        if ($request->dealer_id) {
            foreach ($tor_line_list as $item) {
                $dealer_check_car = new DealerCheckCar();
                $dealer_check_car->lt_rental_id = $request->lt_rental_id;
                $dealer_check_car->dealer_id = $request->dealer_id;
                $dealer_check_car->tor_line_id = $item->id;
                $dealer_check_car->save();
            }
        }
        return response()->json([
            'success' => true,
        ]);
    }

    public function sendMail(Request $request)
    {
        $id = $request->id;
        $dealer_id = $request->dealer_id;
        $dealer_email = $request->dealer_email;
        $dealer = Creditor::find($dealer_id);
        $dealer_check_car = DealerCheckCar::where('lt_rental_id', $id)->where('dealer_id', $dealer_id)->first();
        $url = null;
        $dealer_name = null;
        $image = 'https://uat-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
        if (App::environment('production')) {
            $image = 'https://production-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
        }
        $mails = $dealer_email;
        if (empty($mails)) {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ]);
        }

        if ($dealer) {
            $url = route('long-term-rental-vendor.specs.edit', [$id, $dealer_id]);
            $dealer_name = ($dealer->name) ? $dealer->name : null;
        }

        $mail_data = [
            'url' => $url,
            'dealer_name' => $dealer_name,
            'image' => $image,
        ];

        EmailJobSpec::dispatch($mails, $mail_data);
        return response()->json([
            'success' => true,
        ]);
    }

    function getDefaultTorLine(Request $request)
    {
        $tor_id = $request->tor_id;
        $data = [];
        $tor = LongTermRentalTor::find($tor_id);
        if ($tor) {
            $data['tor_remark'] = $tor->remark_tor;
            $tor_line = LongTermRentalTorLine::where('lt_rental_tor_id', $tor->id)
                ->select(
                    'id as tor_line_id',
                    'car_class_id',
                    'car_color_id',
                    'amount',
                    'remark',
                    'have_accessories'
                )
                ->get();
            $tor_line->map(function ($item) {
                $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
                $item->car_color_text = ($item->color) ? $item->color->name : '';
                $item->amount_car = $item->amount;
            })->toArray();
            $data['tor_line'] = $tor_line;
        }
        return [
            'success' => true,
            'tor_id' => $request->tor_id,
            'data' => $data
        ];
    }

    public function storeTorAccessory(Request $request)
    {
        $tor_id = $request->tor_id;
        $lt_tor = LongTermRentalTor::find($tor_id);
        $bom_accessory_lines = $request->bom_accessory_lines;
        $tor_line = LongTermRentalTorLine::where('lt_rental_tor_id', $lt_tor->id)
            ->where('have_accessories', BOOL_TRUE)
            ->get();
        if ($bom_accessory_lines) {
            foreach ($tor_line as $item_line) {
                foreach ($bom_accessory_lines as $item_accessory) {
                    $tor_line_accessory = LongTermRentalTorLineAccessory::firstOrNew(['id' => $item_accessory['tor_line_accessory_id']]);
                    $tor_line_accessory->lt_rental_tor_line_id = $item_line->id;
                    $tor_line_accessory->accessory_id = $item_accessory['accessory_id'];
                    $tor_line_accessory->amount =  intval($item_accessory['amount_per_car'] * $item_line->amount);
                    $tor_line_accessory->amount_per_car = $item_accessory['amount_per_car'];
                    $tor_line_accessory->remark = $item_accessory['remark'];
                    $tor_line_accessory->type_accessories = $item_accessory['type_accessories'];
                    $tor_line_accessory->save();
                }
            }


            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ]);
        }
    }

    public function destroyTor(Request $request)
    {
        $lt_rental_tor_id = $request->lt_rental_tor_id;
        $lt_rental_id = $request->lt_rental_id;

        if ($lt_rental_tor_id) {
            $lt_rental_tor = LongTermRentalTor::where('lt_rental_id', $lt_rental_id)
                ->where('id', $lt_rental_tor_id)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
