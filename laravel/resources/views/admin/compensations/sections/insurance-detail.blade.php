<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('compensations.insurance_detail'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.label id="vmi_worksheet_no" :value="null" :label="__('compensations.vmi_worksheet_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="company" :value="null" :label="__('compensations.company')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="insurance_type" :value="null" :label="__('compensations.insurance_type')" />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.label id="insurance_start_date" :value="null" :label="__('compensations.insurance_start_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="insurance_end_date" :value="null" :label="__('compensations.insurance_end_date')" />
            </div>
        </div>
    </div>
</div>
