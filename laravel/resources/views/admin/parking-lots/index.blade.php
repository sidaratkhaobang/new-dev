@extends('admin.layouts.layout')
@section('page_title', __('lang.manage') . __('parking_lots.page_title'))
@push('styles')
    <style>
        .td-table {
            padding-right: 0px !important;
        }

        .block-rounded {
            border-radius: 0.25rem !important;
            border: 1px solid rgba(0, 0, 0, 0.2);
        }

        .item-bold {
            font-weight: bolder;
            font-size: 1.8rem;
            color: #4D4D4D;
        }

        .item-content {
            font-weight: 500;
            font-size: 1.2rem;
            color: #4D4D4D;
        }

        .table-responsive {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true
        ])
        <div class="block-content pt-0">
            <form action="" method="GET" id="form-search">
                <div class="form-group row push">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="s" :value="$s" :label="__('lang.search_label')" :optionals="['placeholder' => __('lang.search_placeholder')]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option :value="$zone_size_id" id="zone_size_id" :list="$car_zone_size_list" :label="__('parking_lots.zone_size')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option :value="$car_group_id" id="car_group_id" :list="$car_group_list" :label="__('parking_lots.group_car')"/>
                    </div>
                </div>
                @include('admin.components.btns.search')
            </form>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        <div class="block-content" >
            <div class="row">
                <div class="col-4">
                    <div class="block block-rounded mb-0">
                        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                            <div class="me-3 item-content">
                                {{ __('parking_lots.sum_total_slot') }}
                            </div>
                            <div class="item-bold">
                                {{ $over_all_total }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="block block-rounded mb-0">
                        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                            <div class="me-3 item-content">
                                {{ __('parking_lots.total_unavailable_slot') }}
                            </div>
                            <div class="item-bold text-danger">
                                {{ $over_all_unavailable }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="block block-rounded mb-0">
                        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                            <div class="me-3 item-content">
                                {{ __('parking_lots.total_available_slot') }}
                            </div>
                            <div class="item-bold text-primary">
                                {{ $over_all_available }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @section('block_options_list')
            <div class="block-options">
                <div class="block-options-item">
                    @can(Actions::Manage . '_' . Resources::ParkingZone)
                    <button type="button" class="btn btn-primary btn-modal-remove-car-from-parking me-2" >{{ __('parking_lots.remove_car_from_parking') }}</button>
                    <button type="button" class="btn btn-primary btn-modal-add-car-to-parking me-2" >{{ __('parking_lots.add_car_to_parking') }}</button>
                    <x-btns.add-new btn-text="{{ __('parking_lots.add_zone') }}" route-create="{{ route('admin.parking-lots.create') }}" />
                    @endcan
                </div>
            </div>
        @endsection
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
            'block_option_id' => '_list'
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                {{-- <div class="table-wrap db-scroll"> --}}
                    {{-- <div class="table-wrap "> --}}
                    <table class="table table-striped table-vcenter">
                        {{-- <table class="table table-borderless table-vcenter"> --}}
                        <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 1px;"></th>
                            <th style="width: 1px;">#</th>
                            <th style="width: 250px">@sortablelink('code', __('parking_lots.zone_code'))</th>
                            <th style="width: 250px">@sortablelink('name', __('parking_lots.zone_name'))</th>
                            <th style="width: 15%">{{ __('parking_lots.zone_size') }}</th>
                            <th style="width: 15%">{{ __('parking_lots.total_slot') }}</th>
                            <th style="width: 15%">{{ __('parking_lots.total_unavailable_slot') }}</th>
                            <th style="width: 15%">{{ __('parking_lots.total_available_slot') }}</th>
                            <th style="width: 1px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                            @if (sizeof($list) > 0)
                            @foreach ($list as $_index => $d)
                                <tr class="{{ $loop->iteration % 2 == 0 ? 'table-active' : '' }}">
                                    <td class="text-center toggle-table" style="width: 30px">
                                        <i class="fa fa-angle-right text-muted"></i>
                                    </td>
                                    <td>{{ $list->firstItem() + $_index }}</td>
                                    <td style="width: 250px">{{ $d->code }}</td>
                                    <td style="width: 250px">{{ $d->name }}</td>
                                    <td style="width: 15%">{{ __('parking_lots.zone_' . $d->zone_size) }}</td>
                                    <td style="width: 15%">{{ $d->sum_total_slot }}</td>
                                    <td style="width: 15%">{{ $d->sum_unavailable_slot }}</td>
                                    <td style="width: 15%">{{ $d->sum_available_slot }}</td>
                                    <td class="sticky-col text-center" style="width: 5%">
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.parking-lots.show', [
                                                'parking_lot' => $d,
                                            ]),
                                            'edit_route' => route('admin.parking-lots.edit', [
                                                'parking_lot' => $d,
                                            ]),
                                            'delete_route' => route('admin.parking-lots.destroy', [
                                                'parking_lot' => $d,
                                            ]),
                                            'view_permission' => Actions::View . '_' . Resources::ParkingZone,
                                            'manage_permission' => Actions::Manage . '_' . Resources::ParkingZone,
                                        ])
                                    </td>
                                </tr>
                                <tr style="display: none;">
                                    <td></td>
                                    <td class="td-table" colspan="8">
                                        {{-- <div class="table-wrap db-scroll">
                                            <div class="table-wrap db-scroll"> --}}
                                        <table class="table table-striped">
                                            <thead class="bg-body-dark">
                                                <th style="width: 1px;">#</th>
                                                <th style="width: 150px">{{ __('parking_lots.start_slot_no') }}
                                                </th>
                                                <th style="width: 150px">{{ __('parking_lots.end_slot_no') }}</th>
                                                <th style="width: 150px">{{ __('parking_lots.group_car') }}</th>
                                                <th>{{ __('parking_lots.slot_size') }}</th>
                                                <th>{{ __('parking_lots.zone_type') }}</th>
                                                <th>{{ __('parking_lots.total_slot') }}</th>
                                                <th>{{ __('parking_lots.total_unavailable_slot') }}</th>
                                                <th>{{ __('parking_lots.total_available_slot') }}</th>
                                                <th>{{ __('lang.status') }}</th>
                                                <th style="width: 1px;" class="sticky-col text-center"></th>
                                            </thead>
                                            <tbody>
                                            @if (sizeof($d->car_slot_list) > 0)
                                                @foreach ($d->car_slot_list as $index => $item)
                                                    <tr>
                                                        <td style="width: 50px">{{ $index + 1 }}</td>
                                                        <td style="width: 150px">{{ $item->start_number }}</td>
                                                        <td style="width: 150px">{{ $item->end_number }}</td>
                                                        <td style="width: 150px">{{ $item->car_group_text }}
                                                        </td>
                                                        <td>{{ $item->area_size_text }}</td>
                                                        <td>{{ __('parking_lots.zone_type_' . $item->zone_type) }}
                                                        </td>
                                                        <td>{{ $item->total_slot }}</td>
                                                        <td>{{ $item->unavailable_car_count }}</td>
                                                        <td>{{ $item->available_car_count }}</td>
                                                        <td>{!! badge_render(__('parking_lots.class_' . $item->status), __('parking_lots.status_' . $item->status)) !!}</td>
                                                        <td class="sticky-col text-center">
                                                            <div class="btn-group">
                                                                <div class="col-sm-12">
                                                                    <div
                                                                        class="dropdown dropleft dropdown-width">
                                                                        <button type="button"
                                                                                class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                                                id="dropdown-dropleft-dark"
                                                                                data-bs-toggle="dropdown"
                                                                                aria-haspopup="true"
                                                                                aria-expanded="false">
                                                                            <i
                                                                                class="fa fa-ellipsis-vertical"></i>
                                                                        </button>
                                                                        <div class="dropdown-menu"
                                                                            aria-labelledby="dropdown-dropleft-dark">
                                                                            @can(Actions::View . '_' .
                                                                                Resources::ParkingZone)
                                                                                <a class="dropdown-item"
                                                                                href="{{ route('admin.car-park-areas.index', ['area_id' => $item->id]) }}">
                                                                                    <i class="fa fa-eye me-1"></i>
                                                                                    ดูช่องจอดทั้งหมด</a>
                                                                            @endcan
                                                                            @can(Actions::Manage . '_' .
                                                                                Resources::ParkingZone)
                                                                                <a class="dropdown-item"
                                                                                href="{{ route('admin.parking-lots.edit', ['parking_lot' => $d]) }}">
                                                                                    <i
                                                                                        class="far fa-edit me-1"></i>
                                                                                    แก้ไขข้อมูลโซนจอด</a>
                                                                            @endcan
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center" colspan="10">"
                                                        {{ __('lang.no_list') }} "
                                                    </td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                        {{-- </div>
                                    </div> --}}
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
                {{-- </div> --}}
                {{-- </div> --}}
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
    @include('admin.parking-lots.modals.add-car-to-parking')
    @include('admin.parking-lots.modals.remove-car-from-parking')
@endsection

@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-default')

@include('admin.parking-lots.scripts.add-car-to-parking')

@include('admin.components.select2-ajax', [
    'id' => 'car_park_zone_id_outside',
    'url' => route('admin.util.select2.car-park-zone-code-name'),
    'modal' => '#modal-add-car-to-parking'
])

@include('admin.components.select2-ajax', [
    'id' => 'car_id_outside',
    'url' => route('admin.util.select2.car-park-car-outside-parking'),
    'modal' => '#modal-add-car-to-parking'
])

@include('admin.components.select2-ajax', [
    'id' => 'car_park_id_outside',
    'url' => route('admin.util.select2.car-park-free'),
    'parent_id' => 'car_park_zone_id_outside',
    'modal' => '#modal-add-car-to-parking'
])

@include('admin.components.select2-ajax', [
    'id' => 'car_id_inside',
    'url' => route('admin.util.select2.car-park-car-in-parking'),
    'modal' => '#modal-remove-car-from-parking'
])

@push('scripts')
    <script>
        $('.toggle-table').click(function () {
            $(this).parent().next('tr').toggle();
            $(this).children().toggleClass('fa fa-angle-down text-muted').toggleClass(
                'fa fa-angle-right text-muted');
        });

        $(document).ready(function () {
            $('.dropleft').click(function () {
                $('.table-responsive').removeAttr('style');
            });
            $('.dropdown-width').click(function () {
                $('.table-responsive').removeAttr('style');
            });
        });
    </script>
@endpush
