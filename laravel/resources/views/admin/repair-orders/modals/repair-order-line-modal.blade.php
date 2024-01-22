<div class="modal fade" id="modal-repair-order-line" tabindex="-1" aria-labelledby="modal-repair-order-line"
    aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout" style="width:1250px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repair-order-line-modal-label">เพิ่มรายการตรวจเช็ก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.select-option id="code_name_field" :value="null" :list="null"
                            :label="__('repair_orders.code_name')" :optionals="['select_class' => 'js-select2-custom', 'ajax' => true, 'required' => true]" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="amount_field" :value="null" :label="__('repair_orders.amount')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.select-option id="check_field" :value="null" :list="$check_list" :label="__('repair_orders.check')"
                            :optionals="['required' => true]" />
                    </div>
                </div>
                <div class="row push mb-4">
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="price_field" :value="null" :label="__('repair_orders.prices')" />
                    </div>
                    <div class="col-sm-3">
                        <x-forms.input-new-line id="total_field" :value="null" :label="__('repair_orders.totals')" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-clear-search"
                    data-bs-dismiss="modal">{{ __('lang.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveData()">{{ __('lang.save') }}</button>
            </div>
        </div>
    </div>
</div>
