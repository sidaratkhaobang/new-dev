@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('block_options_1')
    @canany([Actions::Manage . '_' . Resources::Repair, Actions::Manage . '_' . Resources::CallCenterRepair])
        <x-btns.add-new btn-text="{{ __('lang.add') . __('repairs.page_title') }}" route-create="{{ $create_uri }}" />
    @endcanany
@endsection

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
                            <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_no_list" :label="__('repairs.worksheet_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="repair_type" :value="$repair_type" :list="$repair_type_list" :label="__('repairs.type_job')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="license_plate" :value="$license_plate" :list="$license_plate_list"
                                :label="__('repairs.license_plate')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="contact" :value="$contact" :list="$contact_list" :label="__('repairs.contact')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="order_worksheet_no" :value="$order_worksheet_no" :list="$order_worksheet_no_list"
                                :label="__('repair_orders.worksheet_no')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                :label="__('lang.status')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="alert_date" :value="$alert_date" :label="__('repairs.alert_date')" />
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
            'block_option_id' => '_1',
        ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 1px;">#</th>
                            <th>{{ __('repairs.worksheet_no') }}</th>
                            <th>{{ __('repairs.repair_ref') }}</th>
                            <th>{{ __('repairs.type_job') }}</th>
                            <th>{{ __('repairs.contact') }}</th>
                            <th>{{ __('repairs.license_plate') }}</th>
                            <th>{{ __('repairs.alert_date') }}</th>
                            <th>{{ __('repairs.center_date') }}</th>
                            <th>{{ __('repairs.expected_date') }}</th>
                            <th>{{ __('repairs.completed_date') }}</th>
                            <th class="text-center">{{ __('lang.status') }}</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($list->count()))
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->worksheet_no }}</td>
                                    <td>{{ $d->order_worksheet_no }}</td>
                                    <td>{{ __('repairs.repair_type_' . $d->repair_type) }}</td>
                                    <td>{{ $d->contact }}</td>
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ get_thai_date_format($d->repair_date, 'd/m/Y') }}</td>
                                    <td>{{ $d->in_center_date ? get_thai_date_format($d->in_center_date, 'd/m/Y') : null }}
                                    </td>
                                    <td>{{ $d->expected_repair_date ? get_thai_date_format($d->expected_repair_date, 'd/m/Y') : null }}
                                    </td>
                                    <td>{{ $d->completed_date ? get_thai_date_format($d->completed_date, 'd/m/Y') : null }}
                                    </td>
                                    <td class="text-center">
                                        {!! badge_render(__('repairs.repair_class_' . $d->status), __('repairs.repair_text_' . $d->status)) !!}
                                    </td>
                                    <td class="sticky-col text-center">
                                        @if (strcmp($d->status, RepairStatusEnum::WAIT_OPEN_REPAIR_ORDER) == 0)
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
                                <td class="text-center" colspan="12">" {{ __('lang.no_list') }} "</td>
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
