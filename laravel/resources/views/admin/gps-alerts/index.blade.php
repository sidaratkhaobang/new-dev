@extends('admin.layouts.layout')
@section('page_title', __('gps.page_title_alert'))

@section('content')
    <div class="block {{ __('block.styles') }}">
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
                            <x-forms.select-option id="chassis_no" :value="$chassis_no" :list="$chassis_no_list" :label="__('gps.chassis_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="license_plate" :value="$license_plate" :list="$license_plate_list"
                                :label="__('gps.license_plate')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="vid" :value="$vid" :list="$vid_list" :label="__('gps.vid')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="date" :value="$date" :label="__('gps.date')" :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>

        </div>
    </div>


    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.total_list'),
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 1px;">#</th>
                            <th style="width: 20%;">{{ __('gps.chassis_no') }}</th>
                            <th style="width: 20%;">{{ __('gps.license_plate') }}</th>
                            <th style="width: 20%;">{{ __('gps.vid') }}</th>
                            <th style="width: 20%;">{{ __('gps.current_location') }}</th>
                            <th style="width: 20%;">{{ __('gps.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->chassis_no }}</td>
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ $d->vid }}</td>
                                    <td>{{ $d->current_location }}</td>
                                    <td>{{ $d->gps_event_timestamp }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="table-empty">
                                <td class="text-center" colspan="6">"
                                    {{ __('lang.no_list') }} "
                                </td>
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
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
