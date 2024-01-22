<div class="row items-push mb-4">
    <div class="col-sm-6">
        <div class="btn-group" role="group">
            <a type="button" href="{{ $route_group['tab_repair_order'] ?? '' }}"
                class="btn btn-outline-primary {{ in_array(Route::currentRouteName(), [
                    'admin.repair-orders.edit',
                    'admin.repair-orders.show',
                    'admin.call-center-repair-orders.edit',
                    'admin.call-center-repair-orders.show',
                    'admin.repair-quotation-approves.show',
                ])
                    ? 'active'
                    : '' }}">
                {{ __('repair_orders.table_order') }}
            </a>
            <a type="button" href="{{ $route_group['tab_condition'] ?? '' }}"
                class="btn btn-outline-primary {{ in_array(Route::currentRouteName(), [
                    'admin.repair-order-conditions.edit',
                    'admin.repair-order-conditions.show',
                ])
                    ? 'active'
                    : '' }}">
                {{ __('repair_orders.btn_condition') }}
            </a>
        </div>
    </div>
</div>
