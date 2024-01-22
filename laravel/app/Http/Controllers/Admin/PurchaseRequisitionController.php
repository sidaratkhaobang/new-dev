<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Classes\NotificationManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\LongTermRentalTypeAccessoryEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\PRStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Enums\DepartmentEnum;
use App\Http\Controllers\Controller;
use App\Models\ComparisonPrice;
use App\Models\ComparisonPriceLine;
use App\Models\Customer;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalLineAccessory;
use App\Models\LongTermRentalPRLine;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLine;
use App\Models\PurchaseRequisitionLineAccessory;
use App\Models\Rental;
use App\Traits\HistoryTrait;
use App\Traits\PurchaseRequisitionTrait;
use App\Traits\NotificationTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PurchaseRequisitionController extends Controller
{
    use PurchaseRequisitionTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::PurchaseRequisition);
        $rental_type = null;
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
        $list = PurchaseRequisition::where(function ($q) use ($rental_type) {
            if (!is_null($rental_type)) {
                $q->where('rental_type', $rental_type);
            }
        })
            ->where(function ($q) use ($status) {
                if (!is_null($status)) {
                    $q->where('status', $status);
                }
            })
            // ->branch()
            ->search($request->s, $request)
            ->sortable(['pr_no' => 'desc'])
            ->with(['reference'])
            ->paginate(PER_PAGE);


        $pr_list = PurchaseRequisition::select('pr_no as name', 'id')->orderBy('pr_no')->get();
        $rental_type_list = $this->getRentalType();
        $status_list = $this->getStatus();
        $from_request_date = $request->from_request_date;
        $to_request_date = $request->to_request_date;
        $from_require_date = $request->from_require_date;
        $to_require_date = $request->to_require_date;
        return view('admin.purchase-requisitions.index', [
            'list' => $list,
            's' => $request->s,
            'pr_list' => $pr_list,
            'pr_no' => $request->pr_no,
            'rental_type_list' => $rental_type_list,
            'rental_type' => $request->rental_type,
            'status_list' => $status_list,
            'status' => $request->status,
            'from_request_date' => $from_request_date,
            'to_request_date' => $to_request_date,
            'from_require_date' => $from_require_date,
            'to_require_date' => $to_require_date,
        ]);
    }

    public static function getRentalType()
    {
        $rental_type = collect([
            (object)[
                'id' => RentalTypeEnum::SHORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::SHORT),
                'value' => RentalTypeEnum::SHORT,
            ],
            (object)[
                'id' => RentalTypeEnum::LONG,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::LONG),
                'value' => RentalTypeEnum::LONG,
            ],
            (object)[
                'id' => RentalTypeEnum::REPLACEMENT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::REPLACEMENT),
                'value' => RentalTypeEnum::REPLACEMENT,
            ],
            (object)[
                'id' => RentalTypeEnum::TRANSPORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::TRANSPORT),
                'value' => RentalTypeEnum::TRANSPORT,
            ],
            (object)[
                'id' => RentalTypeEnum::OTHER,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::OTHER),
                'value' => RentalTypeEnum::OTHER,
            ],
        ]);
        return $rental_type;
    }

    public function getStatus()
    {
        $status = collect([
            (object)[
                'id' => PRStatusEnum::DRAFT,
                'name' => __('purchase_requisitions.status_' . PRStatusEnum::DRAFT . '_text'),
                'value' => PRStatusEnum::DRAFT,
            ],
            (object)[
                'id' => PRStatusEnum::PENDING_REVIEW,
                'name' => __('purchase_requisitions.status_' . PRStatusEnum::PENDING_REVIEW . '_text'),
                'value' => PRStatusEnum::PENDING_REVIEW,
            ],
            (object)[
                'id' => PRStatusEnum::CONFIRM,
                'name' => __('purchase_requisitions.status_' . PRStatusEnum::CONFIRM . '_text'),
                'value' => PRStatusEnum::CONFIRM,
            ],
            (object)[
                'id' => PRStatusEnum::REJECT,
                'name' => __('purchase_requisitions.status_' . PRStatusEnum::REJECT . '_text'),
                'value' => PRStatusEnum::REJECT,
            ],
            (object)[
                'id' => PRStatusEnum::CANCEL,
                'name' => __('purchase_requisitions.status_' . PRStatusEnum::CANCEL . '_text'),
                'value' => PRStatusEnum::CANCEL,
            ],
            (object)[
                'id' => PRStatusEnum::COMPLETE,
                'name' => __('purchase_requisitions.status_' . PRStatusEnum::COMPLETE . '_text'),
                'value' => PRStatusEnum::COMPLETE,
            ],
        ]);
        return $status;
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseRequisition);
        $step_approve_management = new StepApproveManagement();
        $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::PURCHASE_REQUISITION);
        if (!$is_configured) {
            return redirect()->back()->with('warning', __('lang.config_approve_warning') . __('purchase_requisitions.page_title'));
        }
        $d = new PurchaseRequisition();
        $d->request_date = date('Y-m-d');
        $rental_images_files = [];
        $approve_images_files = [];
        $refer_images_files = [];
        $replacement_approve_files = [];
        $rental_type_list = $this->getRentalType();
        $parent_list = PurchaseRequisition::select('pr_no as name', 'id')->orderBy('pr_no')->get();
        $parent_no = null;
        $reference_name = null;
        $page_title = __('lang.create') . __('purchase_requisitions.page_title');
        return view('admin.purchase-requisitions.form', [
            'd' => $d,
            'page_title' => $page_title,
            'rental_images_files' => $rental_images_files,
            'refer_images_files' => $refer_images_files,
            'approve_images_files' => $approve_images_files,
            'replacement_approve_files' => $replacement_approve_files,
            'rental_type_list' => $rental_type_list,
            'parent_list' => $parent_list,
            'parent_no' => $parent_no,
            'reference_name' => $reference_name,
        ]);
    }

    public function edit(PurchaseRequisition $purchase_requisition)
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseRequisition);
        $rental_type_list = $this->getRentalType();
        $rental_images_files = $purchase_requisition->getMedia('rental_images');
        $rental_images_files = get_medias_detail($rental_images_files);
        $approve_images_files = $purchase_requisition->getMedia('approve_images');
        $approve_images_files = get_medias_detail($approve_images_files);
        $refer_images_files = $purchase_requisition->getMedia('refer_images');
        $refer_images_files = get_medias_detail($refer_images_files);
        $quotation_files = $purchase_requisition->getMedia('quotation_files');
        $quotation_files = get_medias_detail($quotation_files);
        $replacement_approve_files = $purchase_requisition->getMedia('replacement_approve_files');
        $replacement_approve_files = get_medias_detail($replacement_approve_files);
        $parent_list = PurchaseRequisition::where('parent_id', $purchase_requisition->parent_id)->select('pr_no as name', 'id')->orderBy('pr_no')->get();

        $parent_no = null;
        if (!empty($purchase_requisition->parent_id)) {
            $parent = PurchaseRequisition::find($purchase_requisition->parent_id);
            if ($parent) {
                $parent_no = $parent->pr_no;
            }
        }

        $reference_name = ($purchase_requisition->reference) ? $purchase_requisition->reference->worksheet_no : null;

        $purchase_requisition->customer_type = null;
        if ($purchase_requisition->reference) {
            $customer = Customer::find($purchase_requisition->reference->customer_id);
            if (!empty($customer)) {
                $purchase_requisition->customer_type = $customer->customer_type;
            }
        }

        $pr_car_list = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_requisition->id)->get();
        $pr_car_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $item->amount_car_total = $item->amount;
            $item->remark_car = $item->remark;
            return $item;
        });
        $accessory_list = PurchaseRequisitionLineAccessory::whereIn('purchase_requisition_line_id', $pr_car_list->pluck('id'))->get();

        $car_accessory = [];
        $index = 0;
        foreach ($pr_car_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_index => $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->purchase_requisition_line_id) == 0) {
                    if (strcmp($accessory_item->type_accessories, LongTermRentalTypeAccessoryEnum::ATTACHMENT) == 0) {
                        $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                        $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                        $car_accessory[$index]['accessory_version_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->version : '';
                        $car_accessory[$index]['amount_per_car_accessory'] = $accessory_item->amount;
                        $car_accessory[$index]['amount_accessory'] = $accessory_item->amount * $car_item->amount_car;
                        $car_accessory[$index]['remark_accessory'] = $accessory_item->remark;
                        $car_accessory[$index]['type_accessories'] = $accessory_item->type_accessories;
                        $car_accessory[$index]['car_index'] = $car_index;
                        $index++;
                    }
                    if (strcmp($accessory_item->type_accessories, LongTermRentalTypeAccessoryEnum::ADDITIONAL) == 0) {
                        $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                        $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                        $car_accessory[$index]['accessory_version_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->version : '';
                        $car_accessory[$index]['amount_per_car_accessory'] = $accessory_item->amount;
                        $car_accessory[$index]['amount_accessory'] = $accessory_item->amount * $car_item->amount_car;
                        $car_accessory[$index]['remark_accessory'] = $accessory_item->remark;
                        $car_accessory[$index]['type_accessories'] = $accessory_item->type_accessories;
                        $car_accessory[$index]['car_index'] = $car_index;
                        $index++;
                    }
                }
            }
        }

        // approve log
        // $approve_line_list = new StepApproveManagement();
        // $approve_return = $approve_line_list->logApprove(PurchaseRequisition::class, $purchase_requisition->id);
        // $approve_line_list = $approve_return['approve_line_list'];
        // $approve = $approve_return['approve'];
        // if (!is_null($approve_line_list)) {
        //     // can approve or super user
        //     $approve_line_owner = new StepApproveManagement();
        //     $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
        // } else {
        //     $approve_line_owner = null;
        // }

        $approve_line = HistoryTrait::getHistory(PurchaseRequisition::class, $purchase_requisition->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(PurchaseRequisition::class, $purchase_requisition->id,ConfigApproveTypeEnum::PURCHASE_REQUISITION);

        } else {
            $approve_line_owner = null;
        }

        $transaction = $purchase_requisition->audits()
            ->orderBy('created_at', 'desc')
            ->get();
        // $transaction->map(function ($item) {
        //     $item->datetime = $item->created_at ? $item->created_at : null;
        //     $item->datetime = $item->user->name ? $item->user->name : null;
        //     $item->datetime = $item->role->name ? $item->role->name : null;
        //     $item->datetime = $item->branch->name ? $item->branch->name : null;
        //     $item->datetime = $item->user->name ? $item->user->name : null;
        //     // dd($item);
        // });

        $page_title = __('lang.edit') . __('purchase_requisitions.page_title');
        return view('admin.purchase-requisitions.form', [
            'd' => $purchase_requisition,
            'page_title' => $page_title,
            'rental_type_list' => $rental_type_list,
            'rental_images_files' => $rental_images_files,
            'approve_images_files' => $approve_images_files,
            'refer_images_files' => $refer_images_files,
            'quotation_files' => $quotation_files,
            'replacement_approve_files' => $replacement_approve_files,
            'parent_list' => $parent_list,
            'parent_no' => $parent_no,
            'pr_car_list' => $pr_car_list,
            'car_accessory' => $car_accessory,
            'reference_name' => $reference_name,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            // 'is_super_user' => $is_super_user_check,
            'transaction' => $transaction,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseRequisition);
        $validator = Validator::make($request->all(), [
            'require_date' => ['required',],
            'rental_type' => ['required',],
            'pr_car' =>
                [
                    'pr_car.*' => 'required',
                ],

        ], [], [
            'require_date' => __('purchase_requisitions.require_date'),
            'rental_type' => __('purchase_requisitions.rental_type'),
            'pr_car' => __('purchase_requisitions.data_car_table'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $purchase_requisitions = PurchaseRequisition::firstOrNew(['id' => $request->id]);
        $pr_count = PurchaseRequisition::all()->count() + 1;
        $prefix = 'PR';
        if (!($purchase_requisitions->exists)) {
            $purchase_requisitions->pr_no = generateRecordNumber($prefix, $pr_count);
        }
        $purchase_requisitions->request_date = $request->request_date;
        $purchase_requisitions->parent_id = $request->parent_id;
        $purchase_requisitions->require_date = $request->require_date;
        $purchase_requisitions->rental_type = $request->rental_type;
        $purchase_requisitions->remark = $request->remark;
        $purchase_requisitions->rental_refer = $request->rental_refer;
        $purchase_requisitions->contract_refer = $request->contract_refer;
        $purchase_requisitions->approve_refer = $request->approve_ref_no;
        if (strcmp($request->rental_type, RentalTypeEnum::SHORT) == 0) {
            $purchase_requisitions->reference_type = Rental::class;
            $purchase_requisitions->reference_id = $request->reference_id;
        } else if (strcmp($request->rental_type, RentalTypeEnum::LONG) == 0) {
            $purchase_requisitions->reference_type = LongTermRental::class;
            $purchase_requisitions->reference_id = $request->reference_id;
        } else if (strcmp($request->rental_type, RentalTypeEnum::REPLACEMENT) == 0) {
            $purchase_requisitions->approve_refer = $request->replacement_ref_no;
        }

        if ($request->status_draft) {
            $purchase_requisitions->status = PRStatusEnum::DRAFT;
        } else {
            $purchase_requisitions->status = PRStatusEnum::PENDING_REVIEW;
        }
        $purchase_requisitions->save();

        // save media duplicate
        if ($request->rental_images__pending_add_ids) {
            $originalRecordMedia = Media::find($request->rental_images__pending_add_ids[0]);
            $lt_rental = LongTermRental::find($originalRecordMedia->model_id);

            $mediaItem = $lt_rental->getMedia('approval_rental_files');
            foreach ($mediaItem as $data2) {
                $copiedMediaItem = $data2->copy($purchase_requisitions, 'rental_images');
            }
        }

        if ($purchase_requisitions->id) {
            $pr_car_class = $this->savePRCar($request, $purchase_requisitions->id);
        }

        if ($request->rental_images__pending_delete_ids) {
            $pending_delete_ids = $request->rental_images__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $purchase_requisitions->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('rental_images')) {
            foreach ($request->file('rental_images') as $image) {
                if ($image->isValid()) {
                    $purchase_requisitions->addMedia($image)->toMediaCollection('rental_images');
                }
            }
        }

        if ($request->approve_images__pending_delete_ids) {
            $pending_delete_ids = $request->approve_images__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $purchase_requisitions->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('approve_images')) {
            foreach ($request->file('approve_images') as $image) {
                if ($image->isValid()) {
                    $purchase_requisitions->addMedia($image)->toMediaCollection('approve_images');
                }
            }
        }

        if ($request->replacement_approve_files__pending_delete_ids) {
            $pending_delete_ids = $request->replacement_approve_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $purchase_requisitions->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('replacement_approve_files')) {
            foreach ($request->file('replacement_approve_files') as $image) {
                if ($image->isValid()) {
                    $purchase_requisitions->addMedia($image)->toMediaCollection('replacement_approve_files');
                }
            }
        }

        if ($request->refer_images__pending_delete_ids) {
            $pending_delete_ids = $request->refer_images__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $purchase_requisitions->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('refer_images')) {
            foreach ($request->file('refer_images') as $image) {
                if ($image->isValid()) {
                    $purchase_requisitions->addMedia($image)->toMediaCollection('refer_images');
                }
            }
        }

        $pr_check = PurchaseRequisition::find($request->id);
        if (!$pr_check) {
            $step_approve_management = new StepApproveManagement();
            $step_approve_management->createModelApproval(ConfigApproveTypeEnum::PURCHASE_REQUISITION, PurchaseRequisition::class, $purchase_requisitions->id);
        }
//        $this->sendNotificationPrCreate($purchase_requisitions,$purchase_requisitions->pr_no);
       NotificationTrait::sendNotificationPrApprove($purchase_requisitions->id,$purchase_requisitions,$purchase_requisitions->pr_no);
        $redirect_route = route('admin.purchase-requisitions.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function savePRCar($request, $purchase_requisition_id)
    {
        PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_requisition_id)->delete();
        if (!empty($request->pr_car)) {
            foreach ($request->pr_car as $car_index => $request_pr_car) {
                $pr_car = new PurchaseRequisitionLine();
                $pr_car->purchase_requisition_id = $purchase_requisition_id;
                $pr_car->car_class_id = $request_pr_car['car_class_id'];
                $pr_car->car_color_id = $request_pr_car['car_color_id'];
                $pr_car->amount = $request_pr_car['amount_car'];
                $pr_car->remark = $request_pr_car['remark_car'];
                $pr_car->save();

                if (!empty($request->accessories)) {
                    $pr_car_accessory = $this->savePRAccessory($request, $pr_car, $car_index);
                }
            }
        }

        return true;
    }

    private function savePRAccessory($request, $pr_car, $car_index)
    {
        PurchaseRequisitionLineAccessory::where('purchase_requisition_line_id', $pr_car->id)->delete();
        if (!empty($request->accessories)) {
            foreach ($request->accessories as $accessory_index => $accessories) {
                if (strcmp($accessories['car_index'], $car_index) == 0) {
                    $pr_car_accessory = new PurchaseRequisitionLineAccessory();
                    $pr_car_accessory->purchase_requisition_line_id = $pr_car->id;
                    $pr_car_accessory->accessory_id = $accessories['accessory_id'];
                    $pr_car_accessory->amount = intval($accessories['accessory_amount']);
                    $pr_car_accessory->remark = $accessories['remark_accessory'];
                    $pr_car_accessory->type_accessories = $accessories['type_accessories'];
                    $pr_car_accessory->save();
                }
            }
        }

        return true;
    }

    public function sendNotificationPrCreate($modelPurchaseRequisition,$dataPrNo)
    {
        $dataDepartment = [
            DepartmentEnum::PCD_PURCHASE,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        $notiTypeChange = new NotificationManagement('อนุมัติใบขอซื้อ ', 'ใบขอซื้อ ' . $dataPrNo . ' พิจารณาอนุมัติ', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
        $notiTypeChange->send();
    }

    public function updateStatusReview(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseRequisition);
        if (!empty($request->purchase_requisitions)) {
            foreach ($request->purchase_requisitions as $purchase_requisition_id) {
                $purchase_requisition = PurchaseRequisition::find($purchase_requisition_id);
                if (in_array($request->status, [PRStatusEnum::PENDING_REVIEW])) {
                    $purchase_requisition->status = $request->status;
                    $purchase_requisition->save();
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => route('admin.purchase-requisitions.index'),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
                'redirect' => route('admin.purchase-requisitions.index'),
            ]);
        }
    }

    public function show(PurchaseRequisition $purchase_requisition)
    {
        $this->authorize(Actions::View . '_' . Resources::PurchaseRequisition);
        $request_date = Carbon::now()->format('Y-m-d');
        $rental_type_list = $this->getRentalType();
        $rental_images_files = $purchase_requisition->getMedia('rental_images');
        $rental_images_files = get_medias_detail($rental_images_files);
        $approve_images_files = $purchase_requisition->getMedia('approve_images');
        $approve_images_files = get_medias_detail($approve_images_files);
        $refer_images_files = $purchase_requisition->getMedia('refer_images');
        $refer_images_files = get_medias_detail($refer_images_files);
        $replacement_approve_files = $purchase_requisition->getMedia('replacement_approve_files');
        $replacement_approve_files = get_medias_detail($replacement_approve_files);
        $parent_list = PurchaseRequisition::where('parent_id', $purchase_requisition->parent_id)->select('pr_no as name', 'id')->orderBy('pr_no')->get();

        $parent_no = null;
        if (!empty($purchase_requisition->parent_id)) {
            $parent = PurchaseRequisition::find($purchase_requisition->parent_id);
            if ($parent) {
                $parent_no = $parent->pr_no;
            }
        }

        $reference_name = ($purchase_requisition->reference) ? $purchase_requisition->reference->worksheet_no : null;

        $purchase_requisition->customer_type = null;
        if ($purchase_requisition->reference) {
            $customer = Customer::find($purchase_requisition->reference->customer_id);
            if (!empty($customer)) {
                $purchase_requisition->customer_type = $customer->customer_type;
            }
        }

        $pr_car_list = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_requisition->id)->get();
        $pr_car_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $item->remark_car = $item->remark;
            return $item;
        });
        $accessory_list = PurchaseRequisitionLineAccessory::whereIn('purchase_requisition_line_id', $pr_car_list->pluck('id'))->get();
        $car_accessory = [];
        $index = 0;
        foreach ($pr_car_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_index => $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->purchase_requisition_line_id) == 0) {
                    $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                    $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                    $car_accessory[$index]['accessory_version_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->version : '';
                    $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                    $car_accessory[$index]['remark_accessory'] = $accessory_item->remark;
                    $car_accessory[$index]['type_accessories'] = $accessory_item->type_accessories;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $index++;
                }
            }
        }

        // approve log
        // $approve_line_list = new StepApproveManagement();
        // $approve_return = $approve_line_list->logApprove(PurchaseRequisition::class, $purchase_requisition->id);
        // $approve_line_list = $approve_return['approve_line_list'];
        // $approve = $approve_return['approve'];
        // if (!is_null($approve_line_list)) {
        //     // can approve or super user
        //     $approve_line_owner = new StepApproveManagement();
        //     $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
        // } else {
        //     $approve_line_owner = null;
        // }

        $approve_line = HistoryTrait::getHistory(PurchaseRequisition::class, $purchase_requisition->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            $approve_line_owner = $approve_line_owner->checkCanApprove(PurchaseRequisition::class, $purchase_requisition->id,ConfigApproveTypeEnum::PURCHASE_REQUISITION);

        } else {
            $approve_line_owner = null;
        }

        $transaction = $purchase_requisition->audits()
            ->orderBy('created_at', 'desc')
            ->get();

        $page_title = __('lang.view') . __('purchase_requisitions.page_title');
        return view('admin.purchase-requisitions.view', [
            'd' => $purchase_requisition,
            'page_title' => $page_title,
            'request_date' => $request_date,
            'rental_type_list' => $rental_type_list,
            'rental_images_files' => $rental_images_files,
            'approve_images_files' => $approve_images_files,
            'refer_images_files' => $refer_images_files,
            'replacement_approve_files' => $replacement_approve_files,
            'parent_list' => $parent_list,
            'parent_no' => $parent_no,
            'pr_car_list' => $pr_car_list,
            'car_accessory' => $car_accessory,
            'reference_name' => $reference_name,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            // 'is_super_user' => $is_super_user_check,
            'transaction' => $transaction,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseRequisition);
        $purchase_requisitions = PurchaseRequisition::find($id);
        $purchase_requisitions->delete();

        $redirect_route = route('admin.purchase-requisitions.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    function getDefaultCarClassAccessories(Request $request)
    {
        $car_class_id = $request->car_class_id;
        $data = DB::table('car_class_accessories')
            ->select('accessories.id as accessory_id', 'accessories.name as accessory_text', 'accessories.version as accessory_version_text')
            ->join('accessories', 'accessories.id', '=', 'car_class_accessories.accessory_id')
            ->where('car_class_accessories.car_class_id', $car_class_id)
            ->get()->toArray();
        return [
            'success' => true,
            'car_class_id' => $request->car_class_id,
            'data' => $data
        ];
    }

    public function getRentalTypeById(Request $request)
    {
        $rental_type_id = $request->rental_type;
        $list = collect([]);
        if (strcmp($rental_type_id, RentalTypeEnum::SHORT) == 0) {
            $list = Rental::select('id', 'worksheet_no as name')->orderBy('worksheet_no')->get();
        }

        if (strcmp($rental_type_id, RentalTypeEnum::LONG) == 0) {
            $list = LongTermRental::select('id', 'worksheet_no as name')
                ->where('status', PRStatusEnum::COMPLETE)->orderBy('worksheet_no')->get();
            if (!empty($list)) {
                //                Check TotalCar In Pr Is Full Or Not
                $list = $this->CheckCarMaximumLongRentTerm($list);
            }
        }


        $list = $list->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name
            ];
        })->values();

        return response()->json($list);
    }

    public function CheckCarMaximumLongRentTerm($List = null)
    {
        if (!empty($List)) {
            foreach ($List as $keyList => $valueList) {
                $rental_id = $valueList->id;
                $MaximumCarData = [];
                if (!empty($rental_id)) {
                    //                    Get Total Car Data
                    $CarTotal = $this->GetTotalCarFromANotherLongtermRentalData($rental_id);
                    //                    Get MaximumCarData
                    $MaximumCar = LongTermRentalPRLine::where('lt_rental_id', $rental_id)
                        ->get()
                        ->map(function ($item) {
                            $item->amount = $item->ltLine->amount;
                            $item->car_class_id = $item->ltLine->car_class_id;
                            return $item;
                        })->toArray();
                    //                    Check And Unset From List
                    if (!empty($CarTotal) && !empty($MaximumCar)) {
                        $CheckCarTotalData = $this->CheckCarClassAmount($MaximumCar, $CarTotal);
                        //                       If $CheckCarTotalData Not Empty Get amount Data
                        if (!empty($CheckCarTotalData)) {
                            $CheckCarTotalData = collect($CheckCarTotalData)->pluck('amount')->toArray();
                            //                            Remove 0 Data From Array
                            $CheckCarTotalData = array_filter($CheckCarTotalData);
                            //                            If Empty Car Is Full Then Remove From List
                            if (empty($CheckCarTotalData)) {
                                unset($List[$keyList]);
                            }
                        }
                    }
                }
            }
        }

        //        Reset Array Index Before Return
        return $List;
    }

    public function GetTotalCarFromANotherLongtermRentalData($rental_id = null)
    {
        $TotalCarData = null;
        if (!empty($rental_id)) {
            //            Get All PR ID
            $PurchaseRequisition = PurchaseRequisition::where('reference_id', $rental_id)
                ->where('status', "!=", PRStatusEnum::CANCEL)
                ->pluck('id');
            if (!empty($PurchaseRequisition)) {
                //                Get All Total CarTotal Data
                $TotalCar = PurchaseRequisitionLine::select(DB::raw('sum(amount) as amount'), 'car_class_id')
                    ->groupby('car_class_id')
                    ->whereIn('purchase_requisition_id', $PurchaseRequisition)
                    ->get();
                if (!empty($TotalCar)) {
                    //                    Reset Array CarTotalCarData
                    $ResetFormatTotalCar = [];
                    foreach ($TotalCar as $keyTotalCar => $valueTotalCar) {
                        $ResetFormatTotalCar[$valueTotalCar->car_class_id]['amount'] = $valueTotalCar->amount;
                    }
                    //                    If Data Not Empty Return Data
                    if (!empty($ResetFormatTotalCar)) {
                        $TotalCarData = $ResetFormatTotalCar;
                    }
                }
            }
        }
        return $TotalCarData;
    }

    public function CheckCarClassAmount($ListData = null, $TotalCarData = null)
    {

        if (!empty($ListData) && !empty($TotalCarData)) {
            foreach ($ListData as $keyListData => $valueListData) {
                //                Check Data Is Exist In Key And Calculate
                if (array_key_exists($valueListData['car_class_id'], $TotalCarData)) {
                    $left_amount = $valueListData['amount'] - $TotalCarData[$valueListData['car_class_id']]['amount'];
                    $ListData[$keyListData]['amount'] = ($left_amount <= 0) ? 0 : $left_amount;
                }
            }
        }
        return $ListData;
    }

    public function getLongtermRentalData(Request $request)
    {
        $rental_type = $request->rental_type;
        $rental_id = $request->rental_id;
        $line = null;
        $images_files = null;
        $data = null;
        $purchase_requisition_line = null;

        if (strcmp($rental_type, RentalTypeEnum::LONG) == 0) {
            $lt_pr_lines = LongTermRentalPRLine::where('lt_rental_id', $rental_id)->get();
            $pr_line_arr = [];
            foreach ($lt_pr_lines as $lt_pr_line) {
                if (isset($pr_line_arr[$lt_pr_line->lt_rental_line_id])) {
                    $pr_line_arr[$lt_pr_line->lt_rental_line_id] += $lt_pr_line->amount;
                } else {
                    $pr_line_arr[$lt_pr_line->lt_rental_line_id] = $lt_pr_line->amount;
                }
            }

            if (!empty($pr_line_arr)) {
                foreach ($pr_line_arr as $key => $amount) {
                    $lt_rental_line = LongTermRentalLine::find($key);
                    $purchase_requisition_line = new PurchaseRequisitionLine();
                    $purchase_requisition_line->car_class_text = ($lt_rental_line->carClass) ? $lt_rental_line->carClass->full_name . ' - ' . $lt_rental_line->carClass->name : '';
                    $purchase_requisition_line->car_color_text = ($lt_rental_line->color) ? $lt_rental_line->color->name : '';
                    $purchase_requisition_line->amount_car = $amount;
                }
            }
        }

        return [
            'success' => true,
            'data' => $purchase_requisition_line,
        ];
    }

    public function printPdf(Request $request)
    {
        $purchase_requisition = PurchaseRequisition::find($request->purchase_requisition);
        $rental_images_files = $purchase_requisition->getMedia('rental_images');
        $rental_images_files = get_medias_detail($rental_images_files);
        $refer_images_files = $purchase_requisition->getMedia('refer_images');
        $refer_images_files = get_medias_detail($refer_images_files);
        $quotation_files = $purchase_requisition->getMedia('quotation_files');
        $quotation_files = get_medias_detail($quotation_files);

        $pr_car_list = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_requisition->id)->get();
        $pr_car_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            return $item;
        });

        $accessory_list = PurchaseRequisitionLineAccessory::whereIn('purchase_requisition_line_id', $pr_car_list->pluck('id'))->get();
        $car_accessory = [];
        $index = 0;
        foreach ($pr_car_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_index => $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->purchase_requisition_line_id) == 0) {
                    $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                    $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                    $car_accessory[$index]['remark_accessory'] = $accessory_item->remark;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $car_accessory[$index]['type_accessories'] = $accessory_item->type_accessories;
                    $index++;
                }
            }
        }
        $pr_reference = $purchase_requisition->reference;

        $page_title = $purchase_requisition->pr_no;

        $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($purchase_requisition->id);
        $purchase_requisition_cars = PurchaseRequisitionTrait::getPRCar($purchase_requisition->id);
        // $purchase_requisition_dealer_lines = PurchaseRequisitionTrait::getPRDealerLine($purchase_requisition->id);
        $total_car_amount = 0;
        foreach ($purchase_requisition_cars as $key => $cars) {
            $total_car_amount += $cars->amount;
        }
        // $total_dealer_car_amount = 0;
        // foreach ($purchase_requisition_dealer_lines as $key => $dealer_lines) {
        //     $total_dealer_car_amount += $dealer_lines->amount;
        // }

        $rental_data = [];
        $request->rental_type = $purchase_requisition->rental_type;
        $request->rental_id = $purchase_requisition->reference_id;
        $rental_json = $this->getRentalTypeData($request);
        if (isset($rental_json['data']) && sizeof($rental_json['data']) > 0) {
            $rental_data = ($rental_json['data'][0]) ? $rental_json['data'][0]->toArray() : [];
        }

        $compare_price_dealers = ComparisonPrice::where('item_id', $purchase_requisition->id)
            ->where('item_type', PurchaseRequisition::class)->where('creditor_id', $purchase_requisition->creditor_id)->get();
        $compare_price_dealers->map(function ($item) {
            $compare_price_line = ComparisonPriceLine::where('comparison_price_id', $item->id)->get();
            $compare_price_line->map(function ($compare_price_line) {
                $compare_price_line->car_price = $compare_price_line->total;
            });
            $item->dealer_price = $compare_price_line;
            return $item;
        });

        $print_type = ($request->type) ? $request->type : '';

        $pdf = PDF::loadView(
            'admin.purchase-requisitions.component-pdf.pdf',
            [
                'pr_car_list' => $pr_car_list,
                'purchase_order_dealer_list' => $compare_price_list,
                'car_accessory' => $car_accessory,
                'purchase_requisition' => $purchase_requisition,
                'purchase_requisition_cars' => $purchase_requisition_cars,
                // 'purchase_requisition_dealer_lines' => $purchase_requisition_dealer_lines,
                'pr_reference' => $pr_reference,
                'rental_data' => $rental_data,
                'total_car_amount' => $total_car_amount,
                // 'total_dealer_car_amount' => $total_dealer_car_amount,
                'rental_images_files' => $rental_images_files,
                'refer_images_files' => $refer_images_files,
                'quotation_files' => $quotation_files,
                'page_title' => $page_title,
                'compare_price_dealers' => $compare_price_dealers,
                'print_type' => $print_type,
            ]
        );
        return $pdf->stream();
    }

    public function getRentalTypeData(Request $request)
    {

        $rental_type = $request->rental_type;
        $rental_id = $request->rental_id;
        $TotalCarData = $this->GetTotalCarFromANotherLongtermRentalData($rental_id);
        $line = null;
        $images_files = [];
        $data = null;
        $pr_line_arr = [];

        if (strcmp($rental_type, RentalTypeEnum::SHORT) == 0) {
            $data = Rental::leftJoin('customers', 'customers.id', '=', 'rentals.customer_id')
                ->select(
                    'customers.customer_type',
                    'rentals.id',
                    'rentals.customer_name',
                    'rentals.worksheet_no',
                    'rentals.created_at as request_date',
                )
                ->where('rentals.id', $rental_id)
                ->get();
            $data->map(function ($item) {
                $item->customer_type = ($item->customer_type) ? __('customers.type_' . $item->customer_type) : null;
                return $item;
            });
        }

        if (strcmp($rental_type, RentalTypeEnum::LONG) == 0) {

            $data = LongTermRental::leftJoin('customers', 'customers.id', '=', 'lt_rentals.customer_id')
                ->select(
                    'customers.customer_type',
                    'lt_rentals.id',
                    'lt_rentals.customer_name',
                    'lt_rentals.job_type',
                    'lt_rentals.rental_duration',
                    'lt_rentals.worksheet_no',
                    'lt_rentals.created_at as request_date',
                    'lt_rentals.require_date',
                )
                ->where('lt_rentals.id', $rental_id)
                ->where('lt_rentals.status', PRStatusEnum::COMPLETE)
                ->get();
            $data->map(function ($item) {
                $item->customer_type = ($item->customer_type) ? __('customers.type_' . $item->customer_type) : null;
                $item->job_type = ($item->job_type) ? __('long_term_rentals.job_type_' . $item->job_type) : null;
                return $item;
            });

            $lt_rental = LongTermRental::where('id', $rental_id)->first();

            if ($lt_rental) {
                $images_files = $lt_rental->getMedia('approval_rental_files')->toArray();
                $line = LongTermRentalPRLine::where('lt_rental_id', $rental_id)->get();

                $pr_line_arr = collect([]);
                foreach ($line as $lt_pr_line) {

                    if (isset($pr_line_arr[$lt_pr_line->lt_rental_line_id])) {
                        $obj = $pr_line_arr[$lt_pr_line->lt_rental_line_id];
                        $obj->amount += 1;
                    } else {
                        $pr_line_arr[$lt_pr_line->lt_rental_line_id] = $lt_pr_line;
                    }
                }

                $pr_line_arr->map(function ($item) {
                    $lt_rental_line_accessories = LongTermRentalLineAccessory::where('lt_rental_line_id', $item->lt_rental_line_id)->get();
                    $lt_rental_line = LongTermRentalLine::find($item->lt_rental_line_id);
                    $accessory_arr = [];
                    foreach ($lt_rental_line_accessories as $index => $line_accessory) {
                        $accessory = [];
                        $accessory['accessory_id'] = $line_accessory->accessory_id;
                        $accessory['amount'] = $line_accessory->amount;
                        $accessory['accessory_text'] = ($line_accessory->accessory) ? $line_accessory->accessory->name : '';
                        $accessory['remark'] = $line_accessory->remark;
                        $accessory['type_accessories'] = $line_accessory->type_accessories;

                        if (count($accessory) > 0) {
                            $accessory_arr[] = $accessory;
                        }
                    }
                    $item->accessory = $accessory_arr;
                    $item->car_class_id = $lt_rental_line->car_class_id;
                    $item->car_color_id = $lt_rental_line->car_color_id;
                    $item->car_class_text = ($lt_rental_line->carClass) ? $lt_rental_line->carClass->full_name . ' - ' . $lt_rental_line->carClass->name : '';
                    $item->car_color_text = ($lt_rental_line->color) ? $lt_rental_line->color->name : '';
                    return $item;
                });
                $pr_line_arr = $pr_line_arr->values()->toArray();
            }
        }

        if (!empty($pr_line_arr)) {

            $pr_line_arr = $this->CheckCarClassAmount($pr_line_arr, $TotalCarData);
        }
        return [
            'success' => true,
            'rental_type' => $rental_type,
            'data' => $data,
            'line' => $pr_line_arr,
            'images_files' => $images_files,
        ];
    }

    public function duplicatePurchaseRequisition(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseRequisition);
        $purchase_requisition = PurchaseRequisition::find($request->purchase_requisition_id);
        $pr_count = PurchaseRequisition::all()->count() + 1;
        $prefix = 'PR';

        //duplicate data
        $dup_purchase_requisition = $purchase_requisition->replicate();
        $dup_purchase_requisition->pr_no = generateRecordNumber($prefix, $pr_count);
        $dup_purchase_requisition->parent_id = $purchase_requisition->id;
        if (in_array($purchase_requisition->status, [PRStatusEnum::DRAFT, PRStatusEnum::CANCEL])) {
            $dup_purchase_requisition->status = PRStatusEnum::DRAFT;
        } elseif (in_array($purchase_requisition->status, [PRStatusEnum::CONFIRM, PRStatusEnum::PENDING_REVIEW])) {
            $dup_purchase_requisition->status = PRStatusEnum::PENDING_REVIEW;
        }
        $dup_purchase_requisition->save();

        $step_approve_management = new StepApproveManagement();
        $step_approve_management->createModelApproval(ConfigApproveTypeEnum::PURCHASE_REQUISITION, PurchaseRequisition::class, $dup_purchase_requisition->id);

        //duplicate images
        $rental_images_files = $purchase_requisition->getMedia('rental_images');
        if ($rental_images_files) {
            foreach ($rental_images_files as $media) {
                $copied_media = $media->copy($dup_purchase_requisition, 'rental_images');
            }
        }
        $refer_images_files = $purchase_requisition->getMedia('refer_images');
        if ($refer_images_files) {
            foreach ($refer_images_files as $media) {
                $copied_media = $media->copy($dup_purchase_requisition, 'refer_images');
            }
        }

        $replacement_approve_files = $purchase_requisition->getMedia('replacement_approve_files');
        if ($replacement_approve_files) {
            foreach ($replacement_approve_files as $media) {
                $copied_media = $media->copy($dup_purchase_requisition, 'replacement_approve_files');
            }
        }

        //duplicate car accessory
        $purchase_requisition_lines = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_requisition->id)->orderBy('id')->get();
        if ($purchase_requisition_lines) {
            foreach ($purchase_requisition_lines as $purchase_requisition_line) {
                $dup_purchase_requisition_line = $purchase_requisition_line->replicate();
                $dup_purchase_requisition_line->purchase_requisition_id = $dup_purchase_requisition->id;
                $dup_purchase_requisition_line->save();

                $purchase_requisition_line_accessorys = PurchaseRequisitionLineAccessory::where('purchase_requisition_line_id', $purchase_requisition_line->id)->orderBy('id')->get();
                if ($purchase_requisition_line_accessorys) {
                    foreach ($purchase_requisition_line_accessorys as $purchase_requisition_line_accessory) {
                        $dup_purchase_requisition_line_accessory = $purchase_requisition_line_accessory->replicate();
                        $dup_purchase_requisition_line_accessory->purchase_requisition_line_id = $dup_purchase_requisition_line->id;
                        $dup_purchase_requisition_line_accessory->save();
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'redirect' => route('admin.purchase-requisitions.index'),
        ]);
    }
}
