@extends('admin.layouts.layout')
@section('page_title', __('parking_lots.page_title'))
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
                    <label for="s" class="text-start col-form-label">{{ __('lang.search_label') }}</label>
                    <input type="text" id="s" name="s" class="form-control"
                        placeholder="{{ __('lang.search_placeholder') }}" value="{{ $s }}">
                </div>
                @include('admin.car-park-transfers.sections.select-search-car')
            </div>
            <div class="form-group row push">
                <div class="col-sm-3">
                    <x-forms.date-input id="in_date" :value="$in_date" :label="__('car_park_transfer_logs.in_date')" />
                </div>
            </div>
            @include('admin.components.btns.search')
        </form>
    </div>
</div>
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('parking_lots.zone_text') . ' ' . $zone_detail->code . ' : ' . $zone_detail->name . ' : ' . __('parking_lots.slot') . ' ' . $zone_detail->car_park_number,
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th>#</th>
                        <th>@sortablelink('transfer_type', __('car_park_transfer_logs.type'))</th>
                        <th>@sortablelink('car_type_name', __('car_park_transfer_logs.car_type'))</th>
                        <th>@sortablelink('car_type_name', __('car_park_transfer_logs.car_type'))</th>
                        <th>@sortablelink('license_plate', __('car_park_transfer_logs.registration'))</th>
                        <th>@sortablelink('engine_no', __('car_park_transfer_logs.engine_no'))</th>
                        <th>@sortablelink('chassis_no', __('car_park_transfer_logs.chassis_no'))</th>
                        <th>@sortablelink('transfer_type', __('car_park_transfer_logs.in_out_latest'))</th>
                        <th>@sortablelink('fullname', __('car_park_transfer_logs.car_in_out_name'))</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lists as $index => $d)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ __('car_park_transfers.transfer_type_' . $d->transfer_type) }}</td>
                            <td>{{ __('rental_categories.rental_type_' . $d->car_rental_type) }}</td>
                            <td>{{ $d->car_group_name }}</td>
                            <td>{{ $d->license_plate }}</td>
                            <td>{{ $d->engine_no }}</td>
                            <td>{{ $d->chassis_no }}</td>
                            <td>
                                @if ($d->transfer_type == 1)
                                    <i class="fa-solid fa-right-to-bracket" style="color:#157CF2"></i>
                                @else
                                    <i class="fa-solid fa-right-from-bracket" style="color: #E04F1A "></i>
                                @endif {{ get_thai_date_format($d->transfer_date, 'd/m/Y H:i') }}
                            </td>
                            <td>{{ $d->fullname }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {!! $lists->appends(\Request::except('page'))->render() !!}
    </div>
</div>
@endsection

@push('scripts')
@endpush
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2.car-license-plate'),
])

@include('admin.components.select2-ajax', [
    'id' => 'engine_no',
    'url' => route('admin.util.select2.car-engine-no'),
])

@include('admin.components.select2-ajax', [
    'id' => 'chassis_no',
    'url' => route('admin.util.select2.car-chassis-no'),
])
