<div class="modal fade" id="replacement-modal" aria-labelledby="replacement-modal" tabindex="-1" 
    aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icon-menu-car me-1"></i> <span id="replacement-modal-label">{{ __('repairs.replacement_modal_header') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="temp_job_type" :value="null" :list="$replacement_type_list" :label="__('repairs.type_job')"
                            :optionals="['required' => true]" />

                        {{-- <x-forms.select-option id="_check_field" :value="null" :list="$check_list" :label="__('repairs.check')"
                            :optionals="['required' => true]" /> --}}
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="temp_send_pickup_date" :value="null" :label="__('repairs.send_and_pickup_date')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="temp_is_at_tls" :value="null" :list="$yes_no_list"
                            :label="__('repairs.is_at_tls')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="temp_slide_id" :value="null" :list="null" :label="__('repairs.slide_worksheet')" />
                            
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="temp_send_pickup_place" :value="null" :label="__('repairs.send_pickup_place')" 
                        :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.upload-image :id="'replacement_files'" :label="__('repairs.document')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.back') }}</button>
                    <button type="button" class="btn btn-primary" onclick="saveReplacementCar()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
