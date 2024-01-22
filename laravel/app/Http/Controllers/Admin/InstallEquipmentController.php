<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Classes\InstallEquipmentManagement;
use App\Classes\StepApproveManagement;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\InstallEquipmentPOStatusEnum;
use App\Enums\InstallEquipmentStatusEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Models\Accessories;
use App\Models\BomAccessory;
use App\Models\BomLine;
use App\Models\Car;
use App\Models\CarAccessory;
use App\Models\CarParkTransfer;
use App\Models\Creditor;
use App\Models\DrivingJob;
use App\Models\IEInspectionInstallEquipment;
use App\Models\InspectionFlow;
use App\Models\InspectionFormChecklist;
use App\Models\InspectionFormSection;
use App\Models\InspectionJob;
use App\Models\InspectionJobChecklist;
use App\Models\InspectionJobStep;
use App\Models\InspectionStep;
use App\Models\InstallEquipment;
use App\Models\InstallEquipmentInspection;
use App\Models\InstallEquipmentLine;
use App\Models\InstallEquipmentPOLine;
use App\Models\InstallEquipmentPurchaseOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisitionLineAccessory;
use App\Traits\InstallEquipmentTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\InspectionFormEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\TransferTypeEnum;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Factories\DrivingJobFactory;
use App\Factories\CarparkTransferFactory;
use App\Factories\InspectionJobFactory;

class InstallEquipmentController extends Controller
{
    use InstallEquipmentTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InstallEquipment);
        $install_equipment_no = $request->install_equipment_no;
        $install_equipment_no_text = null;
        if ($install_equipment_no) {
            $_install_equipment = InstallEquipment::find($install_equipment_no);
            $install_equipment_no_text = $_install_equipment ? $_install_equipment->worksheet_no : null;
        }
        $purchase_order_no = $request->purchase_order_no;
        $purchase_order_no_text = null;
        if ($purchase_order_no) {
            $po = PurchaseOrder::find($purchase_order_no);
            $purchase_order_no_text = $po ? $po->po_no : null;
        }

        $supplier_id = $request->supplier_id;
        $supplier_text = null;
        if ($supplier_id) {
            $supplier = Creditor::find($supplier_id);
            $supplier_text = $supplier ? $supplier->name : null;
        }

        $install_equipment_po_no = $request->install_equipment_po_no;
        $create_date = $request->create_date;
        $chassis_no = $request->chassis_no;
        $chassis_no_text = null;
        if ($chassis_no) {
            $car = Car::find($chassis_no);
            $chassis_no_text = $car ? $car->chassis_no : null;
        }
        $license_plate = $request->license_plate;
        $license_plate_text = null;
        if ($license_plate) {
            $car = Car::find($license_plate);
            $license_plate_text = $car ? $car->license_plate : null;
        }

        $status_id = $request->status_id;
        $lot_no = $request->lot_no;

        $install_equipment_parent_list = InstallEquipment::with(['supplier', 'install_equipment_po'])
            ->search($request)
            ->select('group_id', 'po_id', 'car_id', DB::raw('MAX(created_at) as latest_created_at'))
            ->groupBy('group_id', 'po_id', 'car_id')
            ->orderBy('latest_created_at', 'desc')
            ->distinct()
            ->paginate(5);

        foreach ($install_equipment_parent_list as $key => $parent) {
            $install_equipment_list = InstallEquipment::with(['supplier', 'install_equipment_po'])
                ->where('group_id', $parent->group_id)
                ->where('car_id', $parent->car_id)
                ->where('po_id', $parent->po_id)
                ->search($request)
                ->get();
            $install_equipment_list->map(function ($item) {
                $accessory_list = InstallEquipmentLine::leftjoin('accessories', 'accessories.id', '=', 'install_equipment_lines.accessory_id')
                    ->where('install_equipment_lines.install_equipment_id', $item->id)
                    ->select('accessories.name')
                    ->get()->toArray();
                $item->accessory_list = $accessory_list;
                if ($item->start_date) {
                    $datetime_start = new DateTime($item->start_date);
                    $datetime_today = new DateTime();
                    $interval = $datetime_start->diff($datetime_today);
                    $days = $interval->format('%a');
                    $item->day_amount = $days;
                    if ($item->install_day_amount) {
                        if (
                            in_array($item->status, [
                                InstallEquipmentStatusEnum::WAITING,
                                InstallEquipmentStatusEnum::PENDING_REVIEW,
                                InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                                InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                            ])
                        ) {
                            if ($days == 0) {
                                $item->status = InstallEquipmentStatusEnum::DUE;
                            }
                            if ($days > $item->install_day_amount) {
                                $item->status = InstallEquipmentStatusEnum::OVERDUE;
                                $item->day_amount = $days - $item->install_day_amount;
                            }
                        }
                    }
                }
                return $item;
            });
            $count = count($install_equipment_list);
            $count_complete = count($install_equipment_list->where('status', InstallEquipmentStatusEnum::INSTALL_COMPLETE));
            $is_allow_inspect = ($count == $count_complete) ? true : false;

            $parent->child_list = $install_equipment_list;
            $parent->is_allow_inspect = $is_allow_inspect;
            $parent->po_worksheet_no = null;
            $po = PurchaseOrder::find($parent->po_id);
            if ($po) {
                $parent->po_worksheet_no = $po->po_no;
            }
            $parent->car_name = null;
            $parent->car_class = null;
            $parent->car_license_plate = null;
            $parent->car_chassis_no = null;
            $parent->type = null;
            $parent->car_status = null;
            $car = $this->getCarCollection($parent->car_id);
            if ($car) {
                $parent->car_class = $car->class;
                $parent->car_license_plate = $car->license_plate;
                $parent->car_chassis_no = $car->chassis_no;
                $parent->image = $car->image;
                $parent->type = $car->rental_type;
                $parent->car_status = $car->status;
            }
        }

        $install_equipment_po_no_list = InstallEquipment::select(['install_equipments.id', 'install_equipment_purchase_orders.worksheet_no as name'])
            ->leftJoin('install_equipment_purchase_orders', 'install_equipment_purchase_orders.install_equipment_id', '=', 'install_equipments.id')
            ->groupBy('install_equipments.id', 'install_equipment_purchase_orders.worksheet_no')
            ->get(); // เลขที่ใบสั่งซื้ออุปกรณ์
        $status_list = $this->getTempIntallEquipmentStatusSearchIndexList();

        return view('admin.install-equipments.index', [
            'list' => $install_equipment_parent_list,
            'create_date' => $create_date,
            'install_equipment_no' => $install_equipment_no,
            'install_equipment_no_text' => $install_equipment_no_text,
            'purchase_order_no' => $purchase_order_no,
            'purchase_order_no_text' => $purchase_order_no_text,
            'supplier_id' => $supplier_id,
            'supplier_text' => $supplier_text,
            'install_equipment_po_no' => $install_equipment_po_no,
            'install_equipment_po_no_list' => $install_equipment_po_no_list,
            'chassis_no' => $chassis_no,
            'chassis_no_text' => $chassis_no_text,
            'license_plate' => $license_plate,
            'license_plate_text' => $license_plate_text,
            'status_id' => $status_id,
            'status_list' => $status_list,
            'lot_no' => $lot_no,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::InstallEquipment);
        $step_approve_management = new StepApproveManagement();
        $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::EQUIPMENT_ORDER);
        if (!$is_configured) {
            return redirect()->back()->with('warning', __('lang.config_approve_warning') . __('install_equipments.page_title'));
        }
        $d = new InstallEquipment();
        $d->created_at = now();
        $user = Auth::user();
        $d->created_by = $user->id;
        $store_uri = route('admin.install-equipments.store-inital');
        $page_title = __('lang.create') . __('install_equipments.page_title');
        return view('admin.install-equipments.form', [
            'd' => $d,
            'page_title' => $page_title,
            'store_uri' => $store_uri,
            'mode' => MODE_CREATE,
            'is_view_only' => false,
            'link_list' => []
        ]);
    }

    public function storeInitial(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InstallEquipment);
        // $step_approve_management = new StepApproveManagement();
        // $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::EQUIPMENT_ORDER);
        // if (!$is_configured) {
        //     return $this->responseWithCode(false, __('lang.config_approve_warning'), null, 422);
        // }
        $validator = Validator::make($request->all(), [
            'license_plate' => 'required',
            'install_equipments' => ['required', 'array', 'min:1'],
        ], [], [
            'license_plate' => __('install_equipments.car'),
            'install_equipments' => __('install_equipments.install_equipment_lines'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        if ($request->license_plate) {
            $car_id = $request->license_plate;
        }
        if ($request->chassis_no) {
            $car_id = $request->chassis_no;
        }
        if ($request->engine_no) {
            $car_id = $request->engine_no;
        }

        // TODO try catch
        DB::transaction(function () use ($request, $car_id) {
            $files = $request->file('attachment');
            $existed_install_equipment_arr = InstallEquipmentTrait::createInstallEquipments($request->po_id, $car_id, $request->install_equipments, $request->remark, $files);
        });

        $redirect_route = route('admin.install-equipments.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InstallEquipment);
        $validator = Validator::make($request->all(), [
            'install_equipments' => ['required', 'array', 'min:1'],
        ], [], [
            'install_equipments' => __('install_equipments.install_equipment_lines'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        if ($request->car_code) {
            $car_id = $request->car_code;
        }
        if ($request->license_plate) {
            $car_id = $request->license_plate;
        }
        if ($request->chassis_no) {
            $car_id = $request->chassis_no;
        }
        if ($request->engine_no) {
            $car_id = $request->engine_no;
        }
        $delete_line_ids = $request->delete_install_equipment_ids;
        if ((!empty($delete_line_ids)) && (is_array($delete_line_ids))) {
            InstallEquipmentLine::whereIn('id', $delete_line_ids)->delete();
            InstallEquipmentPOLine::whereIn('install_equipment_line_id', $delete_line_ids)->delete();
        }

        $install_equipment = InstallEquipment::findOrFail($request->id);
        $install_equipment->end_date = $request->end_date;
        $install_equipment->install_day_amount = intval($request->install_day_amount);
        if (strcmp($install_equipment->status, InstallEquipmentStatusEnum::WAITING) === 0) {
            if (!$request->temp_status) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณากรอก ' . __('lang.status')
                ], 422);
            }
        }
        $install_equipment->remark = $request->remark;
        if ($request->temp_status) {
            if (strcmp($request->temp_status, InstallEquipmentStatusEnum::INSTALL_COMPLETE) == 0) {
                if (!$request->end_date) {
                    return response()->json([
                        'success' => false,
                        'message' => 'กรุณากรอก ' . __('install_equipments.end_date')
                    ], 422);
                }
            }
            if (strcmp($install_equipment->status, InstallEquipmentStatusEnum::INSTALL_COMPLETE) != 0) {
                $install_equipment->status = $request->temp_status;
            }
        }
        if (strcmp($install_equipment->status, InstallEquipmentStatusEnum::CONFIRM) === 0) {
            if (intval($request->install_day_amount) <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('install_equipments.install_day_amount') . ' ต้องมากกว่า 0 วัน'
                ], 422);
            }
            $install_equipment->start_date = $request->start_date;
            $install_equipment->install_day_amount = intval($request->install_day_amount);
            if (!empty($request->start_date)) {
                $install_equipment->status = InstallEquipmentStatusEnum::WAITING;
                if (strcmp($install_equipment->status, InstallEquipmentStatusEnum::WAITING) === 0) {
                    $this->createDrivingJob($install_equipment->id, $install_equipment->car_id);
                    $this->createTransferOutInspectionJobs($install_equipment->id, $install_equipment->car_id, $request->start_date);
                }
            }
        }
        $install_equipment->save();

        if ($request->attachment__pending_delete_ids) {
            $pending_delete_ids = $request->attachment__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $install_equipment->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                if ($file->isValid()) {
                    $install_equipment->addMedia($file)->toMediaCollection('install_equipment_files');
                }
            }
        }

        $install_equipments = $request->install_equipments;
        if ($install_equipments) {
            foreach ($install_equipments as $key => $item) {
                $install_equipment_line = InstallEquipmentLine::firstOrNew(['id' => $item['id']]);
                $install_equipment_line->install_equipment_id = $install_equipment->id;
                $install_equipment_line->accessory_id = $item['accessory_id'];
                $install_equipment_line->amount = intval(str_replace(',', '', $item['amount']));
                $install_equipment_line->price = floatval(str_replace(',', '', $item['price']));
                $install_equipment_line->remark = $item['remark'];
                $install_equipment_line->save();
            }
            $install_equip_management = new InstallEquipmentManagement();
            $created_po_lines = $install_equip_management->updateInstallEquipmentPOLines($install_equipment->id);
        }

        $redirect_route = route('admin.install-equipments.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(InstallEquipment $install_equipment)
    {
        $this->authorize(Actions::View . '_' . Resources::InstallEquipment);
        $install_equipment->car_code = null;
        $install_equipment->license_plate = null;
        $install_equipment->engine_no = null;
        $install_equipment->chassis_no = null;
        $install_equipment_files = $install_equipment->getMedia('install_equipment_files');
        $install_equipment_files = get_medias_detail($install_equipment_files);
        if ($install_equipment->start_date) {
            $datetime_start = new DateTime($install_equipment->start_date);
            $datetime_today = new DateTime();
            $interval = $datetime_start->diff($datetime_today);
            $days = $interval->format('%a');
            $install_equipment->day_amount = $days;
            if ($install_equipment->install_day_amount) {
                if (
                    in_array($install_equipment->status, [
                        InstallEquipmentStatusEnum::WAITING,
                        InstallEquipmentStatusEnum::PENDING_REVIEW,
                        InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                        InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                    ])
                ) {
                    if ($days = $install_equipment->install_day_amount) {
                        $install_equipment->status = InstallEquipmentStatusEnum::DUE;
                    }
                    if ($days > $install_equipment->install_day_amount) {
                        $install_equipment->status = InstallEquipmentStatusEnum::OVERDUE;
                        $install_equipment->day_amount = $days - $install_equipment->install_day_amount;
                    }
                }
            }
            $modified_start_date = clone $datetime_start;
            $modified_start_date->modify('+' . $install_equipment->install_day_amount . ' days');
            $install_equipment->expected_end_date = $modified_start_date->format('Y-m-d');;
        }
        $license_plate_text = null;
        if ($install_equipment->car) {
            $license_plate = $install_equipment->car?->license_plate ?? __('install_equipments.not_register');
            $engine_no = $install_equipment->car?->engine_no ?? __('install_equipments.not_register');
            $chassis_no = $install_equipment->car?->chassis_no ?? __('install_equipments.not_register');
            $license_plate_text = $license_plate . ' / ' . $engine_no . ' / ' . $chassis_no;
        }
        $install_equipment->license_plate = $license_plate_text;

        $install_equipment_line_list = InstallEquipmentLine::where('install_equipment_id', $install_equipment->id)->get();
        $install_equipment_line_list->map(function ($item) use ($install_equipment) {
            $item->accessory_class = $item->version;
            $item->supplier_id = $install_equipment->supplier_id;
            $item->supplier_text = '';
            if ($install_equipment->supplier) {
                $item->supplier_text = $install_equipment->supplier->name;
            }
            $item->accessory_text = '';
            $item->accessory_class = '';
            if ($item->accessory) {
                $item->accessory_text = $item->accessory->code . ' - ' . $item->accessory->name;
                $item->accessory_class = $item->accessory->version;
            }
        });
        $supplier = (object) [];
        $supplier->id = $install_equipment->supplier_id;
        $supplier->name = $install_equipment->supplier ? $install_equipment->supplier->name : '';
        $status_list = $this->getTempIntallEquipmentStatusList();
        $link_list = $this->getLinkList($install_equipment);
        $store_uri = route('admin.install-equipments.store');
        $page_title = __('lang.view') . __('install_equipments.page_title');
        return view('admin.install-equipments.form', [
            'd' => $install_equipment,
            'install_equipment_line_list' => $install_equipment_line_list,
            'page_title' => $page_title,
            'store_uri' => $store_uri,
            'install_equipment_files' => $install_equipment_files,
            'mode' => MODE_VIEW,
            'supplier' => $supplier,
            'status_list' => $status_list,
            'is_view_only' => true,
            'link_list' => $link_list
        ]);
    }

    public function edit(InstallEquipment $install_equipment)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InstallEquipment);
        if (
            !in_array($install_equipment->status, [
                InstallEquipmentStatusEnum::PENDING_REVIEW,
                InstallEquipmentStatusEnum::CONFIRM,
                InstallEquipmentStatusEnum::WAITING,
                InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                InstallEquipmentStatusEnum::OVERDUE,
                InstallEquipmentStatusEnum::DUE,
                // InstallEquipmentStatusEnum::INSTALL_COMPLETE,
            ])
        ) {
            return redirect()->back();
        }
        $is_allow_edit = $this->checkIsAllowEdit($install_equipment);
        if ($install_equipment->start_date) {
            $datetime_start = new DateTime($install_equipment->start_date);
            $datetime_today = new DateTime();
            $interval = $datetime_start->diff($datetime_today);
            $days = $interval->format('%a');
            $install_equipment->day_amount = $days;
            if ($install_equipment->install_day_amount) {
                if (
                    in_array($install_equipment->status, [
                        InstallEquipmentStatusEnum::WAITING,
                        InstallEquipmentStatusEnum::PENDING_REVIEW,
                        InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                        InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                    ])
                ) {
                    if ($days = $install_equipment->install_day_amount) {
                        $install_equipment->status = InstallEquipmentStatusEnum::DUE;
                    }
                    if ($days > $install_equipment->install_day_amount) {
                        $install_equipment->status = InstallEquipmentStatusEnum::OVERDUE;
                        $install_equipment->day_amount = $days - $install_equipment->install_day_amount;
                    }
                }
            }
            $modified_start_date = clone $datetime_start;
            $modified_start_date->modify('+' . $install_equipment->install_day_amount . ' days');
            $install_equipment->expected_end_date = $modified_start_date->format('Y-m-d');;
        }

        $license_plate_text = null;
        $install_equipment_files = $install_equipment->getMedia('install_equipment_files');
        $install_equipment_files = get_medias_detail($install_equipment_files);
        if ($install_equipment->car) {
            $license_plate = $install_equipment->car?->license_plate ?? __('install_equipments.not_register');
            $engine_no = $install_equipment->car?->engine_no ?? __('install_equipments.not_register');
            $chassis_no = $install_equipment->car?->chassis_no ?? __('install_equipments.not_register');
            $license_plate_text = $license_plate . ' / ' . $engine_no . ' / ' . $chassis_no;
        }
        $install_equipment->license_plate = $license_plate_text;

        $install_equipment_line_list = InstallEquipmentLine::where('install_equipment_id', $install_equipment->id)->get();
        $install_equipment_line_list->map(function ($item) use ($install_equipment) {
            $item->supplier_id = $install_equipment->supplier_id;
            $item->supplier_text = '';
            if ($install_equipment->supplier) {
                $item->supplier_text = $install_equipment->supplier->name;
            }
            $item->accessory_text = '';
            $item->accessory_class = '';
            if ($item->accessory) {
                $item->accessory_text = $item->accessory->code . ' - ' . $item->accessory->name;
                $item->accessory_class = $item->accessory->version;
            }
        });
        $supplier = (object) [];
        $supplier->id = $install_equipment->supplier_id;
        $supplier->name = $install_equipment->supplier ? $install_equipment->supplier->name : '';
        $status_list = $this->getTempIntallEquipmentStatusList();
        $link_list = $this->getLinkList($install_equipment);
        $store_uri = route('admin.install-equipments.store');
        $page_title = __('lang.edit') . __('install_equipments.page_title');
        return view('admin.install-equipments.form', [
            'd' => $install_equipment,
            'install_equipment_line_list' => $install_equipment_line_list,
            'page_title' => $page_title,
            'store_uri' => $store_uri,
            'install_equipment_files' => $install_equipment_files,
            'mode' => MODE_UPDATE,
            'supplier' => $supplier,
            'status_list' => $status_list,
            'is_view_only' => !$is_allow_edit,
            'link_list' => $link_list
        ]);
    }

    public function update(Request $request, $id)
    {
        abort(404);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InstallEquipment);
        $install_equipment = InstallEquipment::find($id);
        $install_equipment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    public function getAccessoryDetail(Request $request)
    {
        $accessory = Accessories::findOrfail($request->accessory_id);
        $accessory->creditor_text = '';
        if ($accessory->creditor_id) {
            $creditor = Creditor::find($accessory->creditor_id);
            $accessory->creditor_text = $creditor ? $creditor->name : '';
        }
        return response()->json($accessory);
    }

    public function getCarDetail(Request $request)
    {
        $car = Car::findOrFail($request->car_id);
        return response()->json($car);
    }

    public function getGroupDetail(Request $request)
    {
        $result = [];
        $group_id = $request->group_id;
        if (!$group_id) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        $result['group_id'] = $group_id;
        $install_equipment = InstallEquipment::where('group_id', $group_id)->get();
        if ($install_equipment->isEmpty()) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        $first_install_equipment = $install_equipment->first();
        if (!$first_install_equipment) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        $car = Car::find($first_install_equipment->car_id);
        if (!$car) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        $result['car_code'] = $car->code ?? null;
        $result['license_plate'] = $car->license_plate ?? null;
        $result['engine_no'] = $car->engine_no ?? null;
        $result['chassis_no'] = $car->chassis_no ?? null;

        $worksheet_nos = $install_equipment->pluck('worksheet_no')->toArray();
        $worksheet_nos_as_string = implode(', ', $worksheet_nos);
        $result['worksheet_no'] = $worksheet_nos_as_string;
        return $result;
    }

    public function getCarCollection($car_id)
    {
        $car = Car::find($car_id);
        if (!$car) {
            $car = collect([]);
        }
        $car->class = $car->carClass ? $car->carClass->name : '';
        $car_images_files = $car->getMedia('car_images');
        $car_images_files = get_medias_detail($car_images_files);
        $car->image = $car_images_files[0] ?? null;
        return $car;
    }

    public function getTempIntallEquipmentStatusSearchIndexList()
    {
        $status = collect([
            (object) [
                'id' => InstallEquipmentStatusEnum::WAITING,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::WAITING),
                'value' => InstallEquipmentStatusEnum::WAITING,
            ],
            (object) [
                'id' => InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::INSTALL_IN_PROCESS),
                'value' => InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
            ],
            (object) [
                'id' => InstallEquipmentStatusEnum::OVERDUE,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::OVERDUE),
                'value' => InstallEquipmentStatusEnum::OVERDUE,
            ],
            (object) [
                'id' => InstallEquipmentStatusEnum::DUE,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::DUE),
                'value' => InstallEquipmentStatusEnum::DUE,
            ],
            (object) [
                'id' => InstallEquipmentStatusEnum::INSTALL_COMPLETE,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::INSTALL_COMPLETE),
                'value' => InstallEquipmentStatusEnum::INSTALL_COMPLETE,
            ],
            (object) [
                'id' => InstallEquipmentStatusEnum::INSPECT_IN_PROCESS,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::INSPECT_IN_PROCESS),
                'value' => InstallEquipmentStatusEnum::INSPECT_IN_PROCESS,
            ],
            (object) [
                'id' => InstallEquipmentStatusEnum::INSPECT_FAIL,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::INSPECT_FAIL),
                'value' => InstallEquipmentStatusEnum::INSPECT_FAIL,
            ],
            (object) [
                'id' => InstallEquipmentStatusEnum::REJECT,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::REJECT),
                'value' => InstallEquipmentStatusEnum::REJECT,
            ],
            (object) [
                'id' => InstallEquipmentStatusEnum::CANCEL,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::CANCEL),
                'value' => InstallEquipmentStatusEnum::CANCEL,
            ],
            (object) [
                'id' => InstallEquipmentStatusEnum::COMPLETE,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::COMPLETE),
                'value' => InstallEquipmentStatusEnum::COMPLETE,
            ],
        ]);
        return $status;
    }

    public function getTempIntallEquipmentStatusList()
    {
        $status = collect([
            (object) [
                'id' => InstallEquipmentStatusEnum::INSTALL_COMPLETE,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::INSTALL_COMPLETE),
                'value' => InstallEquipmentStatusEnum::INSTALL_COMPLETE,
            ],
            (object) [
                'id' => InstallEquipmentStatusEnum::CANCEL,
                'name' => __('install_equipments.status_' . InstallEquipmentStatusEnum::CANCEL),
                'value' => InstallEquipmentStatusEnum::CANCEL,
            ],
        ]);
        return $status;
    }

    function checkIsAllowEdit($install_equipment)
    {
        $is_allow_edit = true;
        if (!in_array($install_equipment->status, [InstallEquipmentStatusEnum::PENDING_REVIEW])) {
            $is_allow_edit = false;
        }
        $po = InstallEquipmentPurchaseOrder::where('install_equipment_id', $install_equipment->id)->latest()->first();
        if ($po) {
            if (in_array($po->status, [InstallEquipmentPOStatusEnum::CONFIRM, InstallEquipmentPOStatusEnum::COMPLETE])) {
                $is_allow_edit = false;
            }
        }
        return $is_allow_edit;
    }

    function getLinkList($install_equipment)
    {
        $link_list = [];
        $default_arr = [
            'worksheet_no' => NULL,
            'route' => NULL,
        ];
        $link_list['inspect_out'] = $default_arr;
        $link_list['inspect_in'] = $default_arr;
        $link_list['car_park_transfer'] = $default_arr;
        $link_list['driving_job'] = $default_arr;

        if (!$install_equipment) {
            return $link_list;
        }

        $po = InstallEquipmentPurchaseOrder::where('install_equipment_id', $install_equipment->id)->latest()->first();
        if ($po) {
            $inspect_out = InspectionJob::where('item_id', $po->id)
                ->where('transfer_type', TransferTypeEnum::OUT)
                ->first();
            if ($inspect_out) {
                $link_list['inspect_out']['worksheet_no'] = $inspect_out->worksheet_no;
                $link_list['inspect_out']['route'] = route('admin.install-equipments.pdf', ['inspection_job_id' => $inspect_out->id]);
                $link_list['inspect_out']['link'] = route('admin.inspection-job-steps.edit', ['inspection_job_step' => $inspect_out->id]);
            }

            $ie_inspection_line = IEInspectionInstallEquipment::where('install_equipment_id', $install_equipment->id)->get()->last();
            if ($ie_inspection_line) {
                $ie_inslection = InstallEquipmentInspection::find($ie_inspection_line->ie_inspection_id);
                if ($ie_inslection) {
                    $inspect_in = InspectionJob::where('item_id', $ie_inslection->id)
                        ->where('transfer_type', TransferTypeEnum::IN)
                        ->first();
                    if ($inspect_in) {
                        $link_list['inspect_in']['worksheet_no'] = $inspect_in->worksheet_no;
                        $link_list['inspect_in']['route'] = route('admin.install-equipments.pdf', ['inspection_job_id' => $inspect_in->id]);
                        $link_list['inspect_in']['link'] = route('admin.inspection-job-steps.edit', ['inspection_job_step' => $inspect_in->id]);
                    }
                }
            }

            $driving_job = DrivingJob::where('job_id', $install_equipment->id)->latest()->first();
            if ($driving_job) {
                $link_list['driving_job']['worksheet_no'] = $driving_job->worksheet_no;
                $link_list['driving_job']['link'] = route('admin.driving-jobs.edit', ['driving_job' => $driving_job->id]);

                $car_park_transfer = CarParkTransfer::where('driving_job_id', $driving_job->id)->latest()->first();
                if ($car_park_transfer) {
                    $link_list['car_park_transfer']['worksheet_no'] = $car_park_transfer->worksheet_no;
                    $link_list['car_park_transfer']['link'] = route('admin.car-park-transfers.edit', ['car_park_transfer' => $car_park_transfer->id]);
                }
            }
        }
        return $link_list;
    }

    public function createInspection(Request $request)
    {
        $groups = $request->group_id ? [$request->group_id] : [];
        $inspection_date = $request->inspection_date;
        if ($request->inspect_all) {
            $groups = DB::table('install_equipments')
                ->select('group_id')
                ->where('status', '=', InstallEquipmentStatusEnum::INSTALL_COMPLETE)
                ->groupBy('group_id')
                ->havingRaw('COUNT(*) = SUM(CASE WHEN status = "INSTALL_COMPLETE" THEN 1 ELSE 0 END)')
                ->pluck('group_id')
                ->toArray();
            if (sizeof($groups) <= 0) {
                return $this->responseWithCode(false, __('install_equipments.worksheet_not_found'), null, 422);
            }
        }
        foreach ($groups as $group_id) {
            $install_equipments = InstallEquipment::where('group_id', $group_id)->get();
            if ($install_equipments->isEmpty()) {
                return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
            }
            $first_item = $install_equipments->first();

            $ie_inspection_count = DB::table('install_equipment_inspections')->count() + 1;
            $prefix = 'QAI';
            $ie_inspection = new InstallEquipmentInspection;
            $ie_inspection->worksheet_no = generateRecordNumber($prefix, $ie_inspection_count);
            $ie_inspection->inspection_date = $inspection_date;
            $ie_inspection->car_id = $first_item->car_id;
            $ie_inspection->save();

            foreach ($install_equipments as $key => $install_equipment) {
                $ie_inspection_line = new IEInspectionInstallEquipment;
                $ie_inspection_line->ie_inspection_id = $ie_inspection->id;
                $ie_inspection_line->install_equipment_id = $install_equipment->id;
                $ie_inspection_line->save();
            }

            $created_inspection_job = $this->createInspectionJobs($ie_inspection->id);
            if (!$created_inspection_job) {
                return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
            }
            $install_equipment_arr = IEInspectionInstallEquipment::where('ie_inspection_id', $ie_inspection->id)->pluck('install_equipment_id')->toArray();
            InstallEquipmentTrait::updateInstallEquipmentStatus($install_equipment_arr, InstallEquipmentStatusEnum::INSPECT_IN_PROCESS);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, null, 200);
    }

    function createInspectionJobs($ie_inspection_id)
    {
        $ie_inspection = InstallEquipmentInspection::find($ie_inspection_id);
        if (!$ie_inspection) {
            return false;
        }

        $ijf = new InspectionJobFactory(InspectionTypeEnum::EQUIPMENT, InstallEquipmentInspection::class, $ie_inspection_id, $ie_inspection->car_id, [
            'inspection_must_date' => $ie_inspection->inspection_date,
            'transfer_type' => TransferTypeEnum::IN
        ]);
        $ijf->create();

        return true;
    }

    public function printInstallEquipmentPdf(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InstallEquipment);
        $form_sections = InspectionFormSection::leftjoin('inspection_forms', 'inspection_forms.id', '=', 'inspection_form_sections.inspection_form_id')
            ->where('inspection_forms.form_type', InspectionFormEnum::BEFORE_INSTALL)
            ->where('inspection_form_sections.status', STATUS_ACTIVE)
            ->orderBy('inspection_form_sections.seq', 'asc')
            ->pluck('inspection_form_sections.id')->toArray();

        $checklist_list = InspectionFormChecklist::leftjoin('inspection_form_sections', 'inspection_form_sections.id', '=', 'inspection_form_checklists.inspection_form_section_id')
            ->whereIn('inspection_form_checklists.inspection_form_section_id', $form_sections)
            ->where('inspection_form_checklists.status', STATUS_ACTIVE)
            ->orderBy('inspection_form_sections.seq', 'asc')
            ->orderBy('inspection_form_checklists.seq', 'asc')
            ->select('inspection_form_checklists.*')
            ->get();
        $inspection_job_id = $request->inspection_job_id;
        $inspection_job = InspectionJob::find($inspection_job_id);
        if (!$inspection_job) {
            return false;
        }
        $car = Car::find($inspection_job->car_id);
        $creditor_name = '';
        $install_equipments = collect([]);
        if ($inspection_job) {
            $install_equipment_ids = [];
            if (strcmp($inspection_job->item_type, InstallEquipmentPurchaseOrder::class) === 0) {
                $ie_po = InstallEquipmentPurchaseOrder::find($inspection_job->item_id);
                if ($ie_po) {
                    $install_equipment_ids = [$ie_po->install_equipment_id];
                }
            }

            if (strcmp($inspection_job->item_type, InstallEquipmentInspection::class) === 0) {
                $ie_inspection = InstallEquipmentInspection::find($inspection_job->item_id);
                if ($ie_inspection) {
                    $install_equipment_ids = IEInspectionInstallEquipment::where('ie_inspection_id', $ie_inspection->id)->pluck('install_equipment_id')->toArray();
                }
            }
            if (sizeof($install_equipment_ids) > 0) {
                $install_equipments = InstallEquipment::whereIn('id', $install_equipment_ids)->get();
                $install_equipments->map(function ($item, $key) use (&$creditor_name) {
                    $item->supplier_name = $item->supplier ? $item->supplier->name : '';
                    $install_equipment_lines = InstallEquipmentLine::where('install_equipment_id', $item->id)->get();
                    $install_equipment_lines = InstallEquipmentLine::where('install_equipment_id', $item->id)->get();
                    $item->checklist_list = $install_equipment_lines;
                    if ($key == 0) {
                        if ($item->purchaseOrder) {
                            $creditor_name = $item->purchaseOrder->creditor ? $item->purchaseOrder->creditor->name : '';
                        }
                    }
                });
            }
        }
        $pdf = PDF::loadView(
            'admin.install-equipments.pdfs.component.pdf',
            [
                'car' => $car,
                'checklist_list' => $checklist_list,
                'creditor_name' => $creditor_name,
                'install_equipments' => $install_equipments,
            ]
        );
        return $pdf->stream();
    }


    public function exportExcel(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InstallEquipment);
        $result = collect([]);
        $install_equipment_ids = $request->install_equipment_ids;
        $install_equipment_list = InstallEquipment::whereIn('id', $install_equipment_ids)->get();
        foreach ($install_equipment_list as $key => $install_equipment) {
            $install_equipment->index = $key + 1;
            $accessories = InstallEquipmentLine::leftjoin('accessories', 'accessories.id', '=', 'install_equipment_lines.accessory_id')
                ->where('install_equipment_lines.install_equipment_id', $install_equipment->id)
                ->pluck('accessories.name')
                ->toArray();
            $accessory_as_string = implode(', ', $accessories);
            $install_equipment->accessory_as_string = $accessory_as_string;
            $install_equipment->car_brand = '';
            $install_equipment->car_class = '';
            $install_equipment->car_color = '';
            $install_equipment->engine_no = '';
            $install_equipment->chassis_no = '';
            if ($install_equipment->car) {
                $install_equipment->car_brand = $install_equipment->car->carBrand ? $install_equipment->car->carBrand->name : '';
                $install_equipment->car_class = $install_equipment->car->carClass ? $install_equipment->car->carClass->name : '';
                $install_equipment->car_color = $install_equipment->car->CarColor ? $install_equipment->car->CarColor->name : '';
                $install_equipment->engine_no = $install_equipment->car->engine_no;
                $install_equipment->chassis_no = $install_equipment->car->chassis_no;
            }
        }
        $file_name = 'ข้อมูลใบสั่งซื้ออุปกรณ์.xlsx';
        if ($install_equipment->supplier) {
            $file_name = $install_equipment->supplier->name ? $install_equipment->supplier->name . '.xlsx' : $file_name;
        }
        if (count($install_equipment_list) > 0) {
            return (new FastExcel($install_equipment_list))->download('file.xlsx', function ($line) {
                return [
                    'ลำดับ' => $line->index,
                    'ยี่ห้อ' => $line->car_brand,
                    'รุ่น' => $line->car_class,
                    'สี' => $line->car_color,
                    'เลขเครื่อง' => $line->engine_no,
                    'เลขตัวถัง' => $line->chassis_no,
                    'รายการ' => $line->accessory_as_string,
                ];
            });
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function getInstallEquipmentDetail(Request $request)
    {
        if (!$request->install_equipment_id) {
            return response()->json([
                'success' => false,
            ]);
        }

        $install_equipment = InstallEquipment::find($request->install_equipment_id);
        if (!$install_equipment) {
            return response()->json([
                'success' => false,
            ]);
        }
        $install_equipment->po_worksheet_no = $install_equipment->install_equipment_po ? $install_equipment->install_equipment_po->worksheet_no : '';
        $install_equipment->supplier_name = $install_equipment->supplier ? $install_equipment->supplier->name : '';
        return response()->json([
            'success' => false,
            'data' => $install_equipment
        ]);
    }

    public function getBOMAcessories(Request $request)
    {
        $bom_id = $request->bom_id;
        $bom_accessories = BomAccessory::leftjoin('accessories', 'accessories.id', '=', 'bom_accessories.accessories_id')
            ->leftjoin('creditors', 'creditors.id', '=', 'accessories.creditor_id')
            ->where('bom_accessories.bom_id', $bom_id)
            ->select(
                'bom_accessories.id',
                'accessories.id as accessory_id',
                'accessories.version as accessory_class',
                'accessories.name as accessory_text',
                'bom_accessories.amount as amount',
                'accessories.price as price',
                'accessories.creditor_id as supplier_id',
                'creditors.name as supplier_text',
            )
            ->get();
        return response()->json($bom_accessories);
    }

    public function createDrivingJob($install_equipment_id, $car_id)
    {
        $date = new DateTime();
        $open_date = $date->format('Y-m-d H:i:s');
        $install_equipment = InstallEquipment::find($install_equipment_id);
        if (!$install_equipment) {
            return false;
        }

        $djf = new DrivingJobFactory(InstallEquipment::class, $install_equipment->id, $car_id, [
            'driver_name' => ($install_equipment->supplier) ? $install_equipment->supplier->name : '',
            'destination' => ($install_equipment->supplier) ? $install_equipment->supplier->address : '',
        ]);
        $driving_job = $djf->create();

        if ($driving_job) {
            $ctf = new CarparkTransferFactory($driving_job->id, $car_id);
            $ctf->create();
        }
    }


    public function createTransferOutInspectionJobs($install_equipment_id, $car_id, $inspection_date)
    {
        $ijf = new InspectionJobFactory(InspectionTypeEnum::EQUIPMENT, InstallEquipment::class, $install_equipment_id, $car_id, [
            'inspection_must_date' => $inspection_date,
            'transfer_type' => TransferTypeEnum::OUT
        ]);
        $ijf->create();
    }

    public function getAccessoriesPOCarList(Request $request)
    {
        $car_id = $request->car_id;
        $accessory_list = PurchaseRequisitionLineAccessory::leftjoin('accessories', 'accessories.id', '=', 'purchase_requisition_line_accessories.accessory_id')
            ->leftjoin('creditors', 'creditors.id', '=', 'accessories.creditor_id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_requisition_line_accessories.purchase_requisition_line_id')
            ->leftjoin('purchase_order_lines', 'purchase_order_lines.pr_line_id', '=', 'purchase_requisition_lines.id')
            ->leftjoin('import_car_lines', 'import_car_lines.po_line_id', '=', 'purchase_order_lines.id')
            ->select(
                'accessories.id as accessory_id',
                'accessories.name as accessory_text',
                'purchase_requisition_line_accessories.amount as amount',
                'accessories.version as accessory_class',
                'accessories.price as price',
                'accessories.creditor_id as supplier_id',
                'creditors.name as supplier_text'
            )
            ->where('import_car_lines.id', $car_id)
            ->get();
        return response()->json([
            'success' => true,
            'data' => $accessory_list,
        ]);
    }
}
