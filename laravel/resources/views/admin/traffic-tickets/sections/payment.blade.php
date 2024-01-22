<x-blocks.block :title="__('traffic_tickets.payment_data')" id="payment_block">
    @if (strcmp($d->document_type, TrafficTicketDocTypeEnum::LASER) === 0)
        <div class="row push" id="report_block">
            <div class="col-sm-3">
                <x-forms.date-input id="report_date" :value="$d->report_date" :label="__('traffic_tickets.report_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="report_status" :value="$d->report_status" :list="$report_status_list" :label="__('traffic_tickets.report_status')"/>
            </div>
        </div>
    @endif
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.date-input id="payment_date" :value="$d->payment_date" :label="__('traffic_tickets.payment_date')"
                :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="payment_status" :value="$d->payment_status" :list="$payment_status_list" :label="__('traffic_tickets.paytment_status')"
                :optionals="['required' => true]" />
        </div>
    </div>
</x-blocks.block>
