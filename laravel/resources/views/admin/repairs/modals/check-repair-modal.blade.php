<div class="modal fade" id="modal-check-repair" tabindex="-1" aria-labelledby="modal-check-repair" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="check-repair-modal-label">เพิ่มข้อมูลการซ่อม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-12">
                        <x-forms.text-area-new-line id="description_field" :value="null" :label="__('repairs.description')"
                            :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="check_field" :value="null" :list="$check_list" :label="__('repairs.check')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="qc_field" :value="null" :label="__('repairs.qc')" />
                    </div>
                </div>
            </div>
            <input type="hidden" id="line_id" v-bind:value="line_id">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveData()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
