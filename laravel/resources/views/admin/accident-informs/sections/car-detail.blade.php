<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('accident_informs.car_detail'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="license_plate" :value="$d->license_plate" :list="$license_plate_list" :label="__('accident_informs.license_plate_chassis_engine')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="chassis_no" :value="$d->chassis_no" :label="__('accident_informs.chassis_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="car_class" :value="$d->car_class" :label="__('accident_informs.car_class')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="worksheet_no_ref" :value="$d->worksheet_no_ref" :label="__('accident_informs.worksheet_no_ref')"/>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_name" :value="$d->customer_name" :label="__('accident_informs.customer_name')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="policy_number" :value="$d->policy_number" :label="__('accident_informs.policy_number')"/>

            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="insurance_company" :value="$d->insurance_company" :label="__('accident_informs.insurance_company')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="insurance_tel" :value="$d->insurance_tel" :label="__('accident_informs.insurance_tel')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="coverage_start_date" :value="$d->coverage_start_date" :label="__('accident_informs.coverage_start_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="coverage_end_date" :value="$d->coverage_end_date" :label="__('accident_informs.coverage_end_date')"/>
            </div>
        </div>
    </div>
</div>
