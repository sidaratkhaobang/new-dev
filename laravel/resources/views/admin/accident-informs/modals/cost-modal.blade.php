<div class="modal fade" id="modal-cost" tabindex="-1" aria-labelledby="modal-cost" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cost-modal-label">เพิ่มค่าใช้จ่าย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="cost_name" :value="$d->slide_driver" :label="__('accident_informs.list')" :optionals="['required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="cost_price" :value="$d->lift_price" :label="__('accident_informs.price')" :optionals="['required' => true, 'input_class' => 'number-format col-sm-4', 'oninput' => 'true']" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="cost_remark" :value="$d->lift_to" :label="__('accident_informs.remark')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveCost()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
