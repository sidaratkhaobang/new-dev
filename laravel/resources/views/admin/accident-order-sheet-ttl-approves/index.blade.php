@extends('admin.layouts.layout')
@section('page_title', __('accident_orders.accident_order_sheet_ttl_approve'))

@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
        ])
        <div class="block-content">
            <div class="justify-content-between mb-4">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$worksheet" id="repair_worksheet_no" :list="null"
                                :label="__('accident_orders.repair_worksheet_no')" :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $worksheet_text,
                                ]" />
                        </div>

                        <div class="col-sm-3">
                            <x-forms.select-option :value="$accident_type" id="accident_type" :list="$accident_status_list"
                                :label="__('accident_orders.accident_type')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$accident_worksheet_no" id="accident_worksheet_no" :list="null"
                                :label="__('accident_orders.accident_worksheet_no')" :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $accident_worksheet_text,
                                ]" />
                        </div>

                        <div class="col-sm-3">
                            <x-forms.select-option :value="$license_plate" id="license_plate" :list="null"
                                :label="__('accident_orders.license_plate')" :optionals="[
                                    'select_class' => 'js-select2-custom',
                                    'ajax' => true,
                                    'default_option_label' => $license_plate_text,
                                ]" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option :value="$status" id="status" :list="$repair_status_list"
                                :label="__('accident_orders.status')" />
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
            // 'block_option_id' => '_1',
        ])

        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                        <tr>
                            <th style="width: 25%;">@sortablelink('worksheet_no', __('accident_orders.repair_worksheet_no'))</th>
                            <th style="width: 25%;">@sortablelink('accident_worksheet', __('accident_orders.accident_worksheet_no'))</th>
                            <th style="width: 25%;">@sortablelink('accident_type', __('accident_informs.accident_type'))</th>
                            <th style="width: 25%;">@sortablelink('license_plate', __('accident_informs.license_plate'))</th>
                            <th style="width: 25%;">@sortablelink('repair_date', __('accident_orders.send_garage'))</th>
                            <th style="width: 25%;">@sortablelink('amount_completed', __('accident_orders.due_date_complete'))</th>
                            <th style="width: 25%;">@sortablelink('actual_repair_date', __('accident_orders.complete_date'))</th>
                            <th style="width: 25%;">@sortablelink('over_complete_date', __('accident_orders.over_complete_date'))</th>
                            <th style="width: 25%;">@sortablelink('status', __('accident_informs.status'))</th>
                            <th style="width: 100px;" class="sticky-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!$list->isEmpty())
                            @foreach ($list as $d)
                                <tr>
                                    <td>{{ $d->worksheet_no }}</td>
                                    <td>{{ $d->accident_worksheet }}</td>
                                    <td>{{ __('accident_informs.accident_type_index_' . $d->accident_type) }}</td>
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ $d->repair_date ? get_date_time_by_format($d->repair_date, 'd/m/Y') : '' }}</td>
                                    <td>{{ $d->amount_completed }}</td>
                                    <td>{{ $d->actual_repair_date ? get_date_time_by_format($d->actual_repair_date, 'd/m/Y') : '' }}
                                    </td>
                                    <td>{{ $d->over_complete_date }}</td>
                                    <td class="text-center">{!! badge_render(
                                        __('accident_orders.class_job_' . $d->status),
                                        __('accident_orders.status_job_' . $d->status),
                                        'w-25',
                                    ) !!} </td>
                                    <td class="sticky-col text-center">
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.accident-order-sheet-ttl-approves.show', [
                                                'accident_order_sheet_ttl_approve' => $d,
                                            ]),
                                            'view_permission' =>
                                                Actions::View . '_' . Resources::AccidentOrderSheetTTLApprove,
                                            'manage_permission' =>
                                                Actions::Manage . '_' . Resources::AccidentOrderSheetTTLApprove,
                                        ])
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

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.select2-ajax', [
    'id' => 'repair_worksheet_no',
    'url' => route('admin.util.select2-accident.worksheet-list'),
])
@include('admin.components.select2-ajax', [
    'id' => 'license_plate',
    'url' => route('admin.util.select2-accident.license-plate-list'),
])

@include('admin.components.select2-ajax', [
    'id' => 'accident_worksheet_no',
    'url' => route('admin.util.select2-accident.accident-worksheet-list'),
])
@push('scripts')
    <script>
        $(document).ready(function() {
            var $selectAll = $('#selectAll');
            var $table = $('.table');
            var $tdCheckbox = $table.find('tbody input:checkbox');
            var tdCheckboxChecked = 0;

            $selectAll.on('click', function() {
                $tdCheckbox.prop('checked', this.checked);
            });

            $tdCheckbox.on('change', function(e) {
                tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
                $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
            })
        });
    </script>
@endpush
