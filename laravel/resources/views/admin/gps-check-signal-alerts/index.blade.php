@extends('admin.layouts.layout')
@section('page_title', __('gps.alert') . __('gps.page_title_check'))

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
                            <x-forms.select-option id="status" :value="$status" :list="$status_list" :label="__('lang.status')" />
                        </div>
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.date-input id="must_check_date" :value="$must_check_date" :label="__('gps.must_check_date')"
                                :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="check_date" :value="$check_date" :label="__('gps.check_date')" :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                    </div>
                    @include('admin.components.btns.search')
                </form>
            </div>
        </div>
    </div>
    <div class="block {{ __('block.styles') }}">
    @section('block_options_list')
        @can(Actions::Manage . '_' . Resources::GPSCheckSignalAlert)
            <x-btns.add-new btn-text="{{ __('gps.add_new') }}"
                route-create="{{ route('admin.gps-check-signal-alerts.create') }}" />
        @endcan
    @endsection
    @include('admin.components.block-header', [
        'text' => __('lang.total_list'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                    <tr>
                        <th class="text-center" style="width: 70px;">
                            <div class="form-check d-inline-block">
                                <input class="form-check-input" type="checkbox" value="" id="selectAll"
                                    name="selectAll">
                                <label class="form-check-label" for="selectAll"></label>
                            </div>
                        </th>
                        <th style="width: 1px;">#</th>
                        <th style="width: 10%;">@sortablelink('chassis_no', __('gps.chassis_no'))</th>
                        <th style="width: 10%;">@sortablelink('license_plate', __('gps.license_plate'))</th>
                        <th style="width: 10%;">@sortablelink('vid', __('gps.vid'))</th>
                        <th style="width: 10%;">@sortablelink('rental_date', __('gps.rental_date'))</th>
                        <th style="width: 10%;">@sortablelink('must_check_date', __('gps.must_check_date'))</th>
                        <th style="width: 10%;">@sortablelink('check_date', __('gps.check_date'))</th>
                        <th style="width: 8%;">@sortablelink('repair_date', __('gps.repair_date'))</th>
                        <th style="width: 8%;">@sortablelink('remark', __('gps.remark'))</th>
                        <th class="text-center" style="width: 8%;">@sortablelink('status', __('lang.status'))</th>
                        <th style="width: 100px;" class="sticky-col text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($list->count()))
                        @foreach ($list as $index => $d)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input form-check-input-each" type="checkbox"
                                            value="" id="row_{{ $d->id }}" name="row_{{ $d->id }}">
                                        <label class="form-check-label" for="row_{{ $d->id }}"></label>
                                    </div>
                                </td>
                                <td>{{ $list->firstItem() + $index }}</td>
                                <td>{{ $d->chassis_no }}</td>
                                <td>{{ $d->license_plate }}</td>
                                <td>{{ $d->vid }}</td>
                                <td>{{ $d->rental_date }}</td>
                                <td>{{ $d->must_check_date ? get_thai_date_format($d->must_check_date, 'd/m/Y') : null }}
                                </td>
                                <td>{{ $d->check_date ? get_thai_date_format($d->check_date, 'd/m/Y') : null }}</td>
                                <td>{{ $d->repair_date ? get_thai_date_format($d->repair_date, 'd/m/Y') : null }}</td>
                                <td>{{ $d->remark }}</td>
                                <td class="text-center">
                                    {!! badge_render(__('gps.status_class_' . $d->status), __('gps.status_text_' . $d->status)) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    @if (strcmp($d->status, GPSStatusEnum::PENDING) == 0)
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.gps-check-signal-alerts.show', [
                                                'gps_check_signal_alert' => $d,
                                            ]),
                                            'edit_route' => route('admin.gps-check-signal-alerts.edit', [
                                                'gps_check_signal_alert' => $d,
                                            ]),
                                            'view_permission' =>
                                                Actions::View . '_' . Resources::GPSCheckSignalAlert,
                                            'manage_permission' =>
                                                Actions::Manage . '_' . Resources::GPSCheckSignalAlert,
                                        ])
                                    @else
                                        @include('admin.components.dropdown-action', [
                                            'view_route' => route('admin.gps-check-signal-alerts.show', [
                                                'gps_check_signal_alert' => $d,
                                            ]),
                                            'view_permission' =>
                                                Actions::View . '_' . Resources::GPSCheckSignalAlert,
                                        ])
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="12" class="text-center">" {{ __('lang.no_list') }} "</td>
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
