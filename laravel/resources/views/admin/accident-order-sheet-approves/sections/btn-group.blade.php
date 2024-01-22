<div class="row items-push mb-4 ">
    <div class="col-sm-8">
        @if (isset($view))
            <div class="btn-group bg-white" role="group">
                @can(Actions::Manage . '_' . Resources::AccidentOrder)
                    <a type="button" href="{{ route('admin.accident-order-approves.show', $accident_order->id) }}"
                        class="btn btn-outline-primary
                {{ in_array(Route::currentRouteName(), ['admin.accident-order-approves.show']) ? 'active' : '' }}">
                        {{ __('accident_informs.accident_job') }}
                    </a>
                @endcan
                @can(Actions::Manage . '_' . Resources::AccidentOrder)
                    <a type="button" href="{{ route('admin.accident-order-approves.show-claim', $accident_order->id) }}"
                        class="btn btn-outline-primary 
                {{ in_array(Route::currentRouteName(), ['admin.accident-order-approves.show-claim']) ? 'active' : '' }}">
                        {{ __('accident_informs.claim_detail') }}
                    </a>
                @endcan

            </div>
        @endif
    </div>
</div>
