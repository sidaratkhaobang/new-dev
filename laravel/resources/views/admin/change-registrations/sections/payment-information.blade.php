<div class="block {{ __('block.styles') }}">
    @section('block_options_generate_qr_code')
        <button class="btn btn-primary">
            {{__('change_registrations.button_generate_qr')}}
        </button>
    @endsection
    @include('admin.components.block-header', [
        'text' => __('change_registrations.pay_receipt_detail'),
        'block_option_id' => '_generate_qr_code'
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="payment_date" :value="$d?->payment_date"
                                    :label="__('change_registrations.payment_date')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.upload-image :id="'document_payment'" :label="__('change_registrations.document_payment')"/>

            </div>
        </div>
        <div class="table-wrap db-scroll">
            <table class="table table-striped table-vcenter">
                <thead class="bg-body-dark">
                <tr>
                    <th>รายการ</th>
                    <th>จำนวน</th>
                    <th>ราคาต่อหน่วย</th>
                    <th>ราคารวม</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>รวม</th>
                    <th>0</th>
                    <th>0</th>
                    <th>0</th>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
