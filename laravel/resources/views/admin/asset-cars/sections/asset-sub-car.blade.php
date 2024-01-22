<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.company_code') }}</p>
        <p class="grey-text" id="company_code_sub">1005 </p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.asset_class') }}</p>
        <p class="grey-text" id="asset_class_sub">{{ $d->asset_class }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.sub_asset') }}</p>
        <p class="grey-text" id="sub_asset_sub">1</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.serial') }}</p>
        <p class="grey-text" id="serial_sub">{{ $d->engine_no }}</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.unit') }}</p>
        <p class="grey-text" id="unit_sub">EA</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.cost_center') }}</p>
        <p class="grey-text" id="cost_center_sub">{{ $d->cost_center }}</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.fund_code') }}</p>
        <p class="grey-text" id="fund_code_sub">DZZCAR0501</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.fund_center') }}</p>
        <p class="grey-text" id="fund_center_sub">{{ $d->cost_center }}</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.evaluation_group_1') }}</p>
        <p class="grey-text" id="evaluation_group_1_sub"></p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.evaluation_group_3') }}</p>
        <p class="grey-text" id="evaluation_group_3_sub"></p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.dep_01') }}</p>
        <p class="grey-text" id="dep_01_sub">Z001</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.ul_01') }}</p>
        <p class="grey-text" id="ul_01_sub">5</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.scrap_01') }}</p>
        <p class="grey-text" id="scrap_01_sub">1</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.dep_02') }}</p>
        <p class="grey-text" id="dep_02_sub">Z001</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.ul_02') }}</p>
        <p class="grey-text" id="ul_02_sub">5</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.scrap_02') }}</p>
        <p class="grey-text" id="scrap_02_sub">1</p>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.dep_03') }}</p>
        <p class="grey-text" id="dep_03_sub">Z001</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.ul_03') }}</p>
        <p class="grey-text" id="ul_03_sub">5</p>
    </div>
    <div class="col-sm-3">
        <p class="size-text">{{ __('asset_cars.scrap_03') }}</p>
        <p class="grey-text" id="scrap_03_sub">1</p>
    </div>
</div>
<div class="row">
    @foreach ($asset_accessory as $item_accessory)
        <div class="col-sm-3">
            <p class="size-text">{{ __('asset_cars.inventory_note') }}</p>
            <p class="grey-text" id="inventory_note_sub">{{ $item_accessory->worksheet_no }}</p>
        </div>
        <div class="col-sm-9">
            <p class="size-text">{{ __('asset_cars.description_1') . '/' . __('asset_cars.description_2') }}</p>
            <p class="grey-text" id="description_sub">{{ $item_accessory->accessory_name }}</p>
        </div>
    @endforeach
</div>
