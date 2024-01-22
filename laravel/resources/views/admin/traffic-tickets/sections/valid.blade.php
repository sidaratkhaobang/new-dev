<x-blocks.block :title="__('traffic_tickets.valid_data')" id="valid_block">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.radio-inline id="is_guilty" :value="$d->is_guilty" :list="$yes_no_list" :label="__('traffic_tickets.is_guilty')"
                :optionals="['required' => true]" />
        </div>
        <div class="col-sm-3" id="vip_block">
            <x-forms.radio-inline id="is_vip" :value="$d->is_vip" :list="$yes_no_list" :label="__('traffic_tickets.is_vip')"
                :optionals="['required' => true]" />
        </div>
    </div>
</x-blocks.block>