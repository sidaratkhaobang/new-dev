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
                    @include('admin.gps-check-signal-jobs.sections.rental-info')
                    @include('admin.gps-check-signal-jobs.sections.car-info')
                    @include('admin.gps-check-signal-jobs.sections.gps-info')

                    <x-forms.hidden id="id" :value="$d->id" />
                    <x-forms.submit-group :optionals="[
                        'url' => 'admin.gps-check-signal-job-kratos.index',
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
    'store_uri' => route('admin.gps-check-signal-job-kratos.store'),
])


@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('#status').prop('disabled', true);
            $('#remark').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
            $('#repair_date').prop('disabled', true);
            $('#remark_repair').prop('disabled', true);
        }
    </script>
@endpush
