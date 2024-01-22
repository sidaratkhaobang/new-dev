<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' =>  __('vmi_cars.other_discount')
    ])
    <div class="block-content">
        {{-- <div class="act-detail-wrapper"> --}}
            <div class="row mb-4">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="other_discount_percent" :value="getTrueValue($d->other_discount_percent)" :label="__('vmi_cars.other_discount_percent')" 
                        :optionals="['input_class' => 'number-format', 'suffix' => '%', 'placeholder' => $d->other_discount_percent ?? 0.00]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="other_discount" :value="getTrueValue($d->other_discount)" :label="__('vmi_cars.other_discount')" 
                        :optionals="['input_class' => 'number-format', 'placeholder' => $d->other_discount ?? 0.00]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="gps_discount" :value="getTrueValue($d->gps_discount)" :label="__('vmi_cars.gps_discount')" 
                        :optionals="['input_class' => 'number-format', 'placeholder' => $d->gps_discount ?? 0.00]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="total_discount" :value="getTrueValue($d->total_discount)" :label="__('vmi_cars.total_discount')" 
                        :optionals="['input_class' => 'number-format', 'placeholder' => $d->total_discount ?? 0.00]"/>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-3">
                    <x-forms.input-new-line id="net_discount" :value="getTrueValue($d->net_discount)" :label="__('vmi_cars.net_discount')" 
                        :optionals="['input_class' => 'number-format', 'placeholder' => $d->net_discount ?? 0.00]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="cct" :value="getTrueValue($d->cct)" :label="__('vmi_cars.cct')" 
                        :optionals="['input_class' => 'number-format', 'placeholder' => $d->cct ?? 0.00]"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.input-new-line id="gross" :value="getTrueValue($d->gross)" :label="__('vmi_cars.gross')" 
                        :optionals="['input_class' => 'number-format', 'placeholder' => $d->gross ?? 0.00]"/>
                </div>
            </div>
        {{-- </div> --}}
    </div>
</div>
