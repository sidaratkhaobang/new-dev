@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        {{-- <div class="block-header">
            <h3 class="block-title"><i class="fa fa-magnifying-glass me-1"></i> {{ __('lang.search') }}</h3>
        </div> --}}
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <form action="" method="GET" id="form-search">
                <div class="justify-content-between">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_no_list"
                                                   :label="__('repairs.worksheet_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="repair_type" :value="$repair_type" :list="$repair_type_list"
                                                   :label="__('repairs.type_job')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="license_plate" :value="$license_plate"
                                                   :list="$license_plate_list"
                                                   :label="__('repairs.license_plate')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="contact" :value="$contact" :list="$contact_list"
                                                   :label="__('repairs.contact')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="order_worksheet_no" :value="$order_worksheet_no"
                                                   :list="$order_worksheet_no_list"
                                                   :label="__('repair_orders.worksheet_no')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="center" :value="$center" :list="$center_list"
                                                   :label="__('repair_orders.center')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                                   :label="__('lang.status')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="alert_date" :value="$alert_date" :label="__('repairs.alert_date')"/>
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="repair_order_date" :value="$repair_order_date"
                                                :label="__('repair_orders.repair_order_date')"/>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="district_center" :value="$district_center"
                                                   :list="$district_center_list"
                                                   :label="__('repair_orders.district_center')"/>
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </div>
            </form>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
        @section('block_options_1')
            <a target="_blank"
               href="{{ route('admin.repair-orders.print-summary-pdf', [
                'worksheet_no' => $worksheet_no,
                'repair_type' => $repair_type,
                'license_plate' => $license_plate,
                'contact' => $contact,
                'status' => $status,
                'alert_date' => $alert_date,
                'repair_order_date' => $repair_order_date,
                'district_center' => $district_center,
                'open_by' => $open_by,
            ]) }}"
               class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;
                {{ __('repair_orders.print_summary') }}
            </a>
            <a target="_blank"
               href="{{ route('admin.repair-orders.print-daily-summary-pdf', [
                'worksheet_no' => $worksheet_no,
                'repair_type' => $repair_type,
                'license_plate' => $license_plate,
                'contact' => $contact,
                'status' => $status,
                'alert_date' => $alert_date,
                'repair_order_date' => $repair_order_date,
                'district_center' => $district_center,
                'open_by' => $open_by,
            ]) }}"
               class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;
                {{ __('repair_orders.print_daily_summary') }}
            </a>
            @canany([Actions::Manage . '_' . Resources::RepairOrder, Actions::Manage . '_' . Resources::CallCenterRepairOrder])
                <x-btns.add-new btn-text="{{ __('lang.add') . __('repair_orders.page_title') }}"
                                route-create="{{ $create_uri }}"/>
            @endcanany
        @endsection
        @include('admin.components.block-header', [
            'text' => __('transfer_cars.total_items'),
            'block_icon_class' => 'icon-document',
            'block_option_id' => '_1',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>
                        <th style="width: 1px;">#</th>
                        <th>{{ __('repair_orders.worksheet_no') }}</th>
                        <th>{{ __('repair_orders.repair_ref') }}</th>
                        <th>{{ __('repairs.type_job') }}</th>
                        <th>{{ __('repairs.contact') }}</th>
                        <th>{{ __('repairs.license_plate') }}</th>
                        <th>{{ __('repairs.alert_date') }}</th>
                        <th>{{ __('repairs.center_date') }}</th>
                        <th>{{ __('repair_orders.center') }}</th>
                        <th>{{ __('repairs.expected_date') }}</th>
                        <th>{{ __('repairs.completed_date') }}</th>
                        <th class="text-center">{{ __('lang.status') }}</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($list->count()))
                        @foreach ($list as $index => $d)
                            <tr>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{ $d->repair_worksheet_no }}</td>
                                <td>{{ __('repairs.repair_type_' . $d->repair_type) }}</td>
                                <td>{{ $d->contact }}</td>
                                <td>{{ $d->license_plate }}</td>
                                <td>{{ get_thai_date_format($d->repair_date, 'd/m/Y') }}</td>
                                <td>{{ $d->in_center_date ? get_thai_date_format($d->in_center_date, 'd/m/Y') : null }}
                                </td>
                                <td>
                                    {{ $d->center }}
                                </td>
                                <td>{{ $d->expected_repair_date ? get_thai_date_format($d->expected_repair_date, 'd/m/Y') : null }}
                                </td>
                                <td>{{ $d->completed_date ? get_thai_date_format($d->completed_date, 'd/m/Y') : null }}
                                </td>
                                <td class="text-center">
                                    {!! badge_render(__('repairs.repair_class_' . $d->status), __('repairs.repair_text_' . $d->status)) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @if (in_array($d->status, [
                                            RepairStatusEnum::PENDING_REPAIR,
                                            RepairStatusEnum::REJECT_QUOTATION,
                                            RepairStatusEnum::IN_PROCESS,
                                            RepairStatusEnum::EXPIRED,
                                        ]))
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route($view_uri, [$param => $d]),
                                            'edit_route' => route($edit_uri, [$param => $d]),
                                            'view_permission' => $view_permission,
                                            'manage_permission' => $manage_permission,
                                        ])
                                    @else
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route($view_uri, [$param => $d]),
                                            'view_permission' => $view_permission,
                                        ])
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="13">" {{ __('lang.no_list') }} "</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            {!! $list->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
@endsection

@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
