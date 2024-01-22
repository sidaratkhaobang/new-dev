<div id="index-table" class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
           'text' => __('insurance_deduct.title_content_insurance'),
       ])
    <div class="block-content">
        <div class="justify-content-between mb-4">
            <div class="row push mb-4">
                <div class="col-sm-3">
                    <x-forms.label id="insurnace_name" :value="$dataInsurance?->insurer?->insurance_name_th ?? '-'"
                                   :label="__('insurance_deduct.insurance_company')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="insurnace_policy_reference" :value="$dataInsurance?->policy_reference_vmi ?? '-'"
                                   :label="__('insurance_deduct.policy_reference_vmi')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="insurnace_start_date"
                                   :value="$dataInsurance?->term_start_date?get_date_time_by_format($dataInsurance?->term_start_date, 'd/m/Y H:i'):'-'"
                                   :label="__('insurance_deduct.insurance_start_date')"/>
                </div>
                <div class="col-sm-3">
                    <x-forms.label id="insurnace_end_date"
                                   :value="$dataInsurance?->term_start_date ?get_date_time_by_format($dataInsurance?->term_start_date, 'd/m/Y H:i'):'-'"
                                   :label="__('insurance_deduct.insurance_end_date')"/>
                </div>
            </div>
        </div>
    </div>
</div>
