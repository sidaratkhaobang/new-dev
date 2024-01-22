<div class="modal fade" id="send-all-inspection-modal" aria-labelledby="send-all-inspection-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="send-all-inspection-modal-label">{{ __('install_equipments.send_inspection') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-6">
                        <x-forms.input-new-line id="_inspection_type_field" :value="__('install_equipments.default_type')" 
                            :label="__('install_equipments.inspection_type')" />
                    </div>
                    <div class="col-sm-6">
                        <x-forms.date-input id="inspection_all_date__field" :value="null" :label="__('install_equipments.inspection_date')" 
                        :optionals="['required' => true, 'placeholder' => __('lang.select_date')]" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary"
                    onclick="createAllInspection()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
