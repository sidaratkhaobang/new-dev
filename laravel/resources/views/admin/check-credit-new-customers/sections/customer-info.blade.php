<div class="block {{ __('block.styles') }}">
    @include('admin.components.block-header', [
        'text' => __('check_credit.form.section_customer'),
    ])
    <div class="block-content">
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_code" :value="$d->customer_code" :label="__('check_credit.form.customer_code')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="branch_id" :value="$d->branch_id" :list="$branch_list" :label="__('check_credit.form.branch_id')" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="customer_type" :value="$d->customer_type" :list="$customer_type_list" :label="__('check_credit.form.customer_type')" :optionals="['required' => $d->status == \App\Enums\CheckCreditStatusEnum::CONFIRM]" />
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="customer_grade" :value="$d->customer_grade" :list="$customer_grade_list" :label="__('check_credit.form.customer_grade')" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_name" :value="$d->name" :label="__('check_credit.form.customer_name')" :optionals="['placeholder' => __('lang.input.placeholder'),'required' => $d->status == \App\Enums\CheckCreditStatusEnum::CONFIRM]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.select-option id="customer_group[]" :value="isset($d->customer_group) ? json_decode($d->customer_group) : []" :list="$customer_group_list" :label="__('กลุ่ม')" :optionals="['multiple' => true]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_tax_number" :value="$d->tax_no" :label="__('check_credit.form.customer_tax_number')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_prefix_name_th" :value="$d->prefixname_th" :label="__('check_credit.form.customer_prefix_name_th')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_full_name_th" :value="$d->fullname_th" :label="__('check_credit.form.customer_full_name_th')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_prefix_name_en" :value="$d->prefixname_en" :label="__('check_credit.form.customer_prefix_name_en')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_full_name_en" :value="$d->fullname_en" :label="__('check_credit.form.customer_full_name_en')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_email" :value="$d->email" :label="__('check_credit.form.customer_email')" :optionals="['placeholder' => __('lang.input.placeholder'),'type' => 'email']"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_fax" :value="$d->fax" :label="__('check_credit.form.customer_fax')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_mobile_number" :value="$d->tel" :label="__('check_credit.form.customer_mobile_number')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
            </div>
            <div class="col-sm-3">
                <x-forms.input-new-line id="customer_phone_number" :value="$d->phone" :label="__('check_credit.form.customer_phone_number')" :optionals="['placeholder' => __('lang.input.placeholder')]"/>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-6">
                <x-forms.text-area-new-line id="customer_address" :value="$d->address" :label="__('check_credit.form.customer_address')" :optionals="['placeholder' => __('lang.input.placeholder'),'row' => 1]"/>
            </div>
        </div>
    </div>
</div>
