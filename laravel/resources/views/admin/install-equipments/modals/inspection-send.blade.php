<div class="modal fade" id="send-inspection-modal" aria-labelledby="send-inspection-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="send-inspection-modal-label">{{ __('install_equipments.send_inspection') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="inspection_type_field" :value="__('install_equipments.default_type')" 
                            :label="__('install_equipments.inspection_type')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.date-input id="inspection_date_field" :value="null" :label="__('install_equipments.inspection_date')" 
                        :optionals="['required' => true, 'placeholder' => __('lang.select_date')]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="car_code_field" :value="null" 
                        :label="__('install_equipments.car_code')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="license_plate_field" :value="null" 
                            :label="__('install_equipments.license_plate')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="engine_no_field" :value="null" 
                        :label="__('install_equipments.engine_no')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="chassis_no_field" :value="null" 
                        :label="__('install_equipments.chasis_no')" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.input-new-line id="install_worksheet_field" :value="null" 
                        :label="__('install_equipments.worksheet_list')" />
                    </div>                
                </div>
                <x-forms.hidden id="group_id_field" :value="null" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="createInspection()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
