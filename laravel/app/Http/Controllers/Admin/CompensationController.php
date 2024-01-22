<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\CompensationStatusEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\NegotiationResultEnum;
use App\Enums\NegotiationTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Compensation;
use App\Models\CompensationDemandLetter;
use App\Models\CompensationNegotiation;
use App\Models\Province;
use App\Traits\CompensationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompensationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Compensation);
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
        $list = Compensation::with('accident')
            ->search($request->s, $request)
            ->paginate(PER_PAGE);

        $complaint_type_list = CompensationTrait::getComplaintTypeList();
        $status_list = CompensationTrait::getStatusList();
        return view('admin.compensations.index', [
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


    public function edit(Compensation $compensation)
    {
        $insuer_name = $compensation->insurer ? $compensation->insurer->insurance_name_th : null;
        $car_brand_name = $compensation->carBrand ? $compensation->carBrand->name : null;
        $creator_name = $compensation->claimBy ? $compensation->claimBy->name : null;

        $claim_total = $compensation->claim_amount * $compensation->claim_days;
        $compensation->claim_amount_total = price_format($claim_total, true);
        $compensation->claim_amount_total_text = bahtText(price_format($claim_total, false));

        $termination_avg = ($compensation->termination_amount && $compensation->termination_days) ? $compensation->termination_amount / $compensation->termination_days : null;
        $compensation->termination_avg = price_format($termination_avg, true);

        $termination_files = $compensation->getMedia('termination_files');
        $termination_files = get_medias_detail($termination_files);

        $receive_files = $compensation->getMedia('receive_files');
        $receive_files = get_medias_detail($receive_files);

        $payment_files = $compensation->getMedia('payment_files');
        $payment_files = get_medias_detail($payment_files);

        $tax_invoice_files = $compensation->getMedia('tax_invoice_files');
        $tax_invoice_files = get_medias_detail($tax_invoice_files);

        $notice_list = CompensationDemandLetter::where('compensation_id', $compensation->id)->get();
        $complain_type_list = CompensationTrait::getComplaintTypeList();
        $province_list = Province::select('name_th as name', 'id')->orderBy('name_th')->get();
        $negotiation_type_list = CompensationTrait::getNegotiationTypeList();
        $negotiation_result_list = CompensationTrait::getNegotiationResultList();
        $sending_channel_list = CompensationTrait::getSendingChannelList();
        $negotiation_list = CompensationNegotiation::where('compensation_id', $compensation->id)->get();
        $is_end_nogotiation = $negotiation_list->where('result', NegotiationResultEnum::END)->count();
        $can_edit_notice = in_array($compensation->status, [CompensationStatusEnum::UNDER_NEGOTIATION]);
        $can_edit_termination_files = in_array($compensation->status, [CompensationStatusEnum::END_NEGOTIATION]);
        $can_edit_payment_files = in_array($compensation->status, [CompensationStatusEnum::CONFIRM]);
        $page_title = __('lang.edit') . __('compensations.page_title');
        return view('admin.compensations.form', [
            'd' => $compensation,
            'page_title' => $page_title,
            'complain_type_list' => $complain_type_list,
            'province_list' => $province_list,
            'negotiation_type_list' => $negotiation_type_list,
            'negotiation_result_list' => $negotiation_result_list,
            'sending_channel_list' => $sending_channel_list,
            'negotiation_list' => $negotiation_list,
            'is_end_nogotiation' => $is_end_nogotiation,
            'can_edit_notice' => $can_edit_notice,
            'can_edit_termination_files' => $can_edit_termination_files,
            'can_edit_payment_files' => $can_edit_payment_files,
            'insuer_name' => $insuer_name,
            'car_brand_name' => $car_brand_name,
            'creator_name' => $creator_name,
            'termination_files' => $termination_files,
            'receive_files' => $receive_files,
            'payment_files' => $payment_files,
            'tax_invoice_files' => $tax_invoice_files,
            'notice_list' => $notice_list
        ]);
    }

    public function show(Compensation $compensation)
    {
        $insuer_name = $compensation->insurer ? $compensation->insurer->insurance_name_th : null;
        $car_brand_name = $compensation->carBrand ? $compensation->carBrand->name : null;
        $creator_name = $compensation->claimBy ? $compensation->claimBy->name : null;

        $claim_total = $compensation->claim_amount * $compensation->claim_days;
        $compensation->claim_amount_total = price_format($claim_total, true);
        $compensation->claim_amount_total_text = bahtText(price_format($claim_total, false));

        $termination_avg = $compensation->termination_amount / $compensation->termination_days;
        $compensation->termination_avg = price_format($termination_avg, true);

        $termination_files = $compensation->getMedia('termination_files');
        $termination_files = get_medias_detail($termination_files);

        $receive_files = $compensation->getMedia('receive_files');
        $receive_files = get_medias_detail($receive_files);

        $payment_files = $compensation->getMedia('payment_files');
        $payment_files = get_medias_detail($payment_files);

        $tax_invoice_files = $compensation->getMedia('tax_invoice_files');
        $tax_invoice_files = get_medias_detail($tax_invoice_files);
        $complain_type_list = CompensationTrait::getComplaintTypeList();
        $province_list = Province::select('name_th as name', 'id')->orderBy('name_th')->get();
        $negotiation_type_list = CompensationTrait::getNegotiationTypeList();
        $negotiation_result_list = CompensationTrait::getNegotiationResultList();
        $sending_channel_list = CompensationTrait::getSendingChannelList();
        $negotiation_list = CompensationNegotiation::where('compensation_id', $compensation->id)->get();
        $is_end_nogotiation = $negotiation_list->where('result', NegotiationResultEnum::END)->count();
        $page_title = __('lang.view') . __('compensations.page_title');
        return view('admin.compensations.form', [
            'd' => $compensation,
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

    public function store(Request $request)
    {
        // dd($request->all());
        $this->authorize(Actions::Manage . '_' . Resources::Litigation);
        $compensation = Compensation::findOrFail($request->id);
        if ($compensation->status === CompensationStatusEnum::DATA_COLLECTION) {
            $validator = Validator::make($request->all(), [
                'claim_type' => ['required'],
                'insurer_parties_id' => ['required'],
                'insurer_parties_address' => ['required', 'string', 'max:100'],
                'name_parties' => ['required', 'string', 'max:100'],
                'tel_parties' => ['required', 'numeric', 'digits:10'],
                'address_parties' => ['required', 'string', 'max:100'],
                'id_card_parties' => ['required', 'numeric', 'digits:13'],
                'vmi_no_parties' => ['required', 'string', 'max:50'],
                'claim_no_parties' => ['required', 'string', 'max:50'],
                'car_type_parties' => ['required', 'string', 'max:50'],
                'car_brand_parties_id' => ['required'],
                'license_plate_parties' => ['required', 'string', 'max:50'],
                'province_parties_id' => ['required', 'string', 'max:50'],
                'creator_id' => ['required'],
                'claim_amount' => ['required', 'string', 'max:10'],
                'claim_days' => ['required', 'numeric'],
            ], [], [
                    'claim_type' => __('compensations.claim_type'),
                    'insurer_parties_id' => __('compensations.insurer_parties_id'),
                    'insurer_parties_address' => __('compensations.insurer_parties_address'),
                    'name_parties' => __('compensations.name_parties'),
                    'tel_parties' => __('compensations.tel_parties'),
                    'address_parties' => __('compensations.address_parties'),
                    'id_card_parties' => __('compensations.id_card_parties'),
                    'vmi_no_parties' => __('compensations.vmi_no_parties'),
                    'claim_no_parties' => __('compensations.claim_no_parties'),
                    'car_type_parties' => __('compensations.car_type_parties'),
                    'car_brand_parties_id' => __('compensations.car_brand_parties_id'),
                    'license_plate_parties' => __('compensations.license_plate_parties'),
                    'province_parties_id' => __('compensations.provinces'),
                    'creator_id' => __('compensations.creator'),
                    'claim_amount' => __('compensations.claim_amount'),
                    'claim_days' => __('compensations.claim_days'),
                ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            $compensation->type = $request->claim_type;
            $compensation->verdict_date = $request->verdict_date;
            $compensation->insurer_parties_id = $request->insurer_parties_id;
            $compensation->insurer_parties_address = $request->insurer_parties_address;
            $compensation->name_parties = $request->name_parties;
            $compensation->tel_parties = $request->tel_parties;
            $compensation->address_parties = $request->address_parties;
            $compensation->id_card_parties = $request->id_card_parties;
            $compensation->vmi_no_parties = $request->vmi_no_parties;
            $compensation->claim_no_parties = $request->claim_no_parties;
            $compensation->car_type_parties = $request->car_type_parties;
            $compensation->car_brand_parties_id = $request->car_brand_parties_id;
            $compensation->license_plate_parties = $request->license_plate_parties;
            $compensation->province_parties_id = $request->province_parties_id;
            $compensation->creator_id = $request->creator_id;
            $compensation->claim_amount = transform_float($request->claim_amount);
            $compensation->claim_days = transform_float($request->claim_days);
            if (empty($request->is_draft)) {
                $compensation->status = CompensationStatusEnum::UNDER_NEGOTIATION;
            }
            $compensation->save();
            $redirect_route = route('admin.compensations.index');
            return $this->responseValidateSuccess($redirect_route);
        }

        if ($compensation->status === CompensationStatusEnum::UNDER_NEGOTIATION) {
            $validator = Validator::make($request->all(), [
                'negotiation_type' => ['required'],
                'negotiator' => ['string', 'max:100', 'nullable'],
            ], [], [
                    'negotiation_type' => __('compensations.negotiation_type'),
                    'negotiator' => __('compensations.negotiator'),
                ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            if (strcmp($request->negotiation_type, NegotiationTypeEnum::INSURANCE) === 0) {
                __log($request->all());
                $validator = Validator::make($request->all(), [
                    'insurance_report_date' => ['required'],
                    'insurance_negotiation_result' => ['string', 'max:50', 'required'],
                    'insurance_negotiation_amount' => ['required', 'string', 'max:10'],
                    'insurance_channel_result' => ['nullable', 'string', 'max:50'],
                    'insurance_negotiation_person' => ['nullable', 'string', 'max:100'],
                    'insurance_negotiation_tel' => ['nullable', 'numeric', 'digits:10'],
                    'insurance_negotiation_remark' => ['nullable', 'string', 'max:100'],
                    'insurance_negotiation_date' => ['nullable'],
                ], [], [
                        'insurance_report_date' => __('compensations.report_date'),
                        'insurance_negotiation_result' => __('compensations.negotiation_result'),
                        'insurance_negotiation_amount' => __('compensations.negotiation_amount'),
                        'insurance_channel_result' => __('compensations.channel_result'),
                        'insurance_negotiation_person' => __('compensations.negotiation_person'),
                        'insurance_negotiation_tel' => __('compensations.negotiation_tel'),
                        'insurance_negotiation_date' => __('compensations.negotiation_date'),
                        'insurance_negotiation_remark' => __('compensations.negotiation_remark'),
                    ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }
                $negotiation = new CompensationNegotiation();
                $negotiation->compensation_id = $compensation->id;
                $negotiation->type = $request->negotiation_type;
                $negotiation->negotiator = $request->negotiator;
                $negotiation->report_date = $request->insurance_report_date;
                $negotiation->result = $request->insurance_negotiation_result;
                $negotiation->amount = transform_float($request->insurance_negotiation_amount);
                $negotiation->channel_result = $request->insurance_channel_result;
                $negotiation->person = $request->insurance_negotiation_person;
                $negotiation->tel = $request->insurance_negotiation_tel;
                $negotiation->remark = $request->insurance_negotiation_remark;
                $negotiation->negotiation_date = $request->insurance_negotiation_date;
                $negotiation->save();
            }

            if (strcmp($request->negotiation_type, NegotiationTypeEnum::OIC) === 0) {
                $validator = Validator::make($request->all(), [
                    'oic_report_date' => ['required'],
                    'oic_negotiation_result' => ['string', 'max:50', 'required'],
                    'oic_negotiation_amount' => ['required', 'string', 'max:10'],
                    'oic_channel_result' => ['nullable', 'string', 'max:50'],
                    'oic_negotiation_person' => ['nullable', 'string', 'max:100'],
                    'oic_negotiation_tel' => ['nullable', 'numeric', 'digits:10'],
                    'oic_receipt_no' => ['nullable', 'string', 'max:50'],
                    'oic_sss_no' => ['nullable', 'string', 'max:50'],
                    'oic_negotiation_remark' => ['nullable', 'string', 'max:100'],
                    'oic_negotiation_date' => ['nullable'],
                ], [], [
                        'oic_report_date' => __('compensations.report_date'),
                        'oic_negotiation_result' => __('compensations.negotiation_result'),
                        'oic_negotiation_amount' => __('compensations.negotiation_amount'),
                        'oic_channel_result' => __('compensations.channel_result'),
                        'oic_negotiation_person' => __('compensations.negotiation_person'),
                        'oic_negotiation_tel' => __('compensations.negotiation_tel'),
                        'oic_receipt_no' => __('compensations.receipt_no'),
                        'oic_sss_no' => __('compensations.sss_no'),
                        'oic_negotiation_date' => __('compensations.negotiation_date'),
                        'oic_negotiation_remark' => __('compensations.negotiation_remark'),
                    ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }
                $negotiation = new CompensationNegotiation();
                $negotiation->compensation_id = $compensation->id;
                $negotiation->type = $request->negotiation_type;
                $negotiation->negotiator = $request->negotiator;
                $negotiation->report_date = $request->oic_report_date;
                $negotiation->result = $request->oic_negotiation_result;
                $negotiation->amount = transform_float($request->oic_negotiation_amount);
                $negotiation->channel_result = $request->oic_channel_result;
                $negotiation->person = $request->oic_negotiation_person;
                $negotiation->tel = $request->oic_negotiation_tel;
                $negotiation->remark = $request->oic_negotiation_remark;
                $negotiation->negotiation_date = $request->oic_negotiation_date;
                $negotiation->receipt_no = $request->oic_receipt_no;
                $negotiation->sss_no = $request->oic_sss_no;
                $negotiation->save();
            }

            if (strcmp($negotiation->result, NegotiationResultEnum::END) === 0) {
                if (empty($request->is_draft)) {
                    $compensation->status = CompensationStatusEnum::END_NEGOTIATION;
                }
                $compensation->save();
            }

            $delete_notice_ids = $request->delete_notice_ids;
            if (!empty($delete_notice_ids) && is_array($delete_notice_ids)) {
                CompensationDemandLetter::whereIn('id', $delete_notice_ids)->delete();
            }
            if (isset($request->notices) && is_array($request->notices)) {
                foreach ($request->notices as $key => $item) {
                    $notice = CompensationDemandLetter::firstOrNew(['id' => $item['id']]);
                    $notice->compensation_id = $compensation->id;
                    $notice->delivery_date = $item['delivery_date'] ?? null;
                    $notice->rp_no = $item['rp_no'] ?? null;
                    $notice->receive_date = $item['receive_date'] ?? null;
                    $notice->recipient_name = $item['recipient_name'] ?? null;
                    $notice->remark = $item['remark'] ?? null;
                    $notice->save();
                }
            }

            $redirect_route = route('admin.compensations.index');
            return $this->responseValidateSuccess($redirect_route);
        }

        if ($compensation->status === CompensationStatusEnum::END_NEGOTIATION) {
            $step_approve_management = new StepApproveManagement();
            $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::COMPENSATION);
            if (!$is_configured) {
                return $this->responseWithCode(false, __('lang.config_approve_warning') . __('compensations.page_title'), null, 422);
            }

            $validator = Validator::make($request->all(), [
                'termination_amount' => ['required', 'string', 'max:10'],
                'termination_days' => ['required', 'string', 'max:10'],
                'oic_amount' => ['required', 'string', 'max:10'],
                'actual_payment_amount' => ['required', 'string', 'max:10'],
            ], [], [
                    'termination_amount' => __('compensations.termination_amount'),
                    'termination_days' => __('compensations.termination_days'),
                    'oic_amount' => __('compensations.oic_amount'),
                    'actual_payment_amount' => __('compensations.actual_payment_amount'),
                ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }

            $compensation->termination_amount = transform_float($request->termination_amount);
            $compensation->termination_days = transform_float($request->termination_days);
            $compensation->oic_amount = transform_float($request->oic_amount);
            $compensation->actual_payment_amount = transform_float($request->actual_payment_amount);
            if (empty($request->is_draft)) {
                $compensation->status = CompensationStatusEnum::PENDING_REVIEW;
                $step_approve_management = new StepApproveManagement();
                $step_approve_management->createModelApproval(ConfigApproveTypeEnum::COMPENSATION, Compensation::class, $compensation->id);
            }
            $compensation->save();

            if ($request->termination_files__pending_delete_ids) {
                $pending_delete_ids = $request->termination_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $compensation->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('termination_files')) {
                foreach ($request->file('termination_files') as $file) {
                    if ($file->isValid()) {
                        $compensation->addMedia($file)->toMediaCollection('termination_files');
                    }
                }
            }

            $redirect_route = route('admin.compensations.index');
            return $this->responseValidateSuccess($redirect_route);
        }

        if ($compensation->status === CompensationStatusEnum::CONFIRM) {
            $validator = Validator::make($request->all(), [
                'recipient_document' => ['required', 'string', 'max:100'],
                'sending_channel' => ['required', 'string'],
                'sending_channel_detail' => ['nullable', 'string', 'max:100'],
                'receive_date' => ['required', 'date'],
                'confirmation_date' => ['required', 'date'],
                'receive_files' => ['required'],
                'payment_files' => ['required'],
                'tax_invoice_files' => ['required'],
            ], [], [
                    'recipient_document' => __('compensations.recipient_document'),
                    'sending_channel' => __('compensations.sending_channel'),
                    'sending_channel_detail' => __('compensations.sending_channel_detail'),
                    'receive_date' => __('compensations.receive_date'),
                    'confirmation_date' => __('compensations.confirmation_date'),
                    'receive_files' => __('compensations.receive_files'),
                    'payment_files' => __('compensations.payment_files'),
                    'tax_invoice_files' => __('compensations.tax_invoice_files'),
                ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }

            $compensation->recipient_document = $request->recipient_document;
            $compensation->sending_channel = $request->sending_channel;
            $compensation->sending_channel_detail = $request->sending_channel_detail;
            $compensation->receive_date = $request->receive_date;
            $compensation->confirmation_date = $request->confirmation_date;
            $compensation->status = CompensationStatusEnum::COMPLETE;
            $compensation->save();

            if ($request->receive_files__pending_delete_ids) {
                $pending_delete_ids = $request->receive_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $compensation->deleteMedia($media_id);
                    }
                }
            }
            if ($request->hasFile('receive_files')) {
                foreach ($request->file('receive_files') as $file) {
                    if ($file->isValid()) {
                        $compensation->addMedia($file)->toMediaCollection('receive_files');
                    }
                }
            }

            if ($request->payment_files__pending_delete_ids) {
                $pending_delete_ids = $request->payment_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $compensation->deleteMedia($media_id);
                    }
                }
            }
            if ($request->hasFile('payment_files')) {
                foreach ($request->file('payment_files') as $file) {
                    if ($file->isValid()) {
                        $compensation->addMedia($file)->toMediaCollection('payment_files');
                    }
                }
            }

            if ($request->tax_invoice_files__pending_delete_ids) {
                $pending_delete_ids = $request->tax_invoice_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $compensation->deleteMedia($media_id);
                    }
                }
            }
            if ($request->hasFile('tax_invoice_files')) {
                foreach ($request->file('tax_invoice_files') as $file) {
                    if ($file->isValid()) {
                        $compensation->addMedia($file)->toMediaCollection('tax_invoice_files');
                    }
                }
            }
            $redirect_route = route('admin.compensations.index');
            return $this->responseValidateSuccess($redirect_route);
        }

        $redirect_route = route('admin.compensations.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}