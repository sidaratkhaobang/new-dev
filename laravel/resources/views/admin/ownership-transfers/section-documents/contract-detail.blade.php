<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('ownership_transfers.contract_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="contract_no" :value="$d->hirePurchase?->contract_no ?? null" :label="__('ownership_transfers.contract_no_rent')" />
        </div>
            <div class="col-sm-3">
                <x-forms.label id="close_account_date" :value="$d->hirePurchase && $d->hirePurchase->account_closing_date ? get_date_by_format($d->hirePurchase->account_closing_date) : null " :label="__('ownership_transfers.close_account_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="must_last_payment_date" :value="$d->hirePurchase && $d->hirePurchase->contract_end_date ? get_date_by_format($d->hirePurchase->contract_end_date) : null " :label="__('ownership_transfers.must_last_payment_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="actual_last_payment_date" :value="$d->hirePurchase && $d->hirePurchase->actual_last_payment_date ? get_date_by_format($d->hirePurchase->actual_last_payment_date) : null " :label="__('ownership_transfers.actual_last_payment_date')" />
            </div>
           
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="leasing" :value="$d?->hirePurchase?->insurance_lot?->creditor?->name ?? '-'" :label="__('registers.leasing')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="finance_receipt_date" :value="$d->hirePurchase && $d->hirePurchase->finance_receipt_date ? get_date_by_format($d->hirePurchase->finance_receipt_date) : null " :label="__('ownership_transfers.finance_receipt_date')" />
            </div>
        </div>
    </div>
</div>
