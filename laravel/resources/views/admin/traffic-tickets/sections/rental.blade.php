<x-blocks.block :title="__('traffic_tickets.rental_data')" id="rental_block">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.label id="rental_worksheet_no" :value="$d->rental_worksheet_no" :label="__('traffic_tickets.rental_worksheet_no')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="renter" :value="$d->renter" :label="__('traffic_tickets.renter')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="business" :value="$d->business" :label="__('traffic_tickets.business')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="customer_group" :value="$d->customer_group" :label="__('traffic_tickets.customer_group')" />
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.label id="contract_worksheet_no" :value="$d->contract_worksheet_no" :label="__('traffic_tickets.contract_worksheet_no')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="contract_start_date" :value="$d->contract_start_date" :label="__('traffic_tickets.contract_start_date')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="contract_end_date" :value="$d->contract_end_date" :label="__('traffic_tickets.contract_end_date')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="car_type" :value="$d->car_type" :label="__('traffic_tickets.car_type')" />
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-6">
            <x-forms.label id="rental_address" :value="$d->rental_address" :label="__('traffic_tickets.rental_address')" />
        </div>
    </div>
</x-blocks.block>