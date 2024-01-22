@extends('admin.layouts.layout')
@section('page_title', $page_title)
@section('btn-nav')
    @if (in_array($mode, [MODE_CREATE, MODE_UPDATE]) && sizeof($replacement_list) == 0)
        <button class="btn btn-primary" onclick="showReplacementSection()" id="btn_add_replacement">
            <i class="icon-add-circle me-1"></i>
            {{ __('repairs.replacement_create') }}
        </button>
    @endif
@endsection

@push('styles')
    <style>
        .profile-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            overflow: hidden;
        }

        .profile-image img {
            width: 10%;
            height: 10%;
            object-fit: cover;
        }

        .img-fluid {
            /* width: 250px; */
            height: 100px;
            object-fit: cover;
        }

        .car-border {
            border: 1px solid #CBD4E1;
            width: 400px;
            border-radius: 6px;
            color: #475569;
            padding: 2rem;
            height: fit-content;
        }

        .hide {
            display: none !important;
        }

        .show {
            display: block !important;
            opacity: 1;
            animation: fade 1s;
        }

        @keyframes fade {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .size-text {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
@endpush

@push('custom_styles')
    <style>
        .badge-custom {
            min-width: 20rem;
        }
    </style>
@endpush

@section('content')
    <form id="save-form">

        {{-- @include('admin.repairs.sections.user') --}}
        @include('admin.components.creator')

        @include('admin.repairs.sections.car-info')

        @include('admin.repairs.sections.repair-info')

        @include('admin.repairs.sections.service-center')

        {{-- @include('admin.repairs.sections.replacement') --}}
        @include('admin.repairs.sections.replacement-new')


        @include('admin.repairs.sections.check-repair')

        <div class="block {{ __('block.styles') }}">
            <div class="block-content">
                <x-forms.hidden id="id" :value="$d->id" />
                <x-forms.hidden id="open_by" :value="$d->open_by" />
                <x-forms.hidden id="redirect_route" :value="$index_uri" />
                <div class="row push">
                    <div class="text-end">
                        @if (isset($index_uri))
                            <a class="btn btn-outline-secondary btn-custom-size" href="{{ route($index_uri) }}" >{{ __('lang.back') }}</a>         
                        @endif
                        @if(!isset($view))
                            <button type="button" class="btn btn-primary btn-custom-size btn-save-form-data" >{{ __('lang.save') }}</button>   
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@include('admin.components.select2-default')
@include('admin.components.date-input-script')
@include('admin.components.sweetalert')
@include('admin.components.form-save', [
    'store_uri' => route('admin.repairs.store'),
])
@include('admin.repairs.scripts.input-script')
@include('admin.repairs.scripts.repair-script')
@include('admin.repairs.scripts.check-repair-script')
@include('admin.repairs.scripts.replacement-script')


@include('admin.components.select2-ajax', [
    'id' => 'car_id',
    'url' => route('admin.repairs.default-car-id'),
])

@include('admin.components.select2-ajax', [
    'id' => 'temp_slide_id',
    'modal' => '#replacement-modal',
    'url' => route('admin.util.select2-repair.slide-list'),
])

@include('admin.components.upload-image-scripts')
@include('admin.components.upload-image', [
    'id' => 'repair_documents',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf,.xls,.xlsx,.csv',
    'mock_files' => $repair_documents_files ?? [],
    'show_url' => true,
    'view_only' => isset($view) ? true : null,
])

@include('admin.components.upload-image', [
    'id' => 'replacement_files',
    'max_files' => 10,
    'accepted_files' => '.jpg,.jpeg,.bmp,.png,.pdf,.xls,.xlsx,.csv',
    'mock_files' => [],
    'show_url' => true,
    'view_only' => isset($view) ? true : null,
])

@push('scripts')
    <script>
        function openModalCondition() {
            $('#modal-condition').modal('show');
        }

        function openModalAccident() {
            $('#modal-accident-history').modal('show');
        }

        function openModalMaintain() {
            $('#modal-maintain-history').modal('show');
        }
        $view = '{{ isset($view) }}';
        if ($view) {
            $('.form-control').prop('disabled', true);
            $("input[type=radio]").attr('disabled', true);
        }

        function appendFormData() {
            var formData = new FormData(document.querySelector('#save-form'));
            if (window.myDropzone) {
                var dropzones = window.myDropzone;
                dropzones.forEach((dropzone) => {
                    let dropzone_id = dropzone.options.params.elm_id;
                    let files = dropzone.getQueuedFiles();
                    files.forEach((file) => {
                        formData.append(dropzone_id + '[]', file);
                    });
                    // delete data
                    let pending_delete_ids = dropzone.options.params.pending_delete_ids;
                    if (pending_delete_ids.length > 0) {
                        pending_delete_ids.forEach((id) => {
                            formData.append(dropzone_id + '__pending_delete_ids[]', id);
                        });
                    }
                });
            }
            if (window.addReplacementVue) {
                let data = window.addReplacementVue.getFiles();
                if (data && data.length > 0) {
                    data.forEach((item) => {
                        if (item.replacement_files && item.replacement_files.length > 0) {
                            item.replacement_files.forEach(function(file) {
                                if ((!file.saved) && (file.raw_file)) {
                                    formData.append('replacement_files[' + item.index + '][]', file.raw_file);
                                }
                            });
                        }
                    });
                }
            }
            return formData;
        }

        $(".btn-save-form-data").on("click", function() {
            let storeUri = "{{ route('admin.repairs.store') }}";
            var formData = appendFormData();
            saveForm(storeUri, formData);
        });
    </script>
@endpush
