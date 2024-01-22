<div class="block {{ __('block.styles') }}">
    {{-- @section('block_options_1')
        <div class="block-options">
            <div class="block-options-item">
                @can(Actions::Manage . '_' . Resources::AccidentOrder)

                    <button type="button" class="btn btn-primary" onclick="addOrder()" id="openModal"><i
                            class="icon-add-circle"></i> {{ __('accident_orders.add_order') }}</button>
                @endcan
            </div>
        </div>
    @endsection --}}

    @include('admin.components.block-header', [
        'text' => __('accident_orders.garage_detail'),
    ])
    <div class="block-content">
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.select-option id="cradle_id" :value="$d->cradle_id" :list="$garage_list" :label="__('accident_orders.garage_car')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="repair_date" :value="$d->repair_date" :label="__('accident_orders.repair_date')" :optionals="['required' => true]" />
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="amount_completed" :value="$d->amount_completed" :label="__('accident_orders.amount_completed')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="scheduled_completion_date" :value="null" :label="__('accident_orders.complete_date')" />
            </div>
        </div>
        <div class="row push mb-4">
            {{-- @if ($accident_order->status == AccidentRepairStatusEnum::PROCESS_REPAIR) --}}
            <div class="col-sm-3">
                <x-forms.date-input id="actual_repair_date" :value="$d->actual_repair_date" :label="__('accident_orders.actual_repair_date')" :optionals="['required' => true]" />
            </div>
            {{-- @endif --}}
        </div>
    </div>
</div>
