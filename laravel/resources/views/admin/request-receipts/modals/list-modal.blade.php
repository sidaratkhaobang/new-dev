<x-modal :id="'list'" :title="'เพิ่มรายการ'">
    <div class="row mb-4">
        <div class="col-sm-12">
            <x-forms.input-new-line id="list_name" :value="null" :label="__('request_receipts.list_name')" />
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-sm-4">
            <label for="input_tag" class="text-start col-form-label">{{ __('request_receipts.amount') }}<span
                    class="text-danger">*</span></label>
            <input type="text" id="amount" class="form-control number-format col-sm-4"  @change="sumTotal" />
        </div>
        <div class="col-sm-4">
                <label for="input_tag" class="text-start col-form-label">{{ __('request_receipts.fee_deducted') }}<span
                    class="text-danger">*</span></label>
            <input type="text" id="fee_deducted" class="form-control number-format col-sm-4"  @change="sumTotal" />
        </div>
        <div class="col-sm-4">
            <x-forms.input-new-line id="total" :value="null" :label="__('request_receipts.total')" :optionals="[ 'input_class' => 'number-format col-sm-4 disabled']" />
        </div>
    </div>
    <x-forms.hidden id="id_line" :value="null" />
    <x-forms.hidden id="status" :value="$d->status" />
    <x-slot name="footer">
        <button type="button" class="btn btn-secondary btn-clear-search"
            data-bs-dismiss="modal">{{ __('lang.back') }}</button>

        <button type="button" class="btn btn-primary btn-save-car" onclick="saveList()">{{ __('lang.save') }}</button>
    </x-slot>
</x-modal>
