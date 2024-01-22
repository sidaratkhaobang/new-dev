<div class="block {{ __('block.styles') }}">
    <div class="block-content">
        <div class="d-md-flex justify-content-md-between align-items-md-center mb-3">
            <div>
                <h4><i class="fa fa-file-lines me-1"></i>{{ __('repair_orders.table_center') }}</h4>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="receive_repair_order_date" :value="$d->receive_repair_order_date" :label="__('repair_orders.receive_repair_order_date')" />
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="receive_quotation" :value="$d->receive_quotation" :label="__('repair_orders.receive_quotation')" />
            </div>
            <div class="col-sm-3">
                <x-forms.upload-image :id="'expense_files'" :label="__('repairs.document')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="user_file" :value="$user_file" :label="__('repair_orders.user_file')" />
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-6">
                <x-forms.radio-inline id="is_expenses" :value="$d->is_expenses ? $d->is_expenses : null" :list="$have_expenses_list" :label="__('repair_orders.is_expenses')" />
            </div>
        </div>
    </div>
</div>
