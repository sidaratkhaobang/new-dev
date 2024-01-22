<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\TrafficTicketDocTypeEnum;
use App\Enums\TrafficTicketPaymentStatusEnum;
use App\Enums\TrafficTicketStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\PoliceStation;
use App\Models\TrafficTicket;
use App\Rules\TelRule;
use App\Traits\TrafficTicketTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TrafficTicketController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::TrafficTicket);
        $traffic_ticket_id = $request->traffic_ticket_id;
        $traffic_ticket_no = null;
        if ($traffic_ticket_id) {
            $traffic_ticket = TrafficTicket::find($traffic_ticket_id);
            $traffic_ticket_no = $traffic_ticket ? $traffic_ticket->traffic_ticket_no : null;
        }
        $license_plate = null;
        $car_id = $request->car_id;
        if ($car_id) {
            $car = Car::find($car_id);
            $license_plate = $car ? $car->license_plate : null;
        }
        $police_station_text = null;
        $police_station_id = $request->police_station_id;
        if ($police_station_id) {
            $police_station = PoliceStation::find($police_station_id);
            $police_station_text = $police_station ? $police_station->code . " / " . $police_station->name : null;
        }
        $list = TrafficTicket::search($request->s, $request)
            ->sortable(['created_at' => 'desc'])
            ->paginate(PER_PAGE);

        foreach ($list as $key => $item) {
            $police_station = PoliceStation::find($item->police_station_id);
            $item->police_station_code = $police_station?->code ?? null;
            $item->police_station = $police_station?->name ?? null;
            $car = Car::find($item->car_id);
            $item->license_plate = $car?->license_plate ?? null;
        }
        $doc_type_list = TrafficTicketTrait::getDocumentTypeList();
        $status_list = TrafficTicketTrait::getTrafficTicketStatusList();
        return view('admin.traffic-tickets.index', [
            'list' => $list,
            's' => $request->s,
            'status_list' => $status_list,
            'doc_type_list' => $doc_type_list,
            'status' => $request->status,
            'document_type' => $request->document_type,
            'offense_date' => $request->offense_date,
            'traffic_ticket_id' => $traffic_ticket_id,
            'traffic_ticket_no' => $traffic_ticket_no,
            'car_id' => $car_id,
            'license_plate' => $license_plate,
            'police_station_id' => $request->police_station_id,
            'police_station_text' => $police_station_text,
        ]);
    }

    public function create()
    {
        $doc_type_list = TrafficTicketTrait::getDocumentTypeList();
        $d = new TrafficTicket();
        $yes_no_list = getYesNoList();
        $state = TrafficTicketTrait::checkStateStatus($d->status, TrafficTicketStatusEnum::GUITY_PENDING);
        $notice_channel_list = TrafficTicketTrait::getNoticeChannelList();
        $send_po_status_list = TrafficTicketTrait::getSendPOStatusList();
        $inbound_outbound_list = TrafficTicketTrait::getInboundOutboundList();
        $slot_list = TrafficTicketTrait::getSlotList();
        $page_title = "";
        return view('admin.traffic-tickets.form', [
            'd' => $d,
            'license_plate' => null,
            'police_station_name' => null,
            'page_title' => $page_title,
            'doc_type_list' => $doc_type_list,
            'yes_no_list' => $yes_no_list,
            'notice_channel_list' => $notice_channel_list,
            'send_po_status_list' => $send_po_status_list,
            'inbound_outbound_list' => $inbound_outbound_list,
            'slot_list' => $slot_list,
        ]);
    }

    public function edit(TrafficTicket $traffic_ticket)
    {
        $car = Car::find($traffic_ticket->car_id);
        $license_plate = $car ? $car->license_plate : null;
        $police_station = PoliceStation::find($traffic_ticket->police_station_id);
        $police_station_name = $police_station ? $police_station->name : null;
        $doc_type_list = TrafficTicketTrait::getDocumentTypeList();
        $yes_no_list = getYesNoList();
        $state = TrafficTicketTrait::checkStateStatus($traffic_ticket->status, TrafficTicketStatusEnum::GUITY_PENDING);
        $notice_channel_list = TrafficTicketTrait::getNoticeChannelList();
        $send_po_status_list = TrafficTicketTrait::getSendPOStatusList();
        $payment_status_list = TrafficTicketTrait::getPaymentStatusList();
        $report_status_list = TrafficTicketTrait::getReportStatusList();
        $inbound_outbound_list = TrafficTicketTrait::getInboundOutboundList();
        $slot_list = TrafficTicketTrait::getSlotList();
        $page_title = "";
        return view('admin.traffic-tickets.form', [
            'd' => $traffic_ticket,
            'license_plate' => $license_plate,
            'police_station_name' => $police_station_name,
            'page_title' => $page_title,
            'doc_type_list' => $doc_type_list,
            'yes_no_list' => $yes_no_list,
            'notice_channel_list' => $notice_channel_list,
            'send_po_status_list' => $send_po_status_list,
            'payment_status_list' => $payment_status_list,
            'report_status_list' => $report_status_list,
            'inbound_outbound_list' => $inbound_outbound_list,
            'slot_list' => $slot_list
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::TrafficTicket);
        $traffic_ticket = TrafficTicket::firstOrNew(['id' => $request->id]);
        if (!$traffic_ticket->exists || $traffic_ticket->status == TrafficTicketStatusEnum::DRAFT) {
            $request->merge(['fine' => transform_float($request->fine)]);
            if ($request->speed) {
                $request->merge(['speed' => transform_float($request->speed)]);
            }

            if (!boolval($request->is_draft)) {
                $validator = Validator::make($request->all(), [
                    'document_type' => ['required'],
                    'car_id' => ['required'],
                    'offense_date' => ['required'],
                    'document_date' => ['required'],
                    'traffic_ticket_no' => ['required', 'string', 'max:50'],
                    'fine' => ['required', 'numeric', ' gt:0', 'max:999999999.99'],
                    'charge' => ['required', 'string', 'max:200'],
                    'speed' => ['nullable', 'numeric', ' gt:0', 'max:99999'],
                    'location' => ['nullable', 'string', 'max:200'],
                    'culprit_name' => ['nullable', 'string', 'max:200'],
                    'culprit_tel' => ['nullable', new TelRule],
                    'culprit_address' => ['nullable', 'string', 'max:200'],
                    'police_station_id' => [
                        Rule::requiredIf(
                            in_array(
                                $request->document_type,
                                [
                                    TrafficTicketDocTypeEnum::TRAFFIC_TICKET,
                                    TrafficTicketDocTypeEnum::WARNING,
                                    TrafficTicketDocTypeEnum::VIOLATION_TRAFFIC_SIGN,
                                ]
                            )
                        )
                    ],
                    'police_address' => ['nullable', 'string', 'max:200'],
                ], [], [
                        'document_type' => __('traffic_tickets.document_type'),
                        'car_id' => __('cars.license_plate'),
                        'offense_date' => __('traffic_tickets.offense_date'),
                        'document_date' => __('traffic_tickets.document_date'),
                        'traffic_ticket_no' => __('traffic_tickets.traffic_ticket_no'),
                        'fine' => __('traffic_tickets.fine'),
                        'charge' => __('traffic_tickets.charge'),
                        'speed' => __('traffic_tickets.speed'),
                        'location' => __('traffic_tickets.location'),
                        'culprit_name' => __('traffic_tickets.culprit_name'),
                        'culprit_tel' => __('traffic_tickets.tel'),
                        'culprit_address' => __('traffic_tickets.culprit_address'),
                        'police_station_id' => __('traffic_tickets.police_station'),
                        'police_address' => __('traffic_tickets.police_address'),
                    ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }
            }
            if (!($traffic_ticket->exists)) {
                $traffic_ticket->worksheet_no = generate_worksheet_no(TrafficTicket::class);
            }
            $traffic_ticket->document_type = $request->document_type;
            $traffic_ticket->car_id = $request->car_id;
            $traffic_ticket->offense_date = $request->offense_date;
            $traffic_ticket->document_date = $request->document_date;
            $traffic_ticket->traffic_ticket_no = $request->traffic_ticket_no;
            $traffic_ticket->fine = $request->fine;
            $traffic_ticket->charge = $request->charge;
            $traffic_ticket->charge_remark = $request->charge_remark;
            $traffic_ticket->speed = $request->speed;
            $traffic_ticket->location = $request->location;
            $traffic_ticket->region_id = $request->region_id;
            $traffic_ticket->province_id = $request->province_id;
            $traffic_ticket->district_id = $request->district_id;
            $traffic_ticket->subdistrict_id = $request->subdistrict_id;
            $traffic_ticket->extra_expressway_id = $request->extra_expressway_id;
            $traffic_ticket->expressway_id = $request->expressway_id;
            $traffic_ticket->inbound_outbound = $request->inbound_outbound;
            $traffic_ticket->slot = intval($request->slot);
            $traffic_ticket->remark = $request->remark;
            $traffic_ticket->culprit_name = $request->culprit_name;
            $traffic_ticket->culprit_tel = $request->culprit_tel;
            $traffic_ticket->culprit_address = $request->culprit_address;
            $traffic_ticket->police_station_id = $request->police_station_id;
            $traffic_ticket->police_address = $request->police_address;
            $traffic_ticket->police_region_id = $request->police_region_id;
            $traffic_ticket->police_province_id = $request->police_province_id;
            $traffic_ticket->police_district_id = $request->police_district_id;
            $traffic_ticket->police_subdistrict_id = $request->police_subdistrict_id;
            if (boolval($request->is_draft)) {
                $traffic_ticket->status = TrafficTicketStatusEnum::DRAFT;
            } else {
                $traffic_ticket->status = TrafficTicketStatusEnum::GUITY_PENDING;
            }
            $traffic_ticket->save();
            $redirect_route = route('admin.traffic-tickets.index');
            return $this->responseValidateSuccess($redirect_route);
        }

        if (in_array($traffic_ticket->status, [TrafficTicketStatusEnum::GUITY_PENDING])) {
            $request->merge([
                'notice_fee' => transform_float($request->notice_fee),
            ]);
            if (!boolval($request->is_draft)) {
                $validator = Validator::make($request->all(), [
                    'is_guilty' => ['required'],
                    'is_vip' => ['required'],
                    'notification_date' => Rule::requiredIf($request->is_guilty && !$request->is_guilty),
                    'notice_channel' => Rule::requiredIf($request->is_guilty && !$request->is_guilty),
                    'notice_fee' => Rule::requiredIf($request->is_guilty && !$request->is_guilty),
                ], [], [
                        'is_guilty' => __('traffic_tickets.is_guilty'),
                        'is_vip' => __('traffic_tickets.is_vip'),
                        'notification_date' => __('traffic_tickets.notification_date'),
                    ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }
            }
            $traffic_ticket->is_guilty = $request->is_guilty;
            $traffic_ticket->is_vip = $request->is_vip;
            $traffic_ticket->notification_date = $request->notification_date;
            $traffic_ticket->notice_channel = $request->notice_channel;
            $traffic_ticket->notice_fee = $request->notice_fee;
            $traffic_ticket->due_date = $request->due_date;
            if (!boolval($request->is_draft)) {
                $traffic_ticket->status = TrafficTicketStatusEnum::SEND_POLICE_PENDING;
                if (in_array($traffic_ticket->document_type, [TrafficTicketDocTypeEnum::WARNING, TrafficTicketDocTypeEnum::LASER])) {
                    $traffic_ticket->status = TrafficTicketStatusEnum::PAYMENT_PENDING;
                }
                if ($traffic_ticket->is_vip) {
                    $traffic_ticket->status = TrafficTicketStatusEnum::PAYMENT_PENDING;
                }
                if (!$traffic_ticket->is_guilty) {
                    $traffic_ticket->status = TrafficTicketStatusEnum::COMPLETE;
                }
            }
            $traffic_ticket->save();
            $redirect_route = route('admin.traffic-tickets.index');
            return $this->responseValidateSuccess($redirect_route);
        }

        if (in_array($traffic_ticket->status, [TrafficTicketStatusEnum::SEND_POLICE_PENDING])) {
            if ($request->police_fee) {
                $request->merge(['police_fee' => transform_float($request->police_fee)]);
            }
            if (!boolval($request->is_draft)) {
                $validator = Validator::make($request->all(), [
                    'deadline_date' => ['required'],
                    'status_send_po' => ['required'],
                    'send_po_date' => ['required'],
                    'police_fee' => ['nullable', 'numeric', ' gt:0', 'max:999999999.99'],
                ], [], [
                        'deadline_date' => __('traffic_tickets.deadline_date'),
                        'status_send_po' => __('traffic_tickets.status_send_po'),
                        'send_po_date' => __('traffic_tickets.send_po_date'),
                        'police_fee' => __('traffic_tickets.police_fee'),
                    ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }
            }
            $traffic_ticket->deadline_date = $request->deadline_date;
            $traffic_ticket->status_send_po = $request->status_send_po;
            $traffic_ticket->send_po_date = $request->send_po_date;
            $traffic_ticket->police_fee = $request->police_fee;
            $traffic_ticket->is_respond = $request->is_respond;
            $traffic_ticket->respond_date = $request->respond_date;
            $traffic_ticket->expiration_date = $request->respondexpiration_date_date;
            if (!boolval($request->is_draft)) {
                $traffic_ticket->status = TrafficTicketStatusEnum::PAYMENT_PENDING;
            }
            $traffic_ticket->save();
            $redirect_route = route('admin.traffic-tickets.index');
            return $this->responseValidateSuccess($redirect_route);
        }

        if (in_array($traffic_ticket->status, [TrafficTicketStatusEnum::PAYMENT_PENDING])) {
            $validator = Validator::make($request->all(), [
                'payment_date' => ['required'],
                'payment_status' => ['required'],
            ], [], [
                    'payment_date' => __('traffic_tickets.payment_date'),
                    'payment_status' => __('traffic_tickets.payment_status'),
                ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            $traffic_ticket->payment_date = $request->payment_date;
            $traffic_ticket->payment_status = $request->payment_status;
            $traffic_ticket->report_date = $request->report_date;
            $traffic_ticket->report_status = $request->report_status;
            if (!boolval($request->is_draft) && strcmp($request->payment_status, TrafficTicketPaymentStatusEnum::COMPLETE) == 0) {
                $traffic_ticket->status = TrafficTicketStatusEnum::COMPLETE;
            }
            $traffic_ticket->save();
        }

        $redirect_route = route('admin.traffic-tickets.index');
        return $this->responseValidateSuccess($redirect_route);
    }

}