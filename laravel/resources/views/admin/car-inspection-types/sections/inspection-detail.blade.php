<div class="row">
    <div class="col-sm-6">
        <x-forms.input-new-line id="inspect_type_name" :value="$d->name" :label="__('car_inspection_types.name')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="car_type_name" :value="$d->name" :label="__('car_inspection_types.car_type_name')" />
    </div>
    <div class="col-sm-3">
        <x-forms.input-new-line id="rental_type" :value="$d->name" :label="__('car_inspection_types.rental_type')" />
    </div>
</div>
