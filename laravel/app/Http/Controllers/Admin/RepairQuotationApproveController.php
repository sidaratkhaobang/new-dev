<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\RepairStatusEnum;
use App\Models\RepairOrder;
use App\Models\Repair;
use App\Models\Car;
use App\Models\RepairLine;
use App\Models\Creditor;
use App\Models\RepairList;
use App\Models\RepairOrderLine;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\RepairTrait;
use App\Traits\ReplacementCarTrait;


class RepairQuotationApproveController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::RepairQuotationApprove);
        $worksheet_no = $request->worksheet_no;
        $repair_type = $request->repair_type;
        $license_plate = $request->license_plate;
        $contact = $request->contact;
        $order_worksheet_no = $request->order_worksheet_no;
        $status = $request->status;
        $center = $request->center;
        $alert_date = $request->alert_date;
        $list = RepairOrder::leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
            ->leftJoin('cars', 'cars.id', '=', 'repairs.car_id')
            ->leftJoin('creditors', 'creditors.id', '=', 'repair_orders.center_id')
            ->select(
                'repairs.id as repairs_id',
                'repairs.worksheet_no as repair_worksheet_no',
                'repairs.repair_type',
                'repairs.repair_date',
                'repairs.contact',
                'cars.license_plate',
                'repairs.in_center_date',
                'repair_orders.id',
                'repair_orders.worksheet_no',
                'repair_orders.expected_repair_date',
                'repair_orders.repair_date as completed_date',
                'creditors.name as center',
                'repair_orders.status',
            )
            ->whereIn('repair_orders.status', [RepairStatusEnum::WAIT_APPROVE_QUOTATION, RepairStatusEnum::REJECT_QUOTATION])
            ->search($request)
            ->when($worksheet_no, function ($query) use ($worksheet_no) {
                return $query->where('repair_orders.repair_id', $worksheet_no);
            })
            ->when($license_plate, function ($query) use ($license_plate) {
                return $query->where('repairs.car_id', $license_plate);
            })
            ->when($repair_type, function ($query) use ($repair_type) {
                return $query->where('repairs.repair_type', $repair_type);
            })
            ->when($contact, function ($query) use ($contact) {
                return $query->where('repairs.contact', $contact);
            })
            ->when($alert_date, function ($query) use ($alert_date) {
                return $query->whereDate('repairs.repair_date', $alert_date);
            })
            ->orderBy('repair_orders.created_at', 'desc')
            ->paginate(PER_PAGE);

        $worksheet_no_list = RepairOrder::select('repairs.id', 'repairs.worksheet_no as name')
            ->leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
            ->whereIn('repair_orders.status', [RepairStatusEnum::WAIT_APPROVE_QUOTATION, RepairStatusEnum::REJECT_QUOTATION])
            ->orderBy('repairs.worksheet_no')->get();
        $repair_type_list = RepairTrait::getRepairType();
        $license_plate_list = Car::select('id', 'license_plate as name')->get();
        $contact_list = RepairOrder::select('repairs.contact as id', 'repairs.contact as name')
            ->leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
            ->whereIn('repair_orders.status', [RepairStatusEnum::WAIT_APPROVE_QUOTATION, RepairStatusEnum::REJECT_QUOTATION])
            ->orderBy('repairs.contact')->distinct()->get();
        $order_worksheet_no_list = RepairOrder::select('id', 'worksheet_no as name')
            ->whereIn('repair_orders.status', [RepairStatusEnum::WAIT_APPROVE_QUOTATION, RepairStatusEnum::REJECT_QUOTATION])->orderBy('worksheet_no')->get();
        $status_list = RepairTrait::getStatus();
        $center_list = RepairOrder::leftJoin('creditors', 'creditors.id', '=', 'repair_orders.center_id')
            ->select('creditors.id', 'creditors.name')
            ->whereIn('repair_orders.status', [RepairStatusEnum::WAIT_APPROVE_QUOTATION, RepairStatusEnum::REJECT_QUOTATION])
            ->orderBy('creditors.name')->distinct()->get();

        $page_title = __('repair_orders.approve_repair_order');
        return view('admin.repair-quotation-approves.index', [
            'list' => $list,
            'page_title' => $page_title,
            'worksheet_no' => $worksheet_no,
            'worksheet_no_list' => $worksheet_no_list,
            'repair_type' => $repair_type,
            'repair_type_list' => $repair_type_list,
            'license_plate' => $license_plate,
            'license_plate_list' => $license_plate_list,
            'contact' => $contact,
            'contact_list' => $contact_list,
            'order_worksheet_no' => $order_worksheet_no,
            'order_worksheet_no_list' => $order_worksheet_no_list,
            'status' => $status,
            'center' => $center,
            'center_list' => $center_list,
            'status_list' => $status_list,
            'alert_date' => $alert_date,
        ]);
    }

    public function show(RepairOrder $repair_quotation_approve)
    {
        $this->authorize(Actions::Manage . '_' . Resources::RepairQuotationApprove);
        $replacement_type_list = RepairTrait::getReplacementTypeList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $repair_no_list = Repair::select('id', 'worksheet_no as name')->get();
        $center_list = RepairTrait::getCenterList();
        $repair_lists = RepairTrait::getRepairListId();
        $have_expenses_list = RepairTrait::getHaveExpensesList();
        $car_data = new Car;
        $rental = 0;
        $repair_quotation_approve->car_license = null;
        $repair = Repair::find($repair_quotation_approve->repair_id);
        $condotion_lt_rental = [];

        if ($repair) {
            $condotion_lt_rental =  RepairTrait::getConditionQuotation($repair);
            $car_data = RepairTrait::getDataCar($repair->car_id);
            if ($car_data) {
                $rental = $car_data->rental;
                if ($car_data->license_plate) {
                    $repair_quotation_approve->car_license = $car_data->license_plate;
                } else if ($car_data->engine_no) {
                    $repair_quotation_approve->car_license = __('inspection_cars.engine_no') . ' ' . $car_data->engine_no;
                } else if ($car_data->chassis_no) {
                    $repair_quotation_approve->car_license = __('inspection_cars.chassis_no') . ' ' . $car_data->chassis_no;
                }
            }
            $repair_quotation_approve->is_replacement = $repair->is_replacement;
            $repair_quotation_approve->replacement_date = $repair->replacement_date;
            $repair_quotation_approve->replacement_type = $repair->replacement_type;
            $repair_quotation_approve->replacement_place = $repair->replacement_place;
            $repair_quotation_approve->in_center = $repair->in_center;
            $repair_quotation_approve->in_center_date = $repair->in_center_date;
            $repair_quotation_approve->is_driver_in_center = $repair->is_driver_in_center;
            $repair_quotation_approve->out_center = $repair->out_center;
            $repair_quotation_approve->out_center_date = $repair->out_center_date;
            $repair_quotation_approve->is_driver_out_center = $repair->is_driver_out_center;

            $repair_documents_media = $repair->getMedia('repair_documents');
            $repair_documents_files = get_medias_detail($repair_documents_media);
        }

        $creditor = Creditor::find($repair_quotation_approve->center_id);
        $repair_quotation_approve->center_address = null;
        if ($creditor) {
            $address =  ($creditor->address) ? $creditor->address : null;
            $mobile =  ($creditor->mobile) ? $creditor->mobile : null;
            $repair_quotation_approve->center_address = ($address) ? $address . '(' . $mobile . ')' : null;
        }

        $repair_line = RepairLine::where('repair_id', $repair_quotation_approve->repair_id)
            ->get()->map(function ($item) {
                $item->check_text = __('check_distances.type_text_' . $item->check);
                return $item;
            });

        $repair_order_line = RepairOrderLine::where('repair_order_id', $repair_quotation_approve->id)
            ->get()->map(function ($item) {
                $item->check_text = __('check_distances.type_text_' . $item->check);
                $item->date = ($item->created_at) ? date('Y-m-d', strtotime($item->created_at)) : null;
                $repair_list = RepairList::find($item->repair_list_id);
                $item->code_name = null;
                if ($repair_list) {
                    $item->code_name = $repair_list->code . ' ' . $repair_list->name;
                }
                return $item;
            });

        $approve_line_list = new StepApproveManagement();
        $approve_return = $approve_line_list->logApprove(RepairOrder::class, $repair_quotation_approve->id);
        $approve_line_list = $approve_return['approve_line_list'];
        $approve = $approve_return['approve'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(RepairOrder::class, $repair_quotation_approve->id);
        } else {
            $approve_line_owner = null;
        }

        $user_file = null;
        $expense_file_media = $repair_quotation_approve->getMedia('expense_files');
        $expense_files = get_medias_detail($expense_file_media);
        if (isset($expense_file_media[0]->custom_properties['created_by'])) {
            $user_id = $expense_file_media[0]->custom_properties['created_by'];
            $user = User::find($user_id);
            if ($user) {
                $user_file = $user->name;
            }
        }

        $route_group = [
            'tab_repair_order' => route('admin.repair-quotation-approves.show', ['repair_quotation_approve' => $repair_quotation_approve]),
            'tab_condition' => route('admin.repair-order-conditions.show', ['repair_order_condition' => $repair_quotation_approve]),
        ];
        $page_title =  __('lang.view') . __('repair_orders.approve_repair_order');
        return view('admin.repair-quotation-approves.form', [
            'd' => $repair_quotation_approve,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'check_list' => $check_list,
            'repair_type_list' => $repair_type_list,
            'service_center_list' => $service_center_list,
            'is_need_driver' => $is_need_driver,
            'repair_no_list' => $repair_no_list,
            'condotion_lt_rental' => $condotion_lt_rental,
            'car_data' => $car_data,
            'rental' => $rental,
            'center_list' => $center_list,
            'repair_lists' => $repair_lists,
            'edit' => true,
            'repair' => $repair,
            'repair_line' => $repair_line,
            'repair_order_line_list' => $repair_order_line,
            'have_expenses_list' => $have_expenses_list,
            'route_group' => $route_group,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'view' => true,
            'expense_files' => $expense_files,
            'user_file' => $user_file,
            'repair_documents_files' => $repair_documents_files,
        ]);
    }

    public function store(Request $request)
    {
        $repair_order = RepairOrder::find($request->id);
        if (!$repair_order) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        $validator = Validator::make($request->all(), [
            'status_update' => 'required',
            'reject_reason' => 'required_if:status_update,REJECT'
        ], [
            'required_if' => 'กรุณากรอก :attribute'
        ], [
            'status_update' => __('lang.status'),
            'reject_reason' => 'เหตุผลการไม่อนุมัติ',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        // update approve step
        $approve_update = new StepApproveManagement();
        // $approve_update = $approve_update->updateApprove($request, $repair_order, $request->status_update, RepairOrder::class);
        $approve_update = $approve_update->updateApprove(RepairOrder::class, $repair_order->id, $request->status_update,null,null);


        if (strcmp($approve_update, 'PENDING_REVIEW') === 0) {
            $approve_update = RepairStatusEnum::WAIT_APPROVE_QUOTATION;
        }
        if (strcmp($approve_update, 'CONFIRM') === 0) {
            $approve_update = RepairStatusEnum::IN_PROCESS;
        }
        if (strcmp($approve_update, 'REJECT') === 0) {
            $approve_update = RepairStatusEnum::REJECT_QUOTATION;
        }
        $repair_order->status = $approve_update;
        $repair_order->save();

        $redirect_route = route('admin.repair-quotation-approves.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
