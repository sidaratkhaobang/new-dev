@extends('admin.layouts.layout')

@section('page_title', $page_title)

@section('content')
<form id="save-form">
    @include('admin.traffic-tickets.sections.doc-type')
    @include('admin.traffic-tickets.sections.car')
    @include('admin.traffic-tickets.sections.rental')
    @include('admin.traffic-tickets.sections.driver')
    @includeWhen(TrafficTicketTrait::checkStateStatus($d->status, TrafficTicketStatusEnum::DRAFT),'admin.traffic-tickets.sections.violation')
    @includeWhen(TrafficTicketTrait::checkStateStatus($d->status, TrafficTicketStatusEnum::DRAFT),'admin.traffic-tickets.sections.culprit')
    @includeWhen(TrafficTicketTrait::checkStateStatus($d->status, TrafficTicketStatusEnum::DRAFT),'admin.traffic-tickets.sections.transport_office')
    @includeWhen(TrafficTicketTrait::checkStateStatus($d->status, TrafficTicketStatusEnum::DRAFT),'admin.traffic-tickets.sections.police')
    @includeWhen(TrafficTicketTrait::checkStateStatus($d->status, TrafficTicketStatusEnum::GUITY_PENDING),'admin.traffic-tickets.sections.valid')
    @includeWhen(TrafficTicketTrait::checkStateStatus($d->status, TrafficTicketStatusEnum::GUITY_PENDING),'admin.traffic-tickets.sections.fine')
    @includeWhen((TrafficTicketTrait::checkStateStatus($d->status, TrafficTicketStatusEnum::GUITY_PENDING) && 
    in_array($d->document_type,[TrafficTicketDocTypeEnum::TRAFFIC_TICKET, TrafficTicketDocTypeEnum::VIOLATION_TRAFFIC_SIGN, TrafficTicketDocTypeEnum::TACHOMETER])),'admin.traffic-tickets.sections.notice')
    @includeWhen(TrafficTicketTrait::checkStateStatus($d->status, TrafficTicketStatusEnum::PAYMENT_PENDING),'admin.traffic-tickets.sections.payment')

    <x-blocks.block>
        <x-forms.hidden id="id" :value="$d->id" />
        @php
            if (strcmp($d->status, TrafficTicketStatusEnum::COMPLETE) === 0) {
                $view = true;
            }
        @endphp
        <x-forms.submit-group :optionals="[
            'view' => empty($view) ? null : $view,
            'isdraft' => true,
            'btn_name' => __('lang.save'),
            'btn_draft_name' => __('registers.save_register_draft'),
            'icon_class_name' => 'icon-save',
        ]"></x-forms.submit-group>
    </x-blocks.block>
</form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.traffic-tickets.store'),
])
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2-car.cars-by-license-plate'),
])

@include('admin.components.select2-ajax', [
    'id' => 'police_station_id',
    'url' => route('admin.util.select2-traffic-ticket.police-stations'),
])

@push('scripts')
    <script>
        const traffic_ticket = @json($d);
        var status = traffic_ticket.status;
       
        const is_show_police_arr = [
            '{{ TrafficTicketDocTypeEnum::TRAFFIC_TICKET }}',
            '{{ TrafficTicketDocTypeEnum::TACHOMETER }}',
            '{{ TrafficTicketDocTypeEnum::WARNING }}',
            '{{ TrafficTicketDocTypeEnum::VIOLATION_TRAFFIC_SIGN }}',
        ];

        const is_show_transport_office_arr = ['{{ TrafficTicketDocTypeEnum::LASER }}'];
        const is_show_express_arr = ['{{ TrafficTicketDocTypeEnum::EXPRESSWAY }}'];

        $('#police_block').hide();
        $('#vip_block').hide();
        $('#transport_office_block').hide();
        $('#express_block').hide();

        if (is_show_police_arr.includes(traffic_ticket.document_type)) {
            $('#police_block').show();
            $('#vip_block').show();
        }

        if (is_show_express_arr.includes(traffic_ticket.document_type)) {
            $('#express_block').show();
            $('#location_block').hide();
        }

        if (is_show_transport_office_arr.includes(traffic_ticket.document_type)) {
            $('#transport_office_block').show();
        }
        $('input[name="document_type"]').change(function() {
            var document_type = this.value;
            if (is_show_police_arr.includes(document_type)) {
                $('#police_block').show();
            } else {
                $('#police_block').hide();
            }
            $('#transport_office_block').hide();
            if (is_show_transport_office_arr.includes(document_type)) {
                $('#transport_office_block').show();
            }
            if (is_show_express_arr.includes(document_type)) {
                $('#express_block').show();
                $('#location_block').hide();
            } else {
                $('#express_block').hide();
                $('#location_block').show();
            }
        });

        if ($('input[name="is_guilty"]:checked').val() == true) {
            $('#fine_block').show();
            $('#notice_block').show();
            $('#payment_block').show();
            if (is_show_police_arr.includes(traffic_ticket.document_type)) {
                $('#vip_block').show();
            }
        } else {
            $('#fine_block').hide();
            $('#notice_block').hide();
            $('#payment_block').hide();
            $('#vip_block').hide();
        }

        if ($('input[name="is_vip"]:checked').val() == true) {
            $('#notice_block').hide();
        }

        $('input[name="is_guilty"]').change(function() {
            var is_guilty = this.value;
            $('#fine_block').hide();
            $('#notice_block').hide();
            $('#payment_block').hide();
            if (is_guilty == true) {
                $('#fine_block').show();
                $('#notice_block').show();
                $('#payment_block').show();
                if (is_show_police_arr.includes(traffic_ticket.document_type)) {
                    $('#vip_block').show();
                }
            }
        });

        if (status === '{{ TrafficTicketStatusEnum::GUITY_PENDING}}') {
            var selector = '#doc_block input, #doc_block select,';
            selector += '#car_block input, #car_block select,';
            selector += '#violation_block input, #violation_block select,';
            selector += '#culprit_block input, #culprit_block select,';
            selector += '#transport_office_block input, #transport_office_block select,';
            selector += '#police_block input, #police_block select';
            const inputs = document.querySelectorAll(selector);
            disableInputs(inputs);
        }

        if (status === '{{ TrafficTicketStatusEnum::SEND_POLICE_PENDING}}') {
            var selector = '#doc_block input, #doc_block select,';
            selector += '#car_block input, #car_block select,';
            selector += '#violation_block input, #violation_block select,';
            selector += '#culprit_block input, #culprit_block select,';
            selector += '#transport_office_block input, #transport_office_block select,';
            selector += '#police_block input, #police_block select,';
            selector += '#valid_block input, #valid_block select,';
            selector += '#fine_block input, #fine_block select';
            const inputs = document.querySelectorAll(selector);
            disableInputs(inputs);
        }

        if (status === '{{ TrafficTicketStatusEnum::PAYMENT_PENDING}}') {
            var selector = '#doc_block input, #doc_block select,';
            selector += '#car_block input, #car_block select,';
            selector += '#violation_block input, #violation_block select,';
            selector += '#culprit_block input, #culprit_block select,';
            selector += '#transport_office_block input, #transport_office_block select,';
            selector += '#police_block input, #police_block select,';
            selector += '#valid_block input, #valid_block select,';
            selector += '#fine_block input, #fine_block select,';
            selector += '#notice_block input, #notice_block select';
            const inputs = document.querySelectorAll(selector);
            disableInputs(inputs);
        }

        if (status === '{{ TrafficTicketStatusEnum::COMPLETE}}') {
            var selector = 'input, select';
            const inputs = document.querySelectorAll(selector);
            disableInputs(inputs);
        }

    </script>
@endpush