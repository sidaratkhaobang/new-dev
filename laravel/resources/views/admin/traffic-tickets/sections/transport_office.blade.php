<x-blocks.block :title="__('traffic_tickets.transport_office_data')" id="transport_office_block">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.select-option id="province_transport_office_id" :value="$d->province_transport_office_id" :list="null" :label="__('traffic_tickets.province_transport_office')"/>
        </div>
    </div>
</x-blocks.block>