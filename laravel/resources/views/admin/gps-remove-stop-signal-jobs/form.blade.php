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
    </style>
@endpush
@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="justify-content-between mb-4">
                    @include('admin.gps-check-signal-jobs.sections.car-info')

                    <h4 class="mt-4">{{ __('gps.gps_data') }}</h4>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.vid') }}</p>
                            <p class="size-text" id="vid">{{ $d->vid }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.sim') }}</p>
                            <p class="size-text" id="sim">{{ $d->sim }}</p>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="inform_date" :value="$d->inform_date" :label="__('gps.inform_date')" :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                    </div>
                    @if (in_array($d->remove_status, [GPSStopStatusEnum::ALERT_REMOVE_GPS, GPSStopStatusEnum::REMOVE_GPS, GPSStopStatusEnum::NOT_INSTALL]))
                        <div class="row push mb-4">
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.remove_status') }}</p>
                                <p class="size-text" id="remove_status">
                                    {{ __('gps.remove_status_text_' . $d->remove_status) }}
                                </p>
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.remove_date') }}</p>
                                <p class="size-text" id="remove_date">{{ $d->remove_date }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.remove_remark') }}</p>
                                <p class="size-text" id="remove_remark">{{ $d->remove_remark }}</p>
                            </div>
                        </div>
                    @endif
                    @if (in_array($d->stop_status, [GPSStopStatusEnum::ALERT_STOP_SIGNAL, GPSStopStatusEnum::STOP_SIGNAL]))
                        <div class="row push mb-4">
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.stop_status') }}</p>
                                <p class="size-text" id="stop_status">{{ __('gps.stop_status_text_' . $d->stop_status) }}
                                </p>
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.stop_date') }}</p>
                                <p class="size-text" id="stop_date">{{ $d->stop_date }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.stop_remark') }}</p>
                                <p class="size-text" id="stop_remark">{{ $d->stop_remark }}</p>
                            </div>
                        </div>
                    @endif
                    <x-forms.hidden id="id" :value="$d->id" />
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.gps-remove-stop-signal-jobs.index',
                        'view' => empty($view) ? null : $view,
                    ]" />
            </form>
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.form-save', [
    'store_uri' => route('admin.gps-remove-stop-signal-jobs.store'),
])


@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('#inform_date').prop('disabled', true);
        }
    </script>
@endpush
