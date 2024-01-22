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
                    <h4>{{ __('gps.rental_data') }}</h4>
                    <hr>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="job_type" :value="$d->job_type" :list="$job_list" :label="__('gps.job_type')"
                                :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="job_id" :value="$d->job_id" :list="null" :label="__('gps.job_id')"
                                :optionals="[
                                    'ajax' => true,
                                    'default_option_label' => $d->worksheet_no,
                                    'required' => true,
                                ]" />
                        </div>
                        <div class="col-sm-3">
                            @if (isset($view))
                                <x-forms.view-image :id="'doc_additional'" :label="__('gps.doc_additional')" :list="$doc_additional_files" />
                            @else
                                <x-forms.upload-image :id="'doc_additional'" :label="__('gps.doc_additional')" />
                            @endif
                        </div>
                    </div>
                    {{-- default short term rental --}}
                    @include('admin.gps-check-signal-alerts.sections.short-info')
                    {{-- default long term rental --}}
                    @include('admin.gps-check-signal-alerts.sections.long-info')
                    {{-- default REPLACEMENT --}}
                    @include('admin.gps-check-signal-alerts.sections.replacement-info')
                    <div id="default-car" style="display: none">
                        <hr>
                        <h4 class="mt-4">{{ __('gps.car_table') }}</h4>
                        <div class="row push mb-4">
                            <div class="col-sm-3">
                                <x-forms.select-option id="car_id" :value="$d->car_id" :list="null"
                                    :label="__('gps.license_plate')" :optionals="[
                                        'ajax' => true,
                                        'default_option_label' => $d->license_plate,
                                    ]" />
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.engine_no') }}</p>
                                <p class="size-text" id="engine_no">{{ $d->engine_no }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.chassis_no') }}</p>
                                <p class="size-text" id="chassis_no">{{ $d->chassis_no }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.car_class') }}</p>
                                <p class="size-text" id="car_class">{{ $d->car_class }}</p>
                            </div>
                        </div>
                        <div class="row push mb-4">
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.car_color') }}</p>
                                <p class="size-text" id="car_color">{{ $d->car_color }}</p>
                            </div>
                            <div class="col-sm-3">
                                <p class="grey-text">{{ __('gps.fleet') }}</p>
                                <p class="size-text" id="fleet">{{ $d->fleet }}</p>
                            </div>
                        </div>
                        <hr>
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
                                <x-forms.date-input id="must_check_date" :value="$d->must_check_date" :label="__('gps.must_check_date')"
                                    :optionals="['placeholder' => __('lang.select_date')]" />
                            </div>
                        </div>
                    </div>
                </div>
                <x-forms.hidden id="id" :value="$d->id" />

                <x-forms.submit-group :optionals="[
                    'url' => 'admin.gps-check-signal-alerts.index',
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
    'store_uri' => route('admin.gps-check-signal-alerts.store'),
])
@include('admin.gps-check-signal-alerts.scripts.job-script')
@include('admin.components.select2-ajax', [
    'id' => 'job_id',
    'url' => route('admin.gps-check-signals.default-job-id'),
    'parent_id' => 'job_type',
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'doc_additional',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.xls,.xlsx,.csv,.pdf',
    'mock_files' => $doc_additional_files ?? [],
])
