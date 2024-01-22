<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
            'text' =>  __('vmi_cars.premium_pa_bb')
        ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="pa" :value="$d->pa" :label="__('vmi_cars.pa')" 
                    :optionals="['class' => 'number-format']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="pa_and_bb" :value="$d->pa_and_bb" :label="__('vmi_cars.pa_and_bb')" 
                    :optionals="['class' => 'number-format']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="pa_per_endorsement" :value="$d->pa_per_endorsement" :label="__('vmi_cars.per_endorsement')" 
                    :optionals="['class' => 'number-format']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="pa_total_premium" :value="$d->pa_total_premium" :label="__('vmi_cars.pa_total_premium')" 
                    :optionals="['class' => 'number-format']"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="id_deductible" :value="$d->id_deductible" :label="__('vmi_cars.id_deductible')" 
                    :optionals="['class' => 'number-format']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="discount_deductible" :value="$d->discount_deductible" :label="__('vmi_cars.discount_deductible')" 
                    :optionals="['class' => 'number-format']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="fit_discount" :value="$d->fit_discount" :label="__('vmi_cars.fit_discount')" 
                    :optionals="['class' => 'number-format', 'suffix' => '%']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="fleet_discount" :value="$d->fleet_discount" :label="__('vmi_cars.fleet_discount')" 
                    :optionals="['class' => 'number-format']"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="ncb" :value="$d->ncb" :label="__('vmi_cars.ncb')" 
                    :optionals="['class' => 'number-format']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="good_vmi" :value="$d->good_vmi" :label="__('vmi_cars.good_vmi')" 
                    :optionals="['class' => 'number-format']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="bad_vmi" :value="$d->bad_vmi" :label="__('vmi_cars.bad_vmi')" 
                    :optionals="['class' => 'number-format']"/>
            </div>
        </div>
    </div>
</div>
