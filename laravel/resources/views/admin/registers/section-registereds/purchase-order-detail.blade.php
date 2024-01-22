<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_list')
    <div class="block-options">
    </div>
@endsection --}}

    @include('admin.components.block-header', [
        'text' => __('registers.po_detail'),
        'block_option_id' => '_list',
        'is_toggle' => true,
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="po_no" :value="$d->purchaseOrder->po_no ?? null" :label="__('registers.po_no')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="creditor_name" :value="$d->purchaseOrder->creditor->name ?? null" :label="__('registers.creditor_name')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="leasing" :value="$d?->purchaseOrder?->creditor?->name ?? '-'" :label="__('registers.leasing')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="paid_date" :value="get_date_by_format($d->paid_date, 'd/m/Y') ?? null" :label="__('registers.paid_date')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.label id="delivery_date" :value="$d->purchaseOrder->po_no ?? '-'" :label="__('registers.delivery_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.label id="receive_data_date" :value="$d->receive_information_date ?? null" :label="__('registers.receive_data_date')" />
            </div>
        </div>
    </div>
</div>
