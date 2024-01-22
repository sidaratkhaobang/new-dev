<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.old_controlling') }}</p>
        <p class="grey-text" id="old_controlling">0009</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.new_controlling') }}</p>
        <p class="grey-text" id="new_controlling">1000</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.old_company') }}</p>
        <p class="grey-text" id="old_company">0009</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.new_company') }}</p>
        <p class="grey-text" id="new_company">1000</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.new_cost') }}</p>
        <p class="grey-text" id="new_cost">{{ $d->cost_center }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.valid_from') }}</p>
        <p class="grey-text" id="valid_from">{{ $d->valid_from }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.valid_to') }}</p>
        <p class="grey-text" id="valid_to">31.12.9999</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.person_responsible') }}</p>
        <p class="grey-text" id="person_responsible">Chay Bor.</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <p class="size-text">{{ __('asset_cars.name') . '/' . __('asset_cars.description') }}</p>
        <p class="grey-text" id="name_car">{{ $d->car_class_name }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.department') }}</p>
        <p class="grey-text" id="department">Vehicles</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.new_cctr') }}</p>
        <p class="grey-text" id="new_cctr">C</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.new_hierarchy') }}</p>
        <p class="grey-text" id="new_hierarchy">TLS</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.new_currency') }}</p>
        <p class="grey-text" id="new_currency">THB</p>
    </div>
</div>
