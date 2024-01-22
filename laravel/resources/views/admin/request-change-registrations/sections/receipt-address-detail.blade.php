<div class="row mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="name_receipt" :value="$d->name_receipt" :label="__('change_registrations.name_contact')" :optionals="['required' => true]"/>    
        </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="tax_no_receipt" :value="$d->tax_no_receipt" :label="__('change_registrations.tax_no')" :optionals="['required' => true, 'oninput' => true]"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="tel_receipt" :value="$d->tel_receipt" :label="__('change_registrations.tel_contact')" :optionals="['required' => true, 'oninput' => true, 'maxlength' => 10]"/>
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="email_receipt" :value="$d->email_receipt" :label="__('change_registrations.email_contact')" :optionals="['required' => true]"/>
    </div>
</div>
<div class="row mb-4">
    <div class="col-sm-12">
        <x-forms.text-area-new-line id="address_receipt" :value="$d->address_receipt" :label="__('change_registrations.address_contact')" :optionals="['placeholder' => __('lang.input.placeholder'),'row' => 2,'required' => true]"/>

    </div>
</div>

@push('scripts')
    <script>
    </script>
@endpush
