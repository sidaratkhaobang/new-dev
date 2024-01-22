<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

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
                        <x-forms.select-option id="status_template" :value="null" :list="$status_register_list" :label="__('registers.status')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="lot_no_template" :value="null" :list="$lot_list"
                            :label="__('registers.lot_no')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="leasing_template" :value="null" :list="[]"
                            :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                                'default_option_label' => null,
                            ]" :label="__('registers.leasing')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_id_template" :value="null" :list="[]"
                            :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                                'default_option_label' => null,
                            ]" :label="__('registers.engine_chassis_no')" />
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <x-forms.select-option id="car_class_template" :value="null" :list="[]"
                            :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                                'default_option_label' => null,
                            ]" :label="__('registers.car_class')" />
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-sm-12 text-end">
                        <button type="button" onclick="clearSelectCar()"
                        class="btn btn-outline-secondary btn-custom-size me-1"><i class="fa fa-rotate-left"></i>
                        {{ __('lang.clear_search') }}</button>
                        <button type="button" onclick="checkCarTemplate()" class="btn btn-primary btn-custom-size"><i
                                class="icon-add-circle"></i> {{ __('registers.add_car') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
