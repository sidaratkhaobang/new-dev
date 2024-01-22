<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\InspectionStatusEnum;
use App\Enums\OwnershipTransferStatusEnum;
use App\Enums\RegisterStatusEnum;
use App\Enums\Resources;
use App\Exports\ExportOwnershipTransferTemplate;
use App\Exports\ExportRegisterAvance;
use App\Exports\ExportRegisterFaceSheet;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarCharacteristicTransport;
use App\Models\CarPark;
use App\Models\CarParkZone;
use App\Models\Creditor;
use App\Models\HirePurchase;
use App\Models\InspectionFlow;
use App\Models\InspectionJob;
use App\Models\InsuranceLot;
use App\Models\LongTermRental;
use App\Models\OwnershipTransfer;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class OwnershipTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::OwnershipTransfer);
        $status_list = $this->getStatus();
        $contract_no = $request->contract_no_search;
        $leasing = $request->leasing_search;
        $status = $request->status_search;
        $actual_last_payment_date = $request->actual_last_payment_date_search;
        $hire_purchase = HirePurchase::find($contract_no);
        $contract_no_text = $hire_purchase && $hire_purchase->contract_no ? $hire_purchase->contract_no : null;
    
        $leasing = Creditor::find($leasing);
        $leasing_text = $leasing ? $leasing->name : '';

        $worksheet_no = $request->worksheet_no;
        $list = OwnershipTransfer::leftjoin('cars as cars_db', 'cars_db.id', '=', 'ownership_transfers.car_id')
            ->leftjoin('hire_purchases as hire_purchases_db', 'hire_purchases_db.id', '=', 'ownership_transfers.hire_purchase_id')
            ->leftjoin('insurance_lots as insurance_lots_db', 'insurance_lots_db.id', '=', 'hire_purchases_db.lot_id')
            ->sortable(['worksheet_no' => 'desc'])
            ->select('ownership_transfers.*')
            ->search($request->s, $request)
            ->paginate(PER_PAGE);
        return view('admin.ownership-transfers.index', [
            'lists' => $list,
            's' => $request->s,
            'status_list' => $status_list,
            'actual_last_payment_date' => $actual_last_payment_date,
            'status' => $status,
            'worksheet_no' => $worksheet_no,
            'contract_no' => $contract_no,
            'contract_no_text' => $contract_no_text,
            'leasing' => $leasing,
            'leasing_text' => $leasing_text,
        ]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (isset($request->status)) {
            $validator = Validator::make($request->all(), [
                'request_transfer_kit_date' => [
                    'required',
                ],
                'receive_transfer_kit_date' => [
                    'required',
                ],
                'request_power_attorney_tls_date' => [
                    'required',
                ],
                'receive_power_attorney_tls_date' => [
                    'required',
                ],

                'memo_no' => [
                    'required',
                ],
                'receipt_avance' => [
                    'required',
                ],
                'operation_fee_avance' => [
                    'required',
                ],


            ], [], [
                'request_transfer_kit_date' => __('ownership_transfers.request_transfer_kit_date'),
                'receive_transfer_kit_date' => __('ownership_transfers.receive_transfer_kit_date'),
                'request_power_attorney_tls_date' => __('ownership_transfers.request_power_attorney_tls_date'),
                'receive_power_attorney_tls_date' => __('ownership_transfers.receive_power_attorney_tls_date'),
                'memo_no' =>  __('ownership_transfers.memo_no'),
                'receipt_avance' =>  __('ownership_transfers.receipt_avance'),
                'operation_fee_avance' =>  __('ownership_transfers.operation_fee_avance'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $ownership_transfer = OwnershipTransfer::find($request->id);
        if ($ownership_transfer) {
            $ownership_transfer->request_transfer_kit_date = $request->request_transfer_kit_date;
            $ownership_transfer->receive_transfer_kit_date = $request->receive_transfer_kit_date;
            $ownership_transfer->request_power_attorney_tls_date = $request->request_power_attorney_tls_date;
            $ownership_transfer->receive_power_attorney_tls_date = $request->receive_power_attorney_tls_date;
            $ownership_transfer->remark = $request->remark;
            $ownership_transfer->memo_no = $request->memo_no;
            if ($request->receipt_avance) {
                $receipt_avance = str_replace(',', '', $request->receipt_avance);
                $ownership_transfer->receipt_avance = $receipt_avance;
            }
            if ($request->operation_fee_avance) {
                $operation_fee_avance = str_replace(',', '', $request->operation_fee_avance);
                $ownership_transfer->operation_fee_avance = $operation_fee_avance;
            }
            if (isset($request->status) && in_array($ownership_transfer->status, [OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER])) {
                $ownership_transfer->status = OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER;
            } else {
                if (strcmp($ownership_transfer->status, OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER) === 0) {

                    $ownership_transfer->status = OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER;
                }
            }
            $ownership_transfer->save();

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $ownership_transfer->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $file) {
                    if ($file->isValid()) {
                        $ownership_transfer->addMedia($file)->toMediaCollection('optional_files');
                    }
                }
            }
        }


        $redirect_route = route('admin.ownership-transfers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function storeWaitingTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'find_copy_chassis_date' => [
                'required',
            ],
            'transfer_date' => [
                'required',
            ],


        ], [], [
            'find_copy_chassis_date' => __('ownership_transfers.find_copy_chassis_date'),
            'transfer_date' => __('ownership_transfers.transfer_date'),

        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        // }

        $ownership_transfer = OwnershipTransfer::find($request->id);
        if ($ownership_transfer) {
            $ownership_transfer->find_copy_chassis_date = $request->find_copy_chassis_date;
            $ownership_transfer->transfer_date = $request->transfer_date;
            $ownership_transfer->remark = $request->remark;
            // if (isset($request->status)) {
            if (in_array($ownership_transfer->status, [OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER])) {
                $ownership_transfer->status = OwnershipTransferStatusEnum::TRANSFERING;
            }
            // }
            $ownership_transfer->save();

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $ownership_transfer->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $file) {
                    if ($file->isValid()) {
                        $ownership_transfer->addMedia($file)->toMediaCollection('optional_files');
                    }
                }
            }
        }


        $redirect_route = route('admin.ownership-transfers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function storeTransfering(Request $request)
    {
        if (isset($request->status)) {
            $validator = Validator::make($request->all(), [
                'receive_registration_book_date' => [
                    'required',
                ],
                'car_ownership_date' => [
                    'required',
                ],
                'return_registration_book_date' => [
                    'required',
                ],
                'receipt_date' => [
                    'required',
                ],
                'receipt_no' => [
                    'required',
                ],
                'tax' => [
                    'required',
                ],
                'service_fee' => [
                    'required',
                ],


            ], [], [
                'receive_registration_book_date' => __('ownership_transfers.receive_registration_book_date'),
                'car_ownership_date' => __('ownership_transfers.car_ownership_date'),
                'return_registration_book_date' => __('ownership_transfers.return_registration_book_date'),
                'receipt_date' => __('ownership_transfers.receipt_date'),
                'receipt_no' => __('ownership_transfers.receipt_no'),
                'tax' => __('ownership_transfers.tax'),
                'service_fee' => __('ownership_transfers.service_fee'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $ownership_transfer = OwnershipTransfer::find($request->id);
        if ($ownership_transfer) {
            $ownership_transfer->receive_registration_book_date = $request->receive_registration_book_date;
            $ownership_transfer->car_ownership_date = $request->car_ownership_date;
            $ownership_transfer->return_registration_book_date = $request->return_registration_book_date;
            $ownership_transfer->remark = $request->remark;
            $ownership_transfer->receipt_no = $request->receipt_no;
            $ownership_transfer->receipt_date = $request->receipt_date;
            $tax = $request->tax ? str_replace(',', '', $request->tax) : null;
            $ownership_transfer->tax = $tax;
            $service_fee = $request->service_fee ? str_replace(',', '', $request->service_fee) : null;
            $ownership_transfer->service_fee = $service_fee;
            $ownership_transfer->link = $request->link;
            if (isset($request->status) && in_array($ownership_transfer->status, [OwnershipTransferStatusEnum::TRANSFERING])) {
                $ownership_transfer->status = $request->status;
            } else {
                if (strcmp($ownership_transfer->status, OwnershipTransferStatusEnum::TRANSFERING) === 0) {
                    $ownership_transfer->status = OwnershipTransferStatusEnum::TRANSFERING;
                }
            }
            $ownership_transfer->save();

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $ownership_transfer->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $file) {
                    if ($file->isValid()) {
                        $ownership_transfer->addMedia($file)->toMediaCollection('optional_files');
                    }
                }
            }
        }


        $redirect_route = route('admin.ownership-transfers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(OwnershipTransfer $ownership_transfer)
    {
        $car = Car::find($ownership_transfer->car_id);
        $car_age_start = Carbon::now()->diff($car->start_date);
        $ownership_transfer->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
        $zone = CarPark::leftJoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $ownership_transfer->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();
        $ownership_transfer->car_slot = $zone ? $zone->code . $zone->car_park_number : null;
        $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
        $ownership_transfer->registered_date = $registered_date ? $registered_date->format('d-m-Y') : null;

        $actual_last_payment_date = $ownership_transfer->actual_last_payment_date ? DateTime::createFromFormat('Y-m-d', $ownership_transfer->actual_last_payment_date) : null;
        $ownership_transfer->actual_last_payment_date = $actual_last_payment_date ? $actual_last_payment_date->format('d-m-Y') : null;

        $page_title = __('lang.edit') . __('ownership_transfers.page_title');
        $url = 'admin.ownership-transfers.index';
        $optional_files = $ownership_transfer->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $ownership_transfer->step = $this->setProgressStep($ownership_transfer->status);
        return view('admin.ownership-transfers.waiting-document-form', [
            'd' => $ownership_transfer,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'view' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnershipTransfer $ownership_transfer)
    {
        $car = Car::find($ownership_transfer->car_id);
        $car_age_start = Carbon::now()->diff($car->start_date);
        $ownership_transfer->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
        $zone = CarPark::leftJoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $ownership_transfer->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();
        $ownership_transfer->car_slot = $zone ? $zone->code . $zone->car_park_number : null;
        $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
        $ownership_transfer->registered_date = $registered_date ? $registered_date->format('d-m-Y') : null;

        $actual_last_payment_date = $ownership_transfer->actual_last_payment_date ? DateTime::createFromFormat('Y-m-d', $ownership_transfer->actual_last_payment_date) : null;
        $ownership_transfer->actual_last_payment_date = $actual_last_payment_date ? $actual_last_payment_date->format('d-m-Y') : null;

        $page_title = __('lang.edit') . __('ownership_transfers.page_title');
        $url = 'admin.ownership-transfers.index';
        $optional_files = $ownership_transfer->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $ownership_transfer->step = $this->setProgressStep($ownership_transfer->status);
        return view('admin.ownership-transfers.waiting-document-form', [
            'd' => $ownership_transfer,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
        ]);
    }

    public function editWaitingTransfer(OwnershipTransfer $ownership_transfer)
    {
        $car = Car::find($ownership_transfer->car_id);
        $car_age_start = Carbon::now()->diff($car->start_date);
        $ownership_transfer->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
        $zone = CarPark::leftJoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $ownership_transfer->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();
        $ownership_transfer->car_slot = $zone ? $zone->code . $zone->car_park_number : null;
        $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
        $ownership_transfer->registered_date = $registered_date ? $registered_date->format('d-m-Y') : null;

        $actual_last_payment_date = $ownership_transfer->actual_last_payment_date ? DateTime::createFromFormat('Y-m-d', $ownership_transfer->actual_last_payment_date) : null;
        $ownership_transfer->actual_last_payment_date = $actual_last_payment_date ? $actual_last_payment_date->format('d-m-Y') : null;

        $page_title = __('lang.edit') . __('ownership_transfers.page_title');
        $url = 'admin.ownership-transfers.index';
        $optional_files = $ownership_transfer->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $ownership_transfer->step = $this->setProgressStep($ownership_transfer->status);
        return view('admin.ownership-transfers.waiting-transfer-form', [
            'd' => $ownership_transfer,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
        ]);
    }

    public function showWaitingTransfer(OwnershipTransfer $ownership_transfer)
    {
        $car = Car::find($ownership_transfer->car_id);
        $car_age_start = Carbon::now()->diff($car->start_date);
        $ownership_transfer->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
        $zone = CarPark::leftJoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $ownership_transfer->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();
        $ownership_transfer->car_slot = $zone ? $zone->code . $zone->car_park_number : null;
        $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
        $ownership_transfer->registered_date = $registered_date ? $registered_date->format('d-m-Y') : null;

        $actual_last_payment_date = $ownership_transfer->actual_last_payment_date ? DateTime::createFromFormat('Y-m-d', $ownership_transfer->actual_last_payment_date) : null;
        $ownership_transfer->actual_last_payment_date = $actual_last_payment_date ? $actual_last_payment_date->format('d-m-Y') : null;

        $page_title = __('lang.edit') . __('ownership_transfers.page_title');
        $url = 'admin.ownership-transfers.index';
        $optional_files = [];
        $ownership_transfer->step = $this->setProgressStep($ownership_transfer->status);
        return view('admin.ownership-transfers.waiting-transfer-form', [
            'd' => $ownership_transfer,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'view' => true,
        ]);
    }

    public function editTransfering(OwnershipTransfer $ownership_transfer)
    {
        $car = Car::find($ownership_transfer->car_id);
        $car_age_start = Carbon::now()->diff($car->start_date);
        $ownership_transfer->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
        $zone = CarPark::leftJoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $ownership_transfer->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();
        $ownership_transfer->car_slot = $zone ? $zone->code . $zone->car_park_number : null;
        $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
        $ownership_transfer->registered_date = $registered_date ? $registered_date->format('d-m-Y') : null;

        $actual_last_payment_date = $ownership_transfer->actual_last_payment_date ? DateTime::createFromFormat('Y-m-d', $ownership_transfer->actual_last_payment_date) : null;
        $ownership_transfer->actual_last_payment_date = $actual_last_payment_date ? $actual_last_payment_date->format('d-m-Y') : null;

        $estimate_transfered_date = Carbon::parse($ownership_transfer->transfer_date)->addDays(4);
        $ownership_transfer->estimate_transfered_date = $estimate_transfered_date->format('Y-m-d');

        $page_title = __('lang.edit') . __('ownership_transfers.page_title');
        $url = 'admin.ownership-transfers.index';
        $optional_files = $ownership_transfer->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $ownership_transfer->step = $this->setProgressStep($ownership_transfer->status);
        return view('admin.ownership-transfers.transfering-form', [
            'd' => $ownership_transfer,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
        ]);
    }

    public function showTransfering(OwnershipTransfer $ownership_transfer)
    {
        $car = Car::find($ownership_transfer->car_id);
        $car_age_start = Carbon::now()->diff($car->start_date);
        $ownership_transfer->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
        $zone = CarPark::leftJoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $ownership_transfer->car_id)
            ->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();
        $ownership_transfer->car_slot = $zone ? $zone->code . $zone->car_park_number : null;
        $registered_date = $car->registered_date ? DateTime::createFromFormat('Y-m-d', $car->registered_date) : null;
        $ownership_transfer->registered_date = $registered_date ? $registered_date->format('d-m-Y') : null;

        $actual_last_payment_date = $ownership_transfer->actual_last_payment_date ? DateTime::createFromFormat('Y-m-d', $ownership_transfer->actual_last_payment_date) : null;
        $ownership_transfer->actual_last_payment_date = $actual_last_payment_date ? $actual_last_payment_date->format('d-m-Y') : null;

        $estimate_transfered_date = Carbon::parse($ownership_transfer->transfer_date)->addDays(4);
        $ownership_transfer->estimate_transfered_date = $estimate_transfered_date->format('Y-m-d');

        $page_title = __('lang.view') . __('ownership_transfers.page_title');
        $url = 'admin.ownership-transfers.index';
        $optional_files = $ownership_transfer->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $ownership_transfer->step = $this->setProgressStep($ownership_transfer->status);
        return view('admin.ownership-transfers.transfering-form', [
            'd' => $ownership_transfer,
            'page_title' => $page_title,
            'url' => $url,
            'optional_files' => $optional_files,
            'view' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function checkCar(Request $request)
    {
        $month_last_payment = $request->month_last_payment;
        $date = null;
        if ($month_last_payment) {
            $date = Carbon::createFromFormat('m/Y', $month_last_payment);
        }
        $car_id = $request->car_id;
        $car_class = $request->car_class;
        $leasing = $request->leasing;

        $ownership_transfer = OwnershipTransfer::leftjoin('hire_purchases', 'hire_purchases.id', '=', 'ownership_transfers.hire_purchase_id')
            ->leftjoin('insurance_lots', 'insurance_lots.id', '=', 'hire_purchases.lot_id')
            ->leftjoin('cars', 'cars.id', '=', 'ownership_transfers.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('creditors', 'creditors.id', '=', 'insurance_lots.leasing_id')
            ->where('ownership_transfers.status', $request->status)
            ->when($date, function ($query) use ($date) {
                $query->whereYear('hire_purchases.actual_last_payment_date', $date->year)
                    ->whereMonth('hire_purchases.actual_last_payment_date', $date->month);
            })
            ->when($car_id, function ($query) use ($car_id) {
                $query->where('ownership_transfers.car_id', $car_id);
            })
            ->when($car_class, function ($query) use ($car_class) {
                $query->where('cars.car_class_id', $car_class);
            })
            ->when($leasing, function ($query) use ($leasing) {
                $query->where('insurance_lots.leasing_id', $leasing);
            })
            ->select(
                'ownership_transfers.*',
                'creditors.name as creditor_name',
                'creditors.id as creditor_id',
                'hire_purchases.actual_last_payment_date',
                'cars.id as car_id',
                'cars.license_plate as license_plate',
                'cars.car_class_id',
                'car_classes.full_name',
                'cars.chassis_no',
                'cars.engine_no',
                'cars.engine_size',
            )->get();
        return response()->json([
            'ownership_transfer' => $ownership_transfer,
            'success' => true,
        ]);
    }

    public static function getStatus()
    {
        $status = collect([
            (object) [
                'id' => OwnershipTransferStatusEnum::WAITING_TRANSFER,
                'name' => __('ownership_transfers.status_' . OwnershipTransferStatusEnum::WAITING_TRANSFER . '_text'),
                'value' => OwnershipTransferStatusEnum::WAITING_TRANSFER,
            ],
            (object) [
                'id' => OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER,
                'name' => __('ownership_transfers.status_' . OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER . '_text'),
                'value' => OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER,
            ],
            (object) [
                'id' => OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER,
                'name' => __('ownership_transfers.status_' . OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER . '_text'),
                'value' => OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER,
            ],
            (object) [
                'id' => OwnershipTransferStatusEnum::TRANSFERING,
                'name' => __('ownership_transfers.status_' . OwnershipTransferStatusEnum::TRANSFERING . '_text'),
                'value' => OwnershipTransferStatusEnum::TRANSFERING,
            ],
            (object) [
                'id' => OwnershipTransferStatusEnum::TRANSFERED,
                'name' => __('ownership_transfers.status_' . OwnershipTransferStatusEnum::TRANSFERED . '_text'),
                'value' => OwnershipTransferStatusEnum::TRANSFERED,
            ],
        ]);
        return $status;
    }

    private function setProgressStep($status)
    {
        $step = 0;
        if (in_array($status, [OwnershipTransferStatusEnum::WAITING_TRANSFER])) {
            $step = 0;
        } elseif (in_array($status, [OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER])) {
            $step = 0;
        } elseif (in_array($status, [OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER])) {
            $step = 1;
        } elseif (in_array($status, [OwnershipTransferStatusEnum::TRANSFERING])) {
            $step = 2;
        } elseif (in_array($status, [OwnershipTransferStatusEnum::TRANSFERED])) {
            $step = 3;
        }
        return $step;
    }

    public function exportExcelFaceSheet(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::OwnershipTransfer);
        $result = collect([]);
        $install_equipment_ids = $request->install_equipment_ids;
        if ($request->ownership_transfer_lists) {
            $ownership_transfers = OwnershipTransfer::whereIn('id', $request->ownership_transfer_lists)->get();
            foreach ($ownership_transfers as $key => $ownership_transfer) {
                $ownership_transfer->index = $key + 1;
                $ownership_transfer->creditor_name = '';
                $ownership_transfer->car_class = '';
                $ownership_transfer->car_color = '';
                $ownership_transfer->engine_no = '';
                $ownership_transfer->chassis_no = '';
                $ownership_transfer->lot_no = $ownership_transfer->hirePurchase &&  $ownership_transfer->hirePurchase->insurance_lot &&
                    $ownership_transfer->hirePurchase->insurance_lot ? $ownership_transfer->hirePurchase->insurance_lot->lot_no : '';

                if ($ownership_transfer->car) {
                    // $register->creditor_name = $register->car->carBrand ? $register->car->carBrand->name : '';
                    $ownership_transfer->car_class = $ownership_transfer->car->CarClass ? $ownership_transfer->car->CarClass->full_name : '';
                    $ownership_transfer->car_color = $ownership_transfer->car->CarColor ? $ownership_transfer->car->CarColor->name : '';
                    $ownership_transfer->engine_no = $ownership_transfer->car->engine_no;
                    $ownership_transfer->chassis_no = $ownership_transfer->car->chassis_no;
                    $ownership_transfer->car_characteristic = $ownership_transfer->car->carCharacteristic ? $ownership_transfer->car->carCharacteristic->name : '';
                    $ownership_transfer->cc = $ownership_transfer->car->engine_size ? $ownership_transfer->car->engine_size : '';
                }
                if ($ownership_transfer->hirePurchase && $ownership_transfer->hirePurchase->insurance_lot) {
                    $ownership_transfer->leasing_name = $ownership_transfer->hirePurchase->insurance_lot ? $ownership_transfer->hirePurchase->insurance_lot->creditor->name : '';
                }
                if ($ownership_transfer->hirePurchase && $ownership_transfer->hirePurchase->purchase_order) {
                    // $register->creditor_name = $register->car->carBrand ? $register->car->carBrand->name : '';
                    $ownership_transfer->creditor_name = $ownership_transfer->hirePurchase && $ownership_transfer->hirePurchase->purchase_order &&
                        $ownership_transfer->hirePurchase->purchase_order->creditor ? $ownership_transfer->hirePurchase->purchase_order->creditor->name : '';

                    if ($ownership_transfer->hirePurchase->purchase_order->purchaseRequisiton) {
                        if ($ownership_transfer->hirePurchase->purchase_order->purchaseRequisiton->reference_type == LongTermRental::class) {
                            $lt_rental = LongTermRental::find($ownership_transfer->hirePurchase->purchase_order->purchaseRequisiton->reference_id);
                            $ownership_transfer->customer_name = $lt_rental->customer->name;
                        } else {
                            $ownership_transfer->customer_name = 'บริษัททรูลีสซิ่ง';
                        }
                    }
                }
            }

            if (count($ownership_transfers) > 0) {
                $topic_face_sheet = $request->topic_face_sheet ?? null;
                $file = Excel::download(new ExportRegisterFaceSheet($ownership_transfers, $topic_face_sheet), 'template.xlsx')->getFile();
                $custom_file_name = mb_convert_encoding($topic_face_sheet . '.xlsx', 'UTF-8', 'ISO-8859-1');
                $fileResource = fopen($file->getPathname(), 'r');
                return response()
                    ->stream(
                        function () use ($fileResource) {
                            fpassthru($fileResource);
                        },
                        200,
                        [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'Content-Disposition' => 'attachment; filename="' . $custom_file_name . '"',
                        ]
                    );
            }
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
        $redirect_route = route('admin.registers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function exportExcelAvance(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::OwnershipTransfer);
        if ($request->avance_list_arr) {
            $ownership_transfer_key = [];
            foreach ($request->avance_list_arr as $key => $avance_list_arr) {
                $ownership_transfer = OwnershipTransfer::find($key);
                if ($ownership_transfer) {
                    $ownership_transfer->memo_no = $avance_list_arr['memo_no'];
                    if ($avance_list_arr['operation_fee_avance']) {
                        $operation_fee_avance = str_replace(',', '', $avance_list_arr['operation_fee_avance']);
                        $ownership_transfer->operation_fee_avance = $operation_fee_avance;
                    }
                    $receipt_avance = str_replace(',', '', $avance_list_arr['receipt_avance']);
                    $ownership_transfer->receipt_avance = $receipt_avance;
                    $ownership_transfer->save();
                    $ownership_transfer_key[] = $key;
                }
            }
        }
        $result = collect([]);
        $install_equipment_ids = $request->install_equipment_ids;
        $ownership_transfers = OwnershipTransfer::whereIn('id', $ownership_transfer_key)->get();
        // $install_equipment_list = InstallEquipment::whereIn('id', $install_equipment_ids)->get();
        $total = 0;
        $receipt_avance_total = 0;
        $operation_fee_avance_total = 0;
        foreach ($ownership_transfers as $key => $ownership_transfer) {
            $ownership_transfer->index = $key + 1;
            $ownership_transfer->creditor_name = '';
            $ownership_transfer->car_class = '';
            $ownership_transfer->car_color = '';
            $ownership_transfer->engine_no = '';
            $ownership_transfer->chassis_no = '';
            $ownership_transfer->lot_no = $ownership_transfer->hirePurchase &&  $ownership_transfer->hirePurchase->insurance_lot &&
                $ownership_transfer->hirePurchase->insurance_lot ? $ownership_transfer->hirePurchase->insurance_lot->lot_no : '';
            if ($ownership_transfer->car) {
                // $register->creditor_name = $register->car->carBrand ? $register->car->carBrand->name : '';
                $ownership_transfer->car_class = $ownership_transfer->car->CarClass ? $ownership_transfer->car->CarClass->full_name : '';
                $ownership_transfer->car_color = $ownership_transfer->car->CarColor ? $ownership_transfer->car->CarColor->name : '';
                $ownership_transfer->engine_no = $ownership_transfer->car->engine_no;
                $ownership_transfer->chassis_no = $ownership_transfer->car->chassis_no;
                $ownership_transfer->car_characteristic = $ownership_transfer->car->carCharacteristic ? $ownership_transfer->car->carCharacteristic->name : '';
                $ownership_transfer->cc = $ownership_transfer->car->engine_size ? $ownership_transfer->car->engine_size : '';
            }
            if ($ownership_transfer->hirePurchase && $ownership_transfer->hirePurchase->insurance_lot) {
                $ownership_transfer->leasing_name = $ownership_transfer->hirePurchase->insurance_lot ? $ownership_transfer->hirePurchase->insurance_lot->creditor->name : '';
            }
            if ($ownership_transfer->hirePurchase && $ownership_transfer->hirePurchase->purchase_order) {
                // $register->creditor_name = $register->car->carBrand ? $register->car->carBrand->name : '';
                $ownership_transfer->creditor_name = $ownership_transfer->hirePurchase && $ownership_transfer->hirePurchase->purchase_order &&
                    $ownership_transfer->hirePurchase->purchase_order->creditor ? $ownership_transfer->hirePurchase->purchase_order->creditor->name : '';

                if ($ownership_transfer->hirePurchase->purchase_order->purchaseRequisiton) {
                    if ($ownership_transfer->hirePurchase->purchase_order->purchaseRequisiton->reference_type == LongTermRental::class) {
                        $lt_rental = LongTermRental::find($ownership_transfer->hirePurchase->purchase_order->purchaseRequisiton->reference_id);
                        $ownership_transfer->customer_name = $lt_rental->customer->name;
                    } else {
                        $ownership_transfer->customer_name = 'บริษัททรูลีสซิ่ง';
                    }
                }
            }
            $ownership_transfer->receipt_avance = $ownership_transfer->receipt_avance;
            $ownership_transfer->operation_fee_avance = $ownership_transfer->operation_fee_avance;
            $ownership_transfer->total = $ownership_transfer->receipt_avance + $ownership_transfer->operation_fee_avance;


            $receipt_avance_total += $ownership_transfer->receipt_avance;
            $operation_fee_avance_total += $ownership_transfer->operation_fee_avance;
        }
        $total = $receipt_avance_total + $operation_fee_avance_total;

        if (count($ownership_transfers) > 0) {
            $topic_face_sheet = $request->topic_face_sheet ?? '';

            $file = Excel::download(new ExportRegisterAvance($ownership_transfers, $topic_face_sheet, $receipt_avance_total, $operation_fee_avance_total, $total), 'template.xlsx')->getFile();
            $now = Carbon::now();
            $file_name = $now->format('dmY:H:i:s');
            $custom_file_name = 'Advance_' . $file_name . '.xlsx';
            $fileResource = fopen($file->getPathname(), 'r');
            return response()
                ->stream(
                    function () use ($fileResource) {
                        fpassthru($fileResource);
                    },
                    200,
                    [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Content-Disposition' => 'attachment; filename="' . $custom_file_name . '"',
                    ]
                );
        }
        // else {
        //     return response()->json([
        //         'success' => false,
        //     ]);
        // }
        $redirect_route = route('admin.ownership-transfers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function exportExcelTemplate(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Register);
        $result = collect([]);
        $install_equipment_ids = $request->install_equipment_ids;
        if ($request->ownership_transfers) {
            $ownership_transfers = OwnershipTransfer::whereIn('id', $request->ownership_transfers)->get();
            // $install_equipment_list = InstallEquipment::whereIn('id', $install_equipment_ids)->get();
            foreach ($ownership_transfers as $key => $ownership_transfer) {
                $ownership_transfer->index = $key + 1;
                $ownership_transfer->creditor_name = '';
                $ownership_transfer->car_class = '';
                $ownership_transfer->car_color = '';
                $ownership_transfer->engine_no = '';
                $ownership_transfer->chassis_no = '';
                $ownership_transfer->lot_no = $ownership_transfer->hirePurchase &&  $ownership_transfer->hirePurchase->insurance_lot &&
                    $ownership_transfer->hirePurchase->insurance_lot ? $ownership_transfer->hirePurchase->insurance_lot->lot_no : '';
                if ($ownership_transfer->car) {
                    // $register->creditor_name = $register->car->carBrand ? $register->car->carBrand->name : '';
                    $ownership_transfer->car_class = $ownership_transfer->car->CarClass ? $ownership_transfer->car->CarClass->full_name : '';
                    $ownership_transfer->car_color = $ownership_transfer->car->CarColor ? $ownership_transfer->car->CarColor->name : '';
                    $ownership_transfer->engine_no = $ownership_transfer->car->engine_no;
                    $ownership_transfer->chassis_no = $ownership_transfer->car->chassis_no;
                    $ownership_transfer->license_plate = $ownership_transfer->car->license_plate ?? '';
                    $ownership_transfer->car_characteristic = $ownership_transfer->car->carCharacteristic ? $ownership_transfer->car->carCharacteristic->name : '';
                    $ownership_transfer->cc = $ownership_transfer->car->engine_size ? $ownership_transfer->car->engine_size : '';
                }
                if ($ownership_transfer->hirePurchase && $ownership_transfer->hirePurchase->insurance_lot) {
                    $ownership_transfer->leasing_name = $ownership_transfer->hirePurchase->insurance_lot ? $ownership_transfer->hirePurchase->insurance_lot->creditor->name : '';
                }
                if ($ownership_transfer->hirePurchase && $ownership_transfer->hirePurchase->purchase_order) {
                    // $register->creditor_name = $register->car->carBrand ? $register->car->carBrand->name : '';
                    $ownership_transfer->creditor_name = $ownership_transfer->hirePurchase && $ownership_transfer->hirePurchase->purchase_order &&
                        $ownership_transfer->hirePurchase->purchase_order->creditor ? $ownership_transfer->hirePurchase->purchase_order->creditor->name : '';

                    if ($ownership_transfer->hirePurchase->purchase_order->purchaseRequisiton) {
                        if ($ownership_transfer->hirePurchase->purchase_order->purchaseRequisiton->reference_type == LongTermRental::class) {
                            $lt_rental = LongTermRental::find($ownership_transfer->hirePurchase->purchase_order->purchaseRequisiton->reference_id);
                            $ownership_transfer->customer_name = $lt_rental->customer->name;
                        } else {
                            $ownership_transfer->customer_name = 'บริษัททรูลีสซิ่ง';
                        }
                    }
                }
            }

            if (count($ownership_transfers) > 0) {
                $topic_face_sheet = $request->topic_face_sheet ?? null;
                $now = Carbon::now();
                $file_name = $now->format('dmY-H:i:s');
                $custom_file_name = 'TRANSFERS_' . $file_name . '.xlsx';
                return Excel::download(new ExportOwnershipTransferTemplate($ownership_transfers, $topic_face_sheet), $custom_file_name);
            }
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
        $redirect_route = route('admin.ownership-transfers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function importExcel(Request $request)
    {
        $data_request = $request->json_object;
        $key_arr = ['วันที่รับเล่มคืน', 'วันที่ครอบครองรถ', 'วันที่ส่งเล่มทะเบียนคืนบัญชี', 'วันที่ออกใบเสร็จ', 'เลขที่ใบเสร็จ', 'ค่าใบเสร็จ', 'ค่าบริการ'];
        $key_arr_size = count($key_arr);

        $key_remove = ['TRANSFERS_ID', 'ลำดับ', 'Leasing', 'รุ่นรถ', 'CC', 'สีรถ', 'หมายเลขเครื่องยนต์', 'หมายเลขตัวถัง', 'ทะเบียน'];

        if ($data_request) {
            foreach ($data_request as $index => $item) {
                foreach ($key_remove as $key) {
                    if (array_key_exists($key, $item)) {
                        unset($data_request[$index][$key]);
                    }
                }
            }

            foreach ($data_request as $key => $item) {
                $item_size = count($item);
                if ($item_size != $key_arr_size) {
                    return response()->json([
                        'success' => false,
                    ]);
                }
            }

            $modified_array = [];
            foreach ($request->json_object as $object) {
                $validate_data = 0;

                if (isset($object['วันที่รับเล่มคืน'])) {
                    $date_string = $object['วันที่รับเล่มคืน'];
                    $date = DateTime::createFromFormat('m/d/y', $date_string);
                    if ($date !== false) {
                        $object['วันที่รับเล่มคืน'] = $date->format('Y-m-d');
                    } else {
                        $validate_data++;
                    }
                }

                if (isset($object['วันที่ครอบครองรถ'])) {
                    $date_string = $object['วันที่ครอบครองรถ'];
                    $date = DateTime::createFromFormat('m/d/y', $date_string);

                    if ($date !== false) {
                        $object['วันที่ครอบครองรถ'] = $date->format('Y-m-d');
                    } else {
                        $validate_data++;
                    }
                }

                if (isset($object['วันที่ส่งเล่มทะเบียนคืนบัญชี'])) {
                    $date_string = $object['วันที่ส่งเล่มทะเบียนคืนบัญชี'];
                    $date = DateTime::createFromFormat('m/d/y', $date_string);
                    if ($date !== false) {
                        $object['วันที่ส่งเล่มทะเบียนคืนบัญชี'] = $date->format('Y-m-d');
                    } else {
                        $validate_data++;
                    }
                }

                if (isset($object['วันที่ออกใบเสร็จ'])) {
                    $date_string = $object['วันที่ออกใบเสร็จ'];
                    $date = DateTime::createFromFormat('m/d/y', $date_string);

                    if ($date !== false) {
                        $object['วันที่ออกใบเสร็จ'] = $date->format('Y-m-d');
                    } else {
                        $validate_data++;
                    }
                }

                if ($validate_data > 0) {
                    return response()->json([
                        'success' => false,
                    ]);
                }

                $modified_object = [
                    'transfers_id' => isset($object['TRANSFERS_ID']) ? $object['TRANSFERS_ID'] : null,
                    'no' => isset($object['ลำดับ']) ? $object['ลำดับ'] : null,
                    'leasing_name' => isset($object['Leasing']) ? $object['Leasing'] : null,
                    'car_class' => isset($object['รุ่นรถ']) ? $object['รุ่นรถ'] : null,
                    'cc' => isset($object['CC']) ? $object['CC'] : null,
                    'car_color' => isset($object['สีรถ']) ? $object['สีรถ'] : null,
                    'engine_no' => isset($object['หมายเลขเครื่องยนต์']) ? $object['หมายเลขเครื่องยนต์'] : null,
                    'chassis_no' => isset($object['หมายเลขตัวถัง']) ? $object['หมายเลขตัวถัง'] : null,
                    'license_plate' => isset($object['ทะเบียน']) ? $object['ทะเบียน'] : null,
                    'receive_registration_book_date' => isset($object['วันที่รับเล่มคืน']) ? $object['วันที่รับเล่มคืน'] : null,
                    'car_ownership_date' => isset($object['วันที่ครอบครองรถ']) ? $object['วันที่ครอบครองรถ'] : null,
                    'return_registration_book_date' => isset($object['วันที่ส่งเล่มทะเบียนคืนบัญชี']) ? $object['วันที่ส่งเล่มทะเบียนคืนบัญชี'] : null,
                    'receipt_date' => isset($object['วันที่ออกใบเสร็จ']) ? $object['วันที่ออกใบเสร็จ'] : null,
                    'receipt_no' => isset($object['เลขที่ใบเสร็จ']) ? $object['เลขที่ใบเสร็จ'] : null,
                    'receipt_fee' => isset($object['ค่าใบเสร็จ']) ? $object['ค่าใบเสร็จ'] : null,
                    'service_fee' => isset($object['ค่าบริการ']) ? $object['ค่าบริการ'] : null,

                ];
                $modified_array[] = $modified_object;
            }

            $request->merge(['json_object' => $modified_array]);


            return response()->json([
                'success' => true,
                'message' => 'ok',
                'data' => $request->json_object,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function storeImportExcel(Request $request)
    {
        foreach ($request->import_list_arr as $key => $import_list) {
            $ownership_transfer = OwnershipTransfer::find($import_list['id']);
            if ($ownership_transfer) {
                // receive_registration_book_date
                $ownership_transfer->receive_registration_book_date = $import_list['receive_registration_book_date'];
                // car_ownership_date
                $ownership_transfer->car_ownership_date = $import_list['car_ownership_date'];
                // return_registration_book_date
                $ownership_transfer->return_registration_book_date = $import_list['return_registration_book_date'];
                // // car_tax_exp_date
                // $ownership_transfer->car_tax_exp_date = $import_list['car_tax_exp_date'];
                // receipt_date
                $ownership_transfer->receipt_date = $import_list['receipt_date'];
                // receipt_no
                $ownership_transfer->receipt_no = $import_list['receipt_no'];
                // receipt_fee
                $receipt_fee = $import_list['receipt_fee'] ? str_replace(',', '', $import_list['receipt_fee']) : null;
                $ownership_transfer->receipt_fee = $receipt_fee;
                // service_fee
                $service_fee = $import_list['service_fee'] ? str_replace(',', '', $import_list['service_fee']) : null;
                $ownership_transfer->service_fee = $service_fee;
                $ownership_transfer->status = isset($import_list['status']) ? $import_list['status'] : $ownership_transfer->status;
                $ownership_transfer->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'ok',
        ]);
    }

    public function exportPowerAttorneyPdf(Request $request)
    {
        $car_arr = [];
        $leasing_arr = [];

        if ($request->attorney_list_arr) {
            foreach ($request->attorney_list_arr as $key => $attorney_list) {
                $ownership_transfer = OwnershipTransfer::find($attorney_list);
                $leasing = $ownership_transfer->hirePurchase->insurance_lot;
                if ($ownership_transfer && $ownership_transfer->car) {
                    
                    $car = $ownership_transfer->car;
                    $car_arr[$key]['license_plate'] = $car->license_plate;
                    $car_arr[$key]['engine_no'] = $car->engine_no;
                    $car_arr[$key]['chassis_no'] = $car->chassis_no;
                    $car_arr[$key]['car_class'] = $car->carClass ? $car->carClass->full_name : '';
                    $car_arr[$key]['registered_date'] = $car->registered_date;
                    $car_arr[$key]['leasing_name'] = $leasing ? $leasing->creditor->name : '';

                    $leasing_id = $leasing->leasing_id;
                    if ($leasing_id && !isset($leasing_arr[$leasing_id])) {
                        $creditor = Creditor::find($leasing_id);
                        if ($creditor) {
                            $leasing_arr[$leasing_id] = ['id' => $leasing_id, 'name' => $creditor->name, 'count' => 1];
                        }
                    }else {
                        $leasing_arr[$leasing_id]['count']++;
                    }
                }
            }
        }
        $car_total = count($request->attorney_list_arr);

        $pdf = PDF::loadView(
            'admin.ownership-transfers.pdf.attorney-pdf',
            [
                'car_arr' => $car_arr,
                'leasing_arr' => $leasing_arr,
                'car_total' => $car_total,
            ]
        );
        return $pdf->download('ขอหนังสือมอบอำนาจ TLS.pdf');
    }

    public function exportTransferPdf(Request $request)
    {
        $car_arr = [];
        $leasing_arr = [];

        if ($request->transfer_list_arr) {
            foreach ($request->transfer_list_arr as $key => $transfer_list) {
                $ownership_transfer = OwnershipTransfer::find($transfer_list);
                $leasing = $ownership_transfer->hirePurchase->insurance_lot;
                if ($ownership_transfer && $ownership_transfer->car) {
                    
                    $car = $ownership_transfer->car;
                    $car_arr[$key]['license_plate'] = $car->license_plate;
                    $car_arr[$key]['engine_no'] = $car->engine_no;
                    $car_arr[$key]['chassis_no'] = $car->chassis_no;
                    $car_arr[$key]['car_class'] = $car->carClass ? $car->carClass->full_name : '';
                    $car_arr[$key]['registered_date'] = $car->registered_date;
                    $car_arr[$key]['leasing_name'] = $leasing ? $leasing->creditor->name : '';

                    $leasing_id = $leasing->leasing_id;
                        $creditor = Creditor::find($leasing_id);
                        if ($creditor) {
                            $leasing_name = $creditor->name ?? '-';
                        }
                }
            }
        }
        $car_total = count($request->transfer_list_arr);
        $pdf = PDF::loadView(
            'admin.ownership-transfers.pdf.transfer-pdf',
            [
                'car_arr' => $car_arr,
                'leasing_name' => $leasing_name,
                'car_total' => $car_total,
            ]
        );
        return $pdf->download('ขอเล่มและชุดโอน' . $leasing_name .'.pdf');
        
    }

    public function checkCarTransfer(Request $request)
    {
        $month_last_payment = $request->month_last_payment;
        $date = null;
        if ($month_last_payment) {
            $date = Carbon::createFromFormat('m/Y', $month_last_payment);
        }
        $car_id = $request->car_id;
        $car_class = $request->car_class;
        $leasing = $request->leasing;

        $ownership_transfer = OwnershipTransfer::leftjoin('hire_purchases', 'hire_purchases.id', '=', 'ownership_transfers.hire_purchase_id')
            ->leftjoin('insurance_lots', 'insurance_lots.id', '=', 'hire_purchases.lot_id')
            ->leftjoin('cars', 'cars.id', '=', 'ownership_transfers.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('creditors', 'creditors.id', '=', 'insurance_lots.leasing_id')
            ->where('ownership_transfers.status', $request->status)
            ->where('insurance_lots.leasing_id', $leasing)
            ->when($date, function ($query) use ($date) {
                $query->whereYear('hire_purchases.actual_last_payment_date', $date->year)
                    ->whereMonth('hire_purchases.actual_last_payment_date', $date->month);
            })
            ->when($car_id, function ($query) use ($car_id) {
                $query->where('ownership_transfers.car_id', $car_id);
            })
            ->when($car_class, function ($query) use ($car_class) {
                $query->where('cars.car_class_id', $car_class);
            })

            ->select(
                'ownership_transfers.*',
                'creditors.name as creditor_name',
                'creditors.id as creditor_id',
                'hire_purchases.actual_last_payment_date',
                'cars.id as car_id',
                'cars.license_plate as license_plate',
                'cars.car_class_id',
                'car_classes.full_name',
                'cars.chassis_no',
                'cars.engine_no',
                'cars.engine_size',
            )->get();
        return response()->json([
            'ownership_transfer' => $ownership_transfer,
            'success' => true,
        ]);
    }
}
