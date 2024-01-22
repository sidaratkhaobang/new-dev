<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center mb-3">
            <div>
                <h4><i class="fa fa-file-lines me-1"></i>{{ __('repair_orders.table_order') }}</h4>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="center_date" :value="$d->in_center_date" :label="__('repairs.center_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="mileage_order" :value="$d->repair ? $d->repair->mileage : null" :label="__('repairs.mileage')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="check_distance" :list="null" :value="$d->check_distance" :label="__('repair_orders.check_distance')"
                    :optionals="[
                        'select_class' => 'js-select2-custom',
                        'ajax' => true,
                        'default_option_label' => $d->check_distance,
                    ]" />
            </div>
        </div>
        <div class="row push mb-4">
            {{-- <div class="col-sm-3">
                <x-forms.date-input id="expected_date" :value="$d->expected_repair_date" :label="__('repairs.expected_date')" />
            </div> --}}
            <div class="col-sm-3">
                <x-forms.date-input id="completed_date" :value="$d->repair_date" :label="__('repairs.completed_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="amount_day" :value="null" :label="__('repair_orders.amount_day')" />
            </div>
        </div>
        <hr>
        <div class="row push mb-4">
            <div class="col-sm-6">
                <x-forms.select-option id="center" :list="$center_list" :value="$d->center_id" :label="__('repair_orders.center')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="address" :value="$d->center_address" :label="__('repair_orders.address')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-12">
                <x-forms.input-new-line id="remark_center" :value="$d->remark" :label="__('repair_orders.remark')" />
            </div>
        </div>
    </div>
</div>
