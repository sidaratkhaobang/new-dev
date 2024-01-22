@push('custom_styles')
    <style>
        .dropzone-area-custom {
            display: flex;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -ms-grid-row-align: center;
            align-items: center;
            flex-direction: column;
            border-radius: 6px;
            width: 100px;
            height: 100px;
            margin-right: 0.75rem;
            margin-left: 0rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            border: 1px solid #A3A3A3;
        }

        .dropzone-area-custom .fa-plus {
            color: #A3A3A3;
        }

        .dropzone.dropzone-custom {
            display: flex;
            /* flex-wrap: wrap; */
            background: white;
            border: 0;
            padding: 0;
        }

        /* .size{
      width: 50%;
    } */

        .dropzone.dropzone-custom .dz-preview {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .dropzone.dropzone-custom .dz-image {
            width: 100px;
            height: 100px;
            border-radius: 6px;
        }

        .dropzone.dropzone-custom .dz-preview .dz-progress {
            opacity: 0;
        }

        .dropzone.dropzone-custom {
            background-color: transparent !important;
            border: none !important;
            /* margin-top: 14px !important; */
            min-height: 100px !important;
        }

        .dropzone.dropzone-custom .dz-preview {
            max-width: 100px;
            max-height: 100px;
            border: none;
            border-radius: 6px;
            margin-left: 0;
            margin-right: 0.75rem;
        }

        .dropzone.dropzone-custom .dz-image {
            border: 1px solid #A3A3A3;
            border-radius: 6px !important;
            overflow: hidden;
            max-width: 100px;
            max-height: 100px;
        }

        .dropzone.dropzone-custom .dz-image img {
            width: 100%;
        }

        .dropzone.dropzone-custom .dz-remove {
            display: inline-block;
            position: absolute;
            z-index: 20;
            padding: 0;
            line-height: 10px;
            width: 24px;
            height: 24px;
            border-radius: 99px;
            top: -8px;
            right: -8px;
        }

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
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('อัปโหลด') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- <form id="form-upload"> --}}
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-6">
                        <x-forms.input-new-line id="additional_file_name" :value="null" :label="__('ชื่อเอกสาร')"
                            :optionals="['placeholder' => __('lang.input.placeholder')]" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <x-forms.upload-image-dropzone id="additional_files" :label="null" />
                    </div>
                </div>
            </div>
            {{-- </form> --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary btn-save-file-modal">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
