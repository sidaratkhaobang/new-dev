<div class="block {{ __('block.styles') }} compen-sum-block">
    @include('admin.components.block-header', [
        'text' => __('compensations.claim_data'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.select-option id="creator_id" :value="$d->creator_id" :list="null" :label="__('compensations.creator')" 
                    :optionals="['ajax' => true, 'default_option_label' => $creator_name, 'required' => true]" />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="claim_amount" :value="$d->claim_amount" :label="__('compensations.claim_amount')" 
                    :optionals="['input_class' => 'number-format', 'required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="claim_days" :value="$d->claim_days" :label="__('compensations.claim_day')" 
                    :optionals="['input_class' => 'number-format', 'required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="claim_amount_total" :value="$d->claim_amount_total" :label="__('compensations.claim_amount_total')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="claim_amount_total_text" :value="$d->claim_amount_total_text" :label="__('compensations.claim_amount_total_text')" />
            </div>
        </div>
    </div>
</div>
