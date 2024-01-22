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
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.select-option id="remove_status" :value="$d->remove_status" :list="$remove_status_list" :label="__('gps.remove_status')"
                                :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="remove_date" :value="$d->remove_date" :label="__('gps.remove_date')" :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="remove_remark" :value="$d->remove_remark" :label="__('gps.remark')" />
                        </div>
                    </div>
                    <x-forms.hidden id="id" :value="$d->id" />
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.gps-remove-signal-jobs.index',
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
    'store_uri' => route('admin.gps-remove-signal-jobs.store'),
])


@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('#remove_status').prop('disabled', true);
            $('#remove_date').prop('disabled', true);
            $('#remove_remark').prop('disabled', true);
        }

        document.getElementById("remove_status").onchange = function() {
            var enum_not_install = '{{ \App\Enums\GPSStopStatusEnum::NOT_INSTALL }}';
            var remove_status = document.getElementById("remove_status").value;
            if (remove_status == enum_not_install) {
                $('#remove_date').val('');
            }
        };
    </script>
@endpush
