<div class="block {{ __('block.styles') }} terminate-block">
    @include('admin.components.block-header', [
        'text' => __('compensations.terminate_detail'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="termination_amount" :value="$d->termination_amount" :label="__('compensations.termination_amount')" 
                    :optionals="['required' => true, 'input_class' => 'number-format']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="termination_days" :value="$d->termination_days" :label="__('compensations.termination_day')" 
                    :optionals="['required' => true , 'input_class' => 'number-format']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="termination_avg" :value="$d->termination_avg" :label="__('compensations.termination_avg')" 
                    :optionals="['required' => true , 'input_class' => 'number-format']"/>
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.input-new-line id="oic_amount" :value="$d->oic_amount" :label="__('compensations.oic_amount')" 
                    :optionals="['required' => true, 'input_class' => 'number-format']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="actual_payment_amount" :value="$d->actual_payment_amount" :label="__('compensations.actual_payment_amount')" 
                    :optionals="['required' => true , 'input_class' => 'number-format']"/>
            </div>
            <div class="col-sm-6">
                <x-forms.input-new-line id="remark" :value="$d->termination_remark" :label="__('lang.remark')"  />
            </div>
        </div>
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.upload-image :id="'termination_files'" :label="__('compensations.termination_files')" :optionals="['required' => true]" />
            </div>
        </div>
    </div>
</div>
