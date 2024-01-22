<div class="block-header">
    <h3 class="block-title">ข้อมูลใบสั่งซื้อ: {{ $d->po_no }}</h3>
    @if (!in_array($d->status, [App\Enums\POStatusEnum::DRAFT]))
        @if (isset($mode) && $mode == MODE_CREATE)
        @else
            <div class="block-options">
                <div class="block-options-item">
                    <a target="_blank" href="{{ route('admin.purchase-orders.print-pdf', ['purchase_order_id' => $d->id]) }}" class="btn btn-primary" >
                    {{ __('purchase_orders.print_po') }}
                    </a>
                </div>
            </div>  
        @endif  
    @endif
</div>