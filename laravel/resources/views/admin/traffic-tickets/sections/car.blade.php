<x-blocks.block :title="__('traffic_tickets.car_data')" id="car_block">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.select-option id="car_id" :value="$d->car_id" :list="null" :label="__('traffic_tickets.license_plate')"
                :optionals="[
                    'required' => true,
                    'select_class' => 'js-select2-custom',
                    'ajax' => true,
                    'default_option_label' => $license_plate,
                ]" />
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="offense_date" :value="$d->offense_date" :label="__('traffic_tickets.offense_date')" :optionals="['required' => true, 'date_enable_time' => true]" />
        </div>
    </div>
</x-blocks.block>
