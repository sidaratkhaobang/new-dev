{{-- <div class="block {{ __('block.styles') }}">


    @include('admin.components.block-header', [
        'text' => __('lang.search'),
        'block_icon_class' => 'icon-search',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <form action="" method="GET" id="form-search">
            <div class="row mb-4">
                <div class="col-sm-3">
                    <x-forms.select-option id="status" :value="null" :list="$status_register_list" :label="__('registers.status')"
                        :optionals="['required' => true]" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="lot_no" :value="null" :list="$lot_list" :label="__('registers.lot_no')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="leasing" :value="null" :list="[]" :label="__('registers.leasing')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="car_id" :value="null" :list="$car_list" :label="__('registers.engine_chassis_no')" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-6">
                    <x-forms.select-option id="car_class" :value="null" :list="$car_class_list" :label="__('registers.car_class')" />
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-sm-12 text-end">
                    <button type="button" onclick="clearSelectCar()" class="btn btn-outline-secondary btn-clear-search btn-custom-size me-1"><i class="fa fa-rotate-left"></i> {{ __('lang.clear_search') }}</button>
                    <button type="button" onclick="checkCarFaceSheet()" class="btn btn-primary btn-custom-size"><i class="icon-add-circle"></i> {{ __('registers.add_car') }}</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    
</div> --}}
