<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('change_registrations.document_registration_information'),
        'block_option_id' => '_list',
    ])
    <div class="block-content">
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                <tr>
                    <th>วันที่ออกไปใบเสร็จ</th>
                    <th>เลขที่ใบเสร็จ</th>
                    <th>ค่าใบเสร็จ</th>
                    <th>ค่าดำเนินการ</th>
                    <th>รวม</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>
                        <x-forms.date-input id="receipt_date" :value="$d?->receipt_date"
                                            :label="null"/>
                    </th>
                    <th>
                        <x-forms.input-new-line id="receipt_no" :value="$d?->receipt_no"
                                                :label="null"/>
                    </th>
                    <th>
                        <x-forms.input-new-line id="receipt_fee" :value="$d?->receipt_fee"
                                                :label="null"
                                                :optionals="[
                                                            'input_class' => 'number-format col-sm-4',
                                                            ]"
                        />
                    </th>
                    <th>
                        <x-forms.input-new-line id="service_fee" :value="$d?->service_fee"
                                                :label="null"
                                                :optionals="[
                                                            'input_class' => 'number-format col-sm-4',
                                                            ]"
                        />
                    </th>
                    <th>
                        <x-forms.input-new-line id="summary_service" :value="$d?->summary_service"
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
