<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('compensations.rental_detail'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.label id="rental_worksheet_no" :value="null" :label="__('compensations.rental_worksheet_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="renter" :value="null" :label="__('compensations.renter')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="business" :value="null" :label="__('compensations.business')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="contract_no" :value="null" :label="__('compensations.contract_no')" />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.label id="contract_start_date" :value="null" :label="__('compensations.contract_start_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="contract_end_date" :value="null" :label="__('compensations.contract_end_date')" />
            </div>
        </div>
    </div>
</div>
