<h4>{{ __('cars.other_detail') }}</h4>
<hr>
<div class="row push mb-5">
    <div class="row push mb-4">
        <div class="col-sm-3">
            <x-forms.input-new-line id="ownership_type" :value="$d->ownership_type" :label="__('cars.ownership_type')" />
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="ownership" :value="$d->ownership" :label="__('cars.ownership')" />
        </div>
        <div class="col-sm-3">
            <x-forms.date-input id="delivery_date" :value="$d->delivery_date" :label="__('cars.delivery_date')" />
        </div>
    </div>
</div>
