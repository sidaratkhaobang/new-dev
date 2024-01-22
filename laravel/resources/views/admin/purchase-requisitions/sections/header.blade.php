<div class="block-header">
    <h3 class="block-title">ข้อมูลใบขอซื้อ: {{ $d->pr_no }}</h3>
    <div class="block-options">
        <div class="block-options-item">
            @if (in_array($d->status, [PRStatusEnum::CONFIRM]))
                <a target="_blank"
                    href="{{ route('admin.purchase-requisition.pdf', ['purchase_requisition' => $d, 'type' => 'RFQ']) }}"
                    class="btn btn-primary">
                    {{ __('purchase_requisitions.print_rfq') }}
                </a>
            @endif
            @if (in_array($d->status, [PRStatusEnum::CONFIRM, PRStatusEnum::PENDING_REVIEW, PRStatusEnum::REJECT]))
                <a target="_blank"
                    href="{{ route('admin.purchase-requisition.pdf', ['purchase_requisition' => $d, 'type' => 'PDF']) }}"
                    class="btn btn-primary">
                    {{ __('purchase_requisitions.print_pr') }}
                </a>
            @endif
        </div>
    </div>
</div>
