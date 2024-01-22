@push('custom_styles')
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/admin/dropzone-image.css') }}">
    <style>
        .dropzone-sections {
            min-height: 200px !important;
            background-color: #f8f9fc !important;
            border: 0.125rem dashed #d1d8ea !important;
            border-radius: 0.3rem !important;
        }

        .dropzone-sections .dz-preview.dz-image-preview {
            background: transparent !important;
        }

        .dropzone-sections .dz-preview .dz-image img {
            width: 100%;
        }

        .dropzone-sections .dz-progress {
            display: none !important;
        }
    </style>
@endpush
<div class="modal fade" id="modal-upload-file" tabindex="-1" aria-labelledby="modal-upload-file" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('อัปโหลด') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-upload">
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-6">
                            <x-forms.input-new-line id="custom_file_name" :value="null" :label="__('ชื่อเอกสาร')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-forms.upload-image-dropzone id="zone-upload-file" :label="null" />
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary btn-save-form-modal">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
