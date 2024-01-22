<div class="row items-push mb-4 ">
    <div class="col-sm-8">
        @if (!isset($view))
            <div class="btn-group bg-white" role="group">
                @can(Actions::Manage . '_' . Resources::AccidentOrder)
                    <a type="button" href="{{ route('admin.accident-orders.edit', $accident_order->id) }}"
                        class="btn btn-outline-primary
                    {{ in_array(Route::currentRouteName(), ['admin.accident-orders.edit']) ? 'active' : '' }}">
                        {{ __('accident_informs.accident_job') }}
                    </a>
                @endcan
                @can(Actions::Manage . '_' . Resources::AccidentOrder)
                    <a type="button" href="{{ route('admin.accident-orders.edit-claim', $accident_order->id) }}"
                        class="btn btn-outline-primary 
                    {{ in_array(Route::currentRouteName(), ['admin.accident-orders.edit-claim']) ? 'active' : '' }}">
                        {{ __('accident_informs.claim_detail') }}
                    </a>
                @endcan
                @if (
                    !in_array($accident_order->status, [
                        AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST,
                        AccidentRepairStatusEnum::REJECT,
                    ]))
                    @can(Actions::Manage . '_' . Resources::AccidentOrder)
                        <a type="button" href="{{ route('admin.accident-orders.edit-repair-price', $accident_order->id) }}"
                            class="btn btn-outline-primary 
                {{ in_array(Route::currentRouteName(), ['admin.accident-orders.edit-repair-price']) ? 'active' : '' }}">
                            {{ __('accident_orders.repair_price_detail') }}
                        </a>
                    @endcan
                @endif
            </div>
        @else
            <div class="btn-group bg-white" role="group">
                @can(Actions::Manage . '_' . Resources::AccidentOrder)
                    <a type="button" href="{{ route('admin.accident-orders.show', $accident_order->id) }}"
                        class="btn btn-outline-primary
                {{ in_array(Route::currentRouteName(), ['admin.accident-orders.show']) ? 'active' : '' }}">
                        {{ __('accident_informs.accident_job') }}
                    </a>
                @endcan
                @can(Actions::Manage . '_' . Resources::AccidentOrder)
                    <a type="button" href="{{ route('admin.accident-orders.show-claim', $accident_order->id) }}"
                        class="btn btn-outline-primary 
                {{ in_array(Route::currentRouteName(), ['admin.accident-orders.show-claim']) ? 'active' : '' }}">
                        {{ __('accident_informs.claim_detail') }}
                    </a>
                @endcan
                @if (strcmp($accident_order->status, AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST) !== 0)
                    @can(Actions::Manage . '_' . Resources::AccidentOrder)
                        <a type="button"
                            href="{{ route('admin.accident-orders.show-repair-price', $accident_order->id) }}"
                            class="btn btn-outline-primary 
            {{ in_array(Route::currentRouteName(), ['admin.accident-orders.show-repair-price']) ? 'active' : '' }}">
                            {{ __('accident_orders.repair_price_detail') }}
                        </a>
                    @endcan
                @endif
            </div>
        @endif
    </div>
</div>
