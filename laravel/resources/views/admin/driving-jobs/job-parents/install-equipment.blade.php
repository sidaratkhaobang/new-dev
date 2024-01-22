<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="install_equipment_no" :value="$d->ref_worksheet_no" :label="__('driving_jobs.worksheet_no')" />
    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="supplier_name" :value="$d->supplier_name" :label="__('driving_jobs.supplier')" />
    </div>
</div>
<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="ie_destination" :value="$d->ie_destination" :label="__('driving_jobs.delivery_place')" />
    </div>
</div>