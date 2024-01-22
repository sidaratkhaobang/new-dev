{{-- Car Detail --}}
<div class="block {{ __('block.styles') }}" id="car_borrow" style="display: none">
    <div class="block-content">
{{-- <h4>{{ __('borrow_cars.borrow_car_detail') }}</h4>
<hr>
<div class="row push mb-4">

    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.select-option id="borrow_branch_id" :value="$d->borrow_branch_id" :list="$branch_list" :label="__('transfer_cars.branch')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option id="car_id" :value="$d->car_id" :list="$car_lists" :label="__('transfer_cars.license_plate_chassis')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="car_class" :value="$d->car && $d->car->carClass ? $d->car->carClass->full_name : null" :label="__('car_classes.class')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="car_color" :value="$d->car && $d->car->carColor ? $d->car->carColor->name : null" :label="__('car_classes.color')" :optionals="['required' => true]"/>
        </div>
    </div>
</div> --}}



<div class="row push">
    <div class="col-sm-12 text-end">
        {{-- all --}}
        <a class="btn btn-outline-secondary btn-custom-size"
            href="{{ route('admin.borrow-car-approves.index') }}">{{ __('lang.back') }}</a>

        {{-- รออนุมัติ --}}
        {{-- @if (StepApproveManagement::canApprove($d->id)) --}}
            @if ($d->status == BorrowCarEnum::PENDING_REVIEW)
                @can(Actions::Manage . '_' . Resources::BorrowCarApprove)
                    <button type="button" class="btn btn-danger btn-not-approve-status"
                        data-id="{{ $d->id }}"
                        data-status="{{ BorrowCarEnum::REJECT }}">{{ __('purchase_requisitions.reject') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-approve-status"
                        data-id="{{ $d->id }}"
                        data-status="{{ BorrowCarEnum::CONFIRM }}">{{ __('purchase_requisitions.approved') }}</button>
                @endcan
            @endif
        {{-- @endif --}}
    </div>
</div>

{{-- @if (!in_array($d->status, [TransferCarEnum::IN_PROCESS, TransferCarEnum::SUCCESS]))
    @include('admin.transfer-cars.submit')
@endif --}}
</div>
</div>
