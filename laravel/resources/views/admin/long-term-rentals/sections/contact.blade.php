<div class="row push mb-4">
    <div class="col-sm-4">
        <x-forms.input-new-line id="contact_name" :value="$d->contact_name" :label="__('long_term_rentals.contact_name')" />
    </div>
    <div class="col-sm-4">
        <x-forms.input-new-line id="contact_email" :value="$d->contact_email" :label="__('long_term_rentals.email')" />
    </div>
    <div class="col-sm-4">
        <x-forms.input-new-line id="contact_tel" :value="$d->contact_tel" :label="__('long_term_rentals.tel')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-12">
        <x-forms.input-new-line id="contact_remark" :value="$d->contact_remark" :label="__('long_term_rentals.remark')" />
    </div>
</div>
