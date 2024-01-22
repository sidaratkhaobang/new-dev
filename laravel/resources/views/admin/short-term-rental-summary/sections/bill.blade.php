<x-blocks.block :title="'การวางบิล'" :optionals="['is_toggle' => false]">
    <div class="row push">
        <div class="col-sm-3">
            <x-forms.radio-inline :id="'type_create_invoice'" :label="'วิธีการออกใบแจ้งหนี้'"
                                  :list="$invoice_type_list ?? []" :value="null"/>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option :id="'start_date'" :label="'เริ่มต้นทุกวันที่'" :list="$invoice_date_length ?? []"
                                   :value="null"/>
        </div>
        <div class="col-sm-3">
            <x-forms.select-option :id="'end_date'" :label="'จนถึงวันที่'" :list="$invoice_date_length ?? []"
                                   :value="null"/>
        </div>
    </div>
    <div class="row push">
        <div class="col-sm-12">
            <x-forms.text-area-new-line id="remark_billing" :value="null" :label="'หมายเหตุ'"/>
        </div>
    </div>
</x-blocks.block>