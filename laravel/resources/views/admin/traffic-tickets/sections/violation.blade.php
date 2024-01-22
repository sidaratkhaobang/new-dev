<x-blocks.block :title="__('traffic_tickets.violation')" id="violation_block">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.date-input id="document_date" name="document_date" :value="$d->document_date" :label="__('traffic_tickets.document_date')"
                :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="traffic_ticket_no" :value="$d->traffic_ticket_no" :label="__('traffic_tickets.traffic_ticket_no')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="fine" :value="$d->fine" :label="__('traffic_tickets.fine')"
                :optionals="['required' => true, 'input_class' => 'number-format']" />
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.input-new-line id="charge" :value="$d->charge" :label="__('traffic_tickets.charge')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-6">
            <x-forms.input-new-line id="charge_remark" :value="$d->charge_remark" :label="__('traffic_tickets.charge_remark')" />
        </div>
        <div class="col-sm-3" id="speed_block">
            <x-forms.input-new-line id="speed" :value="$d->speed" :label="__('traffic_tickets.speed')"
                :optionals="['input_class' => 'number-format']" />
        </div>
    </div>
    <div class="row push" id="location_block">
        <div class="col-sm-3">
            <x-forms.input-new-line id="location" :value="$d->location" :label="__('traffic_tickets.location')"
                :optionals="['required' => false]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="region_id" :value="$d->region_id" :list="null" :label="__('traffic_tickets.region')"
                :optionals="['required' => false]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="province_id" :value="$d->province_id" :list="null" :label="__('traffic_tickets.province')"
                :optionals="['required' => false]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="district_id" :value="$d->district_id" :list="null" :label="__('traffic_tickets.district')"
                :optionals="['required' => false]" />
        </div>
    </div>
    <div class="row push" id="express_block">
        <div class="col-sm-3">
            <x-forms.select-option id="extra_expressway_id" :value="$d->extra_expressway_id" :list="null" :label="__('traffic_tickets.extra_express')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="expressway_id" :value="$d->expressway_id" :list="null" :label="__('traffic_tickets.express_id')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="inbound_outbound" :value="$d->inbound_outbound" :list="$inbound_outbound_list" :label="__('traffic_tickets.inbound_outbound')"
                :optionals="['required' => false]" />
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="slot" :value="$d->slot" :list="$slot_list" :label="__('traffic_tickets.slot')"
                :optionals="['required' => false]" />
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.select-option id="subdistrict_id" :value="$d->subdistrict_id" :list="null" :label="__('traffic_tickets.subdistrict')"
                :optionals="['required' => false]" />
        </div>
        <div class="col-sm-9">
            <x-forms.input-new-line id="remark" :value="$d->remark" :label="__('lang.remark')"/>
        </div>
    </div>
</x-blocks.block>