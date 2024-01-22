<div class="modal fade" id="modal-notice" tabindex="-1" aria-labelledby="modal-notice" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notice-modal-label">{{ __('compensations.add_notice') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.date-input id="temp_delivery_date" :value="null" :label="__('compensations.notice_delivery_date')"
                        :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="temp_rp_no" :value="null"  :label="__('compensations.rp_no')"
                        :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="temp_receive_date" :value="null" :label="__('compensations.notice_receive_date')"
                        :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="temp_recipient_name" :value="null" :label="__('compensations.recipient_name')" 
                        :optionals="['required' => true]" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="addNotice()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
