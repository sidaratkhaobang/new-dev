<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('sign_yellow_tickets.car_detail'),
        'unique_identifier' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">

            <div class="col-sm-3">
                <x-forms.select-option id="car_id" :list="[]" :value="$d->car_id" :label="__('cars.license_plate')" :optionals="['required' => true, 'ajax' => true, 'default_option_label' => $d->car_license_plate]" />
                    <x-forms.hidden id="car_id_hidden" :value="$d->car_id" />
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="car_class" :value="$d->car_class" :label="__('tax_renewals.car_class')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="branch" :value="$d->branch" :label="__('sign_yellow_tickets.branch')" />
            </div>

        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="engine_no" :value="$d->engine_no" :label="__('cars.engine_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no" :label="__('cars.chassis_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_status" :value="null" :label="__('sign_yellow_tickets.car_status')" />
            </div>

        </div>
      
    </div>
</div>
