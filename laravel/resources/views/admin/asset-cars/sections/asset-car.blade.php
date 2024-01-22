<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.company_code') }}</p>
        <p class="grey-text" id="company_code">1005</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.asset_class') }}</p>
        <p class="grey-text" id="asset_class">{{ $d->asset_class }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.sub_asset') }}</p>
        <p class="grey-text" id="sub_asset">0</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.serial') }}</p>
        <p class="grey-text" id="serial">{{ $d->engine_no }}</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <p class="size-text">{{ __('asset_cars.description_1') }}</p>
        <p class="grey-text" id="description_1">{{ $d->car_class_name }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.inventory_note') }}</p>
        <p class="grey-text" id="inventory_note">{{ $d->po_no }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.unit') }}</p>
        <p class="grey-text" id="unit">EA</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.cost_center') }}</p>
        <p class="grey-text" id="cost_center">{{ $d->cost_center }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.fund_code') }}</p>
        <p class="grey-text" id="fund_code">DZZCAR0501</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.fund_center') }}</p>
        <p class="grey-text" id="fund_center">{{ $d->cost_center }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.evaluation_group_1') }}</p>
        <p class="grey-text" id="evaluation_group_1">{{ $d->cc }}</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.evaluation_group_3') }}</p>
        <p class="grey-text" id="evaluation_group_3"></p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.dep_01') }}</p>
        <p class="grey-text" id="dep_01">Z001</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.ul_01') }}</p>
        <p class="grey-text" id="ul_01">5</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.scrap_01') }}</p>
        <p class="grey-text" id="scrap_01">{{ number_format($d->scrap, 2) }}</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.dep_02') }}</p>
        <p class="grey-text" id="dep_02">Z001</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.ul_02') }}</p>
        <p class="grey-text" id="ul_02">5</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.scrap_02') }}</p>
        <p class="grey-text" id="scrap_02">{{ number_format($d->scrap, 2) }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.dep_03') }}</p>
        <p class="grey-text" id="dep_03">Z001</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.ul_03') }}</p>
        <p class="grey-text" id="ul_03">5</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.scrap_03') }}</p>
        <p class="grey-text" id="scrap_03">{{ number_format($d->scrap, 2) }}</p>
    </div>
</div>
