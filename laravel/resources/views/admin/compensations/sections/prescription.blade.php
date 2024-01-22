<div class="block {{ __('block.styles') }} prescription-block">
    @include('admin.components.block-header', [
        'text' => __('compensations.prescription_detail'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.date-input id="accident_date" :value="$d->accident?->accident_date" :label="__('compensations.accident_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="claim_type" :value="$d->type" :list="$complain_type_list" :label="__('compensations.claim_type')" 
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="verdict_date" :value="null" :label="__('compensations.verdict_date')" />
            </div>
        </div>
    </div>
</div>
