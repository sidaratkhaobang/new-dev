@extends('admin.layouts.layout')
@section('page_title', $page_title)

@section('content')
    <div class="block {{ __('block.styles') }}">
        <div class="block-content">
            <form id="save-form">
                <div class="justify-content-between mb-4">
                    <h4>{{ __('gps.worksheet_table') }}</h4>
                    <hr>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="worksheet_no" :value="$d->worksheet_no" :label="__('gps.worksheet_no_gps')" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="job_type" :value="$d->job_type" :list="$job_list" :label="__('gps.stop_job_type')"
                                :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            @if (isset($view))
                                <x-forms.view-image :id="'doc_additional'" :label="__('gps.doc_additional')" :list="$doc_additional_files" />
                            @else
                                <x-forms.upload-image :id="'doc_additional'" :label="__('gps.doc_additional')" />
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <x-forms.date-input id="inform_date" :value="$d->inform_date" :label="__('gps.inform_date')" :optionals="['placeholder' => __('lang.select_date')]" />
                        </div>
                    </div>
                    @include('admin.gps-remove-stop-signal-alerts.sections.car')
                    <x-forms.hidden id="id" :value="$d->id" />
                    @if (isset($edit))
                        <x-forms.hidden id="job_type" :value="$d->job_type" />
                    @endif
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.gps-remove-stop-signal-alerts.index',
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
    'store_uri' => route('admin.gps-remove-stop-signal-alerts.store'),
])
@include('admin.gps-remove-stop-signal-alerts.scripts.car-script')

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'doc_additional',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.xls,.xlsx,.csv,.pdf',
    'mock_files' => $doc_additional_files ?? [],
])

@include('admin.components.select2-ajax', [
    'id' => 'chassis_no_field',
    'modal' => '#modal-gps-car',
    'url' => route('admin.gps-remove-stop-signal-alerts.car-chassis-no'),
])

@include('admin.components.select2-ajax', [
    'id' => 'engine_no_field',
    'modal' => '#modal-gps-car',
    'url' => route('admin.gps-remove-stop-signal-alerts.car-engine-no'),
])

@include('admin.components.select2-ajax', [
    'id' => 'license_plate_field',
    'modal' => '#modal-gps-car',
    'url' => route('admin.gps-remove-stop-signal-alerts.car-license-plate'),
])

@include('admin.components.select2-ajax', [
    'id' => 'vid_field',
    'modal' => '#modal-gps-car',
    'url' => route('admin.gps-remove-stop-signal-alerts.car-vid'),
])

@push('scripts')
    <script>
        $('#worksheet_no').prop('disabled', true);
        $edit = '{{ isset($edit) }}';
        $view = '{{ isset($view) }}';
        if ($edit) {
            $('#job_type').prop('disabled', true);
        }
        if ($view) {
            $('#job_type').prop('disabled', true);
            $('#inform_date').prop('disabled', true);
        }
    </script>
@endpush
