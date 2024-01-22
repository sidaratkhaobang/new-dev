<?php

namespace App\Http\Controllers\Admin;

use Actions;
use App\Classes\NotificationManagement;
use App\Classes\StepApproveManagement;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\RentalTypeEnum;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalLineAccessory;
use App\Models\LongTermRentalMonth;
use App\Models\LongTermRentalPRLine;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLine;
use App\Models\PurchaseRequisitionLineAccessory;
use App\Traits\LongTermRentalTrait;
use App\Traits\NotificationTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use LongTermRentalStatusEnum;
use PRStatusEnum;
use Resources;


class LongTernRentalPRController extends LongTermRentalController
{
    use LongTermRentalTrait;

    public function editPRLongTermRentalLine(LongTermRental $long_term_rental)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRental);
        $lt_status_is_completed = (strcmp($long_term_rental->status, LongTermRentalStatusEnum::COMPLETE) === 0) ? true : false;
        if ($lt_status_is_completed) {
            return redirect()->back();
        }

        $lt_rental_line_list = $this->getLongTermRentalLinesForAddPRLine($long_term_rental);
        $lt_pr_line_list = $this->getLongTermRentalPRLine($long_term_rental->id);
        $approved_rental_files = $long_term_rental->getMedia('approval_rental_files');
        $approved_rental_files = get_medias_detail($approved_rental_files);
        $approval_status_list = LongTermRentalTrait::getLongTermRentalApproveStatusList();

        $allow_confirm = $this->isAllowConfirmCreatePR($long_term_rental);
        $route_group = [
            'route_lt_rental' => route('admin.long-term-rentals.edit', ['long_term_rental' => $long_term_rental]),
            'route_pr_line' => route('admin.long-term-rentals.pr-lines.edit', ['long_term_rental' => $long_term_rental]),
        ];
        $page_title = __('lang.edit') . __('long_term_rentals.page_title');
        return view('admin.long-term-rental-pr-lines.form', [
            'd' => $long_term_rental,
            'page_title' => $page_title,
            'approval_status_list' => $approval_status_list,
            'approved_rental_files' => $approved_rental_files,
            'allow_confirm' => $allow_confirm,
            'lt_rental_line_list' => $lt_rental_line_list,
            'lt_pr_line_list' => $lt_pr_line_list,
            'route_group' => $route_group
        ]);
    }

    public function getLongTermRentalLinesForAddPRLine($long_term_rental)
    {
        $lt_month = LongTermRentalMonth::where('lt_rental_id', $long_term_rental->id)
            // ->where('month', $long_term_rental->rental_duration)
            ->first();
        $rental_lines = LongTermRentalLine::leftjoin('car_classes', 'car_classes.id', '=', 'lt_rental_lines.car_class_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'lt_rental_lines.car_color_id')
            ->select(
                'lt_rental_lines.id',
                'lt_rental_lines.amount as amount',
                'car_classes.full_name as full_name',
                'car_classes.name as name',
                'car_colors.name as color_name',
            )
            ->where('lt_rental_lines.lt_rental_id', $long_term_rental->id)
            ->get()->map(function ($item) use ($lt_month) {
                return [
                    'lt_line' => $item->id,
                    'lt_line_text' => $item->full_name . ' - ' . $item->color_name,
                    'amount' => $item->amount,
                    'month' => $lt_month->id,
                    'month_text' => $lt_month->month,
                    'remark' => '',
                    'approved_rental_files' => [],

                ];
            });
        return $rental_lines;
    }

    public function getLongTermRentalPRLine($long_term_rental_id)
    {
        $lt_rental_pr_lines = LongTermRentalPRLine::where('lt_rental_id', $long_term_rental_id)
            ->get()
            ->map(function ($item) {
                $lt_rental_line = LongTermRentalLine::find($item->lt_rental_line_id);
                $item->lt_line = $item->lt_rental_line_id;
                $item->lt_line_text = ($lt_rental_line->carClass) ? $lt_rental_line->carClass->full_name . ' - ' : '';
                $item->lt_line_text .= ($lt_rental_line->color) ? $lt_rental_line->color->name : '';
                $item->month = $item->lt_rental_month_id;
                $item->month_text = ($item->ltMonth) ? $item->ltMonth->month : '';
                $medias = $item->getMedia('lt_pr_approval_rental_files');
                $approved_rental_files = get_medias_detail($medias);
                $approved_rental_files = collect($approved_rental_files)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->approved_rental_files = $approved_rental_files;
                return $item;
            });
        return $lt_rental_pr_lines;
    }

    public function showPRLongTermRentalLine(LongTermRental $long_term_rental)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRental);
        $lt_status_is_completed = (strcmp($long_term_rental->status, LongTermRentalStatusEnum::COMPLETE) === 0) ? true : false;
        // if ($lt_status_is_completed) {
        //     return redirect()->back();
        // }

        $lt_rental_line_list = $this->getLongTermRentalLinesForAddPRLine($long_term_rental);
        $lt_pr_line_list = $this->getLongTermRentalPRLine($long_term_rental->id);
        $approved_rental_files = $long_term_rental->getMedia('approval_rental_files');
        $approved_rental_files = get_medias_detail($approved_rental_files);
        $approval_status_list = LongTermRentalTrait::getLongTermRentalApproveStatusList();
        $allow_confirm = true;
        $route_group = [
            'route_lt_rental' => route('admin.long-term-rentals.show', ['long_term_rental' => $long_term_rental]),
            'route_pr_line' => route('admin.long-term-rentals.pr-lines.show', ['long_term_rental' => $long_term_rental]),
            'route_car_contract' => route('admin.long-term-rentals.car-info-and-deliver.show', ['long_term_rental' => $long_term_rental])
        ];
        $page_title = __('lang.view') . __('long_term_rentals.page_title');
        return view('admin.long-term-rental-pr-lines.view', [
            'd' => $long_term_rental,
            'page_title' => $page_title,
            'approval_status_list' => $approval_status_list,
            'approved_rental_files' => $approved_rental_files,
            'allow_confirm' => $allow_confirm,
            'lt_rental_line_list' => $lt_rental_line_list,
            'lt_pr_line_list' => $lt_pr_line_list,
            'route_group' => $route_group,
            'view_mode' => true,
        ]);
    }

    public function storePRLongTermRentalLine(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRental);
        $step_approve_management = new StepApproveManagement();
        $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::PURCHASE_REQUISITION);
        if (!$is_configured) {
            return $this->responseWithCode(false, __('lang.config_approve_warning') . __('purchase_requisitions.page_title'), null, 422);
        }
        $validate_data = [
            'approve_status' => 'required',
        ];

        $is_complete = (strcmp($request->approve_status, LongTermRentalStatusEnum::COMPLETE) === 0) ? true : false;
        if ($is_complete) {
            $validate_data['lt_pr_lines'] = 'required|array|min:1';
            $validate_data['require_date'] = 'required';
        }

        $validator = Validator::make($request->all(), $validate_data, [], [
            'approve_status' => __('long_term_rentals.approve_status'),
            'require_date' => __('purchase_requisitions.require_date'),
            'lt_pr_lines' => __('long_term_rentals.pr_line'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $lt_rental_id = $request->id;
        $long_term_rental = LongTermRental::find($lt_rental_id);
        $lt_status_is_completed = (strcmp($long_term_rental->status, LongTermRentalStatusEnum::COMPLETE) === 0) ? true : false;
        if ($lt_status_is_completed) {
            return response()->json([
                'success' => false,
                'message' => ''
            ], 422);
        }
        $long_term_rental->status = $request->approve_status;
        $long_term_rental->require_date = $request->require_date;
        $long_term_rental->save();

        if ($request->approval_rental_files__pending_delete_ids) {
            $pending_delete_ids = $request->approval_rental_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $long_term_rental->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('approved_rental_file')) {
            foreach ($request->file('approved_rental_file') as $image) {
                if ($image->isValid()) {
                    $long_term_rental->addMedia($image)->toMediaCollection('approval_rental_files');
                }
            }
        }

        $lt_pr_lines = $request->lt_pr_lines;
        if ($request->pending_delete_lt_pr_line_ids) {
            LongTermRentalPRLine::whereIn('id', $request->pending_delete_lt_pr_line_ids)->delete();
        }
        if ($lt_pr_lines) {
            foreach ($lt_pr_lines as $key => $lt_pr_line) {
                if ($lt_pr_line['id']) {
                    $pr_line = LongTermRentalPRLine::find($lt_pr_line['id']);
                } else {
                    $pr_line = new LongTermRentalPRLine;
                }
                $pr_line->lt_rental_id = $lt_rental_id;
                $pr_line->lt_rental_line_id = $lt_pr_line['lt_rental_line_id'];
                $pr_line->lt_rental_month_id = $lt_pr_line['lt_rental_month_id'];
                $pr_line->amount = $lt_pr_line['amount'];
                $pr_line->remark = $lt_pr_line['remark'];
                $pr_line->save();

                if ((!empty($request->lt_pr_files)) && (sizeof($request->lt_pr_files) > 0)) {
                    $all_lt_pr_files = $request->lt_pr_files;
                    if (isset($all_lt_pr_files[$key])) {
                        $lt_pr_files = $all_lt_pr_files[$key];
                        foreach ($lt_pr_files as $lt_pr_file) {
                            if ($lt_pr_file) {
                                $pr_line->addMedia($lt_pr_file)->toMediaCollection('lt_pr_approval_rental_files');
                            }
                        }
                    }
                }
            }
        }
        if ($is_complete) {
            $this->createPurchaseRequisition($long_term_rental);
            $this->sendNotificationLongTermRentalPr($long_term_rental, $long_term_rental->worksheet_no);
        }
        $redirect_route = route('admin.long-term-rentals.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function createPurchaseRequisition($long_term_rental)
    {
        $pr_count = PurchaseRequisition::all()->count() + 1;
        $prefix = 'PR';
        $purchase_requisition = new PurchaseRequisition();
        $purchase_requisition->pr_no = generateRecordNumber($prefix, $pr_count);
        $purchase_requisition->request_date = date('Y-m-d');
        $purchase_requisition->require_date = $long_term_rental->require_date;
        $purchase_requisition->rental_type = RentalTypeEnum::LONG;
        $purchase_requisition->reference_type = LongTermRental::class;
        $purchase_requisition->reference_id = $long_term_rental->id;
        $purchase_requisition->status = PRStatusEnum::PENDING_REVIEW;
        $purchase_requisition->save();

        $step_approve_management = new StepApproveManagement();
        $step_approve_management->createModelApproval(ConfigApproveTypeEnum::PURCHASE_REQUISITION, PurchaseRequisition::class, $purchase_requisition->id);

        $medias = $long_term_rental->getMedia('approval_rental_files');
        if ($medias) {
            foreach ($medias as $media) {
                try {
                    $copied_media = $media->copy($purchase_requisition, 'rental_images');
                } catch (Exception $e) {
                    //
                }
            }
        }

        $lt_pr_lines = LongTermRentalPRLine::where('lt_rental_id', $long_term_rental->id)->get();
        $pr_line_arr = [];
        foreach ($lt_pr_lines as $lt_pr_line) {
            $medias = $lt_pr_line->getMedia('lt_pr_approval_rental_files');
            if ($medias) {
                foreach ($medias as $media) {
                    $copied_media = $media->copy($purchase_requisition, 'rental_images');
                }
            }
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
                $purchase_requisition_line->purchase_requisition_id = $purchase_requisition->id;
                $purchase_requisition_line->car_class_id = $lt_rental_line->car_class_id;
                $purchase_requisition_line->car_color_id = $lt_rental_line->car_color_id;
                $purchase_requisition_line->amount = $amount;
                $purchase_requisition_line->remark = $lt_rental_line->remark;
                $purchase_requisition_line->save();

                $long_term_line_accessory = LongTermRentalLineAccessory::where('lt_rental_line_id', $lt_rental_line->id)->get();
                if (!empty($long_term_line_accessory)) {
                    foreach ($long_term_line_accessory as $item_line_accessory) {
                        $purchase_requisition_line_accessory = new PurchaseRequisitionLineAccessory();
                        $purchase_requisition_line_accessory->purchase_requisition_line_id = $purchase_requisition_line->id;
                        $purchase_requisition_line_accessory->accessory_id = $item_line_accessory->accessory_id;
                        $purchase_requisition_line_accessory->amount = $item_line_accessory->amount / $amount;
                        // $purchase_requisition_line_accessory->amount = $item_line_accessory->amount * $amount;
                        $purchase_requisition_line_accessory->type_accessories = $item_line_accessory->type_accessories;
                        $purchase_requisition_line_accessory->remark = $item_line_accessory->remark;
                        $purchase_requisition_line_accessory->save();
                    }
                }
            }
        }
        return true;
    }

    public function sendNotificationLongTermRentalPr($modelLongtermRental, $dataWorkSheetNo = null)
    {
        $dataDepartment = [
            DepartmentEnum::PCD_PURCHASE,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
//        $url = route('admin.quotations.show', ['quotation' => $modelQuotation]);
        $notiTypeChange = new NotificationManagement('ยืนยันเช่า ', 'ใบขอเช่ายาว ' . $dataWorkSheetNo . ' ได้ยืนยันเช่า กรุณาดำเนินการจัดซื้อรถ ', null, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, [], 'success');
        $notiTypeChange->send();
    }
}
