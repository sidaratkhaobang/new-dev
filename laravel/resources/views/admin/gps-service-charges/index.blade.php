@extends('admin.layouts.layout')
@section('page_title', __('gps.page_title_service'))

@push('styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }

        .block-link-list {
            border-radius: 0.25rem;
            background: #F1F4F9 !important;
            border: 1px solid #CBD4E1 !important;
            border-radius: 6px;
        }

        .block-link-list .table {
            margin-bottom: 0rem;
            border-style: none;
        }
    </style>
@endpush

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <div class="block-header">
                <div class="block-title">
                    @if ($year_tab)
                        <div class="row items-push mb-4">
                            <div class="col-sm-8">
                                <div class="btn-group" role="group">
                                    @foreach ($year_tab as $item)
                                        <a type="button"
                                            href="{{ route('admin.gps-service-charges.index-service', ['id' => $item->id]) }}"
                                            class="btn btn-outline-primary {{ $item->id == $service_charge->id ? 'active' : '' }}">
                                            {{ $item->year }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="block-options">
                    <div class="block-options-item">
                        @can(Actions::Manage . '_' . Resources::GPSServiceCharge)
                            <x-btns.add-new btn-text="{{ __('gps.add_new') }}"
                                route-create="{{ route('admin.gps-service-charges.create') }}" />
                        @endcan
                    </div>
                </div>
            </div>
            <div class="table-wrap db-scroll">
                <table class="table table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width:16%;">{{ __('gps.month') }}</th>
                            <th style="width:16%;">{{ __('gps.budget') }}</th>
                            <th style="width:16%;">{{ __('gps.air_time_gps') }}</th>
                            <th style="width:16%;">{{ __('gps.air_time_dvr') }}</th>
                            <th style="width:16%;">{{ __('gps.total') }}</th>
                            <th style="background: #EFB008; width:16%;">{{ __('gps.actual') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($data_months) > 0)
                            @foreach ($data_months as $item)
                                <tr>
                                    <td style="width:16%;">{{ get_name_month($item->month) }}</td>
                                    <td style="width:16%;">
                                        {{ $item->budget > '0:00' ? number_format($item->budget, 2) : null }}</td>
                                    <td style="width:16%;">
                                        {{ $item->air_time_gps > '0:00' ? number_format($item->air_time_gps, 2) : null }}
                                    </td>
                                    <td style="width:16%;">
                                        {{ $item->air_time_dvr > '0:00' ? number_format($item->air_time_dvr, 2) : null }}
                                    </td>
                                    <td style="width:16%;">
                                        {{ $item->total > '0:00' ? number_format($item->total, 2) : null }}</td>
                                    <td style="background: #FFF3D5;" style="width:16%;">
                                        {{ $item->actual > '0:00' ? number_format($item->actual, 2) : null }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @foreach ($fiscal_month_list as $item)
                                <tr>
                                    <td style="width:16%;">{{ get_name_month($item) }}</td>
                                    <td style="width:16%;"></td>
                                    <td style="width:16%;"></td>
                                    <td style="width:16%;"></td>
                                    <td style="width:16%;"></td>
                                    <td style="background: #FFF3D5;" style="width:16%;"></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="block-link-list mb-4 table-wrap">
                <table class="table table-vcenter">
                    <tbody>
                        <tr>
                            <th style="width:17%;">{{ __('gps.lang_total') }}</th>
                            <th style="width:17%;">
                                {{ $service_charge ? number_format($service_charge->total_budget, 2) : null }}
                            </th>
                            <th style="width:17%;">
                                {{ $service_charge ? number_format($service_charge->total_air_time_gps, 2) : null }}
                            </th>
                            <th style="width:17%;">
                                {{ $service_charge ? number_format($service_charge->total_air_time_dvr, 2) : null }}
                            </th>
                            <th style="width:17%;">
                                {{ $service_charge ? number_format($service_charge->total, 2) : null }}</th>
                            <th style="width:17%;">
                                {{ $service_charge ? number_format($service_charge->total_actual, 2) : null }}
                            </th>
                            <th style="width: 10%;" class="text-center">
                                @if ($service_charge)
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                            id="dropdown-dropleft-dark" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @can(Actions::View . '_' . Resources::GPSServiceCharge)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.gps-service-charges.show', ['gps_service_charge' => $service_charge->id]) }}"><i
                                                        class="fa fa-eye me-1"></i>
                                                    {{ __('car_inspection_types.view') }}
                                                </a>
                                            @endcan
                                            @can(Actions::Manage . '_' . Resources::GPSServiceCharge)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.gps-service-charges.edit', ['gps_service_charge' => $service_charge->id]) }}"><i
                                                        class="far fa-edit me-1"></i>
                                                    {{ __('car_inspection_types.edit') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                @endif
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
