@extends('admin.layouts.layout')
@section('page_title', __('parking_lots.page_title'))

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
    @include('admin.components.block-header',[
        'text' => __('lang.search'),
        'block_icon_class' => 'icon-search',
        'is_toggle' => true
    ])
    <div class="block-content pt-0">
        <form action="" method="GET" id="form-search">
            <div class="form-group row push mb-4">
                <x-forms.hidden id="area_id" name="area_id" :value="$area_id" />
                <div class="col-sm-3">
                    <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                    <input type="text" id="s" name="s" class="form-control"
                        placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="license_plate" :value="$license_plate" :list="$license_plate_list"
                        :label="__('parking_lots.license_plate')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="engine_no" :value="$engine_no" :list="$engine_no_list" :label="__('parking_lots.engine_no')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="chassis_no" :value="$chassis_no" :list="$chassis_no_list" :label="__('parking_lots.chassis_no')" />
                </div>
            </div>
            <div class="form-group row push mb-4">
                <div class="col-sm-3">
                    <x-forms.select-option id="slot_number" :value="$slot_number" :list="$slot_number_list"
                        :label="__('parking_lots.slot_number')" />
                </div>
                <div class="col-sm-3">
                    <x-forms.select-option id="status" :value="$status" :list="$status_list"
                        :label="__('lang.status')" />
                </div>
            </div>
            @include('admin.components.btns.search')
        </form>
    </div>
</div>
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('parking_lots.zone_text') . ' ' . $zone_detail->code . ' : ' . $zone_detail->name . ' : ' . __('parking_lots.slot') . ' ' . $zone_detail->start_number . ' - ' . $zone_detail->end_number,
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped">
                <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th>@sortablelink('car_park_name_code', __('parking_lots.slot_number'))</th>
                        <th>@sortablelink('car_type', __('parking_lots.car_type'))</th>
                        <th>@sortablelink('car_category', __('parking_lots.car_category'))</th>
                        <th>@sortablelink('license_plate', __('parking_lots.license_plate'))</th>
                        <th>@sortablelink('engine_no', __('parking_lots.engine_no'))</th>
                        <th>@sortablelink('chassis_no', __('parking_lots.chassis_no'))</th>
                        {{-- <th>@sortablelink('est_transfer_date', __('parking_lots.expected_transfer_date'))</th>
                        <th>@sortablelink('transfer_date', __('parking_lots.parked_date_time'))</th> --}}
                        <th style="width: 1px;" class="text-center" >@sortablelink('status', __('lang.status'))</th>
                        <th style="width: 1px;" class="sticky-col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $index => $d)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $d->car_park_name_code }}</td>
                            <td>{{ ($d->car_status) ? __('cars.status_' . $d->car_status) : '' }}</td>
                            <td>{{ $d->car_group_name }}</td>
                            <td>{{ $d->license_plate }}</td>
                            <td>{{ $d->engine_no }}</td>
                            <td>{{ $d->chassis_no }}</td>
                            {{-- <td>{{ $d->est_transfer_date }}</td>
                            <td>{{ $d->transfer_date }}</td> --}}
                            <td>{!! badge_render(__('parking_lots.slot_class_' . $d->status), __('parking_lots.slot_status_' . $d->status)) !!}</td>
                            <td class="sticky-col text-center">
                                <div class="btn-group">
                                    <div class="col-sm-12">
                                        <div class="dropdown dropleft">
                                            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.car-park-areas.view-parking-history', ['car_park_id' => $d->car_park_id]) }}"><i
                                                        class="fa fa-clock-rotate-left me-1"></i>
                                                    ดูประวัติการจอดทั้งหมด</a>
                                                    @can(Actions::Manage . '_' . Resources::ParkingZone)
                                                @if (in_array($d->status, [\App\Enums\CarParkStatusEnum::FREE]))
                                                    <a class="dropdown-item btn-show-close-car-park-modal"
                                                        data-status="{{ \App\Enums\CarParkStatusEnum::DISABLED }}"
                                                        data-id="{{ $d->car_park_id }}" href="javascript:void(0)">
                                                        <i class="far fa-circle-xmark me-1"></i> ปิดช่องจอด
                                                    </a>
                                                @endif
                                                @if (in_array($d->status, [\App\Enums\CarParkStatusEnum::DISABLED]))
                                                    <a class="dropdown-item btn-car-park-update-status"
                                                        data-status="{{ \App\Enums\CarParkStatusEnum::FREE }}"
                                                        data-id="{{ $d->car_park_id }}" href="javascript:void(0)">
                                                        <i class="far fa-circle-check me-1"></i> เปิดช่องจอด
                                                    </a>
                                                @endif
                                                @endcan
                                                
                                                {{-- @if (in_array($d->status, [\App\Enums\CarParkStatusEnum::USED, \App\Enums\CarParkStatusEnum::BOOKING]))
                                                    <a class="dropdown-item" href="#"><i class="fa fa-retweet me-1"></i> ย้ายช่องจอดรถคันนี้</a>
                                                @endif --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {!! $list->appends(\Request::except('page'))->render() !!}
    </div>
</div>
@include('admin.car-park-areas.modals.close-car-park-modal')
@endsection


@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')

@include('admin.components.update-status', [
    'route' => route('admin.car-park-areas.update-car-park-disable-date'),
])

@push('scripts')
    <script>
        $('.btn-show-close-car-park-modal').on('click', function() {
            var car_park_id = $(this).attr('data-id');
            $('#modal-close-car-park').modal('show');
            $('#car_park_id').val(car_park_id);
        });

        $('.btn-car-park-update-status').on('click', function() {
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            var data = {
                car_park_id: id,
                car_park_status: status,
            };
            var route = '{{ route('admin.car-park-areas.update-car-park-status') }}';
            updateDefaultStatus(data, route);
        });
    </script>
@endpush
