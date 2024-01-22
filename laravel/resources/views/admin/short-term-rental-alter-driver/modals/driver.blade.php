<div class="modal fade" id="modal-driver" tabindex="-1" aria-labelledby="modal-driver" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driver-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.driver_detail') }}</h4>
                <hr>
                <div class="form-group row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="driver_name" :value="null" :label="__('short_term_rentals.customer_name')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="driver_tel" :value="null" :label="__('short_term_rentals.tel')" />
                    </div>
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="driver_email" :value="null" :label="__('short_term_rentals.email')" />
                    </div>
                </div>
                <div class="form-group row push mb-4">
                    <div class="col-sm-4">
                        <x-forms.input-new-line id="driver_id_card_no" :value="null" :label="__('short_term_rentals.id_card_no')"
                            :optionals="['required' => true]" />
                    </div>
                </div>
                <h4 class="fw-light text-gray-darker">{{ __('short_term_rentals.upload_file') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.upload-image :id="'driving_license_file'" :label="__('customers.driving_license_file')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.upload-image :id="'citizen_file'" :label="__('customers.citizen_file')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveDriver()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
