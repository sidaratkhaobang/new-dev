<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\LitigationCaseStatusEnum;
use App\Enums\LitigationStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Litigation;
use App\Models\LitigationTrackCost;
use App\Models\LitigationTrackStatus;
use App\Models\User;
use App\Traits\LitigationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use LitigationLocationEnum;

class LitigationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Litigation);
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
            ->paginate(PER_PAGE);
        $tls_type_list = LitigationTrait::getTLSTypeList();
        $case_type_list = LitigationTrait::getCaseTypeList();
        $status_list = LitigationTrait::getLitigationStatusList();
        return view('admin.litigations.index', [
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

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Litigation);
        $d = new Litigation();
        $tls_type_list = LitigationTrait::getTLSTypeList();
        $case_type_list = LitigationTrait::getCaseTypeList();

        $police_follow_status_list = LitigationTrait::getInitPoliceFollowStatusList();
        $court_follow_status_list = LitigationTrait::getInitCourtFollowStatusList();
        $case_name_list = LitigationTrait::getCaseNameList();
        $location_name_list = LitigationTrait::getLocationCaseList();
        $page_title = __('lang.create') . __('litigations.page_title');
        $responsible_person_name = null;
        return view('admin.litigations.form', [
            'd' => $d,
            'page_title' => $page_title,
            'tls_type_list' => $tls_type_list,
            'case_type_list' => $case_type_list,
            'police_follow_status_list' => $police_follow_status_list,
            'court_follow_status_list' => $court_follow_status_list,
            'case_name_list' => $case_name_list,
            'location_name_list' => $location_name_list,
            'responsible_person_name' => $responsible_person_name
        ]);
    }

    public function show(Litigation $litigation)
    {
        $this->authorize(Actions::View . '_' . Resources::Litigation);
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

        $status_list = LitigationTrackStatus::where('litigation_id', $litigation->id)->get();
        foreach ($status_list as $key => $item) {
            $item->status_text = __('litigations.case_status_' . $item->status);
        }
        
        $responsible_person_name =null;
        if (!empty($litigation->responsible_person_id)) {
            $user = User::find($litigation->responsible_person_id);
            $responsible_person_name = $user ? $user->name : null;
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
        $page_title = __('lang.view') . __('litigations.page_title');
        return view('admin.litigations.form', [
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

    public function edit(Litigation $litigation)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Litigation);
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
        
        $responsible_person_name = null;
        if (!empty($litigation->responsible_person_id)) {
            $user = User::find($litigation->responsible_person_id);
            $responsible_person_name = $user ? $user->name : null;
        }

        $litigation_files = $litigation->getMedia('litigation_files');
        $litigation_files = get_medias_detail($litigation_files);
        $litigation_files = collect($litigation_files)->map(function ($item) {
            $item['formated'] = true;
            $item['saved'] = true;
            $item['raw_file'] = null;
            return $item;
        })->toArray();

        $status_list = LitigationTrackStatus::where('litigation_id', $litigation->id)->get();
        foreach ($status_list as $key => $item) {
            $item->status_text = __('litigations.case_status_' . $item->status);
        }

        $cost_list = LitigationTrackCost::where('litigation_id', $litigation->id)->get();
        $summary = 0;
        foreach ($cost_list as $key => $item) {
            ;
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
        $page_title = __('lang.edit') . __('litigations.page_title');
        return view('admin.litigations.form', [
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
            'responsible_person_name' => $responsible_person_name
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Litigation);

        $litigation = Litigation::firstOrNew(['id' => $request->id]);
        if ($request->is_close || in_array($litigation->status, [LitigationStatusEnum::PENDING_REVIEW])) {
            $step_approve_management = new StepApproveManagement();
            $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::LITIGATION);
            if (!$is_configured) {
                return $this->responseWithCode(false, __('lang.config_approve_warning') . __('litigations.page_title'), null, 422);
            }

            if (strcmp($litigation->location_case, LitigationLocationEnum::COURT) === 0) {
                $validator = Validator::make($request->all(), [
                    'red_number' => ['required', 'string', 'max:200'],
                ], [], [
                        'red_number' => __('litigations.red_number'),
                    ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }
            }
        }
        if (empty($litigation->status)) {
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:255'],
                'case' => ['required'],
                'case_type' => ['required'],
                'tls_type' => ['required'],
                'accuser_defendant' => ['required', 'string', 'max:100'],
                'consultant' => ['string', 'max:100', 'nullable'],
                'fund' => ['string', 'max:10', 'nullable'],
                'responsible_person_id' => ['nullable'],
                'incident_date' => ['required'],
                'location_case' => ['required'],
                'legal_service_provider' => ['string', 'max:100', 'nullable'],
                'legal_service_fee' => ['string', 'max:10', 'nullable'],
                'legal_note' => ['string', 'max:250', 'nullable']
            ], [], [
                    'title' => __('litigations.title'),
                    'case' => __('litigations.case'),
                    'case_type' => __('litigations.case_type'),
                    'tls_type' => __('litigations.tls_type'),
                    'incident_date' => __('litigations.incident_date'),
                    'responsible_person_id' => __('litigations.responsible_person'),
                    'location_case' => __('litigations.location_case'),
                    'accuser_defendant' => __('litigations.plaintiff_defendent'),
                    'consultant' => __('litigations.plaintiff_defendent'),
                    'fund' => __('litigations.fund'),
                    'legal_service_provider' => __('litigations.legal_service_provider'),
                    'legal_service_fee' => __('litigations.legal_service_fee'),
                    'legal_note' => __('litigations.legal_note'),
                ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            if (empty($litigation->worksheet_no)) {
                $count = DB::table('litigations')->count() + 1;
                $prefix = 'LG';
                $litigation->worksheet_no = generateRecordNumber($prefix, $count);
            }
            $litigation->title = $request->title;
            $litigation->case = $request->case;
            $litigation->case_type = $request->case_type;
            $litigation->tls_type = $request->tls_type;
            $litigation->accuser_defendant = $request->accuser_defendant;
            $litigation->consultant = $request->consultant;
            $litigation->responsible_person_id = $request->responsible_person_id;
            $litigation->fund = floatval(str_replace(',', '', $request->fund));
            $litigation->legal_service_provider = $request->legal_service_provider;
            $litigation->legal_note = $request->legal_note;
            $litigation->legal_service_fee = floatval(str_replace(',', '', $request->legal_service_fee));
            $litigation->incident_date = $request->incident_date;
            $litigation->location_case = $request->location_case;
            $litigation->details = $request->details;
            $litigation->status = LitigationStatusEnum::PENDING;
            $litigation->save();
        } else if (in_array($litigation->status, [LitigationStatusEnum::PENDING, LitigationStatusEnum::IN_PROCESS, LitigationStatusEnum::FOLLOW, LitigationStatusEnum::PENDING_REVIEW])) {
            if (strcmp($litigation->location_case, LitigationLocationEnum::COURT) === 0) {
                $validator = Validator::make($request->all(), [
                    'court_filing_date' => ['required'],
                    'location_name' => ['required', 'string', 'max:200'],
                    'red_number' => ['nullable', 'string', 'max:100'],
                    'black_number' => ['nullable', 'string', 'max:100'],
                    'age' => ['required', 'integer'],
                    'remark' => ['required', 'string'],
                    'due_date' => ['required'],
                ], [], [
                        'court_filing_date' => __('litigations.sue_date'),
                        'location_name' => __('litigations.court_name'),
                        'red_number' => __('litigations.red_number'),
                        'black_number' => __('litigations.black_number'),
                        'age' => __('litigations.age'),
                        'remark' => __('litigations.remark'),
                        'due_date' => __('litigations.due_date'),
                    ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }
            }

            if (strcmp($litigation->location_case, LitigationLocationEnum::POLICE_STATION) === 0) {
                $validator = Validator::make($request->all(), [
                    'court_filing_date' => ['required'],
                    'location_name' => ['required', 'string', 'max:200'],
                    'inquiry_official' => ['nullable', 'string', 'max:100'],
                    'inquiry_official_tel' => ['nullable', 'string', 'max:100'],
                    'age' => ['required', 'integer'],
                    'remark' => ['required', 'string'],
                    'due_date' => ['required'],
                    'statuses' => ['required', 'array', 'min:1'],
                ], [], [
                        'court_filing_date' => __('litigations.police_filing_date'),
                        'location_name' => __('litigations.police_station'),
                        'inquiry_official' => __('litigations.inquiry_official'),
                        'inquiry_official_tel' => __('litigations.inquiry_official_tel'),
                        'age' => __('litigations.age'),
                        'remark' => __('litigations.remark'),
                        'due_date' => __('litigations.due_date'),
                        'statuses' => __('litigations.status_data'),
                    ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    __log('ff');
                    return $this->responseValidateFailed($validator);
                }
            }
            $litigation->request_date = $request->request_date;
            $litigation->receive_date = $request->receive_date;
            $litigation->court_filing_date = $request->court_filing_date;
            $litigation->location_name = $request->location_name;
            $litigation->red_number = $request->red_number;
            $litigation->black_number = $request->black_number;
            $litigation->age = $request->age;
            $litigation->remark = $request->remark;
            $litigation->due_date = $request->due_date;
            $litigation->inquiry_official = $request->inquiry_official;
            $litigation->inquiry_official_tel = $request->inquiry_official_tel;
            if (empty($request->is_draft)) {
                $litigation->status = LitigationStatusEnum::IN_PROCESS;
            }
            $litigation->save();
            $update_status = false;

            $delete_status_ids = $request->delete_status_ids;
            if (!empty($delete_status_ids) && is_array($delete_status_ids)) {
                LitigationTrackStatus::whereIn('id', $delete_status_ids)->delete();
            }
            if (isset($request->statuses) && is_array($request->statuses)) {
                foreach ($request->statuses as $key => $item) {
                    $track_status = LitigationTrackStatus::firstOrNew(['id' => $item['id']]);
                    $track_status->litigation_id = $litigation->id;
                    $track_status->date = $item['date'] ?? null;
                    $track_status->description = $item['description'] ?? null;
                    $track_status->appointment_date = $item['appointment_date'] ?? null;
                    $track_status->status = $item['status'] ?? null;
                    if (
                        in_array($item['status'], [
                            LitigationCaseStatusEnum::SUMMON,
                            LitigationCaseStatusEnum::ARREST_WARRANT,
                            LitigationCaseStatusEnum::SUE,
                            LitigationCaseStatusEnum::INVESTIGATE,
                            LitigationCaseStatusEnum::VERDICT_HEARING,
                            LitigationCaseStatusEnum::BETWEEN_APPEALING,
                            LitigationCaseStatusEnum::BETWEEN_SUPREME_COURT,
                            LitigationCaseStatusEnum::WEALTH_INVESTIGATE,
                            LitigationCaseStatusEnum::CASE_FINAL,
                        ])
                    ) {
                        $update_status = true;
                    }
                    $track_status->save();
                }
            }

            if ($update_status && strcmp($litigation->status, LitigationStatusEnum::IN_PROCESS) === 0) {
                if (empty($request->is_draft)) {
                    $litigation->status = LitigationStatusEnum::FOLLOW;
                }
                $litigation->save();
            }
        }

        if ($request->litigation_files__pending_delete_ids) {
            $pending_delete_ids = $request->litigation_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $litigation->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('litigation_files')) {
            foreach ($request->file('litigation_files') as $file) {
                if ($file->isValid()) {
                    $litigation->addMedia($file)->toMediaCollection('litigation_files');
                }
            }
        }
        $delete_cost_ids = $request->delete_cost_ids;
        if (!empty($delete_cost_ids) && is_array($delete_cost_ids)) {
            LitigationTrackCost::whereIn('id', $delete_cost_ids)->delete();
        }

        if (isset($request->costs) && is_array($request->costs)) {
            foreach ($request->costs as $key => $item) {
                $track_cost = LitigationTrackCost::firstOrNew(['id' => $item['id']]);
                $track_cost->litigation_id = $litigation->id;
                $track_cost->list = $item['list'] ?? null;
                $track_cost->number = $item['number'] ?? null;
                $track_cost->date = $item['date'] ?? null;
                $track_cost->bank_id = $item['bank_id'] ?? null;
                $track_cost->payment_channel = $item['payment_channel'] ?? null;
                $track_cost->amount = $item['amount'] ? floatval(str_replace(',', '', $item['amount'])) : 0;
                $track_cost->save();
            }
        }

        if ($request->delete_additional_file_ids) {
            $delete_additional_file_ids = $request->delete_additional_file_ids;
            if ((is_array($delete_additional_file_ids)) && (sizeof($delete_additional_file_ids) > 0)) {
                foreach ($delete_additional_file_ids as $media_id) {
                    $litigation->deleteMedia($media_id);
                }
            }
        }

        if ($request->additional_files) {
            foreach ($request->additional_files as $item) {
                if ($item['file']->isValid()) {
                    $litigation->addMedia($item['file'])
                        ->usingName($item['file_name'])
                        ->toMediaCollection('additional_files');
                }
            }
        }

        if ($request->is_close) {
            $step_approve_management = new StepApproveManagement();
            $step_approve_management->createModelApproval(ConfigApproveTypeEnum::LITIGATION, Litigation::class, $litigation->id);
            $litigation->status = LitigationStatusEnum::PENDING_REVIEW;
            $litigation->save();

        }

        $redirect_route = route('admin.litigations.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}