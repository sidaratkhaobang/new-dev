<div class="block {{ __('block.styles') }}">


    @include('admin.components.block-header', [
        'text' => __('accident_orders.repair_cost_total'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="wage" :value="$d->wage" :label="__('accident_orders.wage')" :optionals="['input_class' => 'number-format', 'required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="spare_parts" :value="$d->spare_parts" :label="__('accident_orders.spare_part_cost')" :optionals="['input_class' => 'number-format', 'required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="discount_spare_parts" :value="$d->discount_spare_parts" :label="__('accident_orders.spare_part_discount')"
                    :optionals="['input_class' => 'number-format', 'required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="spare_part_total" :value="$d->spare_part_total" :label="__('accident_orders.spare_part_total')" :optionals="['input_class' => 'number-format']" />
            </div>
        </div>
    </div>
</div>
