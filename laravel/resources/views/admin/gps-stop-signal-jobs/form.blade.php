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
                            <p class="grey-text">{{ __('gps.inform_date') }}</p>
                            <p class="size-text" id="inform_date">
                                {{ $d->inform_date ? get_thai_date_format($d->inform_date, 'd/m/Y') : null }}</p>
                        </div>
                    </div>
                    @if (in_array($d->remove_status, [GPSStopStatusEnum::REMOVE_GPS, GPSStopStatusEnum::NOT_INSTALL]))
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
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="stop_status" :value="$d->stop_status" :list="$stop_status_list" :label="__('gps.stop_status')"
                                :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="stop_date" :value="$d->stop_date" :label="__('gps.stop_date')" :optionals="['placeholder' => __('lang.select_date'), 'required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="stop_remark" :value="$d->stop_remark" :label="__('gps.remark')" />
                        </div>
                    </div>
                    <x-forms.hidden id="id" :value="$d->id" />
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.gps-stop-signal-jobs.index',
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
    'store_uri' => route('admin.gps-stop-signal-jobs.store'),
])


@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('#stop_status').prop('disabled', true);
            $('#stop_date').prop('disabled', true);
            $('#stop_remark').prop('disabled', true);
        }
    </script>
@endpush
