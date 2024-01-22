<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="customer_code" :value="$d->customer_code" :label="__('customers.customer_code')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="debtor_code" :value="$d->debtor_code" :label="__('customers.debtor_code')" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="customer_type" :value="$d->customer_type" :list="$customer_type_list" :label="__('customers.customer_type')"
            :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="customer_grade" :value="$d->customer_grade" :list="$customer_grade_list" :label="__('customers.customer_grade')" />
    </div>
</div>

<div class="row push">
    <div class="col-sm-6">
        <x-forms.input-new-line id="name" :value="$d->name" :label="__('customers.name')" :optionals="['required' => true]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="tax_no" :value="$d->tax_no" :label="__('customers.tax_no')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="email" :value="$d->email" :label="__('customers.email')" />
    </div>
</div>

<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="prefixname_th" :value="$d->prefixname_th" :label="__('customers.prefixname_th')" :optionals="['maxlength' => 255]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="fullname_th" :value="$d->fullname_th" :label="__('customers.fullname_th')" :optionals="['maxlength' => 255]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="prefixname_en" :value="$d->prefixname_en" :label="__('customers.prefixname_en')" :optionals="['maxlength' => 255]" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="fullname_en" :value="$d->fullname_en" :label="__('customers.fullname_en')" :optionals="['maxlength' => 255]" />
    </div>
</div>

<div class="row push">
    <div class="col-sm-12">
        <x-forms.text-area-new-line id="address" :value="$d->address" :label="__('customers.address')" />
    </div>
</div>

<div class="row push">
    <div class="col-sm-3">
        <x-forms.select-option id="province_id" :value="$d->province_id" :list="null" :label="__('customers.province')"
            :optionals="[
                'ajax' => true,
                'default_option_label' => $province_name,
            ]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="district_id" :value="$d->district_id" :list="null" :label="__('customers.district')"
            :optionals="[
                'ajax' => true,
                'default_option_label' => $district_name,
            ]" />
    </div>
    <div class="col-sm-3">
        <x-forms.select-option id="subdistrict_id" :value="$d->subdistrict_id" :list="null" :label="__('customers.subdistrict')"
            :optionals="[
                'ajax' => true,
                'default_option_label' => $subdistrict_name,
            ]" />
    </div>
</div>

<div class="row push">
    <div class="col-sm-3">
        <x-forms.input-new-line id="fax" :value="$d->fax" :label="__('customers.fax')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="tel" :value="$d->tel" :label="__('customers.tel')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="phone" :value="$d->phone" :label="__('customers.phone')" />
    </div>
</div>

<div class="row push">
    <div class="col-sm-6">
        <x-forms.select-option id="sale_id" :value="$d->sale_id" :list="$sale_list" :label="__('customers.sale')" />
    </div>
    <div class="col-sm-6">
        <x-forms.select-option id="customer_group[]" :value="$customer_group" :list="$customer_group_list" :label="__('customers.customer_group')"
            :optionals="['multiple' => true]" />
    </div>
</div>
