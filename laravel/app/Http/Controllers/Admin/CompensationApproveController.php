<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\CompensationStatusEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\NegotiationResultEnum;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Compensation;
use App\Models\CompensationNegotiation;
use App\Models\Province;
use App\Traits\CompensationTrait;
use Illuminate\Http\Request;
use App\Enums\Actions;
use App\Enums\Resources;
use Illuminate\Support\Facades\Validator;

class CompensationApproveController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CompensationApprove);
        $compensation_id = $request->compensation_id;
        $worksheet_no = null;
        if ($compensation_id) {
            $compensation = Compensation::find($compensation_id);
            $worksheet_no = $compensation ? $compensation->worksheet_no : null;
        }

        $accident_id = $request->accident_id;
        $accident_worksheet_no = null;
        if ($accident_id) {
            $accident = Accident::find($accident_id);
            $accident_worksheet_no = $accident ? $accident->worksheet_no : null;
        }

        $accident_date = $request->accident_date;
        $end_date = $request->end_date;
        $complaint_type = $request->complaint_type;
        $status = $request->status;
        $list = Compensation::search($request->s, $request)
            ->whereIn('status', [CompensationStatusEnum::PENDING_REVIEW, CompensationStatusEnum::CONFIRM, CompensationStatusEnum::REJECT])
            ->paginate(PER_PAGE);

        $complaint_type_list = CompensationTrait::getComplaintTypeList();
        $status_list = CompensationTrait::getApproveStatusList();
        return view('admin.compensation-approves.index', [
            'list' => $list,
            's' => $request->s,
            'complaint_type_list' => $complaint_type_list,
            'status_list' => $status_list,
            'compensation_id' => $compensation_id,
            'worksheet_no' => $worksheet_no,
            'accident_id' => $accident_id,
            'accident_worksheet_no' => $accident_worksheet_no,
            'accident_date' => $accident_date,
            'end_date' => $end_date,
            'complaint_type' => $complaint_type,
            'status' => $status,
        ]);
    }

    public function show(Compensation $compensation_approve)
    {
        $insuer_name = $compensation_approve->insurer ? $compensation_approve->insurer->insurance_name_th : null;
        $car_brand_name = $compensation_approve->carBrand ? $compensation_approve->carBrand->name : null;
        $creator_name = $compensation_approve->claimBy ? $compensation_approve->claimBy->name : null;

        $claim_total = $compensation_approve->claim_amount * $compensation_approve->claim_days;
        $compensation_approve->claim_amount_total = price_format($claim_total, true);
        $compensation_approve->claim_amount_total_text = bahtText(price_format($claim_total, false));

        $termination_avg = $compensation_approve->termination_amount / $compensation_approve->termination_days;
        $compensation_approve->termination_avg = price_format($termination_avg, true);

        $termination_files = $compensation_approve->getMedia('termination_files');
        $termination_files = get_medias_detail($termination_files);

        $receive_files = $compensation_approve->getMedia('receive_files');
        $receive_files = get_medias_detail($receive_files);

        $payment_files = $compensation_approve->getMedia('payment_files');
        $payment_files = get_medias_detail($payment_files);

        $tax_invoice_files = $compensation_approve->getMedia('tax_invoice_files');
        $tax_invoice_files = get_medias_detail($tax_invoice_files);
        $complain_type_list = CompensationTrait::getComplaintTypeList();
        $province_list = Province::select('name_th as name', 'id')->orderBy('name_th')->get();
        $negotiation_type_list = CompensationTrait::getNegotiationTypeList();
        $negotiation_result_list = CompensationTrait::getNegotiationResultList();
        $sending_channel_list = CompensationTrait::getSendingChannelList();

        $negotiation_list = CompensationNegotiation::where('compensation_id', $compensation_approve->id)->get();
        $is_end_nogotiation = $negotiation_list->where('result', NegotiationResultEnum::END)->count();
        $page_title = __('lang.view') . __('compensations.page_title');
        return view('admin.compensation-approves.form', [
            'd' => $compensation_approve,
            'page_title' => $page_title,
            'complain_type_list' => $complain_type_list,
            'province_list' => $province_list,
            'negotiation_type_list' => $negotiation_type_list,
            'negotiation_result_list' => $negotiation_result_list,
            'sending_channel_list' => $sending_channel_list,
            'negotiation_list' => $negotiation_list,
            'is_end_nogotiation' => $is_end_nogotiation,
            'insuer_name' => $insuer_name,
            'can_edit_notice' => null,
            'can_edit_termination_files' => null,
            'can_edit_payment_files' => null,
            'car_brand_name' => $car_brand_name,
            'creator_name' => $creator_name,
            'termination_files' => $termination_files,
            'receive_files' => $receive_files,
            'payment_files' => $payment_files,
            'tax_invoice_files' => $tax_invoice_files,
        ]);
    }

    public function updateStatus(Request $request)
    {
        if (
            in_array($request->status, [
                CompensationStatusEnum::REJECT,
            ])
        ) {
            $validator = Validator::make($request->all(), [
                'reject_reason' => ['required', 'max:255'],
            ], [], [
                    'reject_reason' => __('lang.reason')
                ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        if ($request->compensation_id) {
            $compensation = Compensation::find($request->compensation_id);
            if (!$compensation) {
                return response()->json([
                    'success' => false,
                    'message' => __('lang.not_found'),
                    'redirect' => route('admin.compensations-approves.index')
                ]);
            }
            if (in_array($request->status, [CompensationStatusEnum::REJECT])) {
                $compensation->status = $request->status;
            } else {
                $approve_update = new StepApproveManagement();
                $approve_update = $approve_update->updateApprove(Compensation::class, $compensation->id, $request->status, ConfigApproveTypeEnum::COMPENSATION, $request->reject_reason);
                __log($request->status);
                __log($approve_update);
                $compensation->status = $approve_update;
            }

            $compensation->save();
            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => route('admin.compensation-approves.index')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
                'redirect' => route('admin.compensation-approves.index')
            ]);
        }
    }
}