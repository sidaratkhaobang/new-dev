<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="row push">
            <div class="col-12 text-end">

                <a class="btn btn-outline-secondary btn-custom-size"
                    href="{{ route('admin.accident-order-sheet-ttl-approves.index') }}">{{ __('lang.back') }}</a>
                @if ($approve_line_owner)
                    @if (in_array($accident_order->status, [AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_TTL]))
                        @can(Actions::Manage . '_' . Resources::AccidentOrderApprove)
                            <button type="button" class="btn btn-primary btn-offer-new-price"
                                data-id="{{ $accident_order->id }}"
                                data-status="{{ AccidentRepairStatusEnum::REJECT }}"data-status-ttl="{{ AccidentRepairStatusEnum::OFFER_NEW_PRICE }}">
                                <i class="icon-undo me-1"></i>{{ __('accident_orders.offer_new_price') }}
                            </button>
                            <button type="button" class="btn btn-danger btn-not-approve-status"
                                data-id="{{ $accident_order->id }}" data-status="{{ AccidentRepairStatusEnum::REJECT }}"
                                data-status-ttl="{{ AccidentRepairStatusEnum::TTL }}">
                                <i class="icon-close-circle me-1"></i>{{ __('accident_orders.ttl') }}
                            </button>

                            <button type="button" class="btn btn-success btn-approve-status"
                                data-id="{{ $accident_order->id }}" data-status="{{ AccidentRepairStatusEnum::CONFIRM }}">
                                <i class="icon-tick-circle me-1"></i>{{ __('accident_orders.approved') }}</button>
                        @endcan
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
