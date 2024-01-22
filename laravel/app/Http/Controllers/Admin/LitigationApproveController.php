<?php

namespace App\Http\Controllers\Admin;

use Actions;
use App\Classes\StepApproveManagement;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\LitigationStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Litigation;
use App\Models\LitigationTrackCost;
use App\Models\LitigationTrackStatus;
use App\Models\User;
use App\Traits\HistoryTrait;
use App\Traits\LitigationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class LitigationApproveController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LitigationApprove);
        $accuser_defendant = $request->accuser_defendant;
        $worksheet_no = $request->worksheet_no;
        $tls_type = $request->tls_type;
        $case_type = $request->case_type;
        $due_date = $request->due_date;
        $status = $request->status;
        $worksheet_no_text = null;
        if ($worksheet_no) {
            $litigation = Litigation::find($worksheet_no);
            $worksheet_no_text = $litigation ? $litigation->worksheet_no : null;
        }
        $title_id = $request->title_id;
        $title_text = null;
        if ($title_id) {
            $litigation = Litigation::find($title_id);
            $title_text = $litigation ? $litigation->title : null;
        }
        $list = Litigation::search($request->s, $request)
            ->orderBy('created_at', 'desc')
            ->whereIn('status', [
                LitigationStatusEnum::PENDING_REVIEW,
                LitigationStatusEnum::CONFIRM,
                LitigationStatusEnum::REJECT,
            ])
            ->paginate(PER_PAGE);
        $tls_type_list = LitigationTrait::getTLSTypeList();
        $case_type_list = LitigationTrait::getCaseTypeList();
        $status_list = LitigationTrait::getLitigationApproveStatusList();
        return view('admin.litigation-approves.index', [
            'list' => $list,
            's' => $request->s,
            'tls_type_list' => $tls_type_list,
            'case_type_list' => $case_type_list,
            'status_list' => $status_list,
            'accuser_defendant' => $accuser_defendant,
            'worksheet_no' => $worksheet_no,
            'worksheet_no_text' => $worksheet_no_text,
            'tls_type' => $tls_type,
            'case_type' => $case_type,
            'due_date' => $due_date,
            'title_id' => $title_id,
            'title_text' => $title_text,
            'status' => $status
        ]);
    }

    public function show(Litigation $litigation_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::LitigationApprove);
        $litigation = $litigation_approve;
        $tls_type_list = LitigationTrait::getTLSTypeList();
        $case_type_list = LitigationTrait::getCaseTypeList();
        $media = $litigation->getMedia('additional_files');
        $media = get_medias_detail($media);
        $additional_files = collect($media)->map(function ($item) {
            $item['formated'] = true;
            $item['saved'] = true;
            $item['raw_file'] = null;
            $item['date'] = get_date_time_by_format($item['created_at']);
            return $item;
        })->toArray();

        $litigation_files = $litigation->getMedia('litigation_files');
        $litigation_files = get_medias_detail($litigation_files);
        $litigation_files = collect($litigation_files)->map(function ($item) {
            $item['formated'] = true;
            $item['saved'] = true;
            $item['raw_file'] = null;
            return $item;
        })->toArray();

        $responsible_person_name =null;
        if (!empty($litigation->responsible_person_id)) {
            $user = User::find($litigation->responsible_person_id);
            $responsible_person_name = $user ? $user->name : null;
        } 

        $status_list = LitigationTrackStatus::where('litigation_id', $litigation->id)->get();
        foreach ($status_list as $key => $item) {
            $item->status_text = __('litigations.case_status_' . $item->status);
        }

        $cost_list = LitigationTrackCost::where('litigation_id', $litigation->id)->get();
        $summary = 0;
        foreach ($cost_list as $key => $item) {
            $item->bank_text = $item->bank?->key;
            $item->payment_channel_text = __('litigations.payment_channel_' . $item->payment_channel);
            $summary += floatval($item->amount);
        }
        $summary = number_format($summary, 2, '.', ',');
        $police_follow_status_list = ($litigation->status === LitigationStatusEnum::PENDING || sizeof($status_list) <= 0) ? LitigationTrait::getInitPoliceFollowStatusList() : LitigationTrait::getPoliceFollowStatusList();
        $court_follow_status_list = ($litigation->status === LitigationStatusEnum::PENDING || sizeof($status_list) <= 0) ? LitigationTrait::getInitCourtFollowStatusList() : LitigationTrait::getCourtFollowStatusList();
        $case_name_list = LitigationTrait::getCaseNameList();
        $location_name_list = LitigationTrait::getLocationCaseList();
        $payment_channel_list = LitigationTrait::getPaymentChannelList();
        $bank_list = LitigationTrait::getBankList();
        $approve_line = HistoryTrait::getHistory(Litigation::class, $litigation->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            $approve_line_owner = $approve_line_owner->checkCanApprove(Litigation::class, $litigation->id, ConfigApproveTypeEnum::LITIGATION);

        } else {
            $approve_line_owner = null;
        }
        $page_title = __('lang.view') . __('litigations.approve_page_title');
        return view('admin.litigation-approves.form', [
            'd' => $litigation,
            'page_title' => $page_title,
            'tls_type_list' => $tls_type_list,
            'case_type_list' => $case_type_list,
            'police_follow_status_list' => $police_follow_status_list,
            'court_follow_status_list' => $court_follow_status_list,
            'case_name_list' => $case_name_list,
            'location_name_list' => $location_name_list,
            'status_list' => $status_list,
            'bank_list' => $bank_list,
            'payment_channel_list' => $payment_channel_list,
            'cost_list' => $cost_list,
            'summary' => $summary,
            'additional_files' => $additional_files,
            'litigation_files' => $litigation_files,
            'responsible_person_name' => $responsible_person_name,
            'view' => true
        ]);
    }

    public function updateStatus(Request $request)
    {
        __log($request->all());
        if (
            in_array($request->status, [
                LitigationStatusEnum::REJECT,
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

        if ($request->litigation_id) {
            $litigation = Litigation::find($request->litigation_id);
            if (!$litigation) {
                return response()->json([
                    'success' => false,
                    'message' => __('lang.not_found'),
                    'redirect' => route('admin.litigation-approves.index')
                ]);
            }
            if (in_array($request->status, [LitigationStatusEnum::REJECT])) {
                $litigation->status = $request->status;
                // $litigation->reason = $request->reject_reason;
            } else {
                // update approve step
                $approve_update = new StepApproveManagement();
                // $approve_update = $approve_update->updateApprove($request, $litigation, $request->status, Litigation::class);
                $approve_update = $approve_update->updateApprove(Litigation::class, $litigation->id, $request->status, null, $request->reject_reason);

                $litigation->status = $approve_update;
                // $litigation->reason = $request->reject_reason;
            }

            // if (in_array($request->status, [LitigationStatusEnum::CONFIRM, LitigationStatusEnum::REJECT])) {
            //     $user = Auth::user();
            //     $litigation->reviewed_by = $user->id;
            //     $litigation->reviewed_at = date('Y-m-d H:i:s');
            // }
            $litigation->save();

            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => route('admin.litigation-approves.index')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
                'redirect' => route('admin.litigation-approves.index')
            ]);
        }
    }
}