<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' =>  __('vmi_cars.other_discount')
    ])
    <div class="block-content">
        {{-- <div class="act-detail-wrapper"> --}}
            <div class="row mb-4">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="other_discount_percent" :value="$d->other_discount_percent" :label="__('vmi_cars.other_discount_percent')" 
                        :optionals="['class' => 'number-format', 'suffix' => '%']"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="other_discount" :value="$d->other_discount" :label="__('vmi_cars.other_discount')" 
                        :optionals="['class' => 'number-format']"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="gps_discount" :value="$d->gps_discount" :label="__('vmi_cars.gps_discount')" 
                        :optionals="['class' => 'number-format']"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="total_discount" :value="$d->total_discount" :label="__('vmi_cars.total_discount')" 
                        :optionals="['class' => 'number-format']"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="net_discount" :value="$d->net_discount" :label="__('vmi_cars.net_discount')" 
                        :optionals="['class' => 'number-format']"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="cct" :value="$d->cct" :label="__('vmi_cars.cct')" 
                        :optionals="['class' => 'number-format']"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="gross" :value="$d->gross" :label="__('vmi_cars.gross')" 
                        :optionals="['class' => 'number-format']"/>
                </div>
            </div>
        {{-- </div> --}}
    </div>
</div>
