<div class="modal fade" id="modal-driver" tabindex="-1" aria-labelledby="modal-driver" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driver-modal-label">เพิ่มข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="fw-light text-gray-darker">{{ __('customers.driver_table') }}</h4>
                <hr>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="full_name_field" :value="null" :label="__('customers.full_name')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="tel_driver_field" :value="null" :label="__('customers.tel_driver')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="citizen_field" :value="null" :label="__('customers.citizen')" :optionals="['maxlength' => 20]"/>
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="email_driver_field" :value="null" :label="__('customers.email')" />
                    </div>
                </div>

                <h4 class="fw-light text-gray-darker">{{ __('customers.upload_table') }}</h4>
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
