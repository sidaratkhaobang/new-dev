<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Enums\Actions;
use App\Enums\CarEnum;
use App\Enums\CreditorTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\ImportCarLineStatusEnum;
use App\Enums\ImportCarStatusEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\LongTermRentalTypeAccessoryEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\Resources;
use App\Enums\SelfDriveTypeEnum;
use App\Http\Controllers\Controller;
use App\Jobs\EmailJob;
use App\Models\Car;
use App\Models\CarAccessory;
use App\Models\CarParkTransfer;
use App\Models\Creditor;
use App\Models\DrivingJob;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Models\InspectionFlow;
use App\Models\InspectionJob;
use App\Models\InspectionStep;
use App\Models\LongTermRental;
use App\Models\LongTermRentalPRCar;
use App\Models\LongTermRentalPRLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLine;
use App\Models\PurchaseRequisitionLineAccessory;
use App\Traits\InspectionTrait;
use App\Traits\NotificationTrait;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Factories\DrivingJobFactory;
use App\Factories\CarparkTransferFactory;
use App\Factories\InspectionJobFactory;

class ImportCarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ImportCar);
        $s = $request->s;
        $dealer = $request->dealer;
        $purchase_order_no = $request->purchase_order_no;
        $purchase_requisition_no = $request->purchase_requisition_no;
        $car_category_id = $request->car_category_id;
        $status = $request->status;
        $rental_type = null;
        $status_list = $this->getImportCarStatus();
        if (strcmp($request->rental_type, "0") == 0) {
            $rental_type = 0;
        } else {
            $rental_type = $request->rental_type;
        }

        $status = null;
        if (strcmp($request->status, "0") == 0) {
            $status = 0;
        } else {
            $status = $request->status;
        }
        $rental_type_list = PurchaseOrderOpenController::getRentalType();
        $lists = ImportCar::sortable(['created_at' => 'desc'])
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'import_cars.po_id')
            ->leftjoin('purchase_requisitions', 'purchase_orders.pr_id', '=', 'purchase_requisitions.id')
            // ->leftJoin('purchase_requisition_lines', 'purchase_requisition_lines.purchase_requisition_id', '=', 'purchase_requisitions.id')
            ->leftJoin('purchase_order_lines', 'purchase_order_lines.purchase_order_id', '=', 'purchase_orders.id')
            ->leftJoin('creditors', 'creditors.id', '=', 'purchase_orders.creditor_id')
            ->selectRaw(
                '
            import_cars.id,
            purchase_orders.po_no,
            purchase_orders.require_date,
            import_cars.status,
            purchase_orders.total,
            purchase_requisitions.pr_no as pr_no,
            purchase_requisitions.rental_type as rental_type,
            creditors.name as creditor_name,
            SUM(purchase_order_lines.amount) as total_amount'
            )
            ->groupBy(
                'import_cars.id',
                'purchase_orders.po_no',
                'purchase_orders.require_date',
                'import_cars.status',
                'purchase_orders.total',
                'pr_no',
                'rental_type',
                'creditor_name'
            )
            ->where(function ($q) use ($rental_type, $dealer, $status, $purchase_requisition_no) {
                if (!is_null($rental_type)) {
                    $q->where('purchase_requisitions.rental_type', $rental_type);
                }
                if (!is_null($dealer)) {
                    $q->where('purchase_orders.creditor_id', $dealer);
                }
                if (!is_null($status)) {
                    $q->where('import_cars.status', $status);
                }
                if (!empty($purchase_requisition_no)) {
                    $q->where('purchase_requisitions.pr_no', 'like', '%' . $purchase_requisition_no . '%');
                }
            })
            // ->where('purchase_orders.status',POStatusEnum::CONFIRM)
            ->branch()
            ->search($s, $request)
            ->paginate(PER_PAGE);

        $lists->map(function ($item) use ($rental_type_list) {
            $rental_type = findObjectById($rental_type_list, $item->rental_type);
            $item->rental_type = ($rental_type) ? $rental_type->name : '';
            return $item;
        });

        $dealer_list = Creditor::leftjoin('creditors_types_relation', 'creditors_types_relation.creditor_id', '=', 'creditors.id')
            ->leftjoin('creditor_types', 'creditor_types.id', '=', 'creditors_types_relation.creditor_type_id')
            ->select('creditors.id', 'creditors.name')
            ->where('creditor_types.type', CreditorTypeEnum::DEALER)
            ->where('creditors.status', STATUS_ACTIVE)->get();
        return view('admin.import-cars.index', [
            's' => $request->s,
            'dealer_list' => $dealer_list,
            'dealer' => $dealer,
            'lists' => $lists,
            'purchase_order_no' => $purchase_order_no,
            'purchase_requisition_no' => $purchase_requisition_no,
            'car_category_id' => $car_category_id,
            'status' => $status,
            'status_list' => $status_list,
            'rental_type_list' => $rental_type_list,
            'rental_type' => $rental_type,
        ]);
    }

    public static function getImportCarStatus()
    {
        $status = collect([
            (object)[
                'id' => ImportCarStatusEnum::PENDING,
                'name' => __('import_cars.status_' . ImportCarStatusEnum::PENDING),
                'value' => ImportCarStatusEnum::PENDING,
            ],
            (object)[
                'id' => ImportCarStatusEnum::PENDING_REVIEW,
                'name' => __('import_cars.status_' . ImportCarStatusEnum::PENDING_REVIEW),
                'value' => ImportCarStatusEnum::PENDING_REVIEW,
            ],
            (object)[
                'id' => ImportCarStatusEnum::SENT_REVIEW,
                'name' => __('import_cars.status_' . ImportCarStatusEnum::SENT_REVIEW),
                'value' => ImportCarStatusEnum::SENT_REVIEW,
            ],
            (object)[
                'id' => ImportCarStatusEnum::WAITING_DELIVERY,
                'name' => __('import_cars.status_' . ImportCarStatusEnum::WAITING_DELIVERY),
                'value' => ImportCarStatusEnum::WAITING_DELIVERY,
            ],
            (object)[
                'id' => ImportCarStatusEnum::DELIVERY_COMPLETE,
                'name' => __('import_cars.status_' . ImportCarStatusEnum::DELIVERY_COMPLETE),
                'value' => ImportCarStatusEnum::DELIVERY_COMPLETE,
            ],
            (object)[
                'id' => ImportCarStatusEnum::CANCEL,
                'name' => __('import_cars.status_' . ImportCarStatusEnum::CANCEL),
                'value' => ImportCarStatusEnum::CANCEL,
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
        $this->authorize(Actions::Manage . '_' . Resources::ImportCar);
        $purchase_requisition_cars = PurchaseOrderLine::select('purchase_order_lines.id', 'purchase_order_lines.name', 'purchase_order_lines.amount', 'purchase_order_lines.subtotal', 'purchase_order_lines.discount', 'purchase_order_lines.total')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
            // ->leftjoin('purchase_requisitions', 'purchase_requisitions.id', '=', 'purchase_orders.pr_id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_order_lines.pr_line_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'purchase_requisition_lines.car_color_id')
            ->addSelect('car_colors.name as color_name', 'pr_id')
            ->where('import_cars.id', $request->import_id)
            ->get();
        $idPr = $purchase_requisition_cars->first()->pr_id;
        $modelPurchaseRequisition = PurchaseRequisition::find($idPr);
        $arr_ob = array();
        for ($i = 0; $i < count($purchase_requisition_cars); $i++) {
            $arr_ob[$purchase_requisition_cars[$i]->id] = [];
        }

        $import_car_lines = ImportCarLine::where('import_car_lines.import_car_id', $request->import_id)->get();
        $import_car_save = ImportCar::find($request->import_id);
        if (!($import_car_save->status == ImportCarStatusEnum::SENT_REVIEW && $request->status == ImportCarStatusEnum::PENDING_REVIEW)) {
            $import_car_save->status = $request->status;
            $import_car_save->save();
        }
        $totalFistSectionUpdate = 0;
        $totalConfirmUpdate = 0;
        foreach ($purchase_requisition_cars as $index => $item) {
            $index0 = 0;
            foreach ($import_car_lines as $index2 => $import_car_line) {
                if (strcmp($item->id, $import_car_line->po_line_id) == 0) {
                    if ($request->installation_completed_date[$item->id][$index0] != null) {
                        $date = new DateTime($request->installation_completed_date[$item->id][$index0]);
                        $date_install_new = $date->format('Y-m-d');
                    } else {
                        $date_install_new = null;
                    }

                    if ($request->delivery_date_request[$item->id][$index0] != null) {
                        $date2 = new DateTime($request->delivery_date_request[$item->id][$index0]);
                        $date_delivery_new = $date2->format('Y-m-d');
                    } else {
                        $date_delivery_new = null;
                    }
                    $import_car_lines_save = ImportCarLine::find($import_car_line->id);

                    if (($import_car_lines_save->engine_no != "" || null) && ($import_car_lines_save->chassis_no != "" || null) && ($import_car_lines_save->install_date != "" || null)) {
                        if ($request->status_car_line[$item->id][$index0] == ImportCarLineStatusEnum::CONFIRM_DATA) {
                            $import_car_lines_save->status = ImportCarLineStatusEnum::CONFIRM_DATA;
                        } else if ($request->status_car_line[$item->id][$index0] == ImportCarLineStatusEnum::REJECT_DATA && $import_car_lines_save->status == ImportCarLineStatusEnum::REJECT_DATA) {
                            if (($import_car_lines_save->engine_no != $request->engine_no[$item->id][$index0]) || ($import_car_lines_save->chassis_no != $request->chassis_no[$item->id][$index0]) || ($import_car_lines_save->install_date != $date_install_new)) {
                                if (($request->engine_no[$item->id][$index0] == "" || null) || ($request->chassis_no[$item->id][$index0] == "" || null) || ($date_install_new == "" || null)) {
                                    $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING;
                                } else {
                                    //                                    $totalPendingUpdate++;
                                    $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING_REVIEW;
                                    $import_car_lines_save->reject_reason = NULL;
                                }
                            } else {
                                $import_car_lines_save->status = ImportCarLineStatusEnum::REJECT_DATA;
                            }
                        } else if ($request->status_car_line[$item->id][$index0] == ImportCarLineStatusEnum::REJECT_DATA) {
                            $import_car_lines_save->status = ImportCarLineStatusEnum::REJECT_DATA;
                            $import_car_lines_save->reject_reason = $request->reject_reason_text[$item->id][$index0] == null ? '' : $request->reject_reason_text[$item->id][$index0];
                        } else if (($request->engine_no[$item->id][$index0] == "" || null) || ($request->chassis_no[$item->id][$index0] == "" || null) || ($date_install_new == "" || null)) {
                            $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING;
                        } else {
                            if ($request->status_draft[$item->id][$index0] == ImportCarLineStatusEnum::CONFIRM_DATA) {
                                $totalFistSectionUpdate++;
                                $import_car_lines_save->status = ImportCarLineStatusEnum::CONFIRM_DATA;
                            } else {
                                //                                $totalPendingUpdate++;
                                $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING_REVIEW;
                            }
                        }
                    } else {
                        if (($request->engine_no[$item->id][$index0] != "" || null) && ($request->chassis_no[$item->id][$index0] != "" || null) && ($date_install_new != "" || null)) {
                            //                            $totalPendingUpdate++;
                            $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING_REVIEW;
                        } else {
                            $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING;
                        }
                    }

                    if ((($date_delivery_new != null || "") || ($import_car_lines_save->delivery_date != null || "")) && (($request->delivery_place[$item->id][$index0] != null || "") || ($import_car_lines_save->delivery_location != null || ""))) {
                        if (($date_delivery_new == null || "") || ($request->delivery_place[$item->id][$index0] == null || "")) {
                            $this->createCar($import_car_line->id, CarEnum::NEWCAR);
                            $import_car_lines_save->status_delivery = ImportCarLineStatusEnum::PENDING;
                        } else {
                            if ($import_car_lines_save->status_delivery == ImportCarLineStatusEnum::PENDING) {
                                // $this->createCar($import_car_line->id, CarEnum::PENDING_REVIEW);
                                $totalConfirmUpdate++;
                                $import_car_lines_save->status_delivery = ImportCarLineStatusEnum::PENDING_DELIVERY;
                            }
                            // $this->autoModelDrivingJob($import_car_line->id, $import_car_save->id);
                        }
                    }

                    $import_car_lines_save->engine_no = $request->engine_no[$item->id][$index0] == null ? '' : $request->engine_no[$item->id][$index0];
                    $import_car_lines_save->chassis_no = $request->chassis_no[$item->id][$index0] == null ? '' : $request->chassis_no[$item->id][$index0];
                    $import_car_lines_save->install_date = $date_install_new;
                    $import_car_lines_save->delivery_date = $date_delivery_new;
                    $import_car_lines_save->delivery_location = $request->delivery_place[$item->id][$index0] == null ? '' : $request->delivery_place[$item->id][$index0];
                    $import_car_lines_save->remark = $request->remark_line[$item->id][$index0] == null ? '' : $request->remark_line[$item->id][$index0];
                    $import_car_lines_save->save();
                    if (($request->status_draft[$item->id][$index0] == ImportCarLineStatusEnum::CONFIRM_DATA) && ($import_car_lines_save->status_delivery != ImportCarLineStatusEnum::PENDING_DELIVERY)) {
                        $this->createCar($import_car_line->id, CarEnum::NEWCAR);
                    }

                    if (($import_car_lines_save->status_delivery === ImportCarLineStatusEnum::PENDING_DELIVERY) && ($import_car_lines_save->delivery_date != null)) {
                        $this->autoModelDrivingJob($import_car_line->id, $import_car_save->id);
                    }

                    // }
                    $index0++;
                }
            }
        }
        if (!empty($totalFistSectionUpdate)) {
            $totalCarPending = $this->getTotalCarByStatus($request->import_id, ImportCarLineStatusEnum::CONFIRM_DATA);
            $this->sendNotificationReadyCar($import_car_save, $modelPurchaseRequisition->id, $totalCarPending);
        }
        if (!empty($totalConfirmUpdate)) {
            $totalCarConfirm = $this->getTotalCarByStatusDelivery($request->import_id, ImportCarLineStatusEnum::PENDING_DELIVERY);
            $this->sendNotificationReadyCarDelivery($modelPurchaseRequisition, $modelPurchaseRequisition->id, $totalCarConfirm);
        }


        $redirect_view = route('admin.import-cars.index');
        // return $this->responseValidateSuccess($redirect_view);
        return $this->responseValidateSuccess('');
    }

    public function createCar($import_car_line_id, $status_car)
    {
        $import_car_line = ImportCarLine::select('import_car_lines.*')
            ->leftjoin('import_cars', 'import_cars.id', '=', 'import_car_lines.import_car_id')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'import_cars.po_id')
            ->leftjoin('purchase_requisitions', 'purchase_requisitions.id', '=', 'purchase_orders.pr_id')
            ->leftjoin('purchase_order_lines', 'purchase_order_lines.id', '=', 'import_car_lines.po_line_id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_order_lines.pr_line_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'purchase_requisition_lines.car_color_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'purchase_requisition_lines.car_class_id')
            ->leftjoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
            ->addSelect(
                'car_colors.id as car_color_id',
                'car_classes.id as car_class_id',
                'purchase_requisitions.rental_type as pr_rental_type',
                'car_types.car_brand_id',
                'car_types.car_category_id',
                'car_types.car_group_id',
                'purchase_requisition_lines.id as pr_line_id',
                'purchase_order_lines.total as po_line_total',
                'purchase_order_lines.amount as po_line_amount',
            )
            ->where('import_car_lines.id', $import_car_line_id)
            ->first();
        $car_old = Car::find($import_car_line_id);
        $car = Car::firstOrNew(['id' => $import_car_line_id]);
        $is_new_car = $car->exists ? false : true;
        $car->engine_no = $import_car_line->engine_no;
        $car->chassis_no = $import_car_line->chassis_no;
        $car->car_color_id = $import_car_line->car_color_id;
        $car->status = $status_car;
        $car->car_class_id = $import_car_line->car_class_id;
        $car->car_brand_id = $import_car_line->car_brand_id;
        // $car->car_categorie_id = $import_car_lines->car_category_id;
        // $car->car_group_id = $import_car_lines->car_group_id;
        $car->rental_type = $import_car_line->pr_rental_type;
        $car->purchase_price = $import_car_line->po_line_total / $import_car_line->po_line_amount;
        $car->save();
        if ($is_new_car) {
            $this->saveLongTermRentalCars($import_car_line);
        }
        if (!$car_old) {
            $pr_line_accessory = PurchaseRequisitionLineAccessory::where('purchase_requisition_line_id', $import_car_line->pr_line_id)->get();
            foreach ($pr_line_accessory as $index => $item) {
                if (strcmp($item->type_accessories, LongTermRentalTypeAccessoryEnum::ATTACHMENT) == 0) {
                    $car_accessory = new CarAccessory();
                    $car_accessory->car_id = $car->id;
                    $car_accessory->accessory_id = $item->accessory_id;
                    $car_accessory->amount = $item->amount;
                    $car_accessory->remark = $item->remark;
                    $car_accessory->type_accessories = $item->type_accessories;
                    $car_accessory->save();
                }
            }
        }
    }

    public function saveLongTermRentalCars($import_car_line)
    {
        if (!$import_car_line) {
            return;
        }

        $po_line_id = $import_car_line->po_line_id;
        if (!$po_line_id) {
            return;
        }

        $po_line = PurchaseOrderLine::find($po_line_id);
        if (!$po_line) {
            return;
        }

        $pr_line = PurchaseRequisitionLine::find($po_line->pr_line_id);
        if (!$pr_line) {
            return;
        }

        $pr = PurchaseRequisition::find($pr_line->purchase_requisition_id);
        if (!$pr) {
            return;
        }
        if (strcmp($pr->reference_type, LongTermRental::class) != 0) {
            return;
        }

        $lt_rental = LongTermRental::find($pr->reference_id);
        if (!$lt_rental) {
            return;
        }

        $lt_pr_lines = LongTermRentalPRLine::leftjoin('lt_rental_lines', 'lt_rental_lines.id', '=', 'lt_rental_pr_lines.lt_rental_line_id')
            ->where('lt_rental_pr_lines.lt_rental_id', $lt_rental->id)
            ->select(
                'lt_rental_pr_lines.id as id',
                'lt_rental_lines.car_class_id as car_class_id',
                'lt_rental_lines.car_color_id as car_color_id',
                'lt_rental_pr_lines.amount',
            )->get();

        if (sizeof($lt_pr_lines) <= 0) {
            return;
        }
        foreach ($lt_pr_lines as $key => $lt_pr_line) {
            $count_cars = LongTermRentalPRCar::where('lt_rental_pr_line_id', '=', $lt_pr_line->id)->count();
            $lt_pr_line->amount_left = $lt_pr_line->amount - $count_cars;
        }

        $filtered_lt_pr_line = $lt_pr_lines->where('car_class_id', $import_car_line->car_class_id)
            ->where('car_color_id', $import_car_line->car_color_id)
            ->where('amount_left', '>', 0)
            ->first();

        if ($filtered_lt_pr_line) {
            $lt_pr_car = new LongTermRentalPRCar;
            $lt_pr_car->lt_rental_pr_line_id = $filtered_lt_pr_line->id;
            $lt_pr_car->car_id = $import_car_line->id;
            $lt_pr_car->save();
        }
    }

    public function autoModelDrivingJob($import_car_line, $import_car_id)
    {
        $car = Car::find($import_car_line);
        $import_car = ImportCar::find($import_car_id);
        $import_car_line_status = ImportCarLine::find($import_car_line);

        if (($import_car_line_status->status_delivery === ImportCarLineStatusEnum::PENDING_DELIVERY) && ($import_car_line_status->delivery_date != null)) {
            $driving_job_first = DrivingJob::where('job_id', $import_car_line)
                ->where('job_type', ImportCarLine::class)
                ->where('car_id', $import_car_line)->first();

            if ($driving_job_first == null) {
                $djf = new DrivingJobFactory(ImportCarLine::class, $import_car_line, $car->id, [
                    'start_date' => $import_car_line_status->delivery_date,
                    'destination' => ($import_car_line_status->delivery_location) ? $import_car_line_status->delivery_location : null,
                ]);
                $driving_job = $djf->create();

                if ($driving_job) {
                    $car_park_transfer_first = CarParkTransfer::where('driving_job_id', $driving_job->id)
                        ->where('car_id', $driving_job->car_id)->first();

                    if ($car_park_transfer_first == null) {
                        $ctf = new CarparkTransferFactory($driving_job->id, $car->id);
                        $ctf->create();
                    }
                }
            }

            $ijf = new InspectionJobFactory(InspectionTypeEnum::NEW_CAR, null, $import_car->po_id, $car->id, [
                'inspection_must_date' => $import_car_line_status->delivery_date
            ]);
            $ijf->create();
        }
    }

    public function getTotalCarByStatus($idImport, $statusCar)
    {
        $totalCar = 0;
        if (!empty($idImport)) {
            $totalCar = ImportCarLine::where('import_car_lines.import_car_id', $idImport)
                ->where('status', $statusCar)
                ->count();
        }
        return $totalCar;
    }

    public function sendNotificationReadyCar($modelImportCar, $dataPrOrderNo, $totalCarPending)
    {
        if (!empty($totalCarPending)) {
            $dataDepartment = [
                DepartmentEnum::PCD_PURCHASE,
            ];
            $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
            $url = route('admin.import-cars.show', ['import_car' => $modelImportCar]);
            $notiTypeChange = new NotificationManagement('รถพร้อมส่งมอบ', 'ใบสั่งซื้อ ' . $dataPrOrderNo . ' มีรถพร้อมส่งมอบแล้ว ' . $totalCarPending . ' คัน กรุณากรอกวันที่รับรถเข้าคลัง', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
            $notiTypeChange->send();
        }
    }

    public function getTotalCarByStatusDelivery($idImport, $statusCar)
    {
        $totalCar = 0;
        if (!empty($idImport)) {
            $totalCar = ImportCarLine::where('import_car_lines.import_car_id', $idImport)
                ->where('status_delivery', $statusCar)
                ->count();
        }
        return $totalCar;
    }

    public function sendNotificationReadyCarDelivery($modelPurchaseOrder, $dataPrOrderNo, $totalCarPending)
    {
        if (!empty($totalCarPending)) {
            $dataDepartment = [
                DepartmentEnum::PCD_PURCHASE,
            ];
            $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
            $url = route('admin.prepare-new-cars.index');
            $notiTypeChange = new NotificationManagement('รถรอส่งมอบ', 'ใบสั่งซื้อ ' . $dataPrOrderNo . ' มีรถพร้อมรอส่งมอบ ' . $totalCarPending . ' คัน กรุณากรอกวันที่รับรถเข้าคลัง', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
            $notiTypeChange->send();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(ImportCar $import_car)
    {
        $this->authorize(Actions::View . '_' . Resources::ImportCar);
        $purchase_requisition_cars = PurchaseOrderLine::select('purchase_order_lines.id', 'purchase_order_lines.name', 'purchase_order_lines.amount', 'purchase_order_lines.subtotal', 'purchase_order_lines.discount', 'purchase_order_lines.total')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
            // ->leftjoin('purchase_requisitions', 'purchase_requisitions.id', '=', 'purchase_orders.pr_id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_order_lines.pr_line_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'purchase_requisition_lines.car_color_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'purchase_requisition_lines.car_class_id')
            ->addSelect('car_colors.name as color_name', 'car_classes.full_name as class_name')
            ->where('import_cars.id', $import_car->id)
            ->get();

        $pr_detail = PurchaseRequisition::leftjoin('purchase_orders', 'purchase_orders.pr_id', '=', 'purchase_requisitions.id')->where('purchase_orders.id', $import_car->po_id)->first();
        $po_detail = PurchaseOrder::select('purchase_orders.*', 'purchase_orders.remark as po_remark')->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')->where('import_cars.id', $import_car->id)->first();

        $arr_ob = array();
        $count_car = count($purchase_requisition_cars);

        for ($i = 0; $i < count($purchase_requisition_cars); $i++) {
            $arr_ob[$purchase_requisition_cars[$i]->id] = [];
        }
        $import_car_lines = ImportCarLine::all();
        foreach ($purchase_requisition_cars as $index => $item) {
            $index0 = 0;
            foreach ($import_car_lines as $index2 => $item2) {
                if (strcmp($item->id, $item2->po_line_id) == 0) {

                    if ($item2->install_date != null) {
                        $date = new DateTime($item2->install_date);
                        $date_install_new = $date->format('d-m-Y');
                    } else {
                        $date_install_new = null;
                    }

                    if ($item2->delivery_date != null) {
                        $date_delivery = new DateTime($item2->delivery_date);
                        $date_delivery_new = $date_delivery->format('d-m-Y');
                    } else {
                        $date_delivery_new = null;
                    }

                    if ($item2->verification_date != null) {
                        $verification_date_format = new DateTime($item2->verification_date);
                        $verification_date_new = get_thai_date_format($verification_date_format, 'd/m/Y H:i');
                    } else {
                        $verification_date_new = null;
                    }

                    if (!empty($item2->engine_no) || !empty($item2->chassis_no) || !empty($item2->install_date) || !empty($item2->delivery_date)) {
                        $arr_ob[$item->id][$index0] = (object)array("engine_no" => $item2->engine_no, "chassis_no" => $item2->chassis_no, "installation_completed_date" => $date_install_new, "delivery_date" => $date_delivery_new, 'id' => $item2->id, 'status' => $item2->status, 'status_draft' => $item2->status, 'status_delivery' => $item2->status_delivery, "delivery_place" => $item2->delivery_location, 'verification_date' => $verification_date_new, 'remark_line' => $item2->remark);
                    } else {
                        $arr_ob[$item->id][$index0] = (object)array("engine_no" => '', "chassis_no" => '', "installation_completed_date" => '', "delivery_date" => '', 'id' => $item2->id, 'status' => ImportCarLineStatusEnum::PENDING, 'status_draft' => ImportCarLineStatusEnum::PENDING, 'status_delivery' => $item2->status_delivery, "delivery_place" => '', 'verification_date' => '', 'remark_line' => '');
                    }
                    // }
                    $index0++;
                }
            }
        }
        $accessory_pr = PurchaseRequisitionLineAccessory::leftjoin('accessories', 'accessories.id', '=', 'purchase_requisition_line_accessories.accessory_id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_requisition_line_accessories.purchase_requisition_line_id')
            ->leftjoin('purchase_order_lines', 'purchase_order_lines.pr_line_id', '=', 'purchase_requisition_lines.id')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
            ->select('accessories.name as accessory_name', 'accessories.version', 'purchase_requisition_line_accessories.amount as pr_line_acc_amount', 'purchase_order_lines.id as po_line_id')
            ->where('import_cars.id', $import_car->id)
            ->get();

        $car_park_transfer = CarParkTransfer::leftJoin('driving_jobs', 'driving_jobs.id', '=', 'car_park_transfers.driving_job_id')
            ->where('driving_jobs.job_id', $import_car->id)
            ->where('driving_jobs.job_type', ImportCar::class)
            ->select('car_park_transfers.worksheet_no', 'car_park_transfers.car_id', 'car_park_transfers.id')
            ->get();

        $inspection_job = InspectionJob::where('item_id', $import_car->po_id)
            ->where('item_type', PurchaseOrder::class)
            ->select('worksheet_no', 'car_id', 'id')
            ->get();

        $object = $arr_ob;
        $page_title = __('lang.view') . __('import_cars.page_title');
        $view = true;
        return view('admin.import-cars.form', [
            'object' => $object,
            'count_car' => $count_car,
            'd' => $po_detail,
            'accessory_pr' => $accessory_pr,
            'import_car' => $import_car,
            'purchase_requisition_cars' => $purchase_requisition_cars,
            'view' => $view,
            'page_title' => $page_title,
            'arr_ob_2' => '',
            'pr_detail' => $pr_detail,
            'car_park_transfer' => $car_park_transfer,
            'inspection_job' => $inspection_job,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ImportCar $import_car, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ImportCar);
        $purchase_requisition_cars = PurchaseOrderLine::select('purchase_order_lines.id', 'purchase_order_lines.name', 'purchase_order_lines.amount', 'purchase_order_lines.subtotal', 'purchase_order_lines.discount', 'purchase_order_lines.total')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
            // ->leftjoin('purchase_requisitions', 'purchase_requisitions.id', '=', 'purchase_orders.pr_id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_order_lines.pr_line_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'purchase_requisition_lines.car_color_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'purchase_requisition_lines.car_class_id')
            ->addSelect('car_colors.name as color_name', 'car_classes.full_name as class_name')
            ->where('import_cars.id', $import_car->id)
            ->get();

        $pr_detail = PurchaseRequisition::leftjoin('purchase_orders', 'purchase_orders.pr_id', '=', 'purchase_requisitions.id')->where('purchase_orders.id', $import_car->po_id)->first();
        $po_detail = PurchaseOrder::select('purchase_orders.*', 'purchase_orders.remark as po_remark')->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')->where('import_cars.id', $import_car->id)->first();

        $arr_ob = array();
        for ($i = 0; $i < count($purchase_requisition_cars); $i++) {
            $arr_ob[$purchase_requisition_cars[$i]->id] = [];
        }

        $arr_ob_2 = array();
        for ($i = 0; $i < count($purchase_requisition_cars); $i++) {
            $arr_ob_2[$purchase_requisition_cars[$i]->id] = [];
        }

        $import_car_lines = ImportCarLine::all();
        $index_loop = 0;
        foreach ($purchase_requisition_cars as $index => $item) {
            $index0 = 0;
            foreach ($import_car_lines as $index2 => $item2) {
                if (strcmp($item->id, $item2->po_line_id) == 0) {

                    if ($item2->install_date != null) {
                        $date = new DateTime($item2->install_date);
                        $date_install_new = $date->format('d-m-Y');
                    } else {
                        $date_install_new = null;
                    }

                    if ($item2->delivery_date != null) {
                        $date_delivery = new DateTime($item2->delivery_date);
                        $date_delivery_new = $date_delivery->format('d-m-Y');
                    } else {
                        $date_delivery_new = null;
                    }

                    if ($item2->verification_date != null) {
                        $verification_date_format = new DateTime($item2->verification_date);
                        $verification_date_new = get_thai_date_format($verification_date_format, 'd/m/Y H:i');
                    } else {
                        $verification_date_new = null;
                    }

                    if (!empty($item2->engine_no) || !empty($item2->chassis_no) || !empty($item2->install_date) || !empty($item2->delivery_date) || !empty($request->json_object[$index_loop]['หมายเลขเครื่องยนต์']) || !empty($request->json_object[$index_loop]['เลขตัวถัง']) || !empty($request->json_object[$index_loop]['วันที่พร้อมส่งมอบ'])) {
                        if ($request->json_object == null || '') {
                            $arr_ob[$item->id][$index0] = (object)array("engine_no" => $item2->engine_no, "chassis_no" => $item2->chassis_no, "installation_completed_date" => $date_install_new, "delivery_date" => $date_delivery_new, "delivery_place" => $item2->delivery_location, 'id' => $item2->id, 'status' => $item2->status, 'status_draft' => $item2->status, 'status_delivery' => $item2->status_delivery, 'reject_reason' => $item2->reject_reason, 'verification_date' => $verification_date_new, 'remark_line' => $item2->remark);
                        } else {
                            if ($item2->status != ImportCarLineStatusEnum::CONFIRM_DATA && $item2->status != ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA) {
                                if (!empty($request->json_object[$index_loop]['วันที่พร้อมส่งมอบ'])) {
                                    $date = new DateTime($request->json_object[$index_loop]['วันที่พร้อมส่งมอบ']);
                                    $date_install_new = $date->format('d-m-Y');
                                } else {
                                    $date_install_new = null;
                                }
                                $arr_ob[$item->id][$index0] = (object)array("engine_no" => !empty($request->json_object[$index_loop]['หมายเลขเครื่องยนต์']) ? $request->json_object[$index_loop]['หมายเลขเครื่องยนต์'] : '', "chassis_no" => !empty($request->json_object[$index_loop]['เลขตัวถัง']) ? $request->json_object[$index_loop]['เลขตัวถัง'] : '', "installation_completed_date" => $date_install_new, "delivery_date" => $date_delivery_new, "delivery_place" => $item2->delivery_location, 'id' => $item2->id, 'status' => $item2->status, 'status_draft' => $item2->status, 'status_delivery' => $item2->status_delivery, 'reject_reason' => $item2->reject_reason, 'verification_date' => $verification_date_new, 'remark_line' => $item2->remark);
                                $index_loop++;
                            } else {
                                $arr_ob[$item->id][$index0] = (object)array("engine_no" => $item2->engine_no, "chassis_no" => $item2->chassis_no, "installation_completed_date" => $date_install_new, "delivery_date" => $date_delivery_new, "delivery_place" => $item2->delivery_location, 'id' => $item2->id, 'status' => $item2->status, 'status_draft' => $item2->status, 'status_delivery' => $item2->status_delivery, 'reject_reason' => $item2->reject_reason, 'verification_date' => $verification_date_new, 'remark_line' => $item2->remark);
                                $index_loop++;
                            }
                        }
                    } else {
                        $arr_ob[$item->id][$index0] = (object)array("engine_no" => '', "chassis_no" => '', "installation_completed_date" => '', "delivery_date" => '', "delivery_place" => '', 'id' => $item2->id, 'status' => ImportCarLineStatusEnum::PENDING, 'status_draft' => ImportCarLineStatusEnum::PENDING, 'status_delivery' => $item2->status_delivery, 'reject_reason' => '', 'verification_date' => '', 'remark_line' => '');
                        $index_loop++;
                    }
                    // }
                    $index0++;
                }
            }
        }
        $accessory_pr = PurchaseRequisitionLineAccessory::leftjoin('accessories', 'accessories.id', '=', 'purchase_requisition_line_accessories.accessory_id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_requisition_line_accessories.purchase_requisition_line_id')
            ->leftjoin('purchase_order_lines', 'purchase_order_lines.pr_line_id', '=', 'purchase_requisition_lines.id')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
            ->select('accessories.name as accessory_name', 'accessories.version', 'purchase_requisition_line_accessories.amount as pr_line_acc_amount', 'purchase_order_lines.id as po_line_id')
            ->where('import_cars.id', $import_car->id)
            ->get();

        $import_car_line_id = ImportCarLine::pluck('id')->toArray();
        // dd($import_car_line_id);
        $car_park_transfer = CarParkTransfer::leftJoin('driving_jobs', 'driving_jobs.id', '=', 'car_park_transfers.driving_job_id')
            ->whereIn('driving_jobs.job_id', $import_car_line_id)
            ->where('driving_jobs.job_type', ImportCarLine::class)
            ->select('car_park_transfers.worksheet_no', 'car_park_transfers.car_id', 'car_park_transfers.id')
            ->get();
        // dd($car_park_transfer);

        $inspection_job = InspectionJob::where('item_id', $import_car->po_id)
            ->where('item_type', PurchaseOrder::class)
            ->select('worksheet_no', 'car_id', 'id')
            ->get();

        $object = $arr_ob;
        $purchase_order_dealer_list = [];
        $page_title = __('lang.edit') . __('import_cars.page_title');
        if ($request->json_object == null) {
            return view('admin.import-cars.form', [
                'object' => $object,
                'd' => $po_detail,
                'accessory_pr' => $accessory_pr,
                'page_title' => $page_title,
                'import_car' => $import_car,
                'purchase_requisition_cars' => $purchase_requisition_cars,
                'test' => 4,
                'arr_ob_2' => $arr_ob_2,
                'pr_detail' => $pr_detail,
                'car_park_transfer' => $car_park_transfer,
                'inspection_job' => $inspection_job,
            ]);
        } else {
            return response()->json([
                'success' => $object,
                'message' => 'ok',
                'redirect' => view('admin.import-cars.form')
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Reques $request
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

    public function updateStatus(ImportCar $import_car, Request $request)
    {
        $import_car_save = ImportCar::find($import_car->id);
        // dd($import_car->id);
        // if (!($import_car_save->status == ImportCarStatusEnum::SENT_REVIEW && $request->status == ImportCarStatusEnum::PENDING_REVIEW)) {
        $import_car_save->status = $request->status_update;
        $import_car_save->save();
        return response()->json([
            'success' => 'ok',
            'message' => 'ok',
            // 'redirect' => view('admin.import-cars.form')
        ]);
        // }
    }

    public function sendMail(Request $request)
    {
        $import_car_id = $request->id;
        $import_car = ImportCar::find($import_car_id);
        $url = null;
        $dealer_name = null;
        $po_no = null;
        $image = 'https://uat-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
        if (App::environment('production')) {
            $image = 'https://production-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
        }
        $mails = $request->tags;
        if (empty($mails)) {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ]);
        }

        if ($import_car) {
            $url = route('import-car-dealers.edit', $import_car_id);
            $dealer_name = ($import_car->purchaseOrder && $import_car->purchaseOrder->creditor) ? $import_car->purchaseOrder->creditor->name : null;
            $po = PurchaseOrder::find($import_car->po_id);
            if ($po) {
                $po_no = $po->po_no;
            }
        }

        $mail_data = [
            'url' => $url,
            'dealer_name' => $dealer_name,
            'image' => $image,
            'po_no' => $po_no
        ];

        EmailJob::dispatch($mails, $mail_data);
        return response()->json([
            'success' => true,
        ]);
    }
}
