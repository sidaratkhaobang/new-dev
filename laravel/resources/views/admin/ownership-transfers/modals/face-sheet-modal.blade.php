<div class="modal fade" id="face-sheet-modal" data-target="face-sheet" aria-labelledby="face-sheet-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="face-sheet-modal-label"> <i class="icon-printer"></i>พิมพ์ใบปะหน้า
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- <h4 class="fw-light text-gray-darker">{{ __('install_equipments.add_accessory') }}</h4>
                <hr> --}}
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="facesheet_status" :value="null" :list="[]"
                            :label="__('registers.facesheet_type')" :optionals="[
                                'select_class' => 'js-select2-custom',
                                'ajax' => true,
                                'default_option_label' => null,
                                
                            ]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="topic_face_sheet" :value="null" :list="null"
                            :label="__('registers.topic_face_sheet')" />
                    </div>
                </div>
             
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-custom-size"
                onclick="BackToModalFaceSheetSelectCar()">{{ __('lang.back') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="exportExcelFaceSheet()"><i class="icon-printer"></i>
                    {{ __('registers.select_car_face_sheet') }}</button>
            </div>
        </div>
    </div>
</div>
