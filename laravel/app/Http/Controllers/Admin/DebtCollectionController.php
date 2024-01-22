<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\DebtCollectionSubStatusEnum;
use App\Enums\DebtCollectionChannelTypeEnum;
use App\Enums\DebtCollectionStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\CheckBillingStatus;
use App\Models\CustomerGroupRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Invoice;
use App\Models\DebtCollectionStatus;
use App\Models\LongTermRental;
use App\Models\Rental;

class DebtCollectionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::DebtCollection);
        $invoice_no = $request->invoice_no;
        $customer_code = $request->customer_code;
        $customer_name = $request->customer_name;
        $customer_group = $request->customer_group;
        $status = $request->status;
        $latest_due_date = $request->latest_due_date;
        $list = Invoice::select(
            'id',
            'invoice_no',
            'customer_id',
            'customer_code',
            'customer_name',
            'status',
            'status_debt_collection',
            'sub_total',
            'due_date',
        )
            ->when(!empty($invoice_no), function ($query_search) use ($invoice_no) {
                $query_search->where('id', $invoice_no);
            })
            ->when(!empty($customer_code), function ($query_search) use ($customer_code) {
                $query_search->where('customer_code', $customer_code);
            })
            ->when(!empty($customer_name), function ($query_search) use ($customer_name) {
                $query_search->where('customer_name', $customer_name);
            })
            ->when(!empty($status), function ($query_search) use ($status) {
                $query_search->where('status_debt_collection', $status);
            })
            ->paginate(PER_PAGE);
        $list->map(function ($item) {
            $customer_group = CustomerGroupRelation::where('customer_id', $item->customer_id)
                ->leftJoin('customer_groups', 'customer_groups.id', '=', 'customers_groups_relation.customer_group_id')
                ->select('customer_groups.name')->get();
            $item->customer_group = $customer_group;
            $item->due_date = get_thai_date_format($item->due_date, 'd/m/Y');
            $item->sub_total = ($item->sub_total) ? number_format($item->sub_total, 2, '.', ',') : null;
            return $item;
        });

        $invoice_no_list = Invoice::select('invoice_no as name', 'id')->whereNotNull('invoice_no')->get();
        $customer_code_list = Invoice::select('customer_code as name', 'customer_code as id')->whereNotNull('customer_code')->get();
        $customer_name_list = Invoice::select('customer_name as name', 'customer_name as id')->whereNotNull('customer_name')->get();
        $status_list = $this->getStatusList();

        $page_title = __('debt_collections.page_title');
        return view('admin.debt-collections.index', [
            'page_title' => $page_title,
            'list' => $list,
            'invoice_no' => $invoice_no,
            'customer_code' => $customer_code,
            'customer_name' => $customer_name,
            'customer_group' => $customer_group,
            'status_list' => $status_list,
            'status' => $status,
            'latest_due_date' => $latest_due_date,
            'invoice_no_list' => $invoice_no_list,
            'customer_code_list' => $customer_code_list,
            'customer_name_list' => $customer_name_list,
        ]);
    }

    public function edit(Invoice $debt_collection)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DebtCollection);

        if (strcmp($debt_collection->job_type, Rental::class) == 0) {
            $rental = Rental::find($debt_collection->job_id);
            $debt_collection->worksheet_no = ($rental) ? $rental->worksheet_no : null;
            $debt_collection->contract_start_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y') : null;
            $debt_collection->contract_end_date = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y') : null;
            $debt_collection->rental = true;
        }
        if (strcmp($debt_collection->job_type, LongTermRental::class) == 0) {
            $lt_rental = LongTermRental::find($debt_collection->job_id);
            $debt_collection->worksheet_no = ($lt_rental) ? $lt_rental->worksheet_no : null;
            $debt_collection->contract_start_date = ($lt_rental) ? get_thai_date_format($lt_rental->contract_start_date, 'd/m/Y') : null;
            $debt_collection->contract_end_date = ($lt_rental) ? get_thai_date_format($lt_rental->contract_end_date, 'd/m/Y') : null;
            $debt_collection->lt_rental = true;
        }
        $debt_collection_line = DebtCollectionStatus::where('invoice_id', $debt_collection->id)->get();
        $customer_group = CustomerGroupRelation::where('customer_id', $debt_collection->customer_id)
            ->leftJoin('customer_groups', 'customer_groups.id', '=', 'customers_groups_relation.customer_group_id')
            ->select('customer_groups.name')->get();
        $check_billing_status_list = CheckBillingStatus::leftJoin('check_billing_dates', 'check_billing_dates.id', '=', 'check_billing_status.check_billing_date_id')
            ->where('check_billing_dates.invoice_id', $debt_collection->id)
            ->select('check_billing_status.*')->get()->map(function ($item) {
                $item->sending_billing_date = ($item->sending_billing_date) ? get_thai_date_format($item->sending_billing_date, 'd/m/Y') : null;
                $item->check_billing_date = ($item->check_billing_date) ? get_thai_date_format($item->check_billing_date, 'd/m/Y') : null;
                return $item;
            });
        $channel_list = $this->getChannelList();
        $status_list = $this->getStatusList();
        $page_title = __('lang.edit') . __('debt_collections.page_title');
        return view('admin.debt-collections.form', [
            'd' => $debt_collection,
            'page_title' => $page_title,
            'channel_list' => $channel_list,
            'status_list' => $status_list,
            'debt_collection_line' => $debt_collection_line,
            'customer_group' => $customer_group,
            'check_billing_status_list' => $check_billing_status_list,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_debt_collections' => [
                'required', 'array', 'min:1'
            ],
            'data_debt_collections.*.channel' => [
                'required',
            ],
        ], [], [
            'data_debt_collections' => __('debt_collections.table_channel'),
            'data_debt_collections.*.channel' => __('debt_collections.channel'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if ($request->id) {
            if ($request->del_section != null) {
                DebtCollectionStatus::where('invoice_id', $request->id)->whereIn('id', $request->del_section)->delete();
            }
            if (!empty($request->data_debt_collections)) {
                foreach ($request->data_debt_collections as $item_debt_status) {
                    if ($item_debt_status['id'] != null) {
                        $debt_collection_status = DebtCollectionStatus::firstOrNew(['id' => $item_debt_status['id']]);
                    } else {
                        $debt_collection_status = new DebtCollectionStatus();
                    }
                    $debt_collection_status->invoice_id = $request->id;
                    $debt_collection_status->notification_date = $item_debt_status['notification_date'] ? $item_debt_status['notification_date'] : date('Y-m-d');
                    $debt_collection_status->channel = $item_debt_status['channel'];
                    $debt_collection_status->detail = $item_debt_status['detail'];
                    $debt_collection_status->save();
                }
            }
        }

        $redirect_route = route('admin.debt-collections.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(Invoice $debt_collection)
    {
        $this->authorize(Actions::View . '_' . Resources::DebtCollection);

        if (strcmp($debt_collection->job_type, Rental::class) == 0) {
            $rental = Rental::find($debt_collection->job_id);
            $debt_collection->worksheet_no = ($rental) ? $rental->worksheet_no : null;
            $debt_collection->contract_start_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y') : null;
            $debt_collection->contract_end_date = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y') : null;
            $debt_collection->rental = true;
        }
        if (strcmp($debt_collection->job_type, LongTermRental::class) == 0) {
            $lt_rental = LongTermRental::find($debt_collection->job_id);
            $debt_collection->worksheet_no = ($lt_rental) ? $lt_rental->worksheet_no : null;
            $debt_collection->contract_start_date = ($lt_rental) ? get_thai_date_format($lt_rental->contract_start_date, 'd/m/Y') : null;
            $debt_collection->contract_end_date = ($lt_rental) ? get_thai_date_format($lt_rental->contract_end_date, 'd/m/Y') : null;
            $debt_collection->lt_rental = true;
        }
        $debt_collection_line = DebtCollectionStatus::where('invoice_id', $debt_collection->id)->get();
        $customer_group = CustomerGroupRelation::where('customer_id', $debt_collection->customer_id)
            ->leftJoin('customer_groups', 'customer_groups.id', '=', 'customers_groups_relation.customer_group_id')
            ->select('customer_groups.name')->get();
        $check_billing_status_list = CheckBillingStatus::leftJoin('check_billing_dates', 'check_billing_dates.id', '=', 'check_billing_status.check_billing_date_id')
            ->where('check_billing_dates.invoice_id', $debt_collection->id)
            ->select('check_billing_status.*')->get()->map(function ($item) {
                $item->sending_billing_date = ($item->sending_billing_date) ? get_thai_date_format($item->sending_billing_date, 'd/m/Y') : null;
                $item->check_billing_date = ($item->check_billing_date) ? get_thai_date_format($item->check_billing_date, 'd/m/Y') : null;
                return $item;
            });
        $channel_list = $this->getChannelList();
        $status_list = $this->getStatusList();
        $page_title = __('lang.view') . __('debt_collections.page_title');
        return view('admin.debt-collections.form', [
            'd' => $debt_collection,
            'page_title' => $page_title,
            'channel_list' => $channel_list,
            'status_list' => $status_list,
            'debt_collection_line' => $debt_collection_line,
            'customer_group' => $customer_group,
            'check_billing_status_list' => $check_billing_status_list,
            'view' => true,
        ]);
    }

    public function printPdfInvoice(Request $request)
    {
        //
    }

    public function getStatusList()
    {
        return collect([
            (object) [
                'id' => DebtCollectionStatusEnum::PENDING,
                'name' => __('debt_collections.status_' . DebtCollectionStatusEnum::PENDING),
                'value' => DebtCollectionStatusEnum::PENDING,
            ],
            (object) [
                'id' => DebtCollectionStatusEnum::WAITING,
                'name' => __('debt_collections.status_' . DebtCollectionStatusEnum::WAITING),
                'value' => DebtCollectionStatusEnum::WAITING,
            ],
            (object) [
                'id' => DebtCollectionStatusEnum::COMPLETE,
                'name' => __('debt_collections.status_' . DebtCollectionStatusEnum::COMPLETE),
                'value' => DebtCollectionStatusEnum::COMPLETE,
            ],
            (object) [
                'id' => DebtCollectionStatusEnum::OVERDUE,
                'name' => __('debt_collections.status_' . DebtCollectionStatusEnum::OVERDUE),
                'value' => DebtCollectionStatusEnum::OVERDUE,
            ],
        ]);
    }

    public function getChannelList()
    {
        return collect([
            (object) [
                'id' => DebtCollectionChannelTypeEnum::EMAIL,
                'name' => __('debt_collections.channel_' . DebtCollectionChannelTypeEnum::EMAIL),
                'value' => DebtCollectionChannelTypeEnum::EMAIL,
            ],
            (object) [
                'id' => DebtCollectionChannelTypeEnum::PHONE,
                'name' => __('debt_collections.channel_' . DebtCollectionChannelTypeEnum::PHONE),
                'value' => DebtCollectionChannelTypeEnum::PHONE,
            ],
        ]);
    }
}
