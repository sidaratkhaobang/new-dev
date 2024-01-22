<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MaintenanceStatusEnum;
use App\Enums\RepairStatusEnum;
use App\Exports\ExportMainTenance;
use App\Http\Controllers\Controller;
use App\Models\Repair;
use App\Models\RepairOrder;
use App\Models\RepairOrderLine;
use App\Traits\FinanceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class MaintenanceCostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = __('maintenance_costs.list') . __('maintenance_costs.page_title');
        $worksheet_no = $request->worksheet_no;
        $worksheet_no_name = $this->getWorksheetNoName($worksheet_no);
        $center = $request->center;
        $center_name = FinanceTrait::getCreditorName($center);;
        $geographie = $request->geographie;
        $geographie_name = $this->getGeoGraphieName($geographie);
        $invoice_no = $request->invoice_no;
        $invoice_no_name = $this->getInvoiceNoName($invoice_no);
        $in_center_date = $request->in_center_date;
        $end_date = $request->end_date;
        $car = $request->car;
        $car_name = FinanceTrait::getCarName($car);
        $status = $request->status;
        $status_list = $this->getStatus();
        $list = $this->getRepairList($worksheet_no, $center, $geographie, $invoice_no, $in_center_date, $end_date, $car, $status);
        return view('admin.maintenance-costs.index', [
            'page_title' => $page_title,
            'worksheet_no' => $worksheet_no,
            'worksheet_no_name' => $worksheet_no_name,
            'center' => $center,
            'center_name' => $center_name,
            'geographie' => $geographie,
            'geographie_name' => $geographie_name,
            'invoice_no' => $invoice_no,
            'invoice_no_name' => $invoice_no_name,
            'in_center_date' => $in_center_date,
            'end_date' => $end_date,
            'car' => $car,
            'car_name' => $car_name,
            'status_list' => $status_list,
            'list' => $list,
            'status' => $status,

        ]);
    }

    public function getWorksheetNoName($worksheet_no)
    {
        $worksheet_no_name = null;
        $worksheet_no_name = Repair::find($worksheet_no)?->worksheet_no;

        return $worksheet_no_name;
    }

    public function getGeoGraphieName($geographie)
    {
        $geographie_name = null;
        $geographie_name = DB::table('geographies')->where('id', $geographie)->first()?->name;
        return $geographie_name;
    }

    public function getInvoiceNoName($invoice_no)
    {
        $invoice_no_name = null;
        $invoice_no_name = RepairOrder::find($invoice_no)?->invoice_no;

        return $invoice_no_name;
    }

    public function getStatus()
    {
        return collect([
            (object)[
                'id' => MaintenanceStatusEnum::PENDING,
                'name' => __('maintenance_costs.status_' . MaintenanceStatusEnum::PENDING),
                'value' => MaintenanceStatusEnum::PENDING,
            ],
            (object)[
                'id' => MaintenanceStatusEnum::SUCCESS,
                'name' => __('maintenance_costs.status_' . MaintenanceStatusEnum::SUCCESS),
                'value' => MaintenanceStatusEnum::SUCCESS,
            ],
        ]);
    }

    public function getRepairList($worksheet_no, $center, $geographie, $invoice_no, $in_center_date, $end_date, $car, $status)
    {
        $repair_list = RepairOrder::where('status', RepairStatusEnum::COMPLETED)
            ->when(!empty($worksheet_no), function ($query_search) use ($worksheet_no) {
                $query_search->whereHas('repair', function ($query) use ($worksheet_no) {
                    $query->where('id', $worksheet_no);
                });
            })
            ->when(!empty($center), function ($query_search) use ($center) {
                $query_search->where('center_id', $center);
            })
            ->when(!empty($geographie), function ($query_search) use ($geographie) {
                $query_search->whereHas('creditor.province', function ($query) use ($geographie) {
                    $query->where('geography_id', $geographie);
                });
            })
            ->when(!empty($invoice_no), function ($query_search) use ($invoice_no) {
                $query_search->where('id', $invoice_no);
            })
            ->when(!empty($in_center_date), function ($query_search) use ($in_center_date) {
                $query_search->whereHas('repair', function ($query) use ($in_center_date) {
                    $query->whereDate('in_center_date', $in_center_date);
                });
            })
            ->when(!empty($end_date), function ($query_search) use ($end_date) {
                $query_search->whereHas('repair', function ($query) use ($end_date) {
                    $query->whereDate('out_center_date', $end_date);
                });
            })
            ->when(!empty($car), function ($query_search) use ($car) {
                $query_search->whereHas('repair', function ($query) use ($car) {
                    $query->where('car_id', $car);
                });
            })
            ->when(!empty($status), function ($query_search) use ($status) {
                $query_search->where('status', $status);
            })
            ->paginate(PER_PAGE);
        $repair_list->getCollection()->transform(function ($item) {
            $geographie_data = DB::table('geographies')
                ->where('id', $item?->creditor?->province?->geography_id)
                ->first();
            $item->geographie_name = $geographie_data?->name;
            return $item;
        });
        return $repair_list;
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
        $validator = Validator::make($request->all(), [
            'invoice_no' => ['required'],
            'actual_mileage' => ['required'],
            'sub_total' => ['required'],
            'discount_extra' => ['required'],
            'vat' => ['required'],
            'rubber_week' => ['required'],
            'repair_list' => ['required'],
        ], [], [
            'invoice_no' => __('maintenance_costs.table_invoice_no'),
            'actual_mileage' => __('maintenance_costs.actual_mileage'),
            'sub_total' => __('maintenance_costs.sub_total'),
            'discount_extra' => __('maintenance_costs.discount_extra'),
            'vat' => __('maintenance_costs.vat'),
            'rubber_week' => __('maintenance_costs.rubber_week'),
            'repair_list' => __('maintenance_costs.title_repair_list')

        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $validator = Validator::make($request->repair_list, [
            '*.price_total' => ['required'],
            '*.discount' => ['required'],
            '*.add_debt' => ['required'],
            '*.reduce_debt' => ['required'],
        ], [], [
            '*.price_total' => __('maintenance_costs.table_invoice_no'),
            '*.discount' => __('maintenance_costs.actual_mileage'),
            '*.add_debt' => __('maintenance_costs.sub_total'),
            '*.reduce_debt' => __('maintenance_costs.discount_extra'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $repair_order = RepairOrder::findOrFail($request->repair_oreder_id);
        $repair_order->invoice_no = $request->invoice_no;
        $repair_order->actual_mileage = $request->actual_mileage;
        $repair_order->sub_total = $request->sub_total;
        $repair_order->discount = $request->discount_extra;
        $repair_order->percent_vat = $request->vat;
        $repair_order->vat = $request->vat_price;
        $repair_order->rubber_week = $request->rubber_week;
        $repair_order->remark_expenses = $request->remark_expenses;
        $repair_order->save();
        if (!empty($request->repair_list)) {
            foreach ($request->repair_list as $key => $value) {
                $repair_order_line = RepairOrderLine::findOrNew($value['id']);
                $repair_order_line->repair_order_id = $repair_order?->id;
                $repair_order_line->repair_list_id = $value['repair_list_id'];
                $repair_order_line->price = $value['price_total'];
                $repair_order_line->discount = $value['discount'];
                $repair_order_line->add_debt = $value['add_debt'];
                $repair_order_line->reduce_debt = $value['reduce_debt'];
                $repair_order_line->save();
            }
        }
        if ($repair_order->status == MaintenanceStatusEnum::PENDING) {
            $repair_order->status = MaintenanceStatusEnum::SUCCESS;
            $repair_order->save();
        }
        return $this->responseWithCode(true, DATA_SUCCESS, [], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(RepairOrder $maintenance_cost)
    {
        $d = $maintenance_cost;
        $page_title = __('lang.edit') . __('maintenance_costs.page_title');
        $list = collect([]);
        $view = true;
        $car_name = FinanceTrait::getCarName($d?->repair?->car?->id);
        $repair_order_line = RepairOrderLine::select('*')
            ->where('repair_order_id', $d?->id)
            ->get()
            ->map(function ($item) {
                $item->repair_list_name = $item?->repair_list?->name;
                $item->price_total = $item?->price;
                $item->discount = $item->discount;
                $item->total_repair_price = $this->calTotalRepairPrice($item);
                $item->total_discount = $this->calDiscount($item);
                $item->created_at_new = Carbon::parse($item->created_at)->toDateString();
                return $item;
            });
        return view('admin.maintenance-costs.form', [
            'page_title' => $page_title,
            'd' => $d,
            'list' => $list,
            'repair_order_line' => $repair_order_line,
            'view' => $view,
            'car_name' => $car_name
        ]);
    }

    public function calTotalRepairPrice($repair_data)
    {
        $total_repair_price = 0;
        if (!empty($repair_data?->price)) {
            $total_repair_price = ($repair_data?->price * $repair_data?->amount) - ($repair_data?->discount + $repair_data?->add_debt - $repair_data?->reduce_debt);
        }
        return $total_repair_price;
    }

    public function calDiscount($repair_data)
    {

        $total_discount = '0';
        if (!empty($repair_data?->price) && !empty($repair_data?->discount) && !empty($repair_data?->amount)) {
            $total_discount = ($repair_data?->price * $repair_data?->amount * $repair_data?->discount) / 100;
        }
        return $total_discount;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(RepairOrder $maintenance_cost)
    {
        $d = $maintenance_cost;
        $page_title = __('lang.edit') . __('maintenance_costs.page_title');
        $list = collect([]);
        $car_name = FinanceTrait::getCarName($d?->repair?->car?->id);
        $repair_order_line = RepairOrderLine::select('*')
            ->where('repair_order_id', $d?->id)
            ->get()
            ->map(function ($item) {
                $item->repair_list_name = $item?->repair_list?->name;
                $item->price_total = $item?->price;
                $item->discount = $item->discount;
                $item->total_repair_price = $this->calTotalRepairPrice($item);
                $item->total_discount = $this->calDiscount($item);
                $item->created_at_new = Carbon::parse($item->created_at)->toDateString();
                return $item;
            });
        return view('admin.maintenance-costs.form', [
            'page_title' => $page_title,
            'd' => $d,
            'list' => $list,
            'repair_order_line' => $repair_order_line,
            'car_name' => $car_name,
        ]);
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

    public function getExcelData(Request $request)
    {
        $worksheet_no = $request->worksheet_no;
        $center = $request->center;
        $geographie = $request->geographie;
        $invoice_no = $request->invoice_no;
        $in_center_date = $request->in_center_date;
        $end_date = $request->end_date;
        $car = $request->car;
        $status = $request->status;
        $repair_list_id = $request->repair_list_id;
        $repair_list = RepairOrder::where('status', RepairStatusEnum::COMPLETED)
            ->when(!empty($repair_list_id), function ($query_search) use ($repair_list_id) {
                $query_search->whereNotin('id', $repair_list_id);
            })
            ->when(!empty($worksheet_no), function ($query_search) use ($worksheet_no) {
                $query_search->whereHas('repair', function ($query) use ($worksheet_no) {
                    $query->where('id', $worksheet_no);
                });
            })
            ->when(!empty($center), function ($query_search) use ($center) {
                $query_search->where('center_id', $center);
            })
            ->when(!empty($geographie), function ($query_search) use ($geographie) {
                $query_search->whereHas('creditor.province', function ($query) use ($geographie) {
                    $query->where('geography_id', $geographie);
                });
            })
            ->when(!empty($invoice_no), function ($query_search) use ($invoice_no) {
                $query_search->where('id', $invoice_no);
            })
            ->when(!empty($in_center_date), function ($query_search) use ($in_center_date) {
                $query_search->whereHas('repair', function ($query) use ($in_center_date) {
                    $query->whereDate('in_center_date', $in_center_date);
                });
            })
            ->when(!empty($end_date), function ($query_search) use ($end_date) {
                $query_search->whereHas('repair', function ($query) use ($end_date) {
                    $query->whereDate('out_center_date', $end_date);
                });
            })
            ->when(!empty($car), function ($query_search) use ($car) {
                $query_search->whereHas('repair', function ($query) use ($car) {
                    $query->where('car_id', $car);
                });
            })
            ->when(!empty($status), function ($query_search) use ($status) {
                $query_search->where('status', $status);
            })
            ->get()
            ->map(function ($item) {
                $item->worksheet_no = $item?->repair?->worksheet_no;
                $item->creditor_name = $item?->creditor?->name;
                $geographie_data = DB::table('geographies')
                    ->where('id', $item?->creditor?->province?->geography_id)
                    ->first();
                $item->geographie_name = $geographie_data?->name;
                $item->license_plate = $item?->car?->license_plate;
                $item->engine_no = $item?->car?->engine_no;
                $item->chassis_no = $item?->car?->chassis_no;
                $item->status = __('maintenance_costs.status_' . MaintenanceStatusEnum::PENDING);
                return $item;
            });
        return response()->json([
            'data' => $repair_list,
            'success' => true,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $repair_list_id = $request->repair_list_id;
        $repair_list = [];
        $repair_list = RepairOrder::whereIn('id', $repair_list_id)
            ->get()
            ->map(function ($item) {
                $geographie_data = DB::table('geographies')
                    ->where('id', $item?->creditor?->province?->geography_id)
                    ->first();
                $item->repair_item = $this->getRepairItem($item?->id);
                $item->geographie_name = $geographie_data?->name;
                $item->total_item = count($item->repair_item);
                $item->total_price_item = $item?->repair_order_lines->sum('price');
                $item->total_add_debt = $item?->repair_order_lines->sum('add_debt');
                $item->total_reduce_debt = $item?->repair_order_lines->sum('reduce_debt');
                return $item;
            });
        return Excel::download(new ExportMainTenance($repair_list), 'template.xlsx');
    }

    public function getRepairItem($repair_id)
    {
        if (empty($repair_id)) {
            return [];
        }
        $repair_item = RepairOrder::select(
            'repair_order_lines.repair_list_id',
            DB::raw('sum(repair_order_lines.price) as price'),
            DB::raw('sum(repair_order_lines.add_debt) as add_debt'),
            DB::raw('sum(repair_order_lines.reduce_debt) as reduce_debt'),
        )
            ->leftjoin(
                'repair_order_lines',
                'repair_order_lines.repair_order_id',
                'repair_orders.id')
            ->leftjoin(
                'repairs',
                'repairs.id',
                'repair_orders.repair_id')
            ->where('repair_orders.id', $repair_id)
            ->groupBy('repairs.car_id', 'repair_orders.center_id', 'repair_order_lines.repair_list_id',)
            ->get();
        return $repair_item;
    }
}
