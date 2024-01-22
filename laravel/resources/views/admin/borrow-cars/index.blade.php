@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    <x-blocks.block-search>
        <form action="" method="GET" id="form-search">
            <div class="form-group row push">
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
                    <x-forms.select-option id="status" :value="$status" :list="$status_lists" :label="__('transfer_cars.status')" />
                </div>
            </div>
            <div class="form-group row push">
                <div class="col-sm-3">
                    <x-forms.date-range :label="__('borrow_cars.start_borrow_date')" start-id="pickup_date_start" :start-value="$pickup_date_start"
                        end-id="pickup_date_end" :end-value="$pickup_date_end" />
                </div>
                <div class="col-sm-3">
                    <x-forms.date-range :label="__('borrow_cars.end_borrow_date')" start-id="return_date_start" :start-value="$return_date_start"
                        end-id="return_date_end" :end-value="$return_date_end" />
                </div>
            </div>
            @include('admin.components.btns.search')
        </form>
    </x-blocks.block-search>

    <x-blocks.block-table>
        <x-slot name="options">
            @can(Actions::Manage . '_' . Resources::BorrowCar)
                @if (isset($show_btn_new))
                    <x-btns.add-new btn-text="{{ __('borrow_cars.add_new') }}"
                        route-create="{{ route('admin.borrow-cars.create') }}" />
                @endif
            @endcan
        </x-slot>

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
                                @if ($d->license_plate)
                                    <td>{{ $d->license_plate }}</td>
                                @elseif($d->car && $d->chassis_no)
                                    <td>{{ $d->car?->chassis_no }}</td>
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
                                    @if (isset($d->can_edit) && boolval($d->can_edit))
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.borrow-cars.show', [
                                                'borrow_car' => $d,
                                            ]),
                                            'edit_route' => route('admin.borrow-cars.edit', [
                                                'borrow_car' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::BorrowCar,
                                            'manage_permission' => Actions::Manage . '_' . Resources::BorrowCar,
                                        ])
                                    @else
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.borrow-cars.show', [
                                                'borrow_car' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::BorrowCar,
                                            'manage_permission' => Actions::Manage . '_' . Resources::BorrowCar,
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
    </x-blocks.block-table>
@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')
