<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CreditorTypeEnum;
use App\Enums\ImportCarLineStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarCharacteristic;
use App\Models\CarParkTransfer;
use App\Models\Creditor;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Models\InspectionJob;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisitionLineAccessory;
use Illuminate\Http\Request;

class PrepareNewCarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ImportCarList);
        $s = $request->s;
        $dealer = $request->dealer;
        $purchase_order_no = $request->purchase_order_no;
        $engine_no = $request->engine_no;
        $chassis_no = $request->chassis_no;
        $location = $request->location;
        $status = $request->status;
        $po_no = $request->po_no;
        $delivery_location = $request->delivery_location;
        $from_delivery_date = $request->from_delivery_date;
        $to_delivery_date = $request->to_delivery_date;
        $status_list = $this->getImportCarStatus();
        $status = null;
        if (strcmp($request->status, "0") == 0) {
            $status = 0;
        } else {
            $status = $request->status;
        }
        $lists = ImportCarLine::sortable('po_no')
            ->leftJoin('purchase_order_lines', 'purchase_order_lines.id', '=', 'import_car_lines.po_line_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftJoin('creditors', 'creditors.id', '=', 'purchase_orders.creditor_id')
            ->addSelect('import_car_lines.*', 'purchase_orders.po_no', 'creditors.name as creditor_name', 'purchase_orders.id as po_id')
            ->where(function ($q) use ($dealer, $status) {
                if (!is_null($dealer)) {
                    $q->where('purchase_orders.creditor_id', $dealer);
                }
                if (!is_null($status)) {
                    $q->where('import_car_lines.status_delivery', $status);
                }
            })
            ->where('import_car_lines.status_delivery', ImportCarLineStatusEnum::PENDING_DELIVERY)
            ->orWhere('import_car_lines.status', ImportCarLineStatusEnum::CONFIRM_DATA)
            ->search($s, $request)
            ->paginate(PER_PAGE);
        $object = $lists;
        // dd($object);
        $dealer_list = Creditor::leftjoin('creditors_types_relation', 'creditors_types_relation.creditor_id', '=', 'creditors.id')
            ->leftjoin('creditor_types', 'creditor_types.id', '=', 'creditors_types_relation.creditor_type_id')
            ->select('creditors.id', 'creditors.name')
            ->where('creditor_types.type', CreditorTypeEnum::DEALER)
            ->where('creditors.status', STATUS_ACTIVE)->get();
        $engine_no_list = ImportCarLine::where('status_delivery', ImportCarLineStatusEnum::PENDING_DELIVERY)->select('engine_no as name', 'id')->orderBy('engine_no')->get();
        $chassis_no_list = ImportCarLine::where('status_delivery', ImportCarLineStatusEnum::PENDING_DELIVERY)->select('chassis_no as name', 'id')->orderBy('chassis_no')->get();
        $po_list = ImportCarLine::leftJoin('purchase_order_lines', 'purchase_order_lines.id', '=', 'import_car_lines.po_line_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->where('import_car_lines.status_delivery', ImportCarLineStatusEnum::PENDING_DELIVERY)
            ->select('purchase_orders.po_no as name', 'purchase_orders.id')->orderBy('purchase_orders.po_no')->distinct()->get();
        $delivery_location_list = ImportCarLine::where('status_delivery', ImportCarLineStatusEnum::PENDING_DELIVERY)->select('delivery_location as name', 'delivery_location as id')
            ->groupBy('delivery_location')->get();
        $car_park_transfer = CarParkTransfer::leftJoin('driving_jobs', 'driving_jobs.id', '=', 'car_park_transfers.driving_job_id')
            ->whereIn('driving_jobs.job_id', $object->pluck('import_car_id'))
            ->where('driving_jobs.job_type', ImportCar::class)
            ->select('car_park_transfers.worksheet_no', 'car_park_transfers.car_id', 'car_park_transfers.id')
            ->get();
        $inspection_job = InspectionJob::whereIn('item_id', $object->pluck('po_id'))
            ->where('item_type', PurchaseOrder::class)
            ->select('worksheet_no', 'car_id', 'id')
            ->get();
        $car_characteristic_list = CarCharacteristic::select('id', 'name')->get();
        $car_category_list = CarCategory::select('id', 'name')->get();
        return view('admin.prepare-new-cars.index', [
            's' => $request->s,
            'dealer_list' => $dealer_list,
            'dealer' => $dealer,
            'lists' => $lists,
            'purchase_order_no' => $purchase_order_no,
            'engine_no' => $engine_no,
            'chassis_no' => $chassis_no,
            'status' => $status,
            'status_list' => $status_list,
            'location' => $location,
            'from_delivery_date' => $from_delivery_date,
            'to_delivery_date' => $to_delivery_date,
            'object' => $object,
            'engine_no_list' => $engine_no_list,
            'chassis_no_list' => $chassis_no_list,
            'engine_no' => $engine_no,
            'chassis_no' => $chassis_no,
            'po_list' => $po_list,
            'po_no' => $po_no,
            'delivery_location_list' => $delivery_location_list,
            'delivery_location' => $delivery_location,
            'car_park_transfer' => $car_park_transfer,
            'inspection_job' => $inspection_job,
            'car_characteristic_list' => $car_characteristic_list,
            'car_category_list' => $car_category_list,
        ]);
    }

    public static function getImportCarStatus()
    {
        $status = collect([
            // (object) [
            //     'id' => ImportCarLineStatusEnum::PENDING,
            //     'name' => __('import_cars.status_' . ImportCarStatusEnum::PENDING),
            //     'value' => ImportCarLineStatusEnum::PENDING,
            // ],
            // (object) [
            //     'id' => ImportCarLineStatusEnum::PENDING_REVIEW,
            //     'name' => __('import_cars.status_' . ImportCarLineStatusEnum::PENDING_REVIEW),
            //     'value' => ImportCarLineStatusEnum::PENDING_REVIEW,
            // ],
            (object)[
                'id' => ImportCarLineStatusEnum::PENDING_DELIVERY,
                'name' => __('import_car_lines.status_' . ImportCarLineStatusEnum::PENDING_DELIVERY),
                'value' => ImportCarLineStatusEnum::PENDING_DELIVERY,
            ],
            (object)[
                'id' => ImportCarLineStatusEnum::SUCCESS_DELIVERY,
                'name' => __('import_car_lines.status_' . ImportCarLineStatusEnum::SUCCESS_DELIVERY),
                'value' => ImportCarLineStatusEnum::SUCCESS_DELIVERY,
            ],
        ]);
        return $status;
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    public function getDataModal(Request $request)
    {
        $import_car_line = ImportCarLine::find($request->car_line_id);
        // dd($request->car_line_id);

        $car_park_transfer = CarParkTransfer::leftJoin('driving_jobs', 'driving_jobs.id', '=', 'car_park_transfers.driving_job_id')
            ->where('driving_jobs.job_id', $import_car_line->import_car_id)
            ->where('driving_jobs.job_type', ImportCar::class)
            ->where('car_park_transfers.car_id', $import_car_line->id)
            ->select('car_park_transfers.worksheet_no')
            ->get();

        $po_id = ImportCarLine::leftJoin('import_cars', 'import_cars.id', '=', 'import_car_lines.import_car_id')
            ->where('import_car_lines.id', $import_car_line->id)
            ->select('import_cars.po_id as po_id')
            ->get();
        $inspection_job = InspectionJob::whereIn('item_id', $po_id->pluck('po_id'))
            ->where('item_type', PurchaseOrder::class)
            ->where('car_id', $import_car_line->id)
            ->select('worksheet_no')
            ->get();

        $accessory_pr = PurchaseRequisitionLineAccessory::leftjoin('accessories', 'accessories.id', '=', 'purchase_requisition_line_accessories.accessory_id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_requisition_line_accessories.purchase_requisition_line_id')
            ->leftjoin('purchase_order_lines', 'purchase_order_lines.pr_line_id', '=', 'purchase_requisition_lines.id')
            ->leftjoin('import_car_lines', 'import_car_lines.po_line_id', '=', 'purchase_order_lines.id')
            ->select('accessories.name as accessory_name', 'accessories.version', 'purchase_requisition_line_accessories.amount as pr_line_acc_amount', 'purchase_order_lines.id as po_line_id')
            ->where('import_car_lines.id', $request->car_line_id)
            ->get();
        return response()->json([
            'car_line' => $import_car_line,
            'accessory' => $accessory_pr,
            'car_worksheet_no' => $car_park_transfer,
            'job_worksheet_no' => $inspection_job,
            'characteristic_list_id' => $import_car_line?->car?->carCharacteristic?->id,
            // 'message' => 'ok',
            // 'redirect' => view('admin.import-cars.form')
        ]);
    }

    public function updateCarDetail(Request $request)
    {
        $import_car_line_id = $request->id;
        $registration_type = $request->registration_type;
        $car_characteristic = $request->car_characteristic;
        $import_car_line = ImportCarLine::findOrFail($import_car_line_id);
        $import_car_line->registration_type = $registration_type;
        $import_car_line->save();
        if (!empty($car_characteristic) || !empty($registration_type)) {
            $this->updateCarData($import_car_line_id, $car_characteristic, $registration_type);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $import_car_line, 200);
    }

    public function updateCarData($import_car_line_id, $car_characteristic = null, $registration_type = null)
    {
        if (empty($import_car_line_id)) {
            return false;
        }
        $car_model = Car::find($import_car_line_id);
        if ($car_model) {
            $car_model->car_characteristic_id = $car_characteristic;
            $car_model->car_category_id = $registration_type;
            $car_model->save();
        }
    }

    public function getPrepareCarData(Request $request)
    {
        $po_no = $request->po_no;
        $car_id = $request->car_id;
        $delivery_date = $request->delivery_date;
        $status = $request->status;
        $id_cars = $request->id_cars;
        $car_data = ImportCarLine::when(!empty($po_no), function ($query_search) use ($po_no) {
            $query_search->whereHas('importCar.purchaseOrder', function ($query) use ($po_no) {
                $query->where('po_id', $po_no);
            });
        })
            ->when(!empty($car_id), function ($query_search) use ($car_id) {
                $query_search->where('id', $car_id);
            })
            ->when(!empty($delivery_date), function ($query_search) use ($delivery_date) {
                $query_search->where('delivery_date', $delivery_date);
            })
            ->when(!empty($status), function ($query_search) use ($status) {
                $query_search->where('status_delivery', $status);
            })
            ->when(!empty($id_cars), function ($query_search) use ($id_cars) {
                $query_search->whereNotin('id', $id_cars);
            })
            ->whereNull('lot_id')
            ->get()
            ->map(function ($item) {
                $item->po_no = $item?->importCar?->purchaseOrder?->po_no ?? '-';
                $item->po_id = $item?->importCar?->purchaseOrder?->id ?? '-';
                $item->engine_no = $this->getCarData($item?->id)?->engine_no ?? '-';
                $item->chassis_no = $this->getCarData($item?->id)?->chassis_no ?? '-';
                $item->car_characteristics = $item?->car?->carCharacteristic?->name ?? '-';
                $item->badge_status = __('prepare_new_cars.status_' . $item?->status_delivery);
                $item->badge_class = __('prepare_new_cars.class_' . $item?->status_delivery);
                return $item;
            });

        return response()->json([
            'data' => $car_data,
            'success' => true,
        ]);

    }

    public function getCarData($car_id)
    {
        $car_data = Car::find($car_id);
        return $car_data;
    }
}
