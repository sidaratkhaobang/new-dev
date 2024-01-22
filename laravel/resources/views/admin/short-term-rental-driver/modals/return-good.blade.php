<div class="modal fade" id="modal-return-good" tabindex="-1" aria-labelledby="modal-return-good" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="return-good-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.return_good_detail') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="row push mb-4">
                        <div class="col-sm-4">
                            <x-forms.select-option :value="null" id="return_good_brand_id" :list="null" :label="__('car_classes.car_brand')"
                                :optionals="['ajax' => true]" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.select-option :value="null" id="return_good_class_id" :list="null" :label="__('car_classes.class')"
                                :optionals="['ajax' => true]" />
                        </div>
                        <div class="col-sm-4">
                            <x-forms.select-option id="return_good_color_id" :value="null" :list="null" :label="__('purchase_requisitions.car_color')"
                                :optionals="['ajax' => true]" />
                        </div>
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="return_good_license_plate" :value="null" :label="__('cars.license_plate_current')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="return_good_chassis_no" :value="null" :label="__('cars.chassis_no')" :optionals="['required' => true]" />
                    </div>
                </div>
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.upload_file') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-8">
                        <x-forms.upload-image :id="'return_good_files'" :label="__('short_term_rentals.good_image')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveReturnGood()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
