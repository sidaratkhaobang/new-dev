<div class="block-header">
    <h3 class="block-title">ข้อมูลใบขอซื้อ: {{ $d->worksheet_no }}</h3>
    <div class="block-options">
        <div class="block-options-item">
            {{-- @if (in_array($d->status, [PRStatusEnum::CONFIRM])) --}}
                <a target="_blank" href="{{ route('admin.install-equipment-purchase-orders.pdf', ['install_equipment_po_id' => $d->id]) }}" class="btn btn-primary">
                    {{ __('install_equipment_pos.print') }}
                </a>
            {{-- @endif --}}
        </div>
    </div>
</div>
