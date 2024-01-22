<x-blocks.block :title="__('traffic_tickets.rental_data')">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.label id="driver_name" :value="$d->driver_name" :label="__('traffic_tickets.driver_name')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="section" :value="$d->section" :label="__('traffic_tickets.section')" />
        </div>
        <div class="col-sm-3">
            <x-forms.label id="driver_tel" :value="$d->driver_tel" :label="__('traffic_tickets.driver_tel')" />
        </div>
    </div>
</x-blocks.block>