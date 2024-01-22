<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CheckBillingStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CheckBillingDate;
use App\Models\CheckBillingStatus;
use App\Models\CustomerGroupRelation;
use App\Models\Invoice;
use App\Models\LongTermRental;
use App\Models\Rental;
use App\Models\RentalLine;
use Carbon\Carbon;

class CheckBillingController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CheckBillingDate);
        $invoice_no = $request->invoice_no;
        $credit_note_no = $request->credit_note_no;
        $customer_name = $request->customer_name;
        $license_plate = $request->license_plate;
        $from_billing_date = $request->from_billing_date;
        $to_billing_date = $request->to_billing_date;
        $period_no = $request->period_no;
        $schedule_billing = $request->schedule_billing;
        $status = $request->status;
        $list = CheckBillingDate::leftJoin('invoices', 'invoices.id', '=', 'check_billing_dates.invoice_id')
            ->select(
                'invoices.invoice_no',
                'invoices.customer_name',
                'invoices.sub_total',
                'check_billing_dates.*',
            )->paginate(PER_PAGE);

        $page_title = __('check_billings.page_title');
        return view('admin.check-billings.index', [
            'page_title' => $page_title,
            'list' => $list,
            'invoice_no' => $invoice_no,
            'credit_note_no' => $credit_note_no,
            'customer_name' => $customer_name,
            'license_plate' => $license_plate,
            'from_billing_date' => $from_billing_date,
            'to_billing_date' => $to_billing_date,
            'period_no' => $period_no,
            'schedule_billing' => $schedule_billing,
            'status' => $status,
        ]);
    }

    public function edit(CheckBillingDate $check_billing)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DebtCollection);
        $customer_group = [];
        $check_billing->worksheet_no = null;
        $check_billing->contract_start_date = null;
        $check_billing->contract_end_date =  null;
        $check_billing->rental = false;
        $check_billing->lt_rental = false;
        if (!empty($check_billing->invoice_id)) {
            $invoice = Invoice::find($check_billing->invoice_id);
            if ($invoice) {
                $check_billing->customer_name = $invoice->customer_name;
                $check_billing->customer_code = $invoice->customer_code;
                $check_billing->customer_tax_no = $invoice->customer_tax_no;
                $check_billing->customer_address = $invoice->customer_address;
                $customer_group = CustomerGroupRelation::where('customer_id', $invoice->customer_id)
                    ->leftJoin('customer_groups', 'customer_groups.id', '=', 'customers_groups_relation.customer_group_id')
                    ->select('customer_groups.name')->get();
                if (strcmp($invoice->job_type, Rental::class) == 0) {
                    $rental = Rental::find($invoice->job_id);
                    $check_billing->worksheet_no = ($rental) ? $rental->worksheet_no : null;
                    $check_billing->contract_start_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y') : null;
                    $check_billing->contract_end_date = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y') : null;
                    $check_billing->rental = true;
                    $rental_car = RentalLine::where('rental_id', $rental->id)->first();
                    if ($rental_car) {
                        $car = Car::find($rental_car->car_id);
                        $check_billing->registered_date = ($car) ? get_thai_date_format($rental->registered_date, 'd/m/Y') : null;
                        $car_age = Carbon::now()->diff($car->registered_date);
                        $check_billing->car_age = $car_age->y . " ปี " . $car_age->m . " เดือน " . $car_age->d . " วัน";
                        $check_billing->car_class_name = $car->carClass ? $car->carClass->full_name : null;
                        $check_billing->license_plate = $car ? $car->license_plate : null;
                        $check_billing->engine_no = $car ? $car->engine_no : null;
                        $check_billing->chassis_no = $car ? $car->chassis_no : null;
                        $check_billing->car_status = $car ? __('cars.status_' . $car->status) : null;
                    }
                }
                //ToDo
                // if (strcmp($invoice->job_type, LongTermRental::class) == 0) {
                //     $lt_rental = LongTermRental::find($invoice->job_id);
                //     $check_billing->worksheet_no = ($lt_rental) ? $lt_rental->worksheet_no : null;
                //     $check_billing->contract_start_date = ($lt_rental) ? get_thai_date_format($lt_rental->contract_start_date, 'd/m/Y') : null;
                //     $check_billing->contract_end_date = ($lt_rental) ? get_thai_date_format($lt_rental->contract_end_date, 'd/m/Y') : null;
                //     $check_billing->lt_rental = true;
                // }
            }
        }

        $check_billing_status_line = CheckBillingStatus::where('check_billing_date_id', $check_billing->id)->get();
        $status_list = $this->getStatusList();
        $page_title = __('lang.edit') . __('check_billings.page_title');
        return view('admin.check-billings.form', [
            'd' => $check_billing,
            'page_title' => $page_title,
            'customer_group' => $customer_group,
            'status_list' => $status_list,
            'check_billing_status_line' => $check_billing_status_line,
        ]);
    }

    public function show(CheckBillingDate $check_billing)
    {
        $this->authorize(Actions::View . '_' . Resources::DebtCollection);
        $customer_group = [];
        $check_billing->worksheet_no = null;
        $check_billing->contract_start_date = null;
        $check_billing->contract_end_date =  null;
        $check_billing->rental = false;
        $check_billing->lt_rental = false;
        if (!empty($check_billing->invoice_id)) {
            $invoice = Invoice::find($check_billing->invoice_id);
            if ($invoice) {
                $check_billing->customer_name = $invoice->customer_name;
                $check_billing->customer_code = $invoice->customer_code;
                $check_billing->customer_tax_no = $invoice->customer_tax_no;
                $check_billing->customer_address = $invoice->customer_address;
                $customer_group = CustomerGroupRelation::where('customer_id', $invoice->customer_id)
                    ->leftJoin('customer_groups', 'customer_groups.id', '=', 'customers_groups_relation.customer_group_id')
                    ->select('customer_groups.name')->get();
                if (strcmp($invoice->job_type, Rental::class) == 0) {
                    $rental = Rental::find($invoice->job_id);
                    $check_billing->worksheet_no = ($rental) ? $rental->worksheet_no : null;
                    $check_billing->contract_start_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y') : null;
                    $check_billing->contract_end_date = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y') : null;
                    $check_billing->rental = true;
                    $rental_car = RentalLine::where('rental_id', $rental->id)->first();
                    if ($rental_car) {
                        $car = Car::find($rental_car->car_id);
                        $check_billing->registered_date = ($car) ? get_thai_date_format($rental->registered_date, 'd/m/Y') : null;
                        $car_age = Carbon::now()->diff($car->registered_date);
                        $check_billing->car_age = $car_age->y . " ปี " . $car_age->m . " เดือน " . $car_age->d . " วัน";
                        $check_billing->car_class_name = $car->carClass ? $car->carClass->full_name : null;
                        $check_billing->license_plate = $car ? $car->license_plate : null;
                        $check_billing->engine_no = $car ? $car->engine_no : null;
                        $check_billing->chassis_no = $car ? $car->chassis_no : null;
                        $check_billing->car_status = $car ? __('cars.status_' . $car->status) : null;
                    }
                }
                //ToDo
                // if (strcmp($invoice->job_type, LongTermRental::class) == 0) {
                //     $lt_rental = LongTermRental::find($invoice->job_id);
                //     $check_billing->worksheet_no = ($lt_rental) ? $lt_rental->worksheet_no : null;
                //     $check_billing->contract_start_date = ($lt_rental) ? get_thai_date_format($lt_rental->contract_start_date, 'd/m/Y') : null;
                //     $check_billing->contract_end_date = ($lt_rental) ? get_thai_date_format($lt_rental->contract_end_date, 'd/m/Y') : null;
                //     $check_billing->lt_rental = true;
                // }
            }
        }

        $check_billing_status_line = CheckBillingStatus::where('check_billing_date_id', $check_billing->id)->get();
        $status_list = $this->getStatusList();
        $page_title = __('lang.edit') . __('check_billings.page_title');
        return view('admin.check-billings.form', [
            'd' => $check_billing,
            'page_title' => $page_title,
            'customer_group' => $customer_group,
            'status_list' => $status_list,
            'check_billing_status_line' => $check_billing_status_line,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_check_billing_status' => [
                'required', 'array', 'min:1'
            ],
            'data_check_billing_status.*.check_billing_date' => [
                'required',
            ],
            'data_check_billing_status.*.status' => [
                'required',
            ],
        ], [], [
            'data_check_billing_status' => __('check_billings.table_status'),
            'data_check_billing_status.*.check_billing_date' => __('check_billings.check_billing_date'),
            'data_check_billing_status.*.status' => __('lang.status'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if ($request->id) {
            $check_billing = CheckBillingDate::find($request->id);
            $check_billing->document = $request->document;
            $check_billing->remark = $request->remark;
            $check_billing->save();

            if ($request->del_section != null) {
                CheckBillingStatus::where('check_billing_date_id', $check_billing->id)->whereIn('id', $request->del_section)->delete();
            }
            if (!empty($request->data_check_billing_status)) {
                foreach ($request->data_check_billing_status as $item_billing_status) {
                    if ($item_billing_status['id'] != null) {
                        $check_billing_status = CheckBillingStatus::firstOrNew(['id' => $item_billing_status['id']]);
                    } else {
                        $check_billing_status = new CheckBillingStatus();
                    }
                    $check_billing_status->check_billing_date_id = $check_billing->id;
                    $check_billing_status->sending_billing_date = $item_billing_status['sending_billing_date'] ? $item_billing_status['sending_billing_date'] : date('Y-m-d');
                    $check_billing_status->check_billing_date = $item_billing_status['check_billing_date'] ? $item_billing_status['check_billing_date'] : date('Y-m-d');
                    $check_billing_status->detail = $item_billing_status['detail'];
                    $check_billing_status->status = $item_billing_status['status'];
                    $check_billing_status->save();
                }
            }
        }

        $redirect_route = route('admin.check-billings.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function getStatusList()
    {
        return collect([
            (object) [
                'id' => CheckBillingStatusEnum::SUCCESS,
                'name' => __('check_billings.sub_status_' . CheckBillingStatusEnum::SUCCESS),
                'value' => CheckBillingStatusEnum::SUCCESS,
            ],
            (object) [
                'id' => CheckBillingStatusEnum::UNSUCCESS,
                'name' => __('check_billings.sub_status_' . CheckBillingStatusEnum::UNSUCCESS),
                'value' => CheckBillingStatusEnum::UNSUCCESS,
            ],
        ]);
    }
}
