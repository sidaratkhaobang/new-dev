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
                        <x-forms.select-option :value="$car_plate" id="car_plate" :list="$car_plate_list"
                                               :label="__('insurance_car.license_plate_chassis_no')" :optionals="[
                                    'ajax' => false,
                                ]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option :value="$policy_number" id="policy_number" :list="$policy_reference_cmi_list"
                                               :label="__('insurance_car.policy_number')" :optionals="[
                                    'ajax' => false,
                                ]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option :value="$insurance_company" id="insurance_company" :list="$insurer_list"
                                               :label="__('insurance_car.insurance_company')" :optionals="[
                                    'ajax' => false,
                                ]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option :value="$status" id="status" :list="$status_list"
                                               :label="__('insurance_car.status')" :optionals="[
                                    'ajax' => false,
                                ]"/>
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option :value="$job_type" id="job_type" :list="$job_type_list"
                                               :label="__('insurance_car.type')" :optionals="[
                                    'ajax' => false,
                                ]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="term_start_date" :value="$term_start_date" :label="__('insurance_car.coverage_start_date')" :optionals="['placeholder' => __('lang.select_date')]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="term_end_date" :value="$term_end_date" :label="__('insurance_car.coverage_end_date')" :optionals="['placeholder' => __('lang.select_date')]" />
                    </div>
                </div>
                @include('admin.components.btns.search')
            </form>
        </div>
    </div>
</div>
