@extends('admin.layouts.layout')
@section('page_title', __('car_park_transfer_logs.page_title'))
@section('content')
<x-blocks.block-search>
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
                <x-forms.select-option id="transfer_type" :value="$transfer_type" :list="$transfer_type_list"
                    :label="__('car_park_transfer_logs.sheet_type')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="est_transfer_date" :value="$est_transfer_date" :label="__('car_park_transfer_logs.car_in_out_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-range :label="__('car_park_transfer_logs.use_sheet_date')" 
                    start-id="from_delivery_date" :start-value="$from_delivery_date" 
                    end-id="to_delivery_date" :end-value="$to_delivery_date" 
                />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="driver_id" :value="$driver_id" :list="$driver_list" :label="__('car_park_transfer_logs.car_in_out_name')" />
            </div>
        </div>
        @include('admin.components.btns.search')
    </form>
</x-blocks.block-search>

<x-blocks.block-table>
    <div class="table-wrap db-scroll">
        <table class="table table-striped table-vcenter">
            <thead class="bg-body-dark">
                <tr>
                    <th>#</th>
                    <th>@sortablelink('worksheet_no', __('car_park_transfer_logs.sheet_no'))</th>
                    <th>@sortablelink('transfer_type', __('car_park_transfer_logs.sheet_type'))</th>
                    <th>@sortablelink('est_transfer_date', __('car_park_transfer_logs.car_in_out_date'))</th>
                    <th>@sortablelink('date_start', __('car_park_transfer_logs.use_sheet_date'))</th>
                    <th>@sortablelink('car_type_name', __('car_park_transfer_logs.car_type'))</th>
                    <th>@sortablelink('license_plate', __('car_park_transfer_logs.registration'))</th>
                    <th>@sortablelink('engine_no', __('car_park_transfer_logs.engine_no'))</th>
                    <th>@sortablelink('chassis_no', __('car_park_transfer_logs.chassis_no'))</th>
                    <th>@sortablelink('parking_slot', __('car_park_transfer_logs.parking_slot'))</th>
                    <th>@sortablelink('transfer_type', __('car_park_transfer_logs.in_out_latest'))</th>
                    <th>@sortablelink('fullname', __('car_park_transfer_logs.car_in_out_name'))</th>
                </tr>
            </thead>
            <tbody>
                @if (sizeof($lists) > 0)
                    @foreach ($lists as $index => $d)
                        <tr>
                            <td>{{ $lists->firstItem() + $index }}</td>
                            <td><a href="{{ route('admin.car-park-transfers.show', $d->car_park_transfer_id) }}" target="_blank" >{{ $d->worksheet_no }}</a>
                            </td>
                            <td>{{ __('car_park_transfers.transfer_type_' . $d->transfer_type) }}</td>
                            <td>{{ get_thai_date_format($d->est_transfer_date, 'd/m/Y') }}</td>
                            <td>{{ get_thai_date_format($d->date_start, 'd/m/Y') }} -
                                {{ get_thai_date_format($d->date_end, 'd/m/Y') }}</td>
                            <td>{{ $d->car_type_name }}</td>
                            <td>{{ $d->license_plate }}</td>
                            <td>{{ $d->engine_no }}</td>
                            <td>{{ $d->chassis_no }}</td>
                            <td>{{ $d->parking_slot }}</td>
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
                @else
                    <tr>
                        <td class="text-center" colspan="12">" {{ __('lang.no_list') }} "</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    {!! $lists->appends(\Request::except('page'))->render() !!}
</x-blocks.block-table>
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
