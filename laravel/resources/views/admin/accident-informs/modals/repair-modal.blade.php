<div class="modal fade" id="modal-repair" tabindex="-1" aria-labelledby="modal-repair" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="folklift-modal-label">เพิ่มรายละเอียดการเคลม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.radio-inline id="supplier" :value="$d->supplier" :list="$spare_part_list" :label="__('accident_informs.spare_parts_supplier')"
                            :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <label></label>
                        <x-forms.checkbox-inline id="is_withdraw_true" name="is_withdraw_true" :list="[
                            [
                                'id' => 1,
                                'name' => __('accident_informs.withdraw_true'),
                                'value' => 1,
                            ],
                        ]"
                            :label="null" :value="[$d->is_withdraw_true]" />
                    </div>
                    <div class="col-sm-3" id="tls_cost_modal_label"
                        @if ($d->is_withdraw_true == 1) style="display: block;" @else  style="display: none;" @endif>
                        <x-forms.input-new-line id="tls_cost_modal" :value="$d->tls_cost" :label="__('accident_informs.tls_cost')"
                            :optionals="['required' => true, 'input_class' => 'number-format']" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="accident_claim_id" :value="$d->accident_claim_id" :list="$claim_list"
                            :label="__('accident_informs.repair_characteristics')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="wound_characteristics" :value="$d->wound_characteristics" :list="$wound_list"
                            :label="__('accident_informs.wound_characteristics')" :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.upload-image :id="'before_files'" :label="__('accident_informs.before_image') . __('accident_informs.max_size')" :optionals="['required' => true]" />
                    </div>
                    {{-- <div class="col-sm-3">
                        <x-forms.upload-image :id="'after_files'" :label="__('accident_informs.after_image') . __('accident_informs.max_size')" />
                    </div> --}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveRepair()" id="save-repair-modal">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
