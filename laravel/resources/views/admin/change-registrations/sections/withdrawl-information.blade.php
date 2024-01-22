<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('change_registrations.avance'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                <tr>
                    <th>เลขที่ MEMO เบิกเงิน</th>
                    <th>ค่าใบเสร็จ</th>
                    <th>ค่าดำเนินการ</th>
                    <th>รวมเบิก</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>
                        <x-forms.input-new-line id="memo_no" :value="$d?->memo_no"
                                                :label="null"/>
                    </th>
                    <th>
                        <x-forms.input-new-line id="receipt_avance" :value="$d?->receipt_avance"
                                                :label="null"
                                                :optionals="[
                                                            'input_class' => 'number-format col-sm-4',
                                                            ]"
                        />
                    </th>
                    <th>
                        <x-forms.input-new-line id="operation_fee_avance" :value="$d?->operation_fee_avance"
                                                :label="null"
                                                :optionals="[
                                                            'input_class' => 'number-format col-sm-4',
                                                            ]"
                        />
                    </th>
                    <th>
                        <x-forms.input-new-line id="summary_avance" :value="$d?->summary_avance"
                                                :label="null"
                                                :optionals="[
                                                            'input_class' => 'number-format col-sm-4',
                                                            ]"
                        />
                    </th>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
