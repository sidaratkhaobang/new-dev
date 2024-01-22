
<div class="row mb-4">
    <div class="col-sm-3">
        <x-forms.select-option id="sign_side" :value="null" :list="$contract_signer_side_list" :label="__('contract.contract_side')" :optionals="['placeholder' => __('lang.select_option')]"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="sign_type" :value="null" :label="__('ประเภทผู้เซ็น')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
    </div>
    <div class="col-sm-4">
        <x-forms.input-new-line id="sign_name" :value="null" :label="__('ชื่อผู้เซ็น')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
    </div>
    <div class="col-sm-2 align-self-end">
        <x-forms.checkbox-inline id="is_attorney_check" :list="[
            [
                'id' => STATUS_ACTIVE,
                'name' => __('contract.is_attorney'),
                'value' => STATUS_ACTIVE,
            ],
        ]" :label="null" :value="null" />
    </div>
</div>
<div class="row mb-4">
    <div class="col-sm-3">
        @if (Route::is('*.show'))
            <x-forms.view-image id="contract_attorney_file" :label="__('หนังสือมอบอำนาจ (ถ้ามี)')" :list="[]" />
        @else
            <x-forms.upload-image id="contract_attorney_file" :label="__('หนังสือมอบอำนาจ (ถ้ามี)')" />
        @endif
    </div>
    
    {{-- <div class="col-sm-3">
        @if (Route::is('*.show'))
            <x-forms.view-image id="contract_attach_file" :label="__('แนบไฟล์สัญญา (Final Version)')" :list="isset($contract_attach_file) ? $contract_attach_file : []" />
        @else
            <x-forms.upload-image id="contract_attach_file" :label="__('แนบไฟล์สัญญา (Final Version)')" />
        @endif
    </div> --}}
    @if(!Route::is('*.show'))
        <div class="col-sm-1 align-self-center">
            <button type="button" class="btn btn-primary mt-4 btn-add-attorney-file"><i class="fa fa-plus-circle me-1"></i> {{ __('เพิ่ม') }}</button>
        </div>
    @endif
</div>
