@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('custom_styles')
    <style>
        .grey-text {
            color: #858585;
        }

        .size-text {
            font-size: 16px;
            font-weight: bold;
        }

        .block-car {
            border: solid 1px rgba(163, 163, 163, 0.25);
        }

        .block-car-card {
            height: 200px !important;
        }
    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="justify-content-between mb-4">
                    <h4>{{ __('gps.car_data') }}</h4>
                    <hr>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.engine_no') }}</p>
                            <p class="size-text">{{ $d->engine_no ? $d->engine_no : '-' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.chassis_no') }}</p>
                            <p class="size-text">{{ $d->chassis_no ? $d->chassis_no : '-' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.license_plate') }}</p>
                            <p class="size-text">{{ $d->license_plate ? $d->license_plate : '-' }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.car_class') }}</p>
                            <p class="size-text">{{ $d->carClass ? $d->carClass->full_name : '-' }}</p>
                        </div>
                    </div>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.car_color') }}</p>
                            <p class="size-text">{{ $d->carColor ? $d->carColor->name : '-' }}</p>
                        </div>
                        <div class="col-sm-3">
                            @if (isset($view))
                                <p class="grey-text">{{ __('gps.fleet') }}</p>
                                <p class="size-text">{{ $d->fleet ? $d->fleet : '-' }}</p>
                            @else
                                <x-forms.input-new-line id="fleet" :value="$d->fleet" :label="__('gps.fleet')" />
                            @endif
                        </div>
                        <div class="col-sm-3">
                            @if (isset($view))
                                <p class="grey-text">{{ __('gps.install_gps_date') }}</p>
                                <p class="size-text"></p>
                            @else
                                <x-forms.date-input id="install_gps_date" :value="$d->install_date" :label="__('gps.install_gps_date')"
                                    :optionals="['placeholder' => __('lang.select_date')]" />
                            @endif
                        </div>
                        <div class="col-sm-3">
                            @if (isset($view))
                                <p class="grey-text">{{ __('gps.stop_signal_date') }}</p>
                                <p class="size-text"></p>
                            @else
                                <x-forms.date-input id="stop_signal_date" :value="$d->revoke_date" :label="__('gps.stop_signal_date')"
                                    :optionals="['placeholder' => __('lang.select_date')]" />
                            @endif
                        </div>
                    </div>
                    <h4>{{ __('gps.gps_data') }}</h4>
                    @if (is_null($d->have_gps) && is_null($d->have_dvr) && is_null($d->have_censor_oil) && is_null($d->have_censor_speed))
                        <div class="row push mb-2 mt-2">
                            <div class="col-sm-12">
                                <div class="block block-rounded block-link-shadow block-car" href="javascript:void(0)">
                                    <div class="block-content block-content-full">
                                        <div class="form-group row">
                                            <div class="col-sm-12">
                                                <p class="size-text">ไม่มี GPS
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        @if ($d->have_gps)
                            <div class="block {{ __('block.styles') }}">
                                <div class="block-content">
                                    <div class="form-group row">
                                        @if (!isset($view))
                                            <div class="col-sm-3">
                                                <div style="margin-top: 25px;">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input form-check-input-each"
                                                            type="checkbox" value="1" id="have_gps" name="have_gps"
                                                            @if (isset($d->have_gps) && $d->have_gps == BOOL_TRUE) checked @endif>
                                                        <label class="form-check-label"
                                                            for="have_gps">{{ __('gps.gps') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-sm-3">
                                            @if (isset($view))
                                                <p class="grey-text">{{ __('gps.vid') }}</p>
                                                <p class="size-text">{{ $d->vid ? $d->vid : '-' }}</p>
                                            @else
                                                <x-forms.input-new-line id="vid" :value="$d->vid"
                                                    :label="__('gps.vid')" />
                                            @endif
                                        </div>
                                        <div class="col-sm-3">
                                            @if (isset($view))
                                                <p class="grey-text">{{ __('gps.serial_number') }}</p>
                                                <p class="size-text">
                                                    {{ $d->serial_number ? $d->serial_number : '-' }}</p>
                                            @else
                                                <x-forms.input-new-line id="serial_number" :value="$d->serial_number"
                                                    :label="__('gps.serial_number')" />
                                            @endif
                                        </div>
                                        <div class="col-sm-3">
                                            @if (isset($view))
                                                <p class="grey-text">{{ __('gps.sim') }}</p>
                                                <p class="size-text">
                                                    {{ $d->sim ? __('gps.sim_' . $d->sim) : '-' }}</p>
                                            @else
                                                <x-forms.select-option id="sim" :value="$d->sim" :list="$sim_list"
                                                    :label="__('gps.sim')" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($d->have_dvr)
                            <div class="block {{ __('block.styles') }}">
                                <div class="block-content">
                                    <div class="form-group row">
                                        @if (!isset($view))
                                            <div class="col-sm-3">
                                                <div style="margin-top: 25px;">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input form-check-input-each"
                                                            type="checkbox" value="1" id="have_dvr" name="have_dvr"
                                                            @if (isset($d->have_dvr) && $d->have_dvr == BOOL_TRUE) checked @endif>
                                                        <label class="form-check-label"
                                                            for="have_dvr">{{ __('gps.dvr') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-sm-3">
                                            @if (isset($view))
                                                <p class="grey-text">{{ __('gps.dvr') }}</p>
                                                <p class="size-text">{{ $d->dvr ? $d->dvr : '-' }}</p>
                                            @else
                                                <x-forms.input-new-line id="dvr" :value="$d->dvr"
                                                    :label="__('gps.dvr_online')" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($d->have_censor_oil)
                            <div class="block {{ __('block.styles') }}">
                                <div class="block-content">
                                    <div class="form-group row">
                                        @if (!isset($view))
                                            <div class="col-sm-3">
                                                <div style="margin-top: 25px;">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input form-check-input-each"
                                                            type="checkbox" value="1" id="have_censor_oil"
                                                            name="have_censor_oil"
                                                            @if (isset($d->have_censor_oil) && $d->have_censor_oil == BOOL_TRUE) checked @endif>
                                                        <label class="form-check-label"
                                                            for="have_censor_oil">{{ __('gps.censor_oil') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-sm-3">
                                            @if (isset($view))
                                                <p class="grey-text">{{ __('gps.censor_oil') }}</p>
                                                <p class="size-text">
                                                    {{ $d->censor_oil ? $d->censor_oil : '-' }}
                                                </p>
                                            @else
                                                <x-forms.input-new-line id="censor_oil" :value="$d->censor_oil"
                                                    :label="__('gps.censor_oil')" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($d->have_censor_speed)
                            <div class="block {{ __('block.styles') }}">
                                <div class="block-content">
                                    <div class="form-group row">
                                        @if (!isset($view))
                                            <div class="col-sm-3">
                                                <div style="margin-top: 25px;">
                                                    <div class="form-check d-inline-block">
                                                        <input class="form-check-input form-check-input-each"
                                                            type="checkbox" value="1" id="have_censor_speed"
                                                            name="have_censor_speed"
                                                            @if (isset($d->have_censor_speed) && $d->have_censor_speed == BOOL_TRUE) checked @endif>
                                                        <label class="form-check-label"
                                                            for="have_censor_speed">{{ __('gps.censor_speed') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-sm-3">
                                            @if (isset($view))
                                                <p class="grey-text">{{ __('gps.speed') }}</p>
                                                <p class="size-text">{{ $d->speed ? $d->speed : '-' }}
                                                </p>
                                            @else
                                                <x-forms.input-new-line id="speed" :value="$d->speed"
                                                    :label="__('gps.censor_speed')" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="have_gps" :value="$d->have_gps" />

                <x-forms.submit-group :optionals="[
                    'url' => 'admin.gps-cars.index',
                    'view' => empty($view) ? null : $view,
                ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.gps-cars.store'),
])
@include('admin.components.date-input-script')
@include('admin.components.select2-default')

@push('scripts')
    <script>
        $('#install_gps_date').prop('disabled', true);
        $('#stop_signal_date').prop('disabled', true);
        $('#vid').prop('disabled', true);
        $('#serial_number').prop('disabled', true);
        $('#have_gps').prop('disabled', true);
        $('#have_dvr').prop('disabled', true);
        $('#have_censor_oil').prop('disabled', true);
        $('#have_censor_speed').prop('disabled', true);
    </script>
@endpush
