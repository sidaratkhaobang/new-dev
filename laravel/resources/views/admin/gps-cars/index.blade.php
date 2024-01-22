@extends('admin.layouts.layout')
@section('page_title', __('gps.page_title_car'))
@push('custom_styles')
    <style>
        .input-group-text {
            padding: 0.7rem .75rem;
            background-color: transparent;
            border-radius: 0;
            color: #6c757d;
        }
    </style>
@endpush
@section('block_options_excel')
    @can(Actions::View . '_' . Resources::GPSCar)
        <button class="btn btn-success" onclick="openExcelModal()"><i class="fa fa-fw fa-download  me-1"></i>
            {{ __('gps.download_excel') }}</button>
    @endcan
    @include('admin.gps-cars.modals.excel')
@endsection
@section('content')
    <div class="block {{ __('block.styles') }}">
        @include('admin.components.block-header', [
            'text' => __('lang.search'),
            'block_icon_class' => 'icon-search',
            'is_toggle' => true,
            'block_option_id' => '_excel',
        ])
        <div class="block-content">
            <div class="justify-content-between">
                <form action="" method="GET" id="form-search">
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="engine_no" :value="$engine_no" :list="$engine_no_list" :label="__('gps.engine_no')" />
                        </div>
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
                    </div>
                    <div class="form-group row push mb-4">
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_install_date">{{ __('gps.install_gps_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_install_date" name="from_install_date" value="{{ $from_install_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_install_date" name="to_install_date" value="{{ $to_install_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="text-start col-form-label"
                                for="from_revoke_date">{{ __('gps.stop_signal_date') }}</label>
                            <div class="form-group">
                                <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1"
                                    data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="from_revoke_date" name="from_revoke_date" value="{{ $from_revoke_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1" data-autoclose="true"
                                        data-today-highlight="true">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text font-w600">
                                            <i class="fa fa-fw fa-arrow-right"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="js-flatpickr form-control flatpickr-input"
                                        id="to_revoke_date" name="to_revoke_date" value="{{ $to_revoke_date }}"
                                        placeholder="{{ __('lang.select_date') }}" data-week-start="1"
                                        data-autoclose="true" data-today-highlight="true">
                                </div>
                            </div>
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
                            <th class="text-center" style="width: 70px;">
                                <div class="form-check d-inline-block">
                                    <input class="form-check-input" type="checkbox" value="" id="selectAll"
                                        name="selectAll">
                                    <label class="form-check-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th style="width: 1px;">#</th>
                            <th style="width: 12%;">@sortablelink('engine_no', __('gps.engine_no'))</th>
                            <th style="width: 10%;">@sortablelink('chassis_no', __('gps.chassis_no'))</th>
                            <th style="width: 10%;">@sortablelink('license_plate', __('gps.license_plate'))</th>
                            <th style="width: 10%;">@sortablelink('status', __('lang.status'))</th>
                            <th style="width: 10%;">@sortablelink('vid', __('gps.vid'))</th>
                            <th style="width: 10%;">@sortablelink('status_gps', __('gps.status_gps'))</th>
                            <th style="width: 10%;">@sortablelink('current_location', __('gps.current_location'))</th>
                            <th style="width: 10%;">@sortablelink('install_gps_date', __('gps.install_gps_date'))</th>
                            <th style="width: 8%;">@sortablelink('stop_signal_date', __('gps.stop_signal_date'))</th>
                            <th style="width: 100px;" class="sticky-col text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (sizeof($list) > 0)
                            @foreach ($list as $index => $d)
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input form-check-input-each" type="checkbox"
                                                value="" id="row_{{ $d->id }}"
                                                name="row_{{ $d->id }}">
                                            <label class="form-check-label" for="row_{{ $d->id }}"></label>
                                        </div>
                                    </td>
                                    <td>{{ $list->firstItem() + $index }}</td>
                                    <td>{{ $d->engine_no }}</td>
                                    <td>{{ $d->chassis_no }}</td>
                                    <td>{{ $d->license_plate }}</td>
                                    <td>{{ __('cars.status_' . $d->status) }}</td>
                                    <td>{{ $d->vid }}</td>
                                    <td>{{ $d->status_gps ? __('gps.status_text_' . $d->status_gps) : null }}</td>
                                    <td>
                                        <x-forms.tooltip :title="$d->current_location" :limit="50"></x-forms.tooltip>
                                    </td>
                                    <td>{{ $d->install_date }}</td>
                                    <td>{{ $d->revoke_date }}</td>
                                    <td class="sticky-col text-center">
                                        <div class="dropdown dropleft">
                                            <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle"
                                                id="dropdown-dropleft-dark" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdown-dropleft-dark">
                                                @can(Actions::View . '_' . Resources::GPSCar)
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.gps-cars.show', ['gps_car' => $d]) }}"><i
                                                            class="fa fa-eye me-1"></i>
                                                        {{ __('config_approves.view') }}
                                                    </a>
                                                @endcan
                                                @can(Actions::Manage . '_' . Resources::GPSCar)
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.gps-cars.edit', ['gps_car' => $d]) }}"><i
                                                            class="far fa-edit me-1"></i>
                                                        {{ __('lang.edit') }}
                                                    </a>
                                                @endcan
                                                @can(Actions::Manage . '_' . Resources::GPSCar)
                                                    <a class="dropdown-item btn-check-gps" data-id="{{ $d->vid }}"
                                                        href="javascript:void(0)">
                                                        <i class="fa fa-broadcast-tower"></i> {{ __('gps.check_signal') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="11" class="text-center">" {{ __('lang.no_list') }} "</td>
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
@include('admin.gps-cars.scripts.excel-script')

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

        $(".btn-check-gps").on("click", function() {
            var vehicle = $(this).attr('data-id');
            var data = {
                vehicle: vehicle,
            };
            let storeUri = "{{ route('admin.gps-cars.veh-last-location') }}";
            showLoading();
            axios.post(storeUri, data).then(response => {
                if (response.data.success) {
                    hideLoading();
                    mySwal.fire({
                        title: "{{ __('lang.store_success_title') }}",
                        text: 'เช็กสถานะสัญญาณGPS เสร็จแล้ว',
                        icon: 'success',
                        confirmButtonText: "{{ __('lang.ok') }}"
                    }).then(value => {
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    hideLoading();
                    mySwal.fire({
                        title: "{{ __('lang.store_error_title') }}",
                        text: response.data.message,
                        icon: 'error',
                        confirmButtonText: "{{ __('lang.ok') }}",
                    }).then(value => {
                        if (value) {
                            //
                        }
                    });
                }
            }).catch(error => {
                //
            });
        });
    </script>
@endpush
