<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
           'text' => __('lang.search'),
           'block_icon_class' => 'icon-search',
           'is_toggle' => true
       ])
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <form action="" method="GET" id="form-search">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="policy_number" :value="$searchPolicyReferenceName"
                                               :list="[]"
                                               :label="__('insurance_deduct.policy_number')"
                                               :optionals="['required' => false,'ajax' => true,'default_option_label'=>$searchPolicyReferenceName]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="license_plate_chassis_no" :value="$searchLicensePlate"
                                               :list="[]"
                                               :label="__('insurance_deduct.license_plate_chassis_no')"
                                               :optionals="['required' => false,'ajax' => true,'default_option_label' => $labelLicensePlate]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="insurance_company" :value="$searchInsuranceCompany??null"
                                               :list="[]"
                                               :label="__('insurance_deduct.insurance_company')"
                                               :optionals="['required' => false,'ajax' => true,'default_option_label' => $labelInsuranceCompany]"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="status" :value="$searchStatus??null"
                                               :list="$listStatusJob??[]"
                                               :label="__('insurance_deduct.status')"
                                               :optionals="['required' => false]"/>
                    </div>
                </div>
                @include('admin.components.btns.search')
            </form>
        </div>
    </div>
</div>
