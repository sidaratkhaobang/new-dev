@extends('admin.layouts.layout')
@section('page_title', __('traffic_tickets.page_title'))

@section('block_options_list')
    @can(Actions::Manage . '_' . Resources::TransferCar)
        <x-btns.add-new btn-text="{{ __('traffic_tickets.add_new') }}"
            route-create="{{ route('admin.traffic-tickets.create') }}" />
    @endcan
@endsection
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header',[
            'text' =>   __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="traffic_ticket_id" :value="$traffic_ticket_id" :list="null" :label="__('traffic_tickets.worksheet_no')" 
                            :optionals="['ajax' => true, 'default_option_label' => $traffic_ticket_no]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="car_id" :value="$car_id" :list="null" :label="__('traffic_tickets.license_plate')"
                                :optionals="['ajax' => true, 'default_option_label' => $license_plate]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="document_type" :value="$document_type" :list="$doc_type_list" :label="__('traffic_tickets.doc_type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="police_station_id" :value="$police_station_id" :list="null" :label="__('traffic_tickets.police_station_and_code')" 
                                :optionals="['ajax' => true, 'default_option_label' => $police_station_text]" />
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="offense_date" :value="$offense_date" :label="__('traffic_tickets.time_of_occurrence')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('lang.status')" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>

        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('locations.total_items'),
            'block_option_id' => '_list',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th style="width: 15%;">{{ __('traffic_tickets.worksheet_no') }}</th>
                        <th style="width: 15%;">{{ __('traffic_tickets.license_plate') }}</th>
                        <th style="width: 15%;">{{ __('traffic_tickets.doc_type') }}</th>
                        <th style="width: 15%;">{{ __('traffic_tickets.police_station_code') }}</th>
                        <th style="width: 15%;">{{ __('traffic_tickets.police_station') }}</th>
                        <th style="width: 15%;">{{ __('traffic_tickets.time_of_occurrence') }}</th>
                        <th  class="text-center" style="width: 15%;">{{ __('lang.status') }}</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->traffic_ticket_no }}</td>
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ $d->document_type ? __('traffic_tickets.doc_type_' . $d->document_type) : null }}</td>
                                    <td>{{ $d->police_station_code }}</td>
                                    <td>{{ $d->police_station }}</td>
                                    <td>{{ $d->offense_date ? get_thai_date_format($d->offense_date, 'd/m/Y') : null }}</td>
                                    <td class="text-center">{!! badge_render(__('traffic_tickets.class_' . $d->status), __('traffic_tickets.status_' . $d->status)) !!} </td>
                                    <td class="sticky-col text-center">
                                        <x-tables.dropdown 
                                            :view-route="route('admin.traffic-tickets.show', ['traffic_ticket' => $d])"  
                                            :edit-route="route('admin.traffic-tickets.edit', ['traffic_ticket' => $d])" 
                                            :view-permission="Actions::View . '_' . Resources::TrafficTicket"
                                            :manage-permission="Actions::Manage . '_' . Resources::TrafficTicket">
                                        </x-tables.dropdown>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="table-empty">
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
@include('admin.components.select2-ajax', [
    'id' => 'traffic_ticket_id',
    'url' => route('admin.util.select2-traffic-ticket.worksheets'),
])

@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2-traffic-ticket.cars'),
])

@include('admin.components.select2-ajax', [
    'id' => 'police_station_id',
    'url' => route('admin.util.select2-traffic-ticket.police-stations'),
])
