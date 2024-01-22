<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
            'text' =>  __('vmi_cars.premium_pa_bb')
        ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="pa" :value="getTrueValue($d->pa)" :label="__('vmi_cars.pa')" 
                    :optionals="['input_class' => 'number-format', 'placeholder' => $d->pa ?? 0.00]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="pa_and_bb" :value="getTrueValue($d->pa_and_bb)" :label="__('vmi_cars.pa_and_bb')" 
                    :optionals="['input_class' => 'number-format', 'placeholder' => $d->pa_and_bb ?? 0.00]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="pa_per_endorsement" :value="getTrueValue($d->pa_per_endorsement)" :label="__('vmi_cars.per_endorsement')" 
                    :optionals="['input_class' => 'number-format', 'placeholder' => $d->pa_per_endorsement ?? 0.00]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="pa_total_premium" :value="getTrueValue($d->pa_total_premium)" :label="__('vmi_cars.pa_total_premium')" 
                    :optionals="['input_class' => 'number-format', 'placeholder' => $d->pa_total_premium ?? 0.00]"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="id_deductible" :value="getTrueValue($d->id_deductible)" :label="__('vmi_cars.id_deductible')" 
                    :optionals="['input_class' => 'number-format', 'placeholder' => $d->id_deductible ?? 0.00]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="discount_deductible" :value="getTrueValue($d->discount_deductible)" :label="__('vmi_cars.discount_deductible')" 
                    :optionals="['input_class' => 'number-format', 'placeholder' => $d->discount_deductible ?? 0.00]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="fit_discount" :value="getTrueValue($d->fit_discount)" :label="__('vmi_cars.fit_discount')" 
                    :optionals="['input_class' => 'number-format', 'suffix' => '%', 'placeholder' => $d->fit_discount ?? 0.00]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="fleet_discount" :value="getTrueValue($d->fleet_discount)" :label="__('vmi_cars.fleet_discount')" 
                    :optionals="['input_class' => 'number-format', 'placeholder' => $d->fleet_discount ?? 0.00]"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="ncb" :value="getTrueValue($d->ncb)" :label="__('vmi_cars.ncb')" 
                    :optionals="['input_class' => 'number-format', 'placeholder' => $d->ncb ?? 0.00]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="good_vmi" :value="getTrueValue($d->good_vmi)" :label="__('vmi_cars.good_vmi')" 
                    :optionals="['input_class' => 'number-format', 'placeholder' => $d->good_vmi ?? 0.00]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="bad_vmi" :value="getTrueValue($d->bad_vmi)" :label="__('vmi_cars.bad_vmi')" 
                    :optionals="['input_class' => 'number-format', 'placeholder' => $d->bad_vmi ?? 0.00]"/>
            </div>
        </div>
    </div>
</div>
