<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
           'text' => __('lang.search'),
           'block_icon_class' => 'icon-search',
           'block_option_id' => '_1',
           'is_toggle' => true
       ])
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <form action="" method="GET" id="form-search">
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option :value="null" id="" :list="null"
                                               :label="__('insurance_car.license_plate_chassis_no')" :optionals="[
                                    'ajax' => true,
                                ]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option :value="null" id="" :list="null"
                                               :label="__('insurance_car.policy_number')" :optionals="[
                                    'ajax' => true,
                                ]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option :value="null" id="" :list="null"
                                               :label="__('insurance_car.insurance_company')" :optionals="[
                                    'ajax' => true,
                                ]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option :value="null" id="" :list="null"
                                               :label="__('insurance_car.status')" :optionals="[
                                    'ajax' => true,
                                ]"/>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option :value="null" id="" :list="null"
                                               :label="__('insurance_car.type')" :optionals="[
                                    'ajax' => true,
                                ]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="offer_date" :value="null" :label="__('insurance_car.coverage_start_date')" :optionals="['placeholder' => __('lang.select_date')]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="offer_date" :value="null" :label="__('insurance_car.coverage_end_date')" :optionals="['placeholder' => __('lang.select_date')]" />
                    </div>
                </div>
                @include('admin.components.btns.search')
            </form>
        </div>
    </div>
</div>
