@include('admin.contracts.sections.btn-tap-group')
{{-- @section('block_options_history')
    <button class="btn btn-primary btn-custom-size btn-show-modal-history-edit-contract" type="button">
        <i class="fa fa-rotate-left"></i> {{ __('ประวัติแก้ไขข้อมูล') }}
    </button>
@endsection --}}
<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header',[
        'text' => __('ข้อมูลสัญญา'),
        'block_icon_class' => 'icon-document',
        // 'block_option_id' => '_history'
    ])
    <div class="block-content">
        <div class="row push mb-4">
            {{-- <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="worksheet_no" :value="$data->worksheet_no" :label="__('เลขที่สัญญา')"/>
            </div> --}}
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="worksheet_no_customer" :value="$data->worksheet_no_customer" :label="__('เลขที่สัญญา/ลูกค้าทำเอง')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
            </div>
            <div class="col-sm-3 mb-1">
                @if(isset($data->contract_type))
                    <x-forms.input-new-line id="contract_type" :value="$data->contract_type" :label="__('เทมเพล็ตประเภทสัญญา')"/>
                @else
                    <x-forms.select-option id="contract_type" :value="$data->contract_type" :list="$contractCategoryList" :label="__('เทมเพล็ตประเภทสัญญา')"/>
                @endif
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="job_worksheet_no" :value="$data->job?->worksheet_no" :label="__('เลขที่ใบขอเช่า')"/>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="job_category" :value="$data->job?->getJobTypeName()" :label="__('ประเภทงานเช่า')"/>
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="job_period" :value="null" :label="__('ระยะเวลาเช่า')"/>
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.date-input id="date_document" :value="$data->date_document" :label="__('วันที่ได้รับเอกสาร')" :optionals="['placeholder' => __('lang.select_date')]"/>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3 mb-1">
                <x-forms.date-input id="job_start_date" :value="$data->contract_start_date" :label="__('วันที่เริ่มเช่า')" :optionals="['placeholder' => __('lang.select_date')]"/>
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.date-input id="job_end_date" :value="$data->contract_end_date" :label="__('วันที่สิ้นสุดการเช่า')" :optionals="['placeholder' => __('lang.select_date')]"/>
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="contract_value" :value="null" :label="__('มูลค่าสัญญา')"/>
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="job_value_no_vat" :value="null" :label="__('ค่าเช่า (ไม่รวม vat)')"/>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-6 mb-1">
                <x-forms.text-area-new-line id="remark" :value="$data->remark" :label="__('หมายเหตุ')" :optionals="['placeholder' => __('lang.input.placeholder'),'row' => 1]"/>
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="contract_status" :value="__('contract.status_text_' . $data->status)" :label="__('สถานะ')"/>
            </div>
        </div>
        <div class="row push mb-4">
            <div class="col-sm-3">
                <x-forms.date-input id="date_offer_sign" :value="$data->date_offer_sign" :label="__('วันที่จัดทำเสนอลงนาม')" :optionals="['placeholder' => __('lang.select_date')]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="date_send_contract" :value="$data->date_send_contract" :label="__('วันที่ส่งให้ผู้เช่าเซ็น')" :optionals="['placeholder' => __('lang.select_date')]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.date-input id="date_return_contract" :value="$data->date_return_contract" :label="__('วันที่รับสัญญาคืน')" :optionals="['placeholder' => __('lang.select_date')]"/>
            </div>
            <div class="col-sm-3">
                @if (!isset($view) && in_array($data->status, [ContractEnum::SEND_CUSTOMER_SIGN]))
                    <x-forms.upload-image :id="'contract_attach_file'" :label="__('แนบไฟล์สัญญา (Final Version)')" />
                @else
                    @if (sizeof($contract_attach_file) > 0)
                        <x-forms.view-image :id="'contract_attach_file'" :label="__('แนบไฟล์สัญญา (Final Version)')" :list="$contract_attach_file" />
                    @endif
                @endif
                {{-- @if (isset($view) || 
                    (!in_array($data->status, [ContractEnum::ACTIVE_BETWEEN_CONTRACT]) && sizeof($contract_attach_file) > 0)
                )
                    <x-forms.view-image :id="'contract_attach_file'" :label="__('แนบไฟล์สัญญา (Final Version)')" :list="$contract_attach_file" />
                @else
                    <x-forms.upload-image :id="'contract_attach_file'" :label="__('แนบไฟล์สัญญา (Final Version)')" />
                @endif --}}
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.radio-inline id="start_rent" :value="$data->start_rent" :list="$conditionStartList" :label="__('การเริ่มคิดค่าเช่า')"/>
            </div>
            <div class="col-sm-3">
                <x-forms.radio-inline id="end_rent" :value="$data->end_rent" :list="$conditionEndList" :label="__('สิ้นสุดการเริ่มคิดค่าเช่า')"/>
            </div>
        </div>
    </div>
</div>

<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('ข้อมูลลูกค้า'),
        'block_icon_class' => 'icon-document',
    ])
    <div class="block-content">
        <div class="row">
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="customer_code" :value="$data->customer->customer_code" :label="__('รหัสลูกค้า')"/>
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="customer_name" :value="$data->customer->name" :label="__('ลูกค้า')"/>
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.select-option id="customer_group[]" :value="$data->customer->getCustomerGroupArray()" :list="$customerGroupList" :label="__('กลุ่ม')" :optionals="['multiple' => true]"/>
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="customer_category" :value="$data->customer->account_code" :label="__('หน่วยงาน/สายธุรกิจ')"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="customer_phone_number" :value="$data->customer->tel" :label="__('โทร')"/>
            </div>
            <div class="col-sm-3 mb-1">
                <x-forms.input-new-line id="customer_email" :value="$data->customer->email" :label="__('E-Mail')"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-12 mb-1">
                <x-forms.text-area-new-line id="customer_address" :value="$data->customer->address" :label="__('ที่อยู่')" :optionals="['placeholder' => __('lang.input.placeholder'),'row' => 3]"/>
            </div>
        </div>
    </div>
</div>

{{-- <div class="block {{ __('block.styles') }}"> --}}
        @include('admin.contracts.page-1.table-file-upload')
        {{-- @include('admin.contracts.sections.submit') --}}
        @include('admin.contracts.sections.submit')

@push('scripts')
    <script>
        const elementInput = [
            'contract_status' ,
            'worksheet_no' ,
            'job_worksheet_no' ,
            'job_category' ,
            'job_period' ,
            'job_start_date' ,
            'job_end_date' ,
            'contract_value' ,
            'job_value_no_vat' ,
            'customer_code' ,
            'customer_name' ,
            'customer_type' ,
            'customer_category' ,
            'customer_phone_number' ,
            'customer_address' ,
            'customer_email' ,

            'contract_type' ,
            'date_offer_sign' ,
            'date_send_contract' ,
            'date_return_contract' ,
        ];
        elementInput.forEach((value , index , array) => {
            $('#' + value).prop('disabled' , true);
        });

        $('[name="customer_group[]"]').prop('disabled' , true);

        @if($data->status == \App\Enums\ContractEnum::REQUEST_CONTRACT)
        $('#contract_type').prop('disabled' , false);
        @elseif($data->status == \App\Enums\ContractEnum::ACTIVE_CONTRACT)
        $('#date_offer_sign').prop('disabled' , false);
        @elseif($data->status == \App\Enums\ContractEnum::SEND_OFFER_SIGN)
        $('#date_send_contract').prop('disabled' , false);
        @elseif($data->status == \App\Enums\ContractEnum::SEND_CUSTOMER_SIGN)
        $('#date_return_contract').prop('disabled' , false);
        @endif

        $('.form-control').prop('disabled' , {{Route::is('*.show')}});
        $('.form-check-input').prop('disabled' , {{Route::is('*.show')}});
    </script>
@endpush
