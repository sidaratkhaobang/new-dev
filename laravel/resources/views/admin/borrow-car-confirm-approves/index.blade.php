@extends('admin.layouts.layout')
@section('page_title', $page_title)

@push('custom_styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        {{-- <div class="block-header block-header-default">
            <h3 class="block-title">{{ __('borrow_cars.search') }}</h3>
            <div class="block-options">
                <div class="block-options-item">
                </div>
            </div>
        </div> --}}
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_no_list" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('borrow_cars.worksheet')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="borrow_type" :value="$borrow_type" :list="$borrow_type_list" :optionals="['placeholder' => __('lang.search_placeholder')]"
                                :label="__('borrow_cars.borrow_type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_id" :value="$car_id" :list="$license_plate_list" :label="__('borrow_cars.license_plate')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_lists"
                                :label="__('transfer_cars.status')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_delivery_date">{{ __('borrow_cars.start_borrow_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="pickup_date_start" name="pickup_date_start" value="{{ $pickup_date_start }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="pickup_date_end" name="pickup_date_end" value="{{ $pickup_date_end }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_delivery_date">{{ __('borrow_cars.end_borrow_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="return_date_start" name="return_date_start" value="{{ $return_date_start }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="return_date_end" name="return_date_end" value="{{ $return_date_end }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                        data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('transfer_cars.total_items'),
            'block_icon_class' => 'icon-document',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 1px;">#</th>
                            <th>@sortablelink('worksheet_no', __('borrow_cars.worksheet'))</th>
                            <th>@sortablelink('borrow_type', __('borrow_cars.borrow_type'))</th>
                            <th>@sortablelink('start_date', __('borrow_cars.start_date'))</th>
                            <th>@sortablelink('end_date', __('borrow_cars.end_date'))</th>
                            <th>@sortablelink('car.license_plate', __('borrow_cars.license_plate'))</th>
                            <th>@sortablelink('borrower', __('borrow_cars.borrower'))</th>
                            <th>@sortablelink('status', __('borrow_cars.status'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->worksheet_no }}</td>
                                    <td>{{ __('borrow_cars.type_' . $d->borrow_type) }}</td>
                                    <td>{{ get_thai_date_format($d->start_date, 'd/m/Y H:i') }}</td>
                                    <td>{{ get_thai_date_format($d->end_date, 'd/m/Y H:i') }}</td>
                                    @if ($d->car && $d->car->license_plate)
                                        <td>{{ $d->car->license_plate }}</td>
                                    @elseif($d->car && $d->car->chassis_no)
                                        <td>{{ $d->car->chassis_no }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                    <td>{{ $d->contact }}</td>
                                    <td> {!! badge_render(
                                        __('borrow_cars.status_' . $d->status . '_class'),
                                        __('borrow_cars.status_' . $d->status . '_text'),
                                        null,
                                    ) !!}</td>
                                    <td class="sticky-col text-center">
                                        @if (isset($d->can_edit) && $d->can_edit)
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.borrow-car-confirm-approves.show', [
                                                    'borrow_car_confirm_approve' => $d,
                                                ]),
                                                'edit_route' => route('admin.borrow-car-confirm-approves.edit', [
                                                    'borrow_car_confirm_approve' => $d,
                                                ]),
                                                'view_permission' =>
                                                    Actions::View . '_' . Resources::BorrowCarConfirmApprove,
                                                'manage_permission' =>
                                                    Actions::Manage . '_' . Resources::BorrowCarConfirmApprove,
                                            ])
                                        @else
                                            @include('admin.components.dropdown-action', [
                                                'view_route' => route('admin.borrow-car-confirm-approves.show', [
                                                    'borrow_car_confirm_approve' => $d,
                                                ]),
                                                'view_permission' =>
                                                    Actions::View . '_' . Resources::BorrowCarConfirmApprove,
                                                'manage_permission' =>
                                                    Actions::Manage . '_' . Resources::BorrowCarConfirmApprove,
                                            ])
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="9">" {{ __('lang.no_list') }} "</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
