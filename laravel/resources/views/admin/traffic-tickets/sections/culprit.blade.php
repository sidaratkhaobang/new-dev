<x-blocks.block :title="__('traffic_tickets.culprit')" id="culprit_block">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.input-new-line id="culprit_name" :value="$d->culprit_name" :label="__('traffic_tickets.driver_name')"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="culprit_tel" :value="$d->culprit_tel" :label="__('traffic_tickets.tel')"/>
        </div>
        <div class="col-sm-6">
            <x-forms.input-new-line id="culprit_address" :value="$d->culprit_address" :label="__('traffic_tickets.culprit_address')"/>
        </div>
    </div>
</x-blocks.block>