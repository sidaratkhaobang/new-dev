@extends('admin.layouts.layout')
@section('page_title', __('inspection_cars.page_title'))
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
                    <div class="col-sm-3">
                        <x-forms.select-option id="worksheet_no" :value="$worksheet_no" :list="$worksheet_list"
                                               :label="__('inspection_cars.worksheet_no')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="inspection_form" :value="$inspection_form"
                                               :list="$inspection_form_list"
                                               :label="__('car_inspection_types.inspect_type')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="inspection_must_date" :value="$inspection_must_date"
                                            :label="__('inspection_cars.must_date')"/>
                    </div>
                </div>
                <div class="form-group row push">
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_park_zone" :value="$car_park_zone"
                                               :list="$car_park_zone_list"
                                               :label="__('inspection_cars.car_park_zone')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="car_id" :value="$car_id" :list="$license_plate_list"
                                               :label="__('inspection_cars.license_plate_chassis_engine')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="status" :value="$status" :list="$status_list"
                                               :label="__('inspection_cars.status')"/>
                    </div>
                </div>
                @include('admin.components.btns.search')
            </form>
        </div>
    </div>

    <div class="block {{ __('block.styles') }}">
        @section('block_options_list')
            <div class="block-options">
                @can(Actions::Manage . '_' . Resources::CarInspection)
                    <div class="block-options-item">
                        <x-btns.add-new btn-text="{{ __('inspection_cars.add_new') }}" route-create="{{ route('admin.inspection-jobs.create') }}"/>
                    </div>
                @endcan
            </div>
        @endsection
        @include('admin.components.block-header', [
          'text' => __('lang.total_list'),
          'block_option_id' => '_list'
      ])
        <div class="block-content">
            <div class="table-wrap db-scroll">
                <table class="table table-striped table-vcenter">
                    <thead class="bg-body-dark">
                    <tr>

                        <th style="width: 1px;">#</th>
                        <th style="width: 10%;">@sortablelink('worksheet_no', __('inspection_cars.worksheet_no'))</th>
                        <th style="width: 12%;">@sortablelink('rental_type', __('inspection_cars.car_type'))</th>
                        {{-- <th style="width: 10%;">@sortablelink('chassis_no', __('inspection_cars.rental_type'))</th> --}}
                        <th style="width: 10%;">@sortablelink('inspection_flow_name', __('inspection_cars.inspection_type'))</th>
                        <th style="width: 10%;">@sortablelink('transfer_reason', __('inspection_cars.inspection_name'))</th>
                        <th style="width: 10%;">@sortablelink('license_plate', __('inspection_cars.license_plate'))</th>
                        <th style="width: 10%;">@sortablelink('engine_no', __('inspection_cars.engine_no'))</th>
                        <th style="width: 10%;">@sortablelink('zone_code', __('inspection_cars.car_park_zone'))</th>
                        <th style="width: 10%;">@sortablelink('inspection_must_date', __('inspection_cars.must_date'))</th>
                        <th style="width: 10%;">@sortablelink('inspection_date', __('inspection_cars.inspection_date'))</th>
                        <th style="width: 10%;">@sortablelink('user_department_name', __('inspection_cars.current_inspector'))</th>
                        <th class="text-center" style="width: 8%;">@sortablelink('status', __('inspection_cars.status'))</th>
                        <th style="width: 100px;" class="sticky-col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($lists->count()))
                        @foreach ($lists as $index => $d)
                            <tr>
                                <td>{{ $lists->firstItem() + $index }}</td>
                                <td>{{ $d->worksheet_no }}</td>
                                <td>{{  __('inspection_cars.rental_type_' . $d->rental_type) }}</td>
                                {{-- <td>{{  __('inspection_cars.transfer_' . $d->rental_type), }}</td> --}}
                                <td>{{ $d->inspection_flow_name }}</td>
                                <td>{{ $d->transfer_reason ? __('car_inspection_types.status_condition_name_'. $d->transfer_reason) : '' }}</td>
                                <td>{{ $d->license_plate }}</td>
                                <td>{{ $d->engine_no }}</td>
                                <td>{{ $d->zone_code }}{{ $d->car_park_number }}</td>
                                <td>{{$d->inspection_must_date ? get_thai_date_format($d->inspection_must_date, 'd/m/Y') : '' }}</td>
                                <td>{{$d->inspection_date ? get_thai_date_format($d->inspection_date, 'd/m/Y') : '' }}</td>
                                <td>{{ $d->user_department_name ? $d->user_department_name : ''}}</td>
                                <td class="text-center">
                                    {!! badge_render(
                                        __('inspection_cars.class_' . $d->inspection_status),
                                        __('inspection_cars.status_' . $d->inspection_status),
                                    ) !!}
                                </td>
                                <td class="sticky-col text-center">
                                    <div class="dropdown dropleft">
                                        <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                aria-haspopup="true"
                                                aria-expanded="false">
                                            <i class="fa fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                            @can(Actions::View . '_' . Resources::CarInspection)
                                                <a class="dropdown-item"
                                                   href="{{ route('admin.inspection-job-steps.show', ['inspection_job_step' => $d->id]) }}"><i
                                                        class="fa fa-eye me-1"></i>
                                                    {{ __('car_inspections.view') }}
                                                </a>
                                            @endcan
                                            @if($d->inspection_status != InspectionStatusEnum::CANCEL)
                                                @can(Actions::Manage . '_' . Resources::CarInspection)
                                                    <a class="dropdown-item"
                                                       href="{{ route('admin.inspection-job-steps.edit', ['inspection_job_step' => $d->id]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('car_inspections.edit') }}
                                                    </a>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>
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
            {!! $lists->appends(\Request::except('page'))->render() !!}
        </div>
    </div>
    @include('admin.prepare-new-cars.modals.edit-purchase')

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            var $selectAll = $('#selectAll');
            var $table = $('.table');
            var $tdCheckbox = $table.find('tbody input:checkbox');
            var tdCheckboxChecked = 0;

            $selectAll.on('click', function () {
                $tdCheckbox.prop('checked', this.checked);
            });

            $tdCheckbox.on('change', function (e) {
                tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length;
                $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
            })
        });


    </script>
@endpush

@include('admin.components.select2-default')
@include('admin.components.sweetalert')
@include('admin.components.list-delete')
@include('admin.components.date-input-script')

{{-- @include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.util.select2.car-license-plate'),
]) --}}
