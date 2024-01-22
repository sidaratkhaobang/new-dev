{{-- Car Detail --}}
<div class="block {{ __('block.styles') }}" id="car_borrow" style="display: none">
    <div class="block-content">
<h4>{{ __('borrow_cars.borrow_car_detail') }}</h4>
<hr>
<div class="row push mb-4">

    <div class="row push mb-4">
        {{-- <div class="col-sm-3">
            <x-forms.select-option id="borrow_branch_id" :value="$d->borrow_branch_id" :list="$branch_list" :label="__('transfer_cars.branch')" :optionals="['required' => true]"/>
        </div> --}}
        <div class="col-sm-3">
            <x-forms.select-option id="car_id" :value="$d->car_id" :list="$car_lists" :label="__('transfer_cars.license_plate_chassis')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="car_class" :value="$d->car && $d->car->carClass ? $d->car->carClass->full_name : null" :label="__('car_classes.class')" :optionals="['required' => true]"/>
        </div>
        <div class="col-sm-3">
            <x-forms.input-new-line id="car_color" :value="$d->car && $d->car->carColor ? $d->car->carColor->name : null" :label="__('car_classes.color')" :optionals="['required' => true]"/>
        </div>
    </div>
</div>
</div>
</div>
