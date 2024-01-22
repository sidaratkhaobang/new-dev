<div class="row push mb-4">
    <div class="col-sm-3">
        <x-forms.input-new-line id="import_worksheet_no" :value="$d->ref_worksheet_no" :label="__('driving_jobs.worksheet_no')" />
    </div>
    <div class="col-sm-3">
        <x-forms.date-input id="delivery_date" :value="$d->delivery_date" :label="__('driving_jobs.delivery_date')" />
    </div>
    <div class="col-sm-6">
        <x-forms.input-new-line id="dealer" :value="$d->dealer" :label="__('driving_jobs.dealer')" />
    </div>
</div>
<div class="row push mb-3">
    <div class="col-sm-3">
        <x-forms.input-new-line id="import_delivery_place" :value="$d->import_delivery_place" :label="__('driving_jobs.delivery_place')" />
    </div>
</div>