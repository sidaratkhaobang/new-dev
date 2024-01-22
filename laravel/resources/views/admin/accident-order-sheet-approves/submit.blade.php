<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="row push">
            <div class="col-12 text-end">

                <a class="btn btn-outline-secondary btn-custom-size"
                    href="{{ route('admin.accident-order-approves.index') }}">{{ __('lang.back') }}</a>
                @if ($approve_line_owner)
                    @if ($accident_order->status == AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR)
                        @can(Actions::Manage . '_' . Resources::AccidentOrderApprove)
                            <button type="button" class="btn btn-danger dropdown-toggle dropdown" id="dropdown-default-danger"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-close-circle me-1"></i>{{ __('accident_orders.offer_gm') }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdown-default-danger">
                                <a class="dropdown-item btn btn-not-approve-status" href="javascript:void(0)"
                                    data-id="{{ $accident_order->id }}" data-status="{{ AccidentRepairStatusEnum::REJECT }}"
                                    data-offer="{{ OfferGMStatusEnum::OVER_PRICE }}">ซ่อมเกิน 200,000</a>
                                <a class="dropdown-item btn-not-approve-status" href="javascript:void(0)"
                                    data-id="{{ $accident_order->id }}" data-status="{{ AccidentRepairStatusEnum::REJECT }}"
                                    data-offer="{{ OfferGMStatusEnum::OFFER_NEW_PRICE }}">เสนอราคาใหม่</a>
                                <a class="dropdown-item btn-not-approve-status" href="javascript:void(0)"
                                    data-id="{{ $accident_order->id }}"
                                    data-status="{{ AccidentRepairStatusEnum::REJECT }}"
                                    data-offer="{{ OfferGMStatusEnum::CONSIDER_TOTAL_LOSS }}">พิจารณา Total loss</a>
                                <a class="dropdown-item btn-not-approve-status" href="javascript:void(0)"
                                    data-id="{{ $accident_order->id }}"
                                    data-status="{{ AccidentRepairStatusEnum::REJECT }}"
                                    data-offer="{{ OfferGMStatusEnum::CONSIDER_PARTIAL_LOSS }}">พิจารณา Partial loss</a>
                            </div>
                            <button type="button" class="btn btn-success btn-approve-status"
                                data-id="{{ $accident_order->id }}"
                                data-status="{{ AccidentRepairStatusEnum::CONFIRM }}"><i
                                    class="icon-tick-circle me-1"></i>{{ __('purchase_requisitions.approved') }}</button>
                        @endcan
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
