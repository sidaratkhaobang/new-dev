@extends('admin.layouts.layout')
@section('page_title', $page_title)
@push('custom_styles')
    <style>
        .btn-purple {
            color: #fff;
            background-color: #824DF3;
            border-color: #824DF3;
        }

        .btn-purple:hover {
            color: #fff;
            background-color: #824DF3;
            border-color: #824DF3;
        }

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
                    <h4>{{ __('gps.user_table') }}</h4>
                    <hr>
                    <div class="row push mb-4">
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.request_user') }}</p>
                            <p class="size-text" id="request_user">{{ $d->createdBy ? $d->createdBy->name : null }}</p>
                        </div>
                        <div class="col-sm-3">
                            <p class="grey-text">{{ __('gps.request_date') }}</p>
                            <p class="size-text" id="request_date">
                                {{ $d->created_at ? get_thai_date_format($d->created_at, 'd/m/Y') : null }}</p>
                        </div>
                        <div class="col-sm-3">
                            <x-forms.input-new-line id="purpose" :value="$d->purpose" :label="__('gps.purpose')" :optionals="['required' => true]" />
                        </div>
                        <div class="col-sm-3">
                            <x-forms.select-option id="type_file[]" :value="$type_file_arr" :list="$type_file_list" :label="__('gps.type_file')"
                            :optionals="['multiple' => true]" />
                        </div>
                    </div>
                    @include('admin.gps-historical-data-alerts.sections.car')
                    <x-forms.hidden id="id" :value="$d->id" />
                    <div class="row push">
                        <div class="col-sm-12 text-end">
                            <a class="btn btn-secondary"
                                href="{{ route('admin.gps-historical-data-alerts.index') }}">{{ __('lang.back') }}</a>
                            @if (!isset($view))
                                <button type="button"
                                    class="btn btn-primary btn-save-form">{{ __('lang.save_draft') }}</button>
                                <button type="button" class="btn btn-info btn-save-data"
                                    data-status="">{{ __('lang.save') }}</button>
                            @endif
                        </div>
                    </div>
            </form>
        </div>
    </div>
@endsection

@include('admin.components.sweetalert')
@include('admin.components.date-input-script')
@include('admin.components.select2-default')
@include('admin.components.form-save', [
    'store_uri' => route('admin.gps-historical-data-alerts.store'),
])
@include('admin.gps-historical-data-alerts.scripts.car-script')

@push('scripts')
    <script>
        $view = '{{ isset($view) }}';
        if ($view) {
            $('#purpose').prop('disabled', true);
            $('[name="type_file[]"]').prop('disabled', true);
        }
        $(".btn-save-data").on("click", function() {
            let storeUri = "{{ route('admin.gps-historical-data-alerts.store') }}";
            var formData = new FormData(document.querySelector('#save-form'));
            formData.append('update_status', true);
            saveForm(storeUri, formData);
        });
    </script>
@endpush
