<div class="block {{ __('block.styles') }}">

    @include('admin.components.block-header', [
        'text' => __('accident_orders.repair_price_detail'),
        // 'block_option_id' => '_1',
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="bidding_date" :value="$d->bidding_date" :label="__('accident_orders.garage_bidding')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_insurance" :value="$d->car_insurance" :label="__('accident_orders.car_insurance')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="equipment_insurance" :value="$d->equipment_insurance" :label="__('accident_orders.equipment_insurance')" />
            </div>
        </div>
    </div>
</div>
