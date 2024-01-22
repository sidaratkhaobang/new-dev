

<div class="block {{ __('block.styles') }} withdraw-block">
    @include('admin.components.block-header', [
        'text' => __('compensations.payment_detail'),
    ])
    <div class="block-content">
        <div class="row push">
            <div class="col-sm-3">
                <x-forms.date-input id="confirmation_date" :value="$d->confirmation_date" :label="__('compensations.confirmation_date')"
                    :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.upload-image :id="'payment_files'" :label="__('compensations.payment_files')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.upload-image :id="'tax_invoice_files'" :label="__('compensations.tax_invoice_files')" :optionals="['required' => true]" />
            </div>
        </div>
    </div>
</div>
