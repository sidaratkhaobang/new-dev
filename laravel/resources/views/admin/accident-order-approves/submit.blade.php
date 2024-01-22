<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="row push">
            <div class="col-12 text-end">
                <a class="btn btn-outline-secondary btn-custom-size"
                    href="{{ route('admin.accident-order-approves.index') }}">{{ __('lang.back') }}</a>
                @if ($approve_line_owner)
                    @if ($accident_order->status == AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST)
                        @can(Actions::Manage . '_' . Resources::AccidentOrderApprove)
                            <button type="button" class="btn btn-danger btn-not-approve-status"
                                data-id="{{ $accident_order->id }}" data-status="{{ AccidentRepairStatusEnum::REJECT }}">
                                <i class="icon-close-circle me-1"></i>{{ __('purchase_requisitions.reject') }}
                            </button>
                            <button type="button" class="btn btn-success btn-approve-status"
                                data-id="{{ $accident_order->id }}" data-status="{{ AccidentRepairStatusEnum::CONFIRM }}">
                                <i class="icon-tick-circle me-1"></i>{{ __('purchase_requisitions.approved') }}</button>
                        @endcan
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
