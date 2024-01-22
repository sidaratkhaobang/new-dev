<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="long_worksheet_no" :value="$d->ref_worksheet_no" :label="__('driving_jobs.worksheet_no')" />
    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="long_customer" :value="$d->parent_customer" :label="__('driving_jobs.customer')" />
    </div>
</div>
<div class="row push mb-3">
    <div class="col-sm-3">
        <x-forms.date-input id="contract_start_date" :value="$d->contract_start_date" :label="__('driving_jobs.contract_start_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="contract_end_date" :value="$d->contract_end_date" :label="__('driving_jobs.contract_end_date')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="long_delivery_place" :value="null" :label="__('driving_jobs.delivery_place')" />
    </div>
</div>