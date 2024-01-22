<div class="modal fade" id="modal-status" tabindex="-1" aria-labelledby="modal-status" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cstatus-modal-label">{{ __('litigations.add_status') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push">
                    <div class="col-sm-3">
                        <x-forms.date-input id="temp_date" :value="null" :label="__('litigations.save_date')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="temp_status" :value="null" :list="null" :label="__('lang.status')"/>
                    </div>
                    <div class="col-sm-3">
                        <x-forms.date-input id="temp_appointment_date" :value="null" :label="__('litigations.appointment_date')"/>
                    </div>
                </div>
                <div class="row push">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="temp_description" :value="null" :label="__('litigations.description')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search" data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="addStatus()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
