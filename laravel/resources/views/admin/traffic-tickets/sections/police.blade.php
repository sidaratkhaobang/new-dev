<x-blocks.block :title="__('traffic_tickets.police_data')" id="police_block">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.select-option id="police_station_id" :value="$d->police_station_id" :list="null" :label="__('traffic_tickets.police')" 
                :optionals="[
                    'required' => true,
                    'select_class' => 'js-select2-custom',
                    'ajax' => true,
                    'default_option_label' => $police_station_name,
                ]" />
        </div>
        <div class="col-sm-9">
            <x-forms.input-new-line id="police_address" :value="$d->police_address" :label="__('traffic_tickets.police_address')"/>
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.select-option id="police_region_id" :value="$d->police_region_id" :list="null" :label="__('traffic_tickets.region')"/>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="police_province_id" :value="$d->police_province_id" :list="null" :label="__('traffic_tickets.province')"/>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="police_district_id" :value="$d->police_district_id" :list="null" :label="__('traffic_tickets.district')"/>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="police_subdistrict_id" :value="$d->police_subdistrict_id" :list="null" :label="__('traffic_tickets.subdistrict')"/>
        </div>
    </div>
</x-blocks.block>