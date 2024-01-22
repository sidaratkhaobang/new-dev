<div class="modal fade" id="modal-cost" tabindex="-1" aria-labelledby="modal-cost" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cost-modal-label">{{ __('litigations.add_cost') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="temp_list" :value="null" :label="__('litigations.list')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="temp_bank_id" :value="null" :list="$bank_list" :label="__('litigations.bank')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="temp_payment_channel" :value="null" :list="$payment_channel_list" :label="__('litigations.payment_channel')"/>
                    </div>
                </div>
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="temp_number" :value="null" :label="__('litigations.account_number')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="temp_payment_date" :value="null" :label="__('litigations.payment_date')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="temp_amount" :value="null" :label="__('litigations.amount')"
                        :optionals="[ 'input_class' => 'number-format']" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="addCost()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
